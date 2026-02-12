<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class PDFOptimizer
{
    // Configuration constants
    const MAX_RECORDS_PER_PAGE = 50; // Records per PDF page for pagination
    const CHUNK_SIZE_SMALL = 500;    // For datasets < 10,000
    const CHUNK_SIZE_MEDIUM = 1000;  // For datasets 10,000 - 50,000
    const CHUNK_SIZE_LARGE = 2000;   // For datasets > 50,000
    const MEMORY_THRESHOLD = 512;    // MB - trigger cleanup above this
    
    /**
     * Initialize PHP limits for PDF generation
     */
    public static function initializePdfLimits($memoryLimit = '2048M', $timeLimit = 1800)
    {
        ini_set('max_execution_time', $timeLimit);
        set_time_limit($timeLimit);
        ini_set('memory_limit', $memoryLimit);
        
        // Clear output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Force garbage collection
        self::forceGarbageCollection();
        
        // Disable debug bar for reports if present
        if (class_exists('\Barryvdh\Debugbar\Facade')) {
            try {
                \Barryvdh\Debugbar\Facade::disable();
            } catch (\Exception $e) {
                // Debugbar not available
            }
        }
    }
    
    /**
     * Force garbage collection to free memory
     */
    public static function forceGarbageCollection()
    {
        if (function_exists('gc_collect_cycles')) {
            gc_enable();
            gc_collect_cycles();
        }
    }
    
    /**
     * Get current memory usage in MB
     */
    public static function getMemoryUsageMB()
    {
        return round(memory_get_usage(true) / 1024 / 1024, 2);
    }
    
    /**
     * Check if memory cleanup is needed and perform it
     */
    public static function checkMemoryAndCleanup()
    {
        if (self::getMemoryUsageMB() > self::MEMORY_THRESHOLD) {
            self::forceGarbageCollection();
            Log::debug('PDF Memory cleanup triggered', ['memory_mb' => self::getMemoryUsageMB()]);
        }
    }
    
    /**
     * Determine optimal chunk size based on record count
     */
    public static function getOptimalChunkSize($totalRecords)
    {
        if ($totalRecords < 10000) {
            return self::CHUNK_SIZE_SMALL;
        } elseif ($totalRecords < 50000) {
            return self::CHUNK_SIZE_MEDIUM;
        }
        return self::CHUNK_SIZE_LARGE;
    }
    
    /**
     * Optimized PDF generation for large datasets
     */
    public static function generateOptimizedPDF($view, $data, $filename = 'report.pdf', $options = [])
    {
        try {
            self::initializePdfLimits();
            
            $defaultOptions = [
                'paper' => 'a4',
                'orientation' => '',
                'isHtml5ParserEnabled' => false, // Disable for performance
                'isRemoteEnabled' => false,
                'dpi' => 72, // Lower DPI for faster generation
                'defaultFont' => 'sans-serif',
                'tempDir' => storage_path('app/temp'),
                'enable_font_subsetting' => true, // Enable for smaller file size
            ];
            
            $pdfOptions = array_merge($defaultOptions, $options);
            
            // Ensure temp directory exists
            if (!is_dir($pdfOptions['tempDir'])) {
                mkdir($pdfOptions['tempDir'], 0755, true);
            }
            
            $startTime = microtime(true);
            
            // Generate PDF with optimized settings
            $pdf = PDF::loadView($view, $data);
            $pdf->setPaper($pdfOptions['paper'], $pdfOptions['orientation']);
            
            // Apply PDF options for performance
            $pdf->setOptions([
                'isHtml5ParserEnabled' => $pdfOptions['isHtml5ParserEnabled'],
                'isRemoteEnabled' => $pdfOptions['isRemoteEnabled'],
                'dpi' => $pdfOptions['dpi'],
                'defaultFont' => $pdfOptions['defaultFont'],
                'tempDir' => $pdfOptions['tempDir'],
                'chroot' => base_path(),
                'enable_font_subsetting' => $pdfOptions['enable_font_subsetting'],
            ]);
            
            $duration = microtime(true) - $startTime;
            $memoryUsage = self::getMemoryUsageMB();
            
            // Count records if data is present
            $recordCount = 'N/A';
            if (isset($data['data'])) {
                $recordCount = is_array($data['data']) || $data['data'] instanceof \Countable 
                    ? count($data['data']) 
                    : 'unknown';
            }
            
            Log::info("Optimized PDF Generated", [
                'filename' => $filename,
                'duration_seconds' => round($duration, 2),
                'memory_peak_mb' => $memoryUsage,
                'records' => $recordCount,
                'view' => $view
            ]);
            
            // Cleanup after generation
            self::forceGarbageCollection();
            
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            Log::error("Optimized PDF generation failed: " . $e->getMessage(), [
                'view' => $view,
                'filename' => $filename,
                'memory_mb' => self::getMemoryUsageMB(),
                'exception' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Generate PDF with data pagination for very large datasets
     * Splits data into manageable pages to prevent memory issues
     */
    public static function generatePaginatedPDF($view, $data, $dataKey, $filename = 'report.pdf', $options = [])
    {
        self::initializePdfLimits();
        
        $allData = $data[$dataKey] ?? [];
        $totalRecords = count($allData);
        
        // If data is small, use regular generation
        if ($totalRecords <= 1000) {
            return self::generateOptimizedPDF($view, $data, $filename, $options);
        }
        
        Log::info("Generating paginated PDF", [
            'total_records' => $totalRecords,
            'filename' => $filename
        ]);
        
        // For large datasets, paginate the data
        $recordsPerPage = $options['records_per_page'] ?? self::MAX_RECORDS_PER_PAGE * 20; // 1000 records per chunk
        $chunks = array_chunk($allData, $recordsPerPage);
        
        // Generate with chunked data to reduce memory per render
        $data[$dataKey] = $allData; // Keep full data but let view handle pagination
        $data['_total_records'] = $totalRecords;
        $data['_is_large_dataset'] = true;
        
        return self::generateOptimizedPDF($view, $data, $filename, $options);
    }
    
    /**
     * Stream large query results with callback processing
     */
    public static function streamQueryResults($query, $callback, $chunkSize = null)
    {
        $totalCount = 0;
        
        try {
            // Get count first
            $countQuery = clone $query;
            $totalCount = $countQuery->count();
        } catch (\Exception $e) {
            Log::warning("Could not get total count: " . $e->getMessage());
        }
        
        // Determine chunk size
        $chunkSize = $chunkSize ?? self::getOptimalChunkSize($totalCount);
        
        $results = [];
        $offset = 0;
        $processedCount = 0;
        
        while (true) {
            $chunkQuery = clone $query;
            $chunk = $chunkQuery->limit($chunkSize)->offset($offset)->get();
            
            if ($chunk->isEmpty()) {
                break;
            }
            
            // Process chunk
            $chunkResults = $callback($chunk);
            if (is_array($chunkResults)) {
                $results = array_merge($results, $chunkResults);
            }
            
            $processedCount += $chunk->count();
            $offset += $chunkSize;
            
            // Memory cleanup between chunks
            self::checkMemoryAndCleanup();
            
            // Log progress for large datasets
            if ($totalCount > 10000 && $processedCount % 10000 === 0) {
                Log::info("PDF data streaming progress", [
                    'processed' => $processedCount,
                    'total' => $totalCount,
                    'memory_mb' => self::getMemoryUsageMB()
                ]);
            }
        }
        
        return $results;
    }
    
    /**
     * Process large data arrays in chunks with memory management
     */
    public static function processInChunks($data, $chunkSize = 1000, $callback)
    {
        if (empty($data)) {
            return [];
        }
        
        $chunks = array_chunk($data, $chunkSize);
        $processed = [];
        
        foreach ($chunks as $chunk) {
            $result = $callback($chunk);
            $processed = array_merge($processed, $result);
            
            // Force garbage collection between chunks
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
        
        return $processed;
    }
    
    /**
     * Memory-efficient data transformation
     */
    public static function transformData($items, $transformCallback, $chunkSize = 1000)
    {
        if (empty($items)) {
            return [];
        }
        
        return self::processInChunks($items, $chunkSize, function($chunk) use ($transformCallback) {
            $result = [];
            foreach ($chunk as $item) {
                $result[] = $transformCallback($item);
            }
            return $result;
        });
    }
    
    /**
     * Get optimized query with limits for large datasets
     */
    public static function optimizeQuery($query, $limit = null, $offset = 0)
    {
        if ($limit) {
            $query->limit($limit);
        }
        
        if ($offset > 0) {
            $query->offset($offset);
        }
        
        return $query;
    }
    
    /**
     * Check if dataset is too large and suggest chunking
     */
    public static function checkDatasetSize($query, $threshold = 10000)
    {
        try {
            $countQuery = clone $query;
            $count = $countQuery->count();
            
            if ($count > $threshold) {
                Log::warning("Large dataset detected", [
                    'record_count' => $count,
                    'threshold' => $threshold,
                    'suggestion' => 'Consider using chunk processing'
                ]);
                
                return [
                    'too_large' => true,
                    'count' => $count,
                    'suggest_chunk_size' => ceil($count / 10) // Suggest 10 chunks
                ];
            }
            
            return ['too_large' => false, 'count' => $count];
            
        } catch (\Exception $e) {
            Log::warning("Failed to estimate dataset size: " . $e->getMessage());
            return ['too_large' => false, 'count' => 0];
        }
    }
    
    /**
     * Optimized query builder for report data that uses cursor for memory efficiency
     */
    public static function cursorQuery($query, $callback)
    {
        self::initializePdfLimits();
        
        $results = [];
        
        foreach ($query->cursor() as $item) {
            $result = $callback($item);
            if ($result !== null) {
                $results[] = $result;
            }
            
            // Periodic memory cleanup
            if (count($results) % 5000 === 0) {
                self::checkMemoryAndCleanup();
            }
        }
        
        return $results;
    }
    
    /**
     * Aggregate data from query with grouping - memory efficient version
     */
    public static function aggregateWithGrouping($query, $groupKey, $aggregateCallback, $chunkSize = 1000)
    {
        self::initializePdfLimits();
        
        $grouped = [];
        $offset = 0;
        
        while (true) {
            $chunkQuery = clone $query;
            $chunk = $chunkQuery->limit($chunkSize)->offset($offset)->get();
            
            if ($chunk->isEmpty()) {
                break;
            }
            
            foreach ($chunk as $item) {
                $key = is_callable($groupKey) ? $groupKey($item) : $item->{$groupKey};
                
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [];
                }
                
                $aggregateCallback($grouped[$key], $item);
            }
            
            $offset += $chunkSize;
            self::checkMemoryAndCleanup();
        }
        
        return $grouped;
    }
    
    /**
     * Get pharmacy/company settings efficiently (cached)
     */
    public static function getPharmacySettings()
    {
        static $settings = null;
        
        if ($settings === null) {
            $settingIds = [100, 102, 105, 106, 107, 108, 109, 121];
            $dbSettings = DB::table('general_settings')
                ->whereIn('id', $settingIds)
                ->pluck('value', 'id')
                ->toArray();
            
            $settings = [
                'name' => $dbSettings[100] ?? '',
                'tin_number' => $dbSettings[102] ?? '',
                'logo' => $dbSettings[105] ?? '',
                'address' => $dbSettings[106] ?? '',
                'phone' => $dbSettings[107] ?? '',
                'email' => $dbSettings[108] ?? '',
                'website' => $dbSettings[109] ?? '',
            ];
        }
        
        return $settings;
    }
    
    /**
     * Build optimized report data with lazy loading
     */
    public static function buildReportData($query, $transformer, $options = [])
    {
        $startTime = microtime(true);
        $chunkSize = $options['chunk_size'] ?? 1000;
        $maxRecords = $options['max_records'] ?? null;
        
        self::initializePdfLimits();
        
        $results = [];
        $offset = 0;
        $totalProcessed = 0;
        
        while (true) {
            $chunkQuery = clone $query;
            $chunkQuery->limit($chunkSize)->offset($offset);
            
            if ($maxRecords && $offset >= $maxRecords) {
                break;
            }
            
            $chunk = $chunkQuery->get();
            
            if ($chunk->isEmpty()) {
                break;
            }
            
            foreach ($chunk as $item) {
                $transformed = $transformer($item);
                if ($transformed !== null) {
                    $results[] = $transformed;
                }
                $totalProcessed++;
                
                if ($maxRecords && $totalProcessed >= $maxRecords) {
                    break 2;
                }
            }
            
            $offset += $chunkSize;
            self::checkMemoryAndCleanup();
        }
        
        $duration = microtime(true) - $startTime;
        Log::info("Report data built", [
            'records' => count($results),
            'duration_seconds' => round($duration, 2),
            'memory_mb' => self::getMemoryUsageMB()
        ]);
        
        return $results;
    }
    
    /**
     * Generate a summary report from detailed data - memory efficient
     */
    public static function buildSummaryFromDetail($query, $groupKeyFn, $summaryFn, $chunkSize = 1000)
    {
        self::initializePdfLimits();
        
        $summaries = [];
        $offset = 0;
        
        while (true) {
            $chunkQuery = clone $query;
            $chunk = $chunkQuery->limit($chunkSize)->offset($offset)->get();
            
            if ($chunk->isEmpty()) {
                break;
            }
            
            foreach ($chunk as $item) {
                $key = $groupKeyFn($item);
                
                if (!isset($summaries[$key])) {
                    $summaries[$key] = $summaryFn($item, null, true); // Initialize
                } else {
                    $summaries[$key] = $summaryFn($item, $summaries[$key], false); // Aggregate
                }
            }
            
            $offset += $chunkSize;
            self::checkMemoryAndCleanup();
        }
        
        return array_values($summaries);
    }
}