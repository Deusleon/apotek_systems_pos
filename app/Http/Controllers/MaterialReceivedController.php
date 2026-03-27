<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\GoodsReceiving;
use App\Product;
use App\PurchaseReturn;
use App\Setting;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use View;

class MaterialReceivedController extends Controller
{

    public function index(Request $request)
    {
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $products = Product::all();
        $expire_date = Setting::where('id', 123)->value('value');


        return View::make('purchases.material_received.index',
            (compact('suppliers', 'products', 'expire_date')));

    }

    public function update(Request $request)
    {

        $update_material = GoodsReceiving::find($request->id);
        $update_stock = CurrentStock::where('incoming_stock_id', $request->id)
        ->first(['id', 'incoming_stock_id', 'unit_cost']);


        $quantity = str_replace(',', '', $request->quantity_edit);
        $unit_buy_price = floatval(preg_replace('/[^\d.]/', '', $request->price_edit));
        $total_buyprice = $quantity * $unit_buy_price;
        $total_sellprice = $quantity * $update_material->sell_price;
        $profit = $total_sellprice - $total_buyprice;

        if ($request->expire_date_edit) {
            $update_material->expire_date = date('Y-m-d', strtotime($request->expire_date_edit));
        } else {
            $update_material->expire_date = null;
        }
        $update_material->quantity = $quantity;
        $update_material->unit_cost = $unit_buy_price;
        $update_material->total_cost = $total_buyprice;
        $update_material->total_sell = $total_sellprice;
        $update_material->item_profit = $profit;
        $update_material->supplier_id = $request->supplier_id_edit;
        $update_material->created_by = Auth::user()->id;
        
        // Preserve original time from created_at
        $originalTime = date('H:i:s', strtotime($update_material->created_at)); // ← preserve original time
        $newDate = date('Y-m-d', strtotime($request->receive_date_edit));
        $update_material->created_at = $newDate . ' ' . $originalTime;     // ← merge new date with original time

        if ($update_stock) {
            $update_stock->unit_cost = $unit_buy_price;
            $update_stock->save();
        }
        $update_material->save();

        session()->flash("alert-success", "Material updated successfully!");
        return back();

    }

    public function destroy(Request $request)
    {

        GoodsReceiving::destroy($request->id);
        session()->flash("alert-danger", "Material deleted successfully!");
        return back();

    }

    public function getMaterialsReceived(Request $request)
    {
        try {
            Log::info('getMaterialsReceived called', $request->all());

            $columns = array(
                0 => 'inv_products.name',
                1 => 'inv_products.name',
                2 => 'quantity',
                3 => 'unit_cost',
                4 => 'expire_date',
                5 => 'total_cost',
                6 => 'inv_incoming_stock.created_at',
                7 => 'users.name',
                8 => 'inv_products.name'
            );

            // Set default date range to current month if not provided or invalid
            if (!isset($request->date) || !is_array($request->date) || count($request->date) < 2) {
                $from = date('Y-m-d', strtotime('first day of this month'));
                $to = date('Y-m-d', strtotime('last day of this month'));
            } else {
                $from = date('Y-m-d', strtotime($request->date[0]));
                $to = date('Y-m-d', strtotime($request->date[1]));
            }

            $store_id = current_store_id();
            $useStoreFilter = !is_all_store();
            Log::info('store_id: ' . $store_id . ', useStoreFilter: ' . ($useStoreFilter ? 'true' : 'false'));

            // Cache product, user, and supplier data to avoid N+1 queries
            $productsCache = Product::all()->keyBy('id');
            $usersCache = \App\User::all()->keyBy('id');
            $suppliersCache = Supplier::all()->keyBy('id');

    // Build base query once
    $baseQuery = GoodsReceiving::select(
            'inv_incoming_stock.id',
            'product_id', 'quantity', 'unit_cost', 'total_cost',
            'expire_date', 'inv_incoming_stock.created_at',
            'supplier_id', 'created_by'
        )
        ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to]);

    if ($request->supplier_id) {
        $baseQuery->where('supplier_id', $request->supplier_id);
    }

    if ($useStoreFilter) {
        $baseQuery->where('inv_incoming_stock.store_id', $store_id);
    }

    // Get total count efficiently
    $totalData = $baseQuery->count();
    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    // Build data query
    $query = clone $baseQuery;
    
    if (!empty($request->input('search.value'))) {
        $search = $request->input('search.value');
        
        // Optimize search: use subquery for product and user names to avoid multiple joins
        $query->where(function($q) use ($search) {
            $q->whereIn('inv_incoming_stock.id', function($subQuery) use ($search) {
                    $subQuery->select('inv_incoming_stock.id')
                        ->from('inv_incoming_stock')
                        ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                        ->where('inv_products.name', 'LIKE', "%{$search}%");
                })
                ->orWhere('quantity', 'LIKE', "%{$search}%")
                ->orWhere('unit_cost', 'LIKE', "%{$search}%")
                ->orWhere('expire_date', 'LIKE', "%{$search}%")
                ->orWhere('total_cost', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('date(inv_incoming_stock.created_at)'), 'LIKE', "%{$search}%")
                ->orWhereIn('inv_incoming_stock.id', function($subQuery) use ($search) {
                    $subQuery->select('inv_incoming_stock.id')
                        ->from('inv_incoming_stock')
                        ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                        ->where('users.name', 'LIKE', "%{$search}%");
                });
        });
        
        // Get filtered count efficiently
        $totalFiltered = $query->count();
    }
    
    // Fetch data with pagination
    $material_received = $query->offset($start)
        ->limit($limit)
        ->orderby('created_at', 'DESC')
        ->orderby('id', 'DESC')
        ->get();

    if (!empty($material_received)) {
        // Batch load purchase returns to avoid N+1 queries
        $goodsReceivingIds = $material_received->pluck('id')->toArray();
        $pendingReturns = PurchaseReturn::whereIn('goods_receiving_id', $goodsReceivingIds)
            ->where('status', PurchaseReturn::STATUS_PENDING)
            ->pluck('goods_receiving_id')
            ->toArray();
        
        // Batch load order details to avoid N+1 queries
        $productIds = $material_received->pluck('product_id')->unique()->toArray();
        $orderDetails = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereIn('order_details.product_id', $productIds)
            ->select('order_details.product_id', 'order_details.ordered_qty', 'order_details.received_quantity', 'orders.supplier_id')
            ->orderBy('orders.ordered_at', 'desc')
            ->get()
            ->groupBy('product_id');
        
        foreach ($material_received as $value) {
            // Check if there's a pending purchase return for this goods_receiving_id
            $value->has_pending_return = in_array($value->id, $pendingReturns);

            // --- Calculate remaining quantity (optimized)
            $orderDetail = $orderDetails->get($value->product_id, collect())->first();
            
            if ($orderDetail) {
                $value->ordered_qty = $orderDetail->ordered_qty;
                $value->total_received_qty = $orderDetail->received_quantity;
                $value->remaining_qty = $orderDetail->ordered_qty - $orderDetail->received_quantity;
            } else {
                $value->ordered_qty = $value->quantity;
                $value->total_received_qty = $value->quantity;
                $value->remaining_qty = 0;
            }

            // --- NEW FALLBACK: Avoid showing 0.00 for price/amount (optimized)
            if (empty($value->unit_cost) || floatval($value->unit_cost) == 0) {
                // Try latest current stock
                $lastCurrent = DB::table('inv_current_stock')
                    ->where('product_id', $value->product_id)
                    ->orderBy('id', 'desc')
                    ->first(['unit_cost']);

                if ($lastCurrent && floatval($lastCurrent->unit_cost) != 0) {
                    $value->unit_cost = $lastCurrent->unit_cost;
                } else {
                    // Try latest incoming non-zero
                    $lastIncoming = GoodsReceiving::where('product_id', $value->product_id)
                        ->where('unit_cost', '>', 0)
                        ->orderBy('id', 'desc')
                        ->first(['unit_cost', 'total_cost']);

                    if ($lastIncoming) {
                        $value->unit_cost = $lastIncoming->unit_cost;
                        if (empty($value->total_cost) || floatval($value->total_cost) == 0) {
                            $value->total_cost = floatval($lastIncoming->unit_cost) * floatval($value->quantity);
                        }
                    } else {
                        // Fallback to last order_details price
                        $orderDet = DB::table('order_details')
                            ->join('orders', 'order_details.order_id', '=', 'orders.id')
                            ->where('order_details.product_id', $value->product_id)
                            ->when($value->supplier_id, function ($q) use ($value) {
                                $q->where('orders.supplier_id', $value->supplier_id);
                            })
                            ->orderBy('orders.ordered_at', 'desc')
                            ->select('order_details.unit_price', 'order_details.ordered_qty')
                            ->first();

                        if ($orderDet && floatval($orderDet->unit_price) != 0) {
                            $value->unit_cost = $orderDet->unit_price;
                            $value->total_cost = floatval($orderDet->unit_price) * floatval($value->quantity);
                        }
                    }
                }
            }

            // If unit_cost is present but total_cost missing, compute it
            if (!empty($value->unit_cost) && (empty($value->total_cost) || floatval($value->total_cost) == 0)) {
                $value->total_cost = floatval($value->unit_cost) * floatval($value->quantity);
            }
        }
    }

            // Reorder data to match DataTable column expectations (optimized with cached data)
            $reordered_data = [];
            foreach ($material_received as $item) {
                // Use cached data instead of querying database
                $product = $productsCache->get($item->product_id);
                $user = $usersCache->get($item->created_by);
                $supplier = $item->supplier_id ? $suppliersCache->get($item->supplier_id) : null;
                
                $reordered_data[] = [
                    'id' => $item->id,
                    'product' => $product,
                    'ordered_qty' => $item->ordered_qty ?? 0,
                    'quantity' => $item->quantity,
                    'remaining_qty' => $item->remaining_qty ?? 0,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'created_at' => $item->created_at,
                    'user' => $user,
                    'supplier' => $supplier,
                    'has_pending_return' => $item->has_pending_return ?? false
                ];
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $reordered_data
            );

            return response()->json($json_data);
        } catch (\Exception $e) {
            Log::error('Error in getMaterialsReceived: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

}
