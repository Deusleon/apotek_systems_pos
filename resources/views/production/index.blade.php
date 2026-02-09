@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="{{asset('assets/plugins/data-tables/css/datatables.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .text-center {
            text-align: center !important;
        }

        .align-right {
            justify-content: flex-end !important;
        }

        .dt-center {
            text-align: center !important;
        }

        .modal .datepicker {
            z-index: 9999 !important;
        }

        .distribution-summary {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .distribution-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .distribution-summary-item:last-child {
            border-bottom: none;
        }

        .branch-step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dee2e6;
            margin: 0 5px;
        }

        .step-dot.active {
            background: #007bff;
        }

        .step-dot.completed {
            background: #28a745;
        }
    </style>
@endsection

@section('content-title')
    Productions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Productions</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            @if (current_store_id() === 2)
                <div class="card-body">
                    @if (current_store_id() === 2)
                        <div class="d-flex justify-content-end align-items-end mb-3">
                            <div class="form-inline">
                                <button class="btn btn-secondary" data-toggle="modal" data-target="#productionModal">
                                    <i class="fas fa-plus mr-1"></i> Add Production
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex justify-content-end align-items-end mb-3">
                        <div class="form-inline">
                            <label for="date_range" class="mr-2">Date:</label>
                            <input type="text" id="date_range" class="form-control" autocomplete="off" style="min-width:200px;">
                        </div>
                    </div>
                    <div id="production_table_container" class="table-responsive">
                        <table id="production_table" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Livestock</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-center">Total Weight (kg)</th>
                                    <th class="text-center">Weight Diff (kg)</th>
                                    <th class="text-center">Meat (kg)</th>
                                    <th class="text-center">Steak (kg)</th>
                                    <th class="text-center">Beef Fillet (kg)</th>
                                    <th class="text-center">Beef Liver (kg)</th>
                                    <th class="text-center">Utumbo (kg)</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card-body">
                    <div class="card-title">Production is only allowed from the {{ $productionBranch }} Branch</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Production Modal -->
    <div class="modal fade" id="productionModal" tabindex="-1" role="dialog" aria-labelledby="productionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productionModalLabel">Add
                        Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="production_form">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="production_date" class="col-md-4 col-form-label text-md-right">Date <span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="production_date" name="production_date"
                                            value="{{ date('Y-m-d') }}" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="livestock" class="col-md-4 col-form-label text-md-right">Livestock <span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="livestock" name="livestock" required>
                                            <option value="">Select type...</option>
                                            <option value="Cattle">Cattle</option>
                                            <option value="Goat">Goat</option>
                                            <option value="Sheep">Sheep</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="items_received" class="col-md-4 col-form-label text-md-right">Total Cattle
                                        <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="items_received" name="items_received"
                                            min="1" placeholder="Total cattle" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="total_weight" class="col-md-4 col-form-label text-md-right">Total
                                        Weight<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control bg-light" id="total_weight"
                                            placeholder="Total weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="weight_difference" class="col-md-4 col-form-label text-md-right">Weight
                                        Diff<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input"
                                            id="weight_difference" name="weight_difference" min="0"
                                            placeholder="Weight difference" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="meat" class="col-md-4 col-form-label text-md-right">Meat<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="meat"
                                            name="meat" min="0" placeholder="Meat weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="steak" class="col-md-4 col-form-label text-md-right">Steak<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="steak"
                                            name="steak" min="0" placeholder="Steak weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="beef_fillet" class="col-md-4 col-form-label text-md-right">Beef Fillet<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="beef_fillet"
                                            name="beef_fillet" min="0" placeholder="Beef fillet weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="beef_liver" class="col-md-4 col-form-label text-md-right">Beef Liver</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="beef_liver"
                                            name="beef_liver" min="0" placeholder="Beef liver weight">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="utumbo" class="col-md-4 col-form-label text-md-right">Utumbo</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="utumbo" name="utumbo"
                                            min="0" placeholder="Uzito wa Utumbo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- More Fields Toggle -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="javascript:void(0)" id="toggleMoreFields" class="text-primary"
                                    style="cursor: pointer; font-size: 14px;">
                                    <i class="fas fa-chevron-down mr-1" id="moreFieldsIcon"></i> More byproducts...
                                </a>
                            </div>
                        </div>
                        <!-- Hidden Byproduct Fields -->
                        <div id="moreFieldsContainer" style="display: none;">
                            <hr class="my-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="vichwa" class="col-md-4 col-form-label text-md-right">Vichwa</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="vichwa" name="vichwa"
                                                placeholder="Jumla ya vichwa">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="miguu" class="col-md-4 col-form-label text-md-right">Miguu</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="miguu" name="miguu"
                                                placeholder="Jumla ya miguu">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="mikia" class="col-md-4 col-form-label text-md-right">Mikia</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="mikia" name="mikia"
                                                placeholder="Jumla ya mikia">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="mafuta" class="col-md-4 col-form-label text-md-right">Mafuta</label>
                                        <div class="col-md-8">
                                            <input type="text" step="0.01" class="form-control" id="mafuta" name="mafuta"
                                                min="0" placeholder="Uzito wa Mafuta">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ngozi" class="col-md-4 col-form-label text-md-right">Ngozi</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="ngozi" name="ngozi"
                                                placeholder="Jumla ya ngozi">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Production Modal -->
    <div class="modal fade" id="editProductionModal" tabindex="-1" role="dialog" aria-labelledby="editProductionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductionModalLabel">Edit Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_production_form">
                        @csrf()
                        <input type="hidden" id="edit_production_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="edit_production_date" class="col-md-4 col-form-label text-md-right">Date
                                        <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="edit_production_date"
                                            name="production_date" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_Livestock" class="col-md-4 col-form-label text-md-right">Livestock
                                        <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="edit_Livestock" name="livestock" required>
                                            <option value="">Select type...</option>
                                            <option value="Cattle">Cattle</option>
                                            <option value="Goat">Goat</option>
                                            <option value="Sheep">Sheep</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_items_received" class="col-md-4 col-form-label text-md-right">Total
                                        Cattle <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="edit_items_received"
                                            name="items_received" min="1" placeholder="Total cattle" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_total_weight" class="col-md-4 col-form-label text-md-right">Total
                                        Weight<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control bg-light" id="edit_total_weight"
                                            placeholder="Total weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_weight_difference" class="col-md-4 col-form-label text-md-right">Weight
                                        Diff<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input"
                                            id="edit_weight_difference" name="weight_difference" min="0"
                                            placeholder="Weight difference" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="edit_meat" class="col-md-4 col-form-label text-md-right">Meat<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input" id="edit_meat"
                                            name="meat" min="0" placeholder="Meat weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_steak" class="col-md-4 col-form-label text-md-right">Steak<span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input"
                                            id="edit_steak" name="steak" min="0" placeholder="Steak weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_beef_fillet" class="col-md-4 col-form-label text-md-right">Beef
                                        Fillet<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input"
                                            id="edit_beef_fillet" name="beef_fillet" min="0"
                                            placeholder="Beef fillet weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_beef_liver" class="col-md-4 col-form-label text-md-right">Beef
                                        Liver</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="edit_beef_liver"
                                            name="beef_liver" min="0" placeholder="Beef liver weight">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_utumbo" class="col-md-4 col-form-label text-md-right">Utumbo</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="edit_utumbo" name="utumbo"
                                            min="0" placeholder="Utumbo weight">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- More Fields Toggle for Edit Modal -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="javascript:void(0)" id="toggleEditMoreFields" class="text-primary"
                                    style="cursor: pointer; font-size: 14px;">
                                    <i class="fas fa-chevron-down mr-1" id="editMoreFieldsIcon"></i> More byproducts...
                                </a>
                            </div>
                        </div>
                        <!-- Hidden Byproduct Fields for Edit Modal -->
                        <div id="editMoreFieldsContainer" style="display: none;">
                            <hr class="my-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="edit_vichwa"
                                            class="col-md-4 col-form-label text-md-right">Vichwa</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="edit_vichwa" name="vichwa"
                                                placeholder="Jumla ya Vichwa">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="edit_miguu" class="col-md-4 col-form-label text-md-right">Miguu</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="edit_miguu" name="miguu"
                                                placeholder="Jumla ya Miguu">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="edit_mikia" class="col-md-4 col-form-label text-md-right">Mikia</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="edit_mikia" name="mikia"
                                                placeholder="Jumla ya Mikia">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="edit_mafuta"
                                            class="col-md-4 col-form-label text-md-right">Mafuta</label>
                                        <div class="col-md-8">
                                            <input type="text" step="0.01" class="form-control" id="edit_mafuta"
                                                name="mafuta" min="0" placeholder="Uzito wa Mafuta">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="edit_ngozi" class="col-md-4 col-form-label text-md-right">Ngozi</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="edit_ngozi" name="ngozi"
                                                placeholder="Ngozi">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Modal -->
    <div class="modal fade" id="distributionModal" tabindex="-1" role="dialog" aria-labelledby="distributionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distributionModalLabel">Record Distribution</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Loading Overlay -->
                    <div id="distributionLoading" class="text-center py-5">
                        <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
                    </div>

                    <!-- Content Container (hidden while loading) -->
                    <div id="distributionContent" style="display: none;">
                        <div class="d-flex justify-content-between">
                            <div style="display: none;"><strong>Production Date:</strong> <span
                                    id="dist_production_date"></span></div>
                        </div>
                        <div class="branch-step-indicator" id="stepIndicator" style="display: none;">
                            <!-- Step dots will be dynamically added -->
                        </div>
                        <div class="mb-3">

                        </div>

                        <div id="distributionFormContainer">
                            <input type="hidden" id="dist_production_id">
                            <div class="form-group d-flex align-items-center">
                                <label class="col-4 col-form-label text-md-right" for="dist_distribution_type">Distribution
                                    Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="dist_distribution_type" name="distribution_type" required>
                                    <option value="branch">Branch</option>
                                    <option value="cash_sale">Cash Sale</option>
                                    <option value="order">Order</option>
                                </select>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="col-4 col-form-label text-md-right" for="dist_meat_type">Product Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="dist_meat_type" name="meat_type" required>
                                    <option value="">Select Product Type</option>
                                    <option value="Meat">Meat</option>
                                    <option value="Steak">Steak</option>
                                    <option value="Beef Liver">Beef Liver</option>
                                    <option value="Beef Fillet">Beef Fillet</option>
                                    <option value="Utumbo">Utumbo</option>
                                    <option value="Mafuta">Mafuta</option>
                                    <option value="Byproduct Pack">Byproduct Pack</option>
                                    <option value="Vichwa">Vichwa</option>
                                    <option value="Miguu">Miguu</option>
                                    <option value="Mikia">Mikia</option>
                                    <option value="Ngozi">Ngozi</option>
                                </select>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="col-4 col-form-label text-md-right" for="dist_total_weight"
                                    id="dist_total_weight_label">Total Weight </label>
                                <input type="text" class="form-control" id="dist_total_weight" name="total_weight"
                                    placeholder="Total weight" disabled>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="col-4 col-form-label text-md-right" for="dist_remaining_weight"
                                    id="dist_remaining_weight_label">Remaining Weight </label>
                                <input type="text" class="form-control" id="dist_remaining_weight" name="remaining_weight"
                                    placeholder="Remaining weight" disabled>
                            </div>
                            <!-- Branch Selection (for branch type) -->
                            <div class="form-group d-flex align-items-center" id="dist_branch_group">
                                <label class="col-4 col-form-label text-md-right" for="dist_store_id">Branch Name <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="dist_store_id" name="store_id">
                                    <option value="">Select Branch</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Customer Input (for cash sale type) -->
                            <div class="form-group d-none align-items-center" id="dist_customer_group">
                                <label class="col-4 col-form-label text-md-right" for="dist_customer_name">Customer
                                    Name</label>
                                <input type="text" class="form-control" id="dist_customer_name" name="customer_name"
                                    placeholder="Walk-in Customer (optional)">
                            </div>
                            <!-- Order Selection (for order type) -->
                            <div class="form-group d-none align-items-center" id="dist_order_group">
                                <label class="col-4 col-form-label text-md-right" for="dist_order_id">Order To</label>
                                <input type="text" class="form-control" id="dist_order_id" name="order_id"
                                    placeholder="Enter Order To">
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="col-4 col-form-label text-md-right" for="dist_weight"
                                    id="dist_weight_label">Distributed (kg) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dist_weight" name="weight_distributed"
                                    placeholder="Enter weight" required>
                            </div>
                        </div>

                        <div class="distribution-summary" id="distributionSummary" style="display: none;">
                            <h6><strong>Distribution Summary</strong></h6>
                            <div id="summaryContent"></div>
                            <div class="mt-2">
                                <strong>Total Distributed:</strong> <span id="totalDistributed">0.00</span> kg
                            </div>
                        </div>
                    </div><!-- End distributionContent -->
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                    <button type="button" class="btn btn-warning" id="skipBranchBtn">Skip</button>
                    <button type="button" class="btn btn-info" id="backBranchBtn" disabled>Prev</button>
                    <button type="button" class="btn btn-primary" id="nextBranchBtn">Next</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push("page_scripts")
    <script src="{{asset('assets/plugins/data-tables/js/datatables.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script>
    <script src="{{asset('assets/apotek/js/notification.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            // Set default date range to this month
            var start = moment().startOf('month');
            var end = moment().endOf('month');
            $('#date_range').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: true,
                locale: { format: 'YYYY/MM/DD', cancelLabel: 'Clear' },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            });
            $('#date_range').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));

            $('#production_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                autoApply: true,
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });

            $('#production_date').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            function isNumberKey(evt, obj) {
                var charCode = evt.which ? evt.which : event.keyCode;
                var value = obj.value;
                var dotcontains = value.indexOf(".") !== -1;
                if (dotcontains) if (charCode === 46) return false;
                if (charCode === 46) return true;
                return !(charCode > 31 && (charCode < 48 || charCode > 57));
            }

            // Format number with thousand separators (decimals only when applicable)
            function numberWithCommas(digit) {
                if (digit === '' || digit === null || isNaN(digit)) return '';
                var num = parseFloat(digit);
                // Check if the number has meaningful decimals
                if (num % 1 === 0) {
                    // No decimal part, format without decimals
                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                } else {
                    // Has decimal part, show up to 2 decimal places
                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                }
            }

            // Remove commas and get raw number
            function unformatNumber(value) {
                if (!value) return 0;
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            // Format input field on blur (when user leaves the field)
            function formatInputOnBlur(selector) {
                $(document).on('blur', selector, function () {
                    var val = $(this).val();
                    if (val !== '') {
                        var num = unformatNumber(val);
                        $(this).val(numberWithCommas(num));
                    }
                });
            }

            // Apply formatting to all weight fields in Add modal
            formatInputOnBlur('#total_weight');
            formatInputOnBlur('#meat');
            formatInputOnBlur('#steak');
            formatInputOnBlur('#beef_fillet');
            formatInputOnBlur('#weight_difference');
            formatInputOnBlur('#beef_liver');
            formatInputOnBlur('#utumbo');
            formatInputOnBlur('#items_received');
            formatInputOnBlur('#mafuta');
            formatInputOnBlur('#mikia');
            formatInputOnBlur('#ngozi');
            formatInputOnBlur('#vichwa');
            formatInputOnBlur('#miguu');

            // Apply formatting to all weight fields in Edit modal
            formatInputOnBlur('#edit_total_weight');
            formatInputOnBlur('#edit_meat');
            formatInputOnBlur('#edit_steak');
            formatInputOnBlur('#edit_beef_fillet');
            formatInputOnBlur('#edit_weight_difference');
            formatInputOnBlur('#edit_beef_liver');
            formatInputOnBlur('#edit_utumbo');
            formatInputOnBlur('#edit_items_received');
            formatInputOnBlur('#edit_mafuta');
            formatInputOnBlur('#edit_mikia');
            formatInputOnBlur('#edit_ngozi');
            formatInputOnBlur('#edit_vichwa');
            formatInputOnBlur('#edit_miguu');

            // Apply formatting to distribution weight field
            formatInputOnBlur('#dist_weight');

            // Toggle More Fields in Add Production Modal
            $('#toggleMoreFields').on('click', function () {
                var container = $('#moreFieldsContainer');
                var icon = $('#moreFieldsIcon');
                if (container.is(':visible')) {
                    container.slideUp(200);
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    $(this).html('<i class="fas fa-chevron-down mr-1" id="moreFieldsIcon"></i> More byproducts...');
                } else {
                    container.slideDown(200);
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $(this).html('<i class="fas fa-chevron-up mr-1" id="moreFieldsIcon"></i> Less byproducts...');
                }
            });

            // Toggle More Fields in Edit Production Modal
            $('#toggleEditMoreFields').on('click', function () {
                var container = $('#editMoreFieldsContainer');
                var icon = $('#editMoreFieldsIcon');
                if (container.is(':visible')) {
                    container.slideUp(200);
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    $(this).html('<i class="fas fa-chevron-down mr-1" id="editMoreFieldsIcon"></i> More byproducts...');
                } else {
                    container.slideDown(200);
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $(this).html('<i class="fas fa-chevron-up mr-1" id="editMoreFieldsIcon"></i> Less byproducts...');
                }
            });

            // Reset More Fields toggle when modal is closed
            $('#productionModal').on('hidden.bs.modal', function () {
                $('#moreFieldsContainer').hide();
                $('#toggleMoreFields').html('<i class="fas fa-chevron-down mr-1" id="moreFieldsIcon"></i> More byproducts...');
            });

            $('#editProductionModal').on('hidden.bs.modal', function () {
                $('#editMoreFieldsContainer').hide();
                $('#toggleEditMoreFields').html('<i class="fas fa-chevron-down mr-1" id="editMoreFieldsIcon"></i> More byproducts...');
            });

            // Auto-calculate byproducts for Add Production modal
            function calculateByproducts(prefix) {
                var livestockType = $('#' + (prefix ? prefix + '_' : '') + 'livestock, #' + (prefix ? prefix + '_' : '') + 'Livestock').val();
                var itemsReceived = unformatNumber($('#' + (prefix ? prefix + '_' : '') + 'items_received').val()) || 0;
                var isCattle = (livestockType === 'Cattle');

                // Mikia: 1 per cattle (only for cattle)
                var mikia = isCattle ? itemsReceived : 0;
                $('#' + (prefix ? prefix + '_' : '') + 'mikia').val(mikia > 0 ? numberWithCommas(mikia) : '');

                // Ngozi: 1 per livestock (all types)
                var ngozi = itemsReceived;
                $('#' + (prefix ? prefix + '_' : '') + 'ngozi').val(ngozi > 0 ? numberWithCommas(ngozi) : '');

                // Vichwa: 1 per livestock (all types)
                var vichwa = itemsReceived;
                $('#' + (prefix ? prefix + '_' : '') + 'vichwa').val(vichwa > 0 ? numberWithCommas(vichwa) : '');

                // Miguu: 4 per livestock (all types)
                var miguu = itemsReceived * 4;
                $('#' + (prefix ? prefix + '_' : '') + 'miguu').val(miguu > 0 ? numberWithCommas(miguu) : '');
            }

            // Trigger byproduct calculation when livestock or items_received changes (Add modal)
            $(document).on('change', '#livestock', function () {
                calculateByproducts('');
            });
            $(document).on('input', '#items_received', function () {
                calculateByproducts('');
            });

            // Trigger byproduct calculation when livestock or items_received changes (Edit modal)
            $(document).on('change', '#edit_Livestock', function () {
                calculateByproducts('edit');
            });
            $(document).on('input', '#edit_items_received', function () {
                calculateByproducts('edit');
            });

            // Initialize DataTable
            var table = $('#production_table').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": true,
                "ajax": {
                    "url": "{{ route('production.data') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (d) {
                        d._token = "{{csrf_token()}}";
                        var dr = $('#date_range').val();
                        if (dr) {
                            var dates = dr.split(' - ');
                            d.start_date = dates[0];
                            d.end_date = dates[1];
                        }
                    },
                    "error": function (xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        notify('Error loading production data', 'top', 'right', 'danger');
                    }
                },
                "columns": [
                    { "data": "production_date" },
                    { "data": "details" },
                    { "data": "items_received", "className": "dt-center" },
                    { "data": "total_weight", "className": "dt-center" },
                    { "data": "weight_difference", "className": "dt-center" },
                    { "data": "meat", "className": "dt-center" },
                    { "data": "steak", "className": "dt-center" },
                    { "data": "beef_fillet", "className": "dt-center" },
                    { "data": "beef_liver", "className": "dt-center" },
                    { "data": "utumbo", "className": "dt-center" },
                    {
                        "data": null,
                        "className": "dt-center",
                        "orderable": false,
                        "render": function (data, type, row) {
                            let hasDistributions = row.has_distributions || false;
                            let distBtn = `<button class='btn btn-sm btn-success btn-rounded dist-btn' data-id='${row.id}'>Distribute</button>`;
                            let editBtn = hasDistributions
                                ? `<button class='btn btn-sm btn-primary btn-rounded' disabled title='Cannot edit - distributions exist'>Edit</button>`
                                : `<button class='btn btn-sm btn-primary btn-rounded edit-btn' data-id='${row.id}'>Edit</button>`;
                            let delBtn = hasDistributions
                                ? `<button class='btn btn-sm btn-danger btn-rounded' disabled title='Cannot delete - distributions exist'>Delete</button>`
                                : `<button class='btn btn-sm btn-danger btn-rounded delete-btn' data-id='${row.id}'>Delete</button>`;
                            return distBtn + ' ' + editBtn + ' ' + delBtn;
                        }
                    }
                ],
                "order": [[0, 'desc']],
                "language": {
                    "emptyTable": "No data available in table",
                    "processing": '<img id="loading-image" style="width: 50px; height: 50px; opacity: 0.5;" src="{{asset('assets/images/spinner.gif')}}" />'
                }
            });

            // Date range filter events
            $('#date_range').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
                table.ajax.reload();
            });
            $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            // Handle form submission (modal)
            $('#production_form').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: "{{ route('production.store') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            notify(response.message || 'Production recorded successfully', 'top', 'right', 'success');
                            $('#production_form')[0].reset();
                            $('#production_date').daterangepicker({
                                singleDatePicker: true,
                                showDropdowns: true,
                                autoUpdateInput: false,
                                autoApply: true,
                                maxDate: moment(),
                                locale: {
                                    format: 'YYYY-MM-DD'
                                },
                            });
                            $('#weight_difference').val('');
                            $('#productionModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            notify('Error recording production', 'top', 'right', 'danger');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMsg = Object.values(errors).join('<br>');
                            notify(errorMsg, 'top', 'right', 'danger');
                        } else {
                            notify('Error recording production', 'top', 'right', 'danger');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html('Save');
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this production record?')) {
                    $.ajax({
                        url: "{{ url('production') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (response) {
                            if (response.success) {
                                notify(response.message || 'Production record deleted successfully', 'top', 'right', 'success');
                                table.ajax.reload();
                            } else {
                                notify('Error deleting production record', 'top', 'right', 'danger');
                            }
                        },
                        error: function () {
                            notify('Error deleting production record', 'top', 'right', 'danger');
                        }
                    });
                }
            });

            // Calculate weight difference: totalWeight - (meat + steak + beefFillet)
            function calculateWeightDifference(prefix) {
                var totalWeight = unformatNumber($('#' + prefix + 'total_weight').val());
                var meat = unformatNumber($('#' + prefix + 'meat').val());
                var steak = unformatNumber($('#' + prefix + 'steak').val());
                var beefFillet = unformatNumber($('#' + prefix + 'beef_fillet').val());
                var weightDiff = totalWeight - (meat + steak + beefFillet);
                $('#' + prefix + 'weight_difference').val(numberWithCommas(weightDiff));
            }

            // Add form - calculate weight difference when total_weight, meat, steak, or beef_fillet changes
            $(document).on('input', '#total_weight, #meat, #steak, #beef_fillet', function () {
                calculateWeightDifference('');
            });

            // Edit form - calculate weight difference when total_weight, meat, steak, or beef_fillet changes
            $(document).on('input', '#edit_total_weight, #edit_meat, #edit_steak, #edit_beef_fillet', function () {
                calculateWeightDifference('edit_');
            });

            // Initialize edit date picker
            $('#edit_production_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                autoApply: true,
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });
            $('#edit_production_date').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            // Handle Edit button click
            $(document).on('click', '.edit-btn', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('production') }}/" + id,
                    type: "GET",
                    success: function (response) {
                        if (response.success) {
                            var data = response.data;
                            $('#edit_production_id').val(data.id);
                            $('#edit_production_date').val(data.production_date);
                            $('#edit_Livestock').val(data.details);
                            $('#edit_items_received').val(numberWithCommas(data.items_received));
                            $('#edit_total_weight').val(numberWithCommas(data.total_weight));
                            $('#edit_meat').val(numberWithCommas(data.meat));
                            $('#edit_steak').val(numberWithCommas(data.steak));
                            $('#edit_beef_fillet').val(numberWithCommas(data.beef_fillet));
                            $('#edit_weight_difference').val(numberWithCommas(data.weight_difference));
                            $('#edit_beef_liver').val(numberWithCommas(data.beef_liver));
                            $('#edit_utumbo').val(numberWithCommas(data.utumbo));
                            $('#edit_mafuta').val(numberWithCommas(data.mafuta));
                            $('#edit_mikia').val(numberWithCommas(data.mikia));
                            $('#edit_ngozi').val(numberWithCommas(data.ngozi));
                            $('#edit_vichwa').val(numberWithCommas(data.vichwa));
                            $('#edit_miguu').val(numberWithCommas(data.miguu));

                            // Auto-expand the more fields section if any byproduct has a value
                            if (data.mafuta || data.mikia || data.ngozi || data.vichwa || data.miguu) {
                                $('#editMoreFieldsContainer').show();
                                $('#toggleEditMoreFields').html('<i class="fas fa-chevron-up mr-1" id="editMoreFieldsIcon"></i> Less byproducts...');
                            }

                            $('#editProductionModal').modal('show');
                        } else {
                            notify('Error loading production data', 'top', 'right', 'danger');
                        }
                    },
                    error: function () {
                        notify('Error loading production data', 'top', 'right', 'danger');
                    }
                });
            });

            // Handle Edit form submission
            $('#edit_production_form').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_production_id').val();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: "{{ url('production') }}/" + id,
                    type: "PUT",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            notify(response.message || 'Production updated successfully', 'top', 'right', 'success');
                            $('#editProductionModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            notify('Error updating production', 'top', 'right', 'danger');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMsg = Object.values(errors).join('<br>');
                            notify(errorMsg, 'top', 'right', 'danger');
                        } else {
                            notify('Error updating production', 'top', 'right', 'danger');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html('Update');
                    }
                });
            });

            // Distribution Modal Logic
            var stores = @json($stores);
            var currentStoreIndex = 0;
            var distributions = [];
            var currentProductionId = null;
            var totalMeatWeight = 0;
            var productionData = null; // Store full production data
            var meatTypeLocked = false; // Flag to lock meat type after first distribution
            var distributionTypeLocked = false; // Flag to lock distribution type after first distribution

            // Handle distribution type change
            $(document).on('change', '#dist_distribution_type', function () {
                var distType = $(this).val();
                // Hide all recipient fields first
                $('#dist_branch_group').addClass('d-none').removeClass('d-flex');
                $('#dist_customer_group').addClass('d-none').removeClass('d-flex');
                $('#dist_order_group').addClass('d-none').removeClass('d-flex');

                // Show the appropriate field and adjust buttons
                switch (distType) {
                    case 'branch':
                        $('#dist_branch_group').removeClass('d-none').addClass('d-flex');
                        // Disable manual branch selection - use only skip/next/prev
                        $('#dist_store_id').prop('disabled', true);
                        // Show branch navigation buttons
                        $('#skipBranchBtn').show();
                        $('#backBranchBtn').show();
                        $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');
                        break;
                    case 'cash_sale':
                        // $('#dist_customer_group').removeClass('d-none').addClass('d-flex');
                        // Hide branch navigation, show Save directly
                        $('#skipBranchBtn').hide();
                        $('#backBranchBtn').hide();
                        $('#nextBranchBtn').text('Save').removeClass('btn-primary').addClass('btn-success');
                        break;
                    case 'order':
                        $('#dist_order_group').removeClass('d-none').addClass('d-flex');
                        // Hide branch navigation, show Save directly
                        $('#skipBranchBtn').hide();
                        $('#backBranchBtn').hide();
                        $('#nextBranchBtn').text('Save').removeClass('btn-primary').addClass('btn-success');
                        break;
                }
            });

            // Store entered values for each branch to maintain on navigation
            var branchDistributions = {};
            // Store previously saved distributions (from database) - used for calculating remaining weight
            var savedDistributions = [];

            function resetDistributionModal() {
                currentStoreIndex = 0;
                distributions = []; // Only new distributions for current session
                savedDistributions = []; // Previously saved distributions from database
                branchDistributions = {};
                totalMeatWeight = 0;
                productionData = null;
                meatTypeLocked = false;
                distributionTypeLocked = false;
                $('#dist_distribution_type').val('branch').prop('disabled', false);
                $('#dist_store_id').val('').prop('disabled', true); // Disable manual selection for branch type
                $('#dist_customer_name').val('');
                $('#dist_order_id').val('');
                $('#dist_notes').val('');
                $('#dist_meat_type').val('').prop('disabled', false);
                $('#dist_weight').val('');
                $('#dist_total_weight').val('0');
                $('#dist_remaining_weight').val('0');
                $('#distributionSummary').hide();
                $('#summaryContent').html('');
                $('#backBranchBtn').prop('disabled', true);
                // Show branch group by default, hide others
                $('#dist_branch_group').removeClass('d-none').addClass('d-flex');
                $('#dist_customer_group').addClass('d-none').removeClass('d-flex');
                $('#dist_order_group').addClass('d-none').removeClass('d-flex');
                // Reset buttons for branch mode
                $('#skipBranchBtn').show();
                $('#backBranchBtn').show();
                $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');
                // Reset labels to default
                updateDistributionLabels(null);
                updateStepIndicator();
            }

            // Helper function to get distributed amount for a specific meat type (saved distributions only)
            function getSavedDistributedForType(meatType) {
                return savedDistributions
                    .filter(d => d.meat_type === meatType)
                    .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);
            }

            // Get weight for specific meat type from production data
            function getMeatTypeWeight(meatType) {
                if (!productionData) return 0;
                switch (meatType) {
                    case 'Meat': return parseFloat(productionData.meat) || 0;
                    case 'Steak': return parseFloat(productionData.steak) || 0;
                    case 'Beef Fillet': return parseFloat(productionData.beef_fillet) || 0;
                    case 'Beef Liver': return parseFloat(productionData.beef_liver) || 0;
                    case 'Utumbo': return parseFloat(productionData.utumbo) || 0;
                    case 'Mafuta': return parseFloat(productionData.mafuta) || 0;
                    case 'Byproduct Pack':
                        // Byproduct Pack is calculated dynamically from Vichwa, Miguu, and Mikia
                        // 1 pack = 1 Vichwa + 4 Miguu + 1 Mikia
                        // This returns the TOTAL available packs (before any Byproduct Pack distributions)
                        // Only deduct individually distributed Vichwa/Miguu/Mikia, NOT Byproduct Pack distributions
                        var originalVichwa = parseFloat(productionData.vichwa) || 0;
                        var originalMiguu = parseFloat(productionData.miguu) || 0;
                        var originalMikia = parseFloat(productionData.mikia) || 0;

                        // Get already distributed amounts for each component (individual distributions only)
                        var distributedVichwa = getSavedDistributedForType('Vichwa');
                        var distributedMiguu = getSavedDistributedForType('Miguu');
                        var distributedMikia = getSavedDistributedForType('Mikia');

                        // Calculate available quantities after individual distributions (NOT including Byproduct Pack usage)
                        var availableVichwa = originalVichwa - distributedVichwa;
                        var availableMiguu = originalMiguu - distributedMiguu;
                        var availableMikia = originalMikia - distributedMikia;

                        // Calculate max packs based on each available component
                        var packsFromVichwa = Math.max(0, availableVichwa); // 1 per pack
                        var packsFromMiguu = Math.max(0, Math.floor(availableMiguu / 4)); // 4 per pack
                        var packsFromMikia = Math.max(0, availableMikia); // 1 per pack

                        // The total available packs is the minimum of all components
                        return Math.min(packsFromVichwa, packsFromMiguu, packsFromMikia);
                    case 'Mikia': return parseFloat(productionData.mikia) || 0;
                    case 'Ngozi': return parseFloat(productionData.ngozi) || 0;
                    case 'Vichwa': return parseFloat(productionData.vichwa) || 0;
                    case 'Miguu': return parseFloat(productionData.miguu) || 0;
                    default: return 0;
                }
            }

            // Get already distributed weight for specific meat type (includes both saved and new distributions)
            function getDistributedWeightForMeatType(meatType) {
                // Sum from previously saved distributions (from database)
                var savedTotal = savedDistributions
                    .filter(d => d.meat_type === meatType)

                    .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);

                // Sum from branchDistributions (current session values for all branches)
                // This gives us real-time tracking as user navigates and edits
                var branchTotal = 0;
                for (var storeId in branchDistributions) {
                    branchTotal += unformatNumber(branchDistributions[storeId]) || 0;
                }

                // Calculate additional deductions from Byproduct Pack distributions
                // Byproduct Pack contains: 1 Vichwa + 4 Miguu + 1 Mikia
                var byproductPackDeduction = 0;
                if (meatType === 'Vichwa' || meatType === 'Miguu' || meatType === 'Mikia') {
                    // Get total Byproduct Pack distributed from saved distributions
                    var byproductPackDistributed = savedDistributions
                        .filter(d => d.meat_type === 'Byproduct Pack')
                        .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);

                    // Calculate deduction based on meat type
                    if (meatType === 'Vichwa') {
                        byproductPackDeduction = byproductPackDistributed * 1; // 1 vichwa per pack
                    } else if (meatType === 'Miguu') {
                        byproductPackDeduction = byproductPackDistributed * 4; // 4 miguu per pack
                    } else if (meatType === 'Mikia') {
                        byproductPackDeduction = byproductPackDistributed * 1; // 1 mikia per pack
                    }
                }

                return savedTotal + branchTotal + byproductPackDeduction;
            }

            // Calculate remaining weight excluding current branch (for validation)
            function getRemainingExcludingCurrentBranch() {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType || !productionData) return 0;

                var totalForType = getMeatTypeWeight(selectedMeatType);

                // Sum from previously saved distributions
                var savedTotal = savedDistributions
                    .filter(d => d.meat_type === selectedMeatType)
                    .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);

                // Sum from branchDistributions excluding current branch
                var currentStoreId = stores[currentStoreIndex]?.id;
                var branchTotal = 0;
                for (var storeId in branchDistributions) {
                    if (storeId != currentStoreId) {
                        branchTotal += unformatNumber(branchDistributions[storeId]) || 0;
                    }
                }

                // Calculate additional deductions from Byproduct Pack distributions
                // Byproduct Pack contains: 1 Vichwa + 4 Miguu + 1 Mikia
                var byproductPackDeduction = 0;
                if (selectedMeatType === 'Vichwa' || selectedMeatType === 'Miguu' || selectedMeatType === 'Mikia') {
                    var byproductPackDistributed = savedDistributions
                        .filter(d => d.meat_type === 'Byproduct Pack')
                        .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);

                    if (selectedMeatType === 'Vichwa') {
                        byproductPackDeduction = byproductPackDistributed * 1;
                    } else if (selectedMeatType === 'Miguu') {
                        byproductPackDeduction = byproductPackDistributed * 4;
                    } else if (selectedMeatType === 'Mikia') {
                        byproductPackDeduction = byproductPackDistributed * 1;
                    }
                }

                return totalForType - savedTotal - branchTotal - byproductPackDeduction;
            }

            // Update weights based on selected meat type
            function updateWeightsForMeatType() {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType || !productionData) {
                    $('#dist_total_weight').val('0');
                    $('#dist_remaining_weight').val('0');
                    return;
                }

                var totalForType = getMeatTypeWeight(selectedMeatType);
                var distributedForType = getDistributedWeightForMeatType(selectedMeatType);
                var remainingForType = totalForType - distributedForType;

                totalMeatWeight = totalForType;
                $('#dist_total_weight').val(formatSmartDecimal(totalForType));
                $('#dist_remaining_weight').val(formatSmartDecimal(remainingForType));
            }

            // Check if selected product type is a byproduct (not measured in kg)
            function isByproduct(meatType) {
                var byproducts = ['Byproduct Pack', 'Mikia', 'Ngozi', 'Vichwa', 'Miguu'];
                return byproducts.includes(meatType);
            }

            // Update labels based on product type
            function updateDistributionLabels(meatType) {
                if (!meatType) {
                    // Reset to default
                    $('#dist_total_weight_label').text('Total Weight ');
                    $('#dist_remaining_weight_label').text('Remaining Weight ');
                    $('#dist_weight_label').html('Distributed (kg) <span class="text-danger">*</span>');
                    $('#dist_weight').attr('placeholder', 'Enter weight');
                    return;
                }

                if (meatType === 'Blood') {
                    // Blood is measured in litres
                    $('#dist_total_weight_label').text('Total (Litres) ');
                    $('#dist_remaining_weight_label').text('Remaining (Litres) ');
                    $('#dist_weight_label').html('Distributed (L) <span class="text-danger">*</span>');
                    $('#dist_weight').attr('placeholder', 'Enter litres');
                } else if (isByproduct(meatType)) {
                    // Byproducts are counted as units
                    $('#dist_total_weight_label').text('Total Qty ');
                    $('#dist_remaining_weight_label').text('Remaining Qty ');
                    $('#dist_weight_label').html('Distributed Qty <span class="text-danger">*</span>');
                    $('#dist_weight').attr('placeholder', 'Enter quantity');
                } else {
                    // Regular meat products measured in kg
                    $('#dist_total_weight_label').text('Total Weight ');
                    $('#dist_remaining_weight_label').text('Remaining Weight ');
                    $('#dist_weight_label').html('Distributed (kg) <span class="text-danger">*</span>');
                    $('#dist_weight').attr('placeholder', 'Enter weight');
                }
            }

            // Handle meat type change
            $(document).on('change', '#dist_meat_type', function () {
                var selectedType = $(this).val();
                updateDistributionLabels(selectedType);
                updateWeightsForMeatType();
            });

            // Handle weight input change - dynamically update remaining weight
            $(document).on('input', '#dist_weight', function () {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType || !productionData) return;

                // Get current input value
                var currentInputWeight = unformatNumber($(this).val()) || 0;

                // Calculate remaining excluding current branch, then subtract current input
                var remainingExcludingCurrent = getRemainingExcludingCurrentBranch();
                var newRemaining = remainingExcludingCurrent - currentInputWeight;

                $('#dist_remaining_weight').val(formatSmartDecimal(newRemaining));
            });

            function updateStepIndicator() {
                var html = '';
                for (var i = 0; i < stores.length; i++) {
                    var dotClass = 'step-dot';
                    if (i < currentStoreIndex) {
                        dotClass += ' completed';
                    } else if (i === currentStoreIndex) {
                        dotClass += ' active';
                    }
                    html += '<div class="' + dotClass + '" title="' + stores[i].name + '"></div>';
                }
                $('#stepIndicator').html(html);
            }

            // Format number for display (decimals only when applicable)
            function formatSmartDecimal(num) {
                if (num === null || num === undefined || num === '' || isNaN(num)) {
                    return '0';
                }
                num = parseFloat(num);
                if (isNaN(num)) return '0';
                if (num % 1 === 0) {
                    return num.toLocaleString('en-US');
                }
                return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }

            function updateRemainingWeight() {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType) {
                    $('#dist_remaining_weight').val('0');
                    $('#totalDistributed').text('0');
                    return 0;
                }

                var distributedForType = getDistributedWeightForMeatType(selectedMeatType);
                var totalForType = getMeatTypeWeight(selectedMeatType);
                var remaining = totalForType - distributedForType;

                $('#dist_remaining_weight').val(formatSmartDecimal(remaining));
                $('#totalDistributed').text(formatSmartDecimal(distributedForType));
                return remaining;
            }

            function updateSummary() {
                if (distributions.length === 0) {
                    $('#distributionSummary').hide();
                    return;
                }
                var html = '';
                distributions.forEach(function (d) {
                    var recipientName = '';
                    switch (d.distribution_type) {
                        case 'branch':
                            recipientName = stores.find(s => s.id == d.store_id)?.name || 'Unknown Branch';
                            break;
                        case 'cash_sale':
                            recipientName = d.customer_name || 'Cash Sale';
                            break;
                        case 'order':
                            recipientName = d.order_to ? 'Order: ' + d.order_to : 'Order';
                            break;
                        default:
                            recipientName = 'Order';
                    }
                    var typeLabel = d.distribution_type === 'branch' ? 'Branch' :
                        (d.distribution_type === 'cash_sale' ? 'Cash Sale' : 'Order');
                    html += '<div class="distribution-summary-item">';
                    html += '<span><small class="badge badge-secondary">' + typeLabel + '</small> ' + recipientName + ' (' + d.meat_type + ')</span>';
                    html += '<span>' + formatSmartDecimal(parseFloat(d.weight_distributed)) + ' kg</span>';
                    html += '</div>';
                });
                $('#summaryContent').html(html);
                $('#distributionSummary').hide();
                updateRemainingWeight();
            }

            function moveToNextBranch() {
                // Save current branch value before moving (save as 0 if empty)
                var currentStoreId = stores[currentStoreIndex]?.id;
                var currentWeight = $('#dist_weight').val();
                if (currentStoreId) {
                    // Always save the value, default to '0' if empty
                    branchDistributions[currentStoreId] = currentWeight || '0';
                }

                // Check if we're on the last branch
                if (currentStoreIndex >= stores.length - 1) {
                    // We're on the last branch, just change button to Save
                    // Don't increment, don't clear - keep showing the last branch with its value
                    $('#nextBranchBtn').text('Save').removeClass('btn-primary').addClass('btn-success');
                    // Enable back button
                    $('#backBranchBtn').prop('disabled', false);
                    updateStepIndicator();
                    updateRemainingWeight();
                    return;
                }

                currentStoreIndex++;
                // Pre-select next store
                $('#dist_store_id').val(stores[currentStoreIndex].id);
                // Restore previously entered value for this branch if exists (including 0)
                var nextStoreId = stores[currentStoreIndex].id;
                if (branchDistributions.hasOwnProperty(nextStoreId)) {
                    $('#dist_weight').val(branchDistributions[nextStoreId]);
                } else {
                    $('#dist_weight').val('');
                }
                // Enable back button since we moved forward
                $('#backBranchBtn').prop('disabled', false);
                updateStepIndicator();
                updateRemainingWeight(); // Update remaining weight for current meat type
            }

            function moveToPreviousBranch() {
                if (currentStoreIndex <= 0) return;

                // Save current branch value before moving back (save as 0 if empty)
                var currentStoreId = stores[currentStoreIndex]?.id;
                var currentWeight = $('#dist_weight').val();
                if (currentStoreId) {
                    branchDistributions[currentStoreId] = currentWeight || '0';
                }

                currentStoreIndex--;

                // Reset Next button if we were on Save
                $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');

                // Pre-select the previous store
                $('#dist_store_id').val(stores[currentStoreIndex].id);

                // Restore previously entered value for this branch (including 0)
                var prevStoreId = stores[currentStoreIndex].id;
                if (branchDistributions.hasOwnProperty(prevStoreId)) {
                    $('#dist_weight').val(branchDistributions[prevStoreId]);
                } else {
                    $('#dist_weight').val('');
                }

                // Disable back button if we're at the first branch
                if (currentStoreIndex === 0) {
                    $('#backBranchBtn').prop('disabled', true);
                    // Unlock meat type if going back to first branch and no distributions yet
                    if (distributions.length === 0) {
                        meatTypeLocked = false;
                        $('#dist_meat_type').prop('disabled', false);
                    }
                }

                updateStepIndicator();
                updateRemainingWeight();
            }

            // Handle Distribution button click
            $(document).on('click', '.dist-btn', function () {
                var id = $(this).data('id');
                currentProductionId = id;
                resetDistributionModal();

                // Show modal immediately with loading state
                $('#distributionLoading').show();
                $('#distributionContent').hide();
                $('#distributionModal').modal('show');

                $.ajax({
                    url: "{{ url('production') }}/" + id + "/distributions",
                    type: "GET",
                    success: function (response) {
                        if (response.success) {
                            var production = response.production;
                            console.log('Distribution data:', response);

                            // Store full production data for meat type weight lookups
                            productionData = production;

                            $('#dist_production_id').val(production.id);
                            $('#dist_production_date').text(production.production_date);

                            // Don't set total weight yet - wait for meat type selection
                            $('#dist_total_weight').val('0');
                            $('#dist_remaining_weight').val('0');

                            // Load existing distributions (previously saved) - these are used for calculating remaining weight only
                            if (response.data && response.data.length > 0) {
                                savedDistributions = response.data.map(function (d) {
                                    return {
                                        distribution_type: d.distribution_type || 'branch',
                                        store_id: d.store_id,
                                        customer_id: d.customer_id,
                                        customer_name: d.customer ? d.customer.name : null,
                                        order_id: d.order_id,
                                        meat_type: d.meat_type,
                                        weight_distributed: d.weight_distributed,
                                        notes: d.notes
                                    };
                                });
                            }

                            // Pre-select first store
                            if (stores.length > 0) {
                                $('#dist_store_id').val(stores[0].id);
                            }
                            updateStepIndicator();
                            $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');

                            // Hide loading, show content
                            $('#distributionLoading').hide();
                            $('#distributionContent').show();
                        } else {
                            $('#distributionModal').modal('hide');
                            notify('Error loading distribution data', 'top', 'right', 'danger');
                        }
                    },
                    error: function () {
                        $('#distributionModal').modal('hide');
                        notify('Error loading distribution data', 'top', 'right', 'danger');
                    }
                });
            });

            // Handle Next/Save button
            $('#nextBranchBtn').on('click', function () {
                var distributionType = $('#dist_distribution_type').val();
                var storeId = $('#dist_store_id').val();
                var customerName = $('#dist_customer_name').val();
                var orderId = $('#dist_order_id').val();
                var meatType = $('#dist_meat_type').val();
                var weight = $('#dist_weight').val();
                var notes = $('#dist_notes').val();

                // Build distribution entry based on type
                function buildDistributionEntry() {
                    var entry = {
                        distribution_type: distributionType,
                        meat_type: meatType,
                        weight_distributed: unformatNumber(weight),
                        notes: notes || null
                    };

                    switch (distributionType) {
                        case 'branch':
                            entry.store_id = storeId;
                            break;
                        case 'cash_sale':
                            entry.customer_name = customerName;
                            break;
                        case 'order':
                            entry.order_to = orderId;
                            break;
                    }
                    return entry;
                }

                // Validate based on distribution type
                function validateEntry() {
                    if (!meatType) {
                        notify('Please select a meat type', 'top', 'right', 'warning');
                        return false;
                    }
                    if (!weight) {
                        notify('Please enter the distributed weight', 'top', 'right', 'warning');
                        return false;
                    }
                    if (distributionType === 'branch' && !storeId) {
                        notify('Please select a branch', 'top', 'right', 'warning');
                        return false;
                    }
                    return true;
                }

                // If we're on the last branch and clicking Save
                if ($(this).text() === 'Save') {
                    var meatTypeForDist = $('#dist_meat_type').val();

                    // Validate meat type is selected
                    if (!meatTypeForDist) {
                        notify('Please select a meat type', 'top', 'right', 'warning');
                        return;
                    }

                    // Handle different distribution types
                    if (distributionType === 'branch') {
                        // Save current branch value first
                        var currentStoreId = stores[currentStoreIndex]?.id;
                        if (currentStoreId) {
                            branchDistributions[currentStoreId] = weight || '0';
                        }

                        // Build distributions array from branchDistributions
                        distributions = []; // Reset distributions array

                        for (var sid in branchDistributions) {
                            var branchWeight = unformatNumber(branchDistributions[sid]);
                            if (branchWeight > 0) {
                                distributions.push({
                                    distribution_type: 'branch',
                                    store_id: sid,
                                    meat_type: meatTypeForDist,
                                    weight_distributed: branchWeight,
                                    notes: null
                                });
                            }
                        }
                    } else if (distributionType === 'cash_sale') {
                        // Validate weight for cash sale
                        if (!weight || unformatNumber(weight) <= 0) {
                            notify('Please enter the distributed weight', 'top', 'right', 'warning');
                            return;
                        }

                        distributions = [{
                            distribution_type: 'cash_sale',
                            customer_name: customerName || null,
                            meat_type: meatTypeForDist,
                            weight_distributed: unformatNumber(weight),
                            notes: notes || null
                        }];
                    } else if (distributionType === 'order') {
                        // Validate weight for order
                        if (!weight || unformatNumber(weight) <= 0) {
                            notify('Please enter the distributed weight', 'top', 'right', 'warning');
                            return;
                        }

                        distributions = [{
                            distribution_type: 'order',
                            order_to: orderId || null,
                            meat_type: meatTypeForDist,
                            weight_distributed: unformatNumber(weight),
                            notes: notes || null
                        }];
                    }

                    if (distributions.length === 0) {
                        notify('Please add at least one distribution with weight greater than 0', 'top', 'right', 'warning');
                        return;
                    }

                    // Save all distributions
                    var btn = $(this);
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                    $.ajax({
                        url: "{{ url('production') }}/" + currentProductionId + "/distributions",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            distributions: distributions
                        },
                        success: function (response) {
                            if (response.success) {
                                notify(response.message || 'Distributions saved successfully', 'top', 'right', 'success');
                                $('#distributionModal').modal('hide');
                                table.ajax.reload();
                            } else {
                                notify('Error saving distributions', 'top', 'right', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var errors = xhr.responseJSON?.errors;
                            if (errors) {
                                var errorMsg = Object.values(errors).flat().join('<br>');
                                notify(errorMsg, 'top', 'right', 'danger');
                            } else {
                                notify('Error saving distributions', 'top', 'right', 'danger');
                            }
                        },
                        complete: function () {
                            btn.prop('disabled', false).html('Save');
                        }
                    });
                    return;
                }

                // Validate current entry
                if (!validateEntry()) {
                    return;
                }

                // Validate weight doesn't exceed remaining (excluding current branch's saved value)
                var enteredWeight = unformatNumber(weight);
                var currentRemaining = getRemainingExcludingCurrentBranch();
                if (enteredWeight > currentRemaining) {
                    notify('Distributed weight ' + formatSmartDecimal(enteredWeight) + 'kg cannot exceed remaining weight ' + formatSmartDecimal(currentRemaining) + 'kg', 'top', 'right', 'warning');
                    return;
                }

                // For branch type, check for duplicate store with same meat type
                if (distributionType === 'branch') {
                    var existingIndex = distributions.findIndex(d => d.distribution_type === 'branch' && d.store_id == storeId && d.meat_type == meatType);
                    if (existingIndex >= 0) {
                        // Update existing
                        distributions[existingIndex] = buildDistributionEntry();
                    } else {
                        // Add new
                        distributions.push(buildDistributionEntry());
                    }
                } else {
                    // For cash sale and order, just add new entry
                    distributions.push(buildDistributionEntry());
                }

                // Lock meat type and distribution type after first distribution is added
                if (!meatTypeLocked) {
                    meatTypeLocked = true;
                    $('#dist_meat_type').prop('disabled', true);
                }
                if (!distributionTypeLocked) {
                    distributionTypeLocked = true;
                    $('#dist_distribution_type').prop('disabled', true);
                }

                updateSummary();
                moveToNextBranch();
            });

            // Handle Skip button
            $('#skipBranchBtn').on('click', function () {
                // Set current branch value to 0 when skipping
                $('#dist_weight').val('0');
                moveToNextBranch();
            });

            // Handle Back button
            $('#backBranchBtn').on('click', function () {
                moveToPreviousBranch();
            });
        });
    </script>
@endpush