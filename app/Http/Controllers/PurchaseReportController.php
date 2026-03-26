<?php

namespace App\Http\Controllers;

use App\Category;
use App\CommonFunctions;
use App\CurrentStock;
use App\GoodsReceiving;
use App\Invoice;
use App\Order;
use App\OrderDetail;
use App\Payment;
use App\PriceCategory;
use App\PurchaseReturn;
use App\Setting;
use App\Supplier;
use App\Http\Controllers\PDFOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use View;

// Use PDFOptimizer for memory and time limits
PDFOptimizer::initializePdfLimits('2048M', 1800);

class PurchaseReportController extends Controller
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
                    'isRemoteEnabled' => true,
                    'dpi' => 96,
                    'enable_font_subsetting' => true,
                    'chroot' => base_path(),
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

    /**
     * Get the branch filter condition for queries.
     * If store_id == 1 (ALL), return null (no filter).
     * Otherwise, apply store_id filter.
     */
    protected function getBranchFilter()
    {
        $store_id = current_store_id();
        
        // If store_id is 1 (ALL branch), no filtering needed
        if ($store_id == 1) {
            return null;
        }
        
        return $store_id;
    }

    public function index()
    {
        $price_category = PriceCategory::all();

        $category = Category::all();
        $invoices = Invoice::all();
        $orders = Order::all();
        $order_details = OrderDetail::all();
        $current_stock = CurrentStock::all();
        $suppliers = Supplier::all();
        $material_received = GoodsReceiving::all();

        return View::make('purchases_reports.index',
            (compact('order_details', 'suppliers', 'orders', 'price_category',
                 'current_stock', 'invoices', 'category',
                'material_received')));
    }

    protected function reportOption(Request $request)
    {
        // Initialize PDF optimizer for large reports
        PDFOptimizer::initializePdfLimits();
        
        // Get pharmacy settings efficiently
        $pharmacy = $this->getPharmacySettingsOptimized();
        
        // Get store/branch name for the report
        $store_id = current_store_id();
        $branch_name = 'ALL';
        if ($store_id != 1) {
            $store = DB::table('inv_stores')->where('id', $store_id)->value('name');
            if ($store) {
                $branch_name = $store;
            }
        }

        switch ($request->report_option) {
            case 1:
                $data = $this->materialReceivedReport($request->supplier, $request->expire_dates, $request->invoice_no);

                if ($request->supplier != null) {
                    if ($data->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    return $this->generateOptimizedPdf(
                        'purchases_reports.material_received_report_pdf',
                        compact( 'data', 'pharmacy', 'branch_name', 'store_id'),
                        'material_received_report.pdf'
                    );
                } else {
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    return $this->generateOptimizedPdf(
                        'purchases_reports.material_received_all_supplier_report_pdf',
                        compact('data', 'pharmacy', 'branch_name', 'store_id'),
                        'material_received_all_supplier.pdf'
                    );
                }
                break;
            case 2:
                $data = $this->InvoiceSummaryReport($request->suppliers, $request->expire_date,
                    $request->received_status, $request->period);
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.invoice_summary_report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'invoice_summary_report.pdf'
                );
            case 3:
                break;
            case 4:
                $data = $this->supplierList();
                return $this->generateOptimizedPdf(
                    'purchases_reports.supplier_list_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'supplier_list_report.pdf'
                );
            case 5:
                $data = $this->supplierPriceComparison();
                return $this->generateOptimizedPdf(
                    'purchases_reports.supplier_price_comparison_report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'supplier_price_comparison_report.pdf'
                );
            case 6:
                $data = $this->purchaseOrderDetailsReport($request->date_range);
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.purchase_Order_Details_Report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'purchase_order_details_report.pdf',
                    'landscape'
                );
            case 7:
                $data = $this->purchaseReturnReport($request->date_range);
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.purchase_return_report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'purchase_return_report.pdf',
                    'landscape'
                );
            case 10:
                $data = $this->purchaseOrderSummaryReport($request->date_range);
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.purchase_order_summary_report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'purchase_order_summary_report.pdf',
                    'landscape'
                );
            case 8:
                $data = $this->supplierStatementReport($request->supplier_statement, $request->statement_date_range);
                if ($data['transactions']->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.supplier_statement_report_pdf',
                    compact( 'data', 'pharmacy', 'branch_name'),
                    'supplier_statement_report.pdf',
                    'landscape'
                );
            case 9:
                $data = $this->materialReceivedSummaryReport($request->supplier, $request->expire_dates);
                if (empty($data)) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                return $this->generateOptimizedPdf(
                    'purchases_reports.material_received_summary_report_pdf',
                    compact('data', 'pharmacy', 'branch_name', 'store_id'),
                    'material_received_summary_report.pdf',
                    'landscape'
                );
            default;
        }
    }

    public function materialReceivedReport($supplier, $date, $invoice_no)
    {
        $dates = explode(" - ", $date);
        
        // Get branch filter
        $store_id = $this->getBranchFilter();


        if ($invoice_no == null) {
            if ($supplier == null) {
                $query = GoodsReceiving::whereBetween(DB::raw('date(created_at)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->orderby('created_at', 'DESC');
                
                // Apply branch filter if not ALL
                if ($store_id !== null) {
                    $query->where('store_id', $store_id);
                }
                
                $datas = $query->get();
            } else {
                $query = GoodsReceiving::where('supplier_id', $supplier)
                    ->whereBetween(DB::raw('date(created_at)'),
                        [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->orderby('created_at', 'DESC');
                
                // Apply branch filter if not ALL
                if ($store_id !== null) {
                    $query->where('store_id', $store_id);
                }
                
                $datas = $query->get();
            }

        } else {
            if ($supplier == null) {
                $query = GoodsReceiving::whereBetween(DB::raw('date(created_at)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->where('invoice_no', '=', $invoice_no)
                    ->orderby('created_at', 'DESC');
                
                // Apply branch filter if not ALL
                if ($store_id !== null) {
                    $query->where('store_id', $store_id);
                }
                
                $datas = $query->get();
            } else {
                $query = GoodsReceiving::where('supplier_id', $supplier)
                    ->whereBetween(DB::raw('date(created_at)'),
                        [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->where('invoice_no', '=', $invoice_no)
                    ->orderby('created_at', 'DESC');
                
                // Apply branch filter if not ALL
                if ($store_id !== null) {
                    $query->where('store_id', $store_id);
                }
                
                $datas = $query->get();
            }

            if ($dates != null) {
                foreach ($datas as $data) {
                    $data->invoice_nos = $data->invoice['invoice_no'];
                }
            }
        }

        foreach ($datas as $d) {
            // Get branch name for this item
            $branch_name = 'ALL';
            if ($d->store_id != null) {
                $store = DB::table('inv_stores')->where('id', $d->store_id)->value('name');
                if ($store) {
                    $branch_name = $store;
                }
            }
            $d->branch_name = $branch_name;
            $d->total_bp = $datas->sum('total_cost');
            $d->total_sp = $datas->sum('total_sell');
            $d->total_p = $datas->sum('item_profit');
            $d->dates = $dates;
            $d->supplier_name = $d->supplier['name'];
        }

        /*push them in an array*/
        $raw_data = array();
        foreach ($datas as $datum) {
            // Get branch name for this item
            $branch_name = 'ALL';
            if ($datum->store_id != null) {
                $store = DB::table('inv_stores')->where('id', $datum->store_id)->value('name');
                if ($store) {
                    $branch_name = $store;
                }
            }
            
            array_push($raw_data, array(
                'code' => $datum->product_id,
                'product_name' => $datum->product['name'] . ' ' .
                          $datum->product['brand'] . ' ' .
                          $datum->product['pack_size'] .
                          $datum->product['sales_uom'],
                'branch' => $branch_name,
                'quantity' => $datum->quantity,
                'unit_cost' => $datum->unit_cost,
                'sell_price' => $datum->sell_price,
                'profit' => $datum->item_profit,
                'total_cost' => $datum->total_cost,
                'total_sell' => $datum->total_sell,
                'date' => date('d-m-Y', strtotime($datum->created_at)),
                'supplier' => $datum->supplier['name'],
                'received_by' => $datum->user['name']
            ));
        }

        /*make supplier key*/
        $raw_data_by_key_supplier = array();
        foreach ($raw_data as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $raw_data_by_key_supplier[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total cost for the total*/
        $total_cost = 0;
        $grand_total_cost = array();
        $grand_total_cost_key = array();
        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_cost = $total_cost + $item['total_cost'];
            }
            array_push($grand_total_cost, array(
                'supplier' => $key,
                'amount' => $total_cost
            ));
        }
        foreach ($grand_total_cost as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_cost_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total sell for the total*/
        $total_sell = 0;
        $grand_total_sell = array();
        $grand_total_sell_key = array();
        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_sell = $total_sell + $item['total_sell'];
            }
            array_push($grand_total_sell, array(
                'supplier' => $key,
                'amount' => $total_sell
            ));
        }
        foreach ($grand_total_sell as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_sell_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total profit for the total*/
        $total_profit = 0;
        $grand_total_profit = array();
        $grand_total_profit_key = array();

        $supplier_sum = array();
        $sum_by_key = new CommonFunctions();
        foreach ($raw_data_by_key_supplier as $value) {
            foreach ($value as $item) {

                $index = $sum_by_key->sumByKey($item['supplier'], $supplier_sum, 'supplier');
                if ($index < 0) {
                    $supplier_sum[] = $item;
                } else {
                    $supplier_sum[$index]['total_cost'] += $item['total_cost'];
                    $supplier_sum[$index]['total_sell'] += $item['total_sell'];
                    $supplier_sum[$index]['profit'] += $item['profit'];

                }
            }

        }

        $test = array();
        foreach ($supplier_sum as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $test[$raw_datum['supplier']][] = $raw_datum;
            }
        }


        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_profit = $total_profit + $item['profit'];
            }
            array_push($grand_total_profit, array(
                'supplier' => $key,
                'amount' => $total_profit
            ));
        }
        foreach ($grand_total_profit as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_profit_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*what to return to be printed*/
        if ($supplier != null) {
            return $datas;
        } else {
            $to_print = array();
            array_push($to_print, array(
                'data' => $raw_data_by_key_supplier,
                'cost_by_supplier' => $test,
                'total_cost' => $grand_total_cost_key,
                'total_sell' => $grand_total_sell_key,
                'total_profit' => $grand_total_profit_key
            ));

            return $to_print;
        }

    }

    public function InvoiceSummaryReport($supplier, $date, $status, $period)
    {
        $dates = explode(" - ", $date);
        
        // Get branch filter
        $store_id = $this->getBranchFilter();
        
        $query = Invoice::whereBetween(DB::raw('date(invoice_date)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))]);

        // Apply branch filter through goods receiving if not ALL
        if ($store_id !== null) {
            $query->whereIn('invoice_no', function($subquery) use ($store_id) {
                $subquery->select('invoice_no')
                    ->from('inv_incoming_stock')
                    ->where('store_id', $store_id);
            });
        }

        if ($supplier !== null) {
            $query->where('supplier_id', $supplier);
        }

        if ($status !== null) {
            $query->where('received_status', $status);
        }

        if ($period !== null) {
            $query->where('grace_period', $period);
        }

        $datas = $query->orderby('invoice_date', 'DESC')->get();

        foreach ($datas as $d) {
            $d->dates = $dates;
            // Calculate remain_balance as invoice_amount - paid_amount
            $d->remain_balance = $d->invoice_amount - $d->paid_amount;
        }

        return $datas;
    }

    public function InvoiceDetailsReport()
    {
        // Get branch filter
        $store_id = $this->getBranchFilter();
        
        if ($store_id !== null) {
            $datas = Invoice::whereIn('invoice_no', function($subquery) use ($store_id) {
                $subquery->select('invoice_no')
                    ->from('inv_incoming_stock')
                    ->where('store_id', $store_id);
            })->get();
        } else {
            $datas = Invoice::all();
        }
        
        return $datas;
    }

    private function supplierList()
    {
        // Suppliers are master records, no branch filtering needed
        $suppliers = Supplier::all();
        return $suppliers;
    }

    private function supplierPriceComparison()
    {
        // Get branch filter
        $store_id = $this->getBranchFilter();
        
        if ($store_id !== null) {
            $supplier_prices = GoodsReceiving::where('store_id', $store_id)->get();
        } else {
            $supplier_prices = GoodsReceiving::all();
        }
        
        return $supplier_prices;
    }

    public function purchaseOrderDetailsReport($date_range)
    {
        if (!$date_range) {
            return collect(); // Return empty collection if no date range
        }

        $dates = explode(" - ", $date_range);

        if (count($dates) < 2) {
            return collect(); // Return empty collection if date range is invalid
        }
        
        // Get branch filter
        $store_id = $this->getBranchFilter();

        $query = Order::with(['supplier', 'details.product'])
            ->whereBetween(DB::raw('date(ordered_at)'), [
                date('Y-m-d', strtotime($dates[0])),
                date('Y-m-d', strtotime($dates[1]))
            ])
            ->where('status', '!=', 'cancelled')
            ->orderBy('ordered_at', 'DESC');
        
        // Apply branch filter if not ALL
        if ($store_id !== null) {
            $query->where('store_id', $store_id);
        }
        
        $orders = $query->get();

        // Add date range to each order for PDF display
        foreach ($orders as $order) {
            $order->date_range = $dates;
        }

        return $orders;
    }

    public function purchaseOrderSummaryReport($date_range)
    {
        if (!$date_range) {
            return collect(); // Return empty collection if no date range
        }

        $dates = explode(" - ", $date_range);

        if (count($dates) < 2) {
            return collect(); // Return empty collection if date range is invalid
        }
        
        // Get branch filter
        $store_id = $this->getBranchFilter();

        $query = Order::with(['supplier', 'details.product', 'user'])
            ->whereBetween(DB::raw('date(ordered_at)'), [
                date('Y-m-d', strtotime($dates[0])),
                date('Y-m-d', strtotime($dates[1]))
            ])
            ->where('status', '!=', 'cancelled')
            ->orderBy('ordered_at', 'DESC');
        
        // Apply branch filter if not ALL
        if ($store_id !== null) {
            $query->where('store_id', $store_id);
        }
        
        $orders = $query->get();

        // Add date range to each order for PDF display
        foreach ($orders as $order) {
            $order->date_range = $dates;
            // Calculate total products count
            $order->total_products = $order->details->count();
        }

        return $orders;
    }

    public function purchaseReturnReport($date_range)
    {
        if (!$date_range) {
            return collect(); // Return empty collection if no date range
        }

        $dates = explode(" - ", $date_range);

        if (count($dates) < 2) {
            return collect(); // Return empty collection if date range is invalid
        }
        
        // Get branch filter
        $store_id = $this->getBranchFilter();

        // Get approved purchase returns - filter by purchase_returns.status = 'approved'
        // Note: We join with inv_incoming_stock to get product and supplier info
        $query = PurchaseReturn::join('inv_incoming_stock', 'inv_incoming_stock.id', '=', 'purchase_returns.goods_receiving_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
            ->join('inv_suppliers', 'inv_suppliers.id', '=', 'inv_incoming_stock.supplier_id')
            ->select(
                'purchase_returns.*', 
                'inv_incoming_stock.*', 
                'inv_products.name as product_name',
                'inv_products.brand as product_brand',
                'inv_products.pack_size as product_pack_size',
                'inv_products.sales_uom as product_sales_uom',
                'inv_suppliers.name as supplier_name',
                'purchase_returns.quantity as return_quantity', 
                'inv_incoming_stock.quantity as received_quantity'
            )
            ->where(DB::Raw("DATE(purchase_returns.date)"), '>=', date('Y-m-d', strtotime($dates[0])))
            ->where(DB::Raw("DATE(purchase_returns.date)"), '<=', date('Y-m-d', strtotime($dates[1])))
            ->where('purchase_returns.status', '=', 'approved')
            ->orderBy('purchase_returns.date', 'desc');
        
        // Apply branch filter if not ALL (through inv_incoming_stock)
        if ($store_id !== null) {
            $query->where('inv_incoming_stock.store_id', $store_id);
        }
        
        $returns = $query->get();

        // Add date range to each return for PDF display
        foreach ($returns as $return) {
            $return->date_range = $dates;
        }

        return $returns;
    }

    private function supplierStatementReport($supplierId, $dateRange)
    {
        if (!$dateRange) {
            return [
                'transactions' => collect(),
                'supplier' => null,
                'from' => date('Y-m-d'),
                'to' => date('Y-m-d'),
                'total_invoiced' => 0,
                'total_paid' => 0,
                'balance' => 0,
                'opening_balance' => 0
            ];
        }

        $dates = explode(" - ", $dateRange);

        if (count($dates) < 2) {
            return [
                'transactions' => collect(),
                'supplier' => null,
                'from' => date('Y-m-d'),
                'to' => date('Y-m-d'),
                'total_invoiced' => 0,
                'total_paid' => 0,
                'balance' => 0,
                'opening_balance' => 0
            ];
        }

        $from = date('Y-m-d', strtotime($dates[0]));
        $to = date('Y-m-d', strtotime($dates[1]));

        // Get supplier info
        $supplier = null;
        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
        }

        if (!$supplier) {
            return [
                'transactions' => collect(),
                'supplier' => null,
                'from' => $from,
                'to' => $to,
                'total_invoiced' => 0,
                'total_paid' => 0,
                'balance' => 0,
                'opening_balance' => 0
            ];
        }

        // Calculate opening balance (all invoices before the date range)
        // Opening balance = total invoiced before from date - total paid before from date
        $openingInvoices = Invoice::where('supplier_id', $supplierId)
            ->where(DB::raw('date(invoice_date)'), '<', $from)
            ->get();
        $openingInvoiced = $openingInvoices->sum('invoice_amount');
        $openingPaid = $openingInvoices->sum('paid_amount');
        $openingBalance = $openingInvoiced - $openingPaid;

        // Get all invoices for this supplier within date range
        $invoices = Invoice::where('supplier_id', $supplierId)
            ->whereBetween(DB::raw('date(invoice_date)'), [$from, $to])
            ->orderBy('invoice_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // Get all payments for this supplier's invoices within date range
        $payments = Payment::whereHas('invoice', function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->whereBetween(DB::raw('date(payment_date)'), [$from, $to])
            ->orderBy('payment_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // Build combined transaction list with invoices and payments in chronological order
        $combinedTransactions = [];

        // Add invoices to combined list
        foreach ($invoices as $invoice) {
            $combinedTransactions[] = [
                'date' => $invoice->invoice_date,
                'type' => 'invoice',
                'reference' => $invoice->invoice_no,
                'invoice_id' => $invoice->id,
                'invoice_amount' => $invoice->invoice_amount,
                'amount' => 0, // For invoices, amount due is the full invoice amount
                'paid' => 0,
                'sort_date' => strtotime($invoice->invoice_date) . sprintf('%06d', $invoice->id)
            ];
        }

        // Add payments to combined list
        foreach ($payments as $payment) {
            $combinedTransactions[] = [
                'date' => $payment->payment_date,
                'type' => 'payment',
                'reference' => $payment->invoice->invoice_no ?? 'N/A',
                'invoice_id' => $payment->invoice_id,
                'invoice_amount' => $payment->invoice->invoice_amount ?? 0,
                'amount' => 0, // Will be calculated below
                'paid' => $payment->amount,
                'payment_id' => $payment->id,
                'sort_date' => strtotime($payment->payment_date) . sprintf('%06d', $payment->id)
            ];
        }

        // Sort combined transactions by date
        usort($combinedTransactions, function($a, $b) {
            return $a['sort_date'] - $b['sort_date'];
        });

        // Calculate running balance and amounts for each transaction
        $invoiceBalances = []; // Track balance for each invoice
        $runningBalance = $openingBalance;
        $totalInvoiced = 0;
        $totalPaid = 0;

        $transactions = collect();

        foreach ($combinedTransactions as $trans) {
            if ($trans['type'] === 'invoice') {
                // Invoice transaction
                $invoiceId = $trans['invoice_id'];
                $invoiceAmount = $trans['invoice_amount'];
                
                // Initialize invoice balance if not exists
                if (!isset($invoiceBalances[$invoiceId])) {
                    $invoiceBalances[$invoiceId] = $invoiceAmount;
                }

                $runningBalance += $invoiceAmount;
                $totalInvoiced += $invoiceAmount;

                $transactions->push([
                    'date' => $trans['date'],
                    'type' => 'invoice',
                    'type_label' => 'Invoice',
                    'reference' => $trans['reference'],
                    'debit' => $invoiceAmount,
                    'credit' => 0,
                    'balance' => $invoiceBalances[$invoiceId]
                ]);
            } else {
                // Payment transaction
                $invoiceId = $trans['invoice_id'];
                $paymentAmount = $trans['paid'];
                
                // Get current balance for this invoice before payment
                $currentInvoiceBalance = $invoiceBalances[$invoiceId] ?? 0;
                
                // Calculate new balance after payment
                $newInvoiceBalance = max(0, $currentInvoiceBalance - $paymentAmount);
                $invoiceBalances[$invoiceId] = $newInvoiceBalance;

                $runningBalance -= $paymentAmount;
                $totalPaid += $paymentAmount;

                $transactions->push([
                    'date' => $trans['date'],
                    'type' => 'payment',
                    'type_label' => 'Payment',
                    'reference' => $trans['reference'],
                    'debit' => $currentInvoiceBalance, // Amount due before payment
                    'credit' => $paymentAmount,
                    'balance' => $newInvoiceBalance
                ]);
            }
        }

        // Outstanding balance = sum of all individual invoice balances
        $outstandingBalance = array_sum($invoiceBalances);

        return [
            'transactions' => $transactions,
            'supplier' => $supplier,
            'from' => $from,
            'to' => $to,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'balance' => $outstandingBalance,
            'opening_balance' => $openingBalance
        ];
    }

    /**
     * Material Received Summary Report - Grouped by Supplier
     * Shows totals per supplier without line-by-line details
     */
    public function materialReceivedSummaryReport($supplier, $date)
    {
        if (!$date) {
            return null;
        }

        $dates = explode(" - ", $date);
        
        if (count($dates) < 2) {
            return null;
        }
        
        // Get branch filter
        $store_id = $this->getBranchFilter();

        // Build base query
        $query = GoodsReceiving::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
            ->orderBy('created_at', 'DESC');
        
        // Apply branch filter if not ALL
        if ($store_id !== null) {
            $query->where('store_id', $store_id);
        }
        
        // Apply supplier filter if selected
        if ($supplier != null) {
            $query->where('supplier_id', $supplier);
        }
        
        $datas = $query->get();

        if ($datas->isEmpty()) {
            return null;
        }

        // Group by supplier and calculate totals
        $summaryData = [];
        $grandTotalQty = 0;
        $grandTotalBuy = 0;
        $grandTotalSell = 0;
        $grandTotalProfit = 0;

        foreach ($datas as $item) {
            $supplierName = $item->supplier->name ?? 'Unknown';
            
            if (!isset($summaryData[$supplierName])) {
                $summaryData[$supplierName] = [
                    'supplier' => $supplierName,
                    'total_quantity' => 0,
                    'total_buy' => 0,
                    'total_sell' => 0,
                    'total_profit' => 0
                ];
            }
            
            $summaryData[$supplierName]['total_quantity'] += $item->quantity;
            $summaryData[$supplierName]['total_buy'] += $item->total_cost;
            $summaryData[$supplierName]['total_sell'] += $item->total_sell;
            $summaryData[$supplierName]['total_profit'] += $item->item_profit;
            
            $grandTotalQty += $item->quantity;
            $grandTotalBuy += $item->total_cost;
            $grandTotalSell += $item->total_sell;
            $grandTotalProfit += $item->item_profit;
        }

        // Sort by supplier name
        ksort($summaryData);

        return [
            'data' => array_values($summaryData),
            'dates' => $dates,
            'grand_total_quantity' => $grandTotalQty,
            'grand_total_buy' => $grandTotalBuy,
            'grand_total_sell' => $grandTotalSell,
            'grand_total_profit' => $grandTotalProfit
        ];
    }


}
