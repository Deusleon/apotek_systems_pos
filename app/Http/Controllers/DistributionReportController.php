<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductionDistribution;
use App\Store;
use App\Setting;
use App\Http\Controllers\PDFOptimizer;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Use PDFOptimizer for memory and time limits
PDFOptimizer::initializePdfLimits('2048M', 1800);

class DistributionReportController extends Controller
{
    /**
     * Generate optimized PDF with memory management
     */
    private function generateOptimizedPdf($view, $data, $filename, $orientation = '')
    {
        PDFOptimizer::initializePdfLimits();
        
        try {
            $pdf = PDF::loadView($view, $data)
                ->setPaper('a4', $orientation)
                ->setOptions([
                    'isHtml5ParserEnabled' => false,
                    'isRemoteEnabled' => false,
                    'dpi' => 96,
                    'enable_font_subsetting' => true,
                ]);
            
            PDFOptimizer::forceGarbageCollection();
            
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error("PDF generation failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get pharmacy settings efficiently with single query
     */
    private function getPharmacySettingsOptimized()
    {
        $settingIds = [100, 102, 105, 106, 107, 108, 109];
        $settings = Setting::whereIn('id', $settingIds)->pluck('value', 'id')->toArray();
        
        return [
            'name' => $settings[100] ?? '',
            'tin_number' => $settings[102] ?? '',
            'logo' => $settings[105] ?? '',
            'address' => $settings[106] ?? '',
            'phone' => $settings[107] ?? '',
            'email' => $settings[108] ?? '',
            'website' => $settings[109] ?? '',
        ];
    }
    
    public function index()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return view('distribution_reports.index', compact('stores'));
    }

    public function filter(Request $request)
    {
        // Initialize PDF optimizer for large reports
        PDFOptimizer::initializePdfLimits();
        
        $date_range = explode('-', $request->date_range);
        $from = trim($date_range[0]);
        $to = trim($date_range[1]);
        $store_id = $request->store_id;

        // Get pharmacy settings efficiently
        $pharmacy = $this->getPharmacySettingsOptimized();
        $pharmacy['from_date'] = date('Y-m-d', strtotime($from));
        $pharmacy['to_date'] = date('Y-m-d', strtotime($to));

        $data = $this->getDistributions($from, $to, $store_id);
        
        if (empty($data['dateGroups'])) {
            return response()->view('error_pages.pdf_zero_data');
        }

        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        $selectedStore = $store_id ? Store::find($store_id) : null;

        return $this->generateOptimizedPdf(
            'distribution_reports.report_pdf',
            compact('data', 'pharmacy', 'stores', 'selectedStore'),
            'Distribution_Report.pdf',
            'landscape'
        );
    }

    private function getDistributions($from, $to, $store_id = null)
    {
        // Get all stores (branches)
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        
        // Get distributions grouped by date and recipient
        $query = ProductionDistribution::with(['production', 'store', 'customer'])
            ->whereHas('production', function($q) use ($from, $to) {
                $q->whereBetween('production_date', [$from, $to]);
            });

        if ($store_id) {
            $query->where('store_id', $store_id);
        }

        $distributions = $query->get();

        // Group by date and recipient (using a unique key)
        $groupedData = [];
        foreach ($distributions as $dist) {
            $date = date('Y-m-d', strtotime($dist->production->production_date));
            $distributionType = $dist->distribution_type ?? 'branch';
            
            // Create unique key for grouping
            $recipientKey = $distributionType . '_';
            switch ($distributionType) {
                case 'branch':
                    $recipientKey .= $dist->store_id ?? 'unknown';
                    $recipientName = $dist->store ? $dist->store->name : 'Unknown Branch';
                    break;
                case 'cash_sale':
                    $recipientKey .= $dist->customer_id ?? 'cash_sale_' . $dist->id;
                    $recipientName = $dist->customer ? $dist->customer->name : 'Cash Sale';
                    break;
                case 'order':
                    $recipientKey .= $dist->order_to ?? 'order_' . $dist->id;
                    $recipientName = $dist->order_to ? 'Order: ' . $dist->order_to : 'Order';
                    break;
                default:
                    $recipientKey .= 'order_' . $dist->id;
                    $recipientName = 'Order';
            }
            
            $meatType = $dist->meat_type;
            $weight = floatval($dist->weight_distributed);

            if (!isset($groupedData[$date])) {
                $groupedData[$date] = [];
            }
            if (!isset($groupedData[$date][$recipientKey])) {
                $groupedData[$date][$recipientKey] = [
                    'distribution_type' => $distributionType,
                    'recipient_name' => $recipientName,
                    'meat' => 0,
                    'steak' => 0,
                    'beef_fillet' => 0,
                    'beef_liver' => 0,
                    'utumbo' => 0,
                    'mafuta' => 0,
                    'byproduct_pack' => 0,
                    'vichwa' => 0,
                    'miguu' => 0,
                    'mikia' => 0,
                    'ngozi' => 0,
                ];
            }

            // Map meat types to columns
            switch (strtolower($meatType)) {
                case 'meat':
                    $groupedData[$date][$recipientKey]['meat'] += $weight;
                    break;
                case 'steak':
                    $groupedData[$date][$recipientKey]['steak'] += $weight;
                    break;
                case 'fillet':
                case 'beef fillet':
                    $groupedData[$date][$recipientKey]['beef_fillet'] += $weight;
                    break;
                case 'beef liver':
                    $groupedData[$date][$recipientKey]['beef_liver'] += $weight;
                    break;
                case 'utumbo':
                    $groupedData[$date][$recipientKey]['utumbo'] += $weight;
                    break;
                case 'mafuta':
                    $groupedData[$date][$recipientKey]['mafuta'] += $weight;
                    break;
                case 'byproduct pack':
                    $groupedData[$date][$recipientKey]['byproduct_pack'] += $weight;
                    break;
                case 'vichwa':
                    $groupedData[$date][$recipientKey]['vichwa'] += $weight;
                    break;
                case 'miguu':
                    $groupedData[$date][$recipientKey]['miguu'] += $weight;
                    break;
                case 'mikia':
                    $groupedData[$date][$recipientKey]['mikia'] += $weight;
                    break;
                case 'ngozi':
                    $groupedData[$date][$recipientKey]['ngozi'] += $weight;
                    break;
            }
        }

        // Sort dates in ascending order
        ksort($groupedData);

        // Structure data grouped by date for the report
        $dateGroups = [];
        $totals = [
            'meat' => 0,
            'steak' => 0,
            'beef_fillet' => 0,
            'beef_liver' => 0,
            'utumbo' => 0,
            'mafuta' => 0,
            'byproduct_pack' => 0,
            'vichwa' => 0,
            'miguu' => 0,
            'mikia' => 0,
            'ngozi' => 0,
        ];

        foreach ($groupedData as $date => $recipientData) {
            $dateGroups[$date] = [
                'rows' => [],
                'totals' => [
                    'meat' => 0,
                    'steak' => 0,
                    'beef_fillet' => 0,
                    'beef_liver' => 0,
                    'utumbo' => 0,
                    'mafuta' => 0,
                    'byproduct_pack' => 0,
                    'vichwa' => 0,
                    'miguu' => 0,
                    'mikia' => 0,
                    'ngozi' => 0,
                ],
            ];

            foreach ($recipientData as $recipientKey => $data) {
                $dateGroups[$date]['rows'][] = [
                    'distribution_type' => $data['distribution_type'],
                    'recipient_name' => $data['recipient_name'],
                    'meat' => $data['meat'],
                    'steak' => $data['steak'],
                    'beef_fillet' => $data['beef_fillet'],
                    'beef_liver' => $data['beef_liver'],
                    'utumbo' => $data['utumbo'],
                    'mafuta' => $data['mafuta'],
                    'byproduct_pack' => $data['byproduct_pack'],
                    'vichwa' => $data['vichwa'],
                    'miguu' => $data['miguu'],
                    'mikia' => $data['mikia'],
                    'ngozi' => $data['ngozi'],
                ];

                // Date totals
                $dateGroups[$date]['totals']['meat'] += $data['meat'];
                $dateGroups[$date]['totals']['steak'] += $data['steak'];
                $dateGroups[$date]['totals']['beef_fillet'] += $data['beef_fillet'];
                $dateGroups[$date]['totals']['beef_liver'] += $data['beef_liver'];
                $dateGroups[$date]['totals']['utumbo'] += $data['utumbo'];
                $dateGroups[$date]['totals']['mafuta'] += $data['mafuta'];
                $dateGroups[$date]['totals']['byproduct_pack'] += $data['byproduct_pack'];
                $dateGroups[$date]['totals']['vichwa'] += $data['vichwa'];
                $dateGroups[$date]['totals']['miguu'] += $data['miguu'];
                $dateGroups[$date]['totals']['mikia'] += $data['mikia'];
                $dateGroups[$date]['totals']['ngozi'] += $data['ngozi'];

                // Grand totals
                $totals['meat'] += $data['meat'];
                $totals['steak'] += $data['steak'];
                $totals['beef_fillet'] += $data['beef_fillet'];
                $totals['beef_liver'] += $data['beef_liver'];
                $totals['utumbo'] += $data['utumbo'];
                $totals['mafuta'] += $data['mafuta'];
                $totals['byproduct_pack'] += $data['byproduct_pack'];
                $totals['vichwa'] += $data['vichwa'];
                $totals['miguu'] += $data['miguu'];
                $totals['mikia'] += $data['mikia'];
                $totals['ngozi'] += $data['ngozi'];
            }
        }

        return [
            'dateGroups' => $dateGroups,
            'totals' => $totals,
        ];
    }
}
