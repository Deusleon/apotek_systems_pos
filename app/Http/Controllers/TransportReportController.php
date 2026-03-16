<?php

namespace App\Http\Controllers;

use App\TransportOrder;
use App\Vehicle;
use App\Transporter;
use App\Payment;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PDFOptimizer;
use PDF;
use DB;

// Use optimized memory and time limits
PDFOptimizer::initializePdfLimits('2048M', 1800);

class TransportReportController extends Controller
{
    /**
     * Generate optimized PDF with memory management
     */
    private function generateOptimizedPdf($view, $data, $filename, $orientation = 'landscape')
    {
        try {
            // Force garbage collection before PDF generation
            PDFOptimizer::forceGarbageCollection();
            
            $pdf = PDF::loadView($view, $data);
            $pdf->setPaper('a4', $orientation);
            
            // Apply optimization options
            $pdf->setOptions([
                'dpi' => 96,
                'isHtml5ParserEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'sans-serif'
            ]);
            
            // Clean up after PDF generation
            PDFOptimizer::forceGarbageCollection();
            
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get pharmacy settings with single optimized query
     */
    private function getPharmacySettingsOptimized()
    {
        $settingIds = [100, 102, 105, 106, 107, 108, 109];
        $settings = Setting::whereIn('id', $settingIds)->pluck('value', 'id');
        
        $logoPath = storage_path('app/public/' . ($settings[105] ?? ''));
        
        return [
            'name' => $settings[100] ?? '',
            'logo' => file_exists($logoPath) ? $logoPath : null,
            'address' => $settings[106] ?? '',
            'email' => $settings[108] ?? '',
            'website' => $settings[109] ?? '',
            'phone' => $settings[107] ?? '',
            'tin_number' => $settings[102] ?? ''
        ];
    }
    
    public function index()
    {
        $orders = TransportOrder::all();
        $vehicles = Vehicle::all();
        $transporters = Transporter::all();
        
        return view('transport_reports.index', compact('orders', 'vehicles', 'transporters'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_option' => 'required|in:1,2,3,4'
        ]);

        try {
            switch ($request->report_option) {
                case '1': // Transport Order Report
                    return $this->generateTransportOrderReport($request);
                case '2': // Transporter Report
                    return $this->generateTransporterReport($request);
                case '3': // Vehicle Report
                    return $this->generateVehicleReport($request);
                case '4': // Payment Report
                    return $this->generatePaymentReport($request);
                default:
                    return redirect()->back()->with('error', 'Invalid report option selected');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating report: ' . $e->getMessage());
        }
    }

    protected function generateTransportOrderReport(Request $request)
    {
        // Get pharmacy settings with optimized single query
        $pharmacy = $this->getPharmacySettingsOptimized();
    
        // Build query
        $query = TransportOrder::with(['transporter', 'vehicle', 'payments'])
            ->orderBy('pickup_date', 'desc');
    
        // Apply filters
        if ($request->transport_order_id) {
            $query->where('id', $request->transport_order_id);
        }
    
        if ($request->order_date_range) {
            $dates = explode(' - ', $request->order_date_range);
            $query->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
        }
    
        $orders = $query->get();
    
        // Generate optimized PDF
        return $this->generateOptimizedPdf(
            'transport_reports.transport_orders',
            [
                'orders' => $orders,
                'pharmacy' => $pharmacy,
                'filter_order' => $request->transport_order_id ? TransportOrder::find($request->transport_order_id)->order_number : null,
                'filter_date_range' => $request->order_date_range
            ],
            'transport-orders-'.now()->format('Y-m-d').'.pdf'
        );
    }

    protected function generateTransporterReport(Request $request)
    {
        // Get pharmacy settings with optimized single query
        $pharmacy = $this->getPharmacySettingsOptimized();

        // Base query
        $query = Transporter::query()
            ->with(['transportOrders' => function($q) use ($request) {
            if ($request->transporter_date_range) {
                $dates = explode(' - ', $request->transporter_date_range);
                $q->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
            }
        }])
        ->orderBy('name');

        // Apply filters
        if ($request->transporter_id) {
            $query->where('id', $request->transporter_id);
        }

        $transporters = $query->get()->map(function($transporter) {
            $transporter->total_orders = $transporter->transportOrders->count();
            $transporter->completed_orders = $transporter->transportOrders->where('status', 'delivered')->count();
            $transporter->total_revenue = $transporter->transportOrders->sum('transport_rate');
            return $transporter;
        });

        // Generate optimized PDF
        return $this->generateOptimizedPdf(
            'transport_reports.transporters_report',
            [
                'transporters' => $transporters,
                'pharmacy' => $pharmacy,
                'filter_transporter' => $request->transporter_id ? Transporter::find($request->transporter_id)->name : null,
                'filter_date_range' => $request->transporter_date_range
            ],
            'transporters_report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    protected function generateVehicleReport(Request $request)
    {
        // Get pharmacy settings with optimized single query
        $pharmacy = $this->getPharmacySettingsOptimized();

        // Base query with correct relationship column
        $query = Vehicle::with(['transporter', 'transportOrders' => function($q) use ($request) {
            $q->orderBy('pickup_date', 'desc')->limit(5);
            if ($request->vehicle_date_range) {
                $dates = explode(' - ', $request->vehicle_date_range);
                $q->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
            }
        }])
        ->orderBy('plate_number');

        // Apply filters - using vehicle_id from request
        if ($request->vehicle_id) {
            $query->where('id', $request->vehicle_id);
        }

        $vehicles = $query->get()->map(function($vehicle) {
            $vehicle->total_orders = $vehicle->transportOrders->count();
            $vehicle->completed_orders = $vehicle->transportOrders->where('status', 'delivered')->count();
            $vehicle->total_revenue = $vehicle->transportOrders->sum('transport_rate');
            return $vehicle;
        });

        // Generate optimized PDF
        return $this->generateOptimizedPdf(
            'transport_reports.vehicles_report',
            [
                'vehicles' => $vehicles,
                'pharmacy' => $pharmacy,
                'filter_vehicle' => $request->vehicle_id ? Vehicle::find($request->vehicle_id)->plate_number : null,
                'filter_date_range' => $request->vehicle_date_range
            ],
            'vehicles_report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function generatePaymentReport(Request $request)
    {
        // Get filter parameters from request
        $filter_order = $request->input('order_number');
        $filter_date = $request->input('payment_date');
        
        // Query payments with filters
        $payments = Payment::query()
            ->when($filter_order, function($query) use ($filter_order) {
                return $query->whereHas('transportOrder', function($q) use ($filter_order) {
                    $q->where('order_number', $filter_order);
                });
            })
            ->when($filter_date, function($query) use ($filter_date) {
                return $query->whereDate('payment_date', $filter_date);
            })
            ->with(['transportOrder', 'user'])
            ->get();

        // Get pharmacy settings with optimized single query
        $pharmacy = $this->getPharmacySettingsOptimized();

        // Generate optimized PDF
        return $this->generateOptimizedPdf(
            'transport_reports.payment_report',
            [
                'payments' => $payments,
                'pharmacy' => $pharmacy,
                'filter_order' => $filter_order,
                'filter_date' => $filter_date,
                'title' => 'Payment Report'
            ],
            'payment_report-'.now()->format('Y-m-d').'.pdf'
        );
    }
}