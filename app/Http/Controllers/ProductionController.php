<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Production;
use App\ProductionDistribution;
use App\Store;

class ProductionController extends Controller

{
    /**
     * Format number with decimals only when applicable
     * e.g., 192.5 shows as "192.5", 174.00 shows as "174"
     */
    private function formatSmartDecimal($num)
    {
        if ($num === null || $num === '') {
            return '0';
        }
        $num = floatval($num);
        if ($num == floor($num)) {
            return number_format($num, 0);
        }
        return rtrim(rtrim(number_format($num, 2), '0'), '.');
    }

    public function index()

    {
        $stores = Store::where('id', '>', 2)->orderBy('name')->get();
        $productionBranch = Store::find(2)->name ?? 'Main';
        return view('production.index', compact('stores', 'productionBranch'));

    }
    public function store(Request $request)
    {
        // Remove commas from numeric fields
        $numericFields = ['items_received', 'total_weight', 'meat', 'steak', 'beef_fillet', 'weight_difference', 'beef_liver', 'utumbo', 'ngozi', 'mafuta', 'mikia', 'vichwa', 'miguu'];
        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => str_replace(',', '', $request->input($field))]);
            }
        }

        $request->validate([
            'production_date' => 'required',
            'livestock' => 'required|string|max:255',
            'items_received' => 'required|numeric|min:1',
            'meat' => 'required|numeric|min:0',
            'steak' => 'required|numeric|min:0',
            'beef_fillet' => 'required|numeric|min:0',
            'weight_difference' => 'required|numeric',
            'beef_liver' => 'nullable|numeric|min:0',
            'utumbo' => 'nullable|numeric|min:0',
            'vichwa' => 'nullable|numeric|min:0',
            'miguu' => 'nullable|numeric|min:0',
            'mikia' => 'nullable|numeric|min:0',
            'mafuta' => 'nullable|numeric|min:0',
            'ngozi' => 'nullable|numeric|min:0',
        ]);

        // Calculate total_weight (meat + steak + beef_fillet + weight_difference, excluding beef_liver)
        $total_weight = floatval($request->meat) + floatval($request->steak) + 
                        floatval($request->beef_fillet) + floatval($request->weight_difference);

        $data = $request->all();
        $data['total_weight'] = $total_weight;
        // Map livestock to details (database column name)
        $data['details'] = $request->input('livestock');
        unset($data['livestock']);

        Production::create($data);

        return response()->json(['success' => true, 'message' => 'Production record saved successfully']);
    }
    public function show($id)
    {
        $production = Production::find($id);
        if ($production) {
            return response()->json(['success' => true, 'data' => $production]);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
    }
    public function update(Request $request, $id)
    {
        // Remove commas from numeric fields
        $numericFields = ['items_received', 'total_weight', 'meat', 'steak', 'beef_fillet', 'weight_difference', 'beef_liver', 'utumbo', 'ngozi', 'mafuta', 'mikia', 'vichwa', 'miguu'];
        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => str_replace(',', '', $request->input($field))]);
            }
        }

        $request->validate([
            'production_date' => 'required|date',
            'livestock' => 'required|string|max:255',
            'items_received' => 'required|numeric|min:1',
            'meat' => 'required|numeric|min:0',
            'steak' => 'required|numeric|min:0',
            'beef_fillet' => 'required|numeric|min:0',
            'weight_difference' => 'required|numeric',
            'beef_liver' => 'nullable|numeric|min:0',
            'utumbo' => 'nullable|numeric|min:0',
            'ngozi' => 'nullable|numeric|min:0',
            'mafuta' => 'nullable|numeric|min:0',
            'mikia' => 'nullable|numeric|min:0',
            'vichwa' => 'nullable|numeric|min:0',
            'miguu' => 'nullable|numeric|min:0',
        ]);

        $production = Production::find($id);
        if ($production) {
            // Calculate total_weight (meat + steak + beef_fillet + weight_difference, excluding beef_liver)
            $total_weight = floatval($request->meat) + floatval($request->steak) + 
                            floatval($request->beef_fillet) + floatval($request->weight_difference);

            $data = $request->all();
            $data['total_weight'] = $total_weight;
            // Map livestock to details (database column name)
            $data['details'] = $request->input('livestock');
            unset($data['livestock']);

            $production->update($data);
            return response()->json(['success' => true, 'message' => 'Production record updated successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
    }
    public function data(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $search_value = $request->input('search.value');
        
        if ($search_value) {
            $productions = Production::whereBetween('production_date', [$start_date, $end_date])
                ->where(function($query) use ($search_value) {
                    $query->where('production_date', 'like', '%' . $search_value . '%')
                          ->orWhere('details', 'like', '%' . $search_value . '%')
                          ->orWhere('items_received', 'like', '%' . $search_value . '%')
                          ->orWhere('total_weight', 'like', '%' . $search_value . '%');
                })
                ->orderBy('production_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $productions = Production::whereBetween('production_date', [$start_date, $end_date])
                ->orderBy('production_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        }
        
        $data = [];
        foreach ($productions as $production) {
            $data[] = [
                'id' => $production->id,
                'production_date' => date('Y-m-d', strtotime($production->production_date)),
                'details' => $production->details ?? '-',
                'items_received' => $this->formatSmartDecimal($production->items_received),
                'total_weight' => $this->formatSmartDecimal($production->total_weight),
                'meat' => $this->formatSmartDecimal($production->meat),
                'steak' => $this->formatSmartDecimal($production->steak),
                'beef_fillet' => $this->formatSmartDecimal($production->beef_fillet),
                'weight_difference' => $this->formatSmartDecimal($production->weight_difference),
                'beef_liver' => $this->formatSmartDecimal($production->beef_liver),
                'utumbo' => $this->formatSmartDecimal($production->utumbo),
                'mikia' => $this->formatSmartDecimal($production->mikia),
                'mafuta' => $this->formatSmartDecimal($production->mafuta),
                'ngozi' => $this->formatSmartDecimal($production->ngozi),
                'vichwa' => $this->formatSmartDecimal($production->vichwa),
                'miguu' => $this->formatSmartDecimal($production->miguu),
                'has_distributions' => ProductionDistribution::where('production_id', $production->id)->exists(),
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }
    public function destroy($id)

    {

        $production = Production::find($id);

        if ($production) {

            $production->delete();

            return response()->json(['success' => true, 'message' => 'Production record deleted successfully']);

        }

        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);

    }
    public function getStores()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $stores]);
    }
    public function getDistributions($id)
    {
        $production = Production::with(['distributions.store', 'distributions.customer'])->find($id);
        if ($production) {
            return response()->json([
                'success' => true, 
                'data' => $production->distributions,
                'production' => $production
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
    }
    public function storeDistributions(Request $request, $id)
    {
        $request->validate([
            'distributions' => 'required|array',
            'distributions.*.distribution_type' => 'required|in:branch,cash_sale,order',
            'distributions.*.meat_type' => 'required|string',
            'distributions.*.weight_distributed' => 'required|numeric|min:0',
        ]);

        $production = Production::find($id);
        if (!$production) {
            return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
        }

        DB::beginTransaction();
        try {
            // For branch distributions, delete existing branch distributions for this production and meat type
            // For cash_sale and order, just add new distributions without deleting existing ones
            $firstDist = $request->distributions[0] ?? null;
            $distributionType = $firstDist['distribution_type'] ?? 'branch';
            $meatType = $firstDist['meat_type'] ?? null;
            
            if ($distributionType === 'branch' && $meatType) {
                // Only delete existing branch distributions for the same meat type
                ProductionDistribution::where('production_id', $id)
                    ->where('distribution_type', 'branch')
                    ->where('meat_type', $meatType)
                    ->delete();
            }

            // Insert new distributions
            foreach ($request->distributions as $dist) {
                $distributionData = [
                    'production_id' => $id,
                    'distribution_type' => $dist['distribution_type'] ?? 'branch',
                    'meat_type' => $dist['meat_type'],
                    'weight_distributed' => $dist['weight_distributed'],
                    'notes' => $dist['notes'] ?? null,
                ];

                // Set appropriate foreign key based on distribution type
                switch ($dist['distribution_type'] ?? 'branch') {
                    case 'branch':
                        $distributionData['store_id'] = $dist['store_id'] ?? null;
                        break;
                    case 'cash_sale':
                        $distributionData['customer_id'] = $dist['customer_id'] ?? null;
                        break;
                    case 'order':
                        $distributionData['order_to'] = $dist['order_to'] ?? null;
                        break;
                }

                ProductionDistribution::create($distributionData);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Distributions saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving distributions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving distributions'], 500);
        }
    }
    public function distributionsReport()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return view('production.distributions', compact('stores'));
    }
    public function distributionsData(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $store_id = $request->input('store_id');
        $search_value = $request->input('search.value');
        $distribution_type = $request->input('distribution_type');

        $query = ProductionDistribution::with(['production', 'store', 'customer'])
            ->whereHas('production', function($q) use ($start_date, $end_date) {
                $q->whereBetween('production_date', [$start_date, $end_date]);
            });

        if ($store_id) {
            $query->where('store_id', $store_id);
        }

        if ($distribution_type) {
            $query->where('distribution_type', $distribution_type);
        }

        if ($search_value) {
            $query->where(function($q) use ($search_value) {
                $q->where('meat_type', 'like', '%' . $search_value . '%')
                  ->orWhere('distribution_type', 'like', '%' . $search_value . '%')
                  ->orWhereHas('store', function($sq) use ($search_value) {
                      $sq->where('name', 'like', '%' . $search_value . '%');
                  })
                  ->orWhereHas('customer', function($sq) use ($search_value) {
                      $sq->where('name', 'like', '%' . $search_value . '%');
                  });
            });
        }

        $distributions = $query->orderBy('created_at', 'desc')
                               ->orderBy('id', 'desc')
                               ->get();

        // Group distributions by production_id and recipient to pivot meat types into columns
        $grouped = [];
        foreach ($distributions as $dist) {
            $date = $dist->production ? date('Y-m-d', strtotime($dist->production->production_date)) : '';
            $recipient = $dist->recipient_name;
            $key = $dist->production_id . '_' . $dist->distribution_type . '_' . ($dist->store_id ?? '') . '_' . ($dist->customer_id ?? '') . '_' . ($dist->order_to ?? '');
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => $dist->id,
                    'production_id' => $dist->production_id,
                    'production_date' => $date,
                    'distribution_type' => $dist->distribution_type,
                    'recipient' => $recipient,
                    'meat' => 0,
                    'steak' => 0,
                    'beef_fillet' => 0,
                    'beef_liver' => 0,
                    'utumbo' => 0,
                    'mafuta' => 0,
                    'byproduct_pack' => 0,
                    'ngozi' => 0,
                    'mikia' => 0,
                    'vichwa' => 0,
                    'miguu' => 0,
                    'dist_ids' => [],
                ];
            }
            
            // Add to the appropriate meat type column
            $meatType = strtolower($dist->meat_type);
            if ($meatType === 'meat') {
                $grouped[$key]['meat'] += $dist->weight_distributed;
            } elseif ($meatType === 'steak') {
                $grouped[$key]['steak'] += $dist->weight_distributed;
            } elseif ($meatType === 'fillet' || $meatType === 'beef fillet') {
                $grouped[$key]['beef_fillet'] += $dist->weight_distributed;
            } elseif ($meatType === 'beef liver') {
                $grouped[$key]['beef_liver'] += $dist->weight_distributed;
            } elseif ($meatType === 'utumbo') {
                $grouped[$key]['utumbo'] += $dist->weight_distributed;
            } elseif ($meatType === 'mafuta') {
                $grouped[$key]['mafuta'] += $dist->weight_distributed;
            } elseif ($meatType === 'byproduct pack') {
                $grouped[$key]['byproduct_pack'] += $dist->weight_distributed;
            } elseif ($meatType === 'ngozi') {
                $grouped[$key]['ngozi'] += $dist->weight_distributed;
            } elseif ($meatType === 'mikia') {
                $grouped[$key]['mikia'] += $dist->weight_distributed;
            } elseif ($meatType === 'vichwa') {
                $grouped[$key]['vichwa'] += $dist->weight_distributed;
            } elseif ($meatType === 'miguu') {
                $grouped[$key]['miguu'] += $dist->weight_distributed;
            }
            
            $grouped[$key]['dist_ids'][] = $dist->id;
        }

        $data = [];
        foreach ($grouped as $row) {
            $data[] = [
                'id' => $row['id'],
                'production_id' => $row['production_id'],
                'production_date' => $row['production_date'],
                'recipient' => $row['recipient'],
                'meat' => $this->formatSmartDecimal($row['meat']),
                'steak' => $this->formatSmartDecimal($row['steak']),
                'beef_fillet' => $this->formatSmartDecimal($row['beef_fillet']),
                'beef_liver' => $this->formatSmartDecimal($row['beef_liver']),
                'utumbo' => $this->formatSmartDecimal($row['utumbo']),
                'mafuta' => $this->formatSmartDecimal($row['mafuta']),
                'byproduct_pack' => $this->formatSmartDecimal($row['byproduct_pack']),
                'ngozi' => $this->formatSmartDecimal($row['ngozi']),
                'mikia' => $this->formatSmartDecimal($row['mikia']),
                'vichwa' => $this->formatSmartDecimal($row['vichwa']),
                'miguu' => $this->formatSmartDecimal($row['miguu']),
                'dist_ids' => implode(',', $row['dist_ids']),
            ];
        }

        // Get total count before pagination
        $totalRecords = count($data);

        // Apply pagination
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));
        
        // Slice the data array for pagination
        $paginatedData = array_slice($data, $start, $length);

        // Calculate summary
        $summaryQuery = ProductionDistribution::whereHas('production', function($q) use ($start_date, $end_date) {
            $q->whereBetween('production_date', [$start_date, $end_date]);
        });

        if ($store_id) {
            $summaryQuery->where('store_id', $store_id);
        }

        $summary = [
            'total_distributions' => $summaryQuery->count(),
            'total_weight' => $summaryQuery->sum('weight_distributed'),
            'branches_served' => DB::table('production_distributions')
                ->join('productions', 'production_distributions.production_id', '=', 'productions.id')
                ->whereBetween('productions.production_date', [$start_date, $end_date])
                ->whereNotNull('production_distributions.store_id')
                ->distinct()
                ->count('production_distributions.store_id'),
            'total_productions' => DB::table('production_distributions')
                ->join('productions', 'production_distributions.production_id', '=', 'productions.id')
                ->whereBetween('productions.production_date', [$start_date, $end_date])
                ->distinct()
                ->count('production_distributions.production_id'),
        ];

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $paginatedData,
            'summary' => $summary
        ]);
    }
    public function deleteDistribution($id)
    {
        $distribution = ProductionDistribution::find($id);
        if ($distribution) {
            $distribution->delete();
            return response()->json(['success' => true, 'message' => 'Distribution deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Distribution not found'], 404);
    }
    public function bulkDeleteDistributions(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            $idArray = explode(',', $ids);
            ProductionDistribution::whereIn('id', $idArray)->delete();
            return response()->json(['success' => true, 'message' => 'Distributions deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'No IDs provided'], 400);
    }
}