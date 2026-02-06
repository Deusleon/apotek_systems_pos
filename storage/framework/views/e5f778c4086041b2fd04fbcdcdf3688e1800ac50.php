<?php $__env->startSection('content-title'); ?>
    Credit Sales
<?php $__env->stopSection(); ?>

<?php
    // Get the active tab from the session or default to "new"
    $activeTab = session('alert-success', '');
    $activeTabView = session('activeTabView', '');
?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Credit Sales</a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>

    <style>
        .iti__flag {
            background-image: url("<?php echo e(asset("assets/plugins/intl-tel-input/img/flags.png")); ?>");
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("<?php echo e(asset("assets/plugins/intl-tel-input/img/flags@2x.png")); ?>");
            }
        }

        .iti {
            width: 100%;
        }

        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }
    </style>

    <div class="col-sm-12 p-0">
        <div class="card-block">

            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <?php if(auth()->user()->checkPermission('View Credit Sales')): ?>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase active" id="credit-sale-receiving-tablist"
                                href="<?php echo e(route('credit-sales.creditSale')); ?>" role="tab" aria-controls="credit_sales" aria-selected="true">New
                                sale</a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->user()->checkPermission('View Credit Tracking')): ?>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-tracking-tablist"
                                href="<?php echo e(route('credits-tracking.creditsTracking')); ?>" role="tab" aria-controls="credit_tracking"
                                aria-selected="false">Tracking
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->user()->checkPermission('View Credit Payments')): ?>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-payment-tablist"
                                href="<?php echo e(route('credit-payments.getCreditsCustomers')); ?>" role="tab" aria-controls="credit_payment" aria-selected="false">Payments
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>     
                <div class="tab-content" id="myTabContent">
                    
                    <?php if(auth()->user()->checkPermission('View Credit Sales')): ?>
                        <div class="tab-pane fade show active" id="credit-sale-receiving" role="tabpanel"
                            aria-labelledby="credit_sales-tab">
                            <form id="credit_sales_form">
                                <?php echo csrf_field(); ?>
                                <?php if(auth()->user()->checkPermission('Add Customers')): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button style="float: right;margin-bottom: 2%;" type="button"
                                                class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create"> Add
                                                New Customer
                                            </button>
                                        </div>

                                    </div>
                                <?php endif; ?>

                                <input type="hidden" name="" id="is_all_store" value="<?php echo e(current_store()->name); ?>">
                                <div id="sale-panel">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label id="cat_label">Price Category<font color="red">*</font></label>
                                                <select id="price_category" class="js-example-basic-single form-control"
                                                    required>
                                                    <option value="">Select Price Category</option>
                                                    <?php $__currentLoopData = $price_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <!-- <option value="<?php echo e($price->id); ?>"><?php echo e($price->name); ?></option> -->
                                                        <option value="<?php echo e($price->id); ?>" <?php echo e($default_sale_type === $price->id ? 'selected' : ''); ?>><?php echo e($price->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" id="credit_barcode_input" style="position:absolute; left:-9999px;"
                                            autofocus>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Products<font color="red">*</font></label>
                                                <select id="products" class="form-control">
                                                    <option value="" disabled selected style="display:none;">Select Product
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3" hidden>
                                            <div class="form-group">
                                                <label for="code">Payment Type</label>
                                                <select name="payment_type" id="payment_type"
                                                    class="js-example-basic-single form-control">
                                                    <option value="">Select Payment</option>
                                                    <?php $__currentLoopData = $payment_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <option value="<?php echo e($payment->id); ?>"><?php echo e($payment->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="code">Customer Name<font color="red">*</font></label>
                                                <select name="customer_id" id="customer_id"
                                                    class="js-example-basic-single form-control" title="Customer" required>
                                                    <option value="">Select Customer</option>
                                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($customer->id); ?>" data-customer='<?php echo json_encode($customer, 15, 512) ?>'>
                                                            <?php echo e($customer->name); ?>

                                                        </option>
                                                        
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="detail">
                                        <hr>
                                        <div class="table table responsive" style="width: 100%;">
                                            <table id="cart_table" class="table nowrap table-striped table-hover pl-3 pr-3" width="100%">
                                            </table>
                                        </div>

                                    </div>
                                    <hr>
                                    <input type="hidden" name="" id="is_backdate_enabled" value="<?php echo e($back_date); ?>">
                                    <?php if($back_date == "NO"): ?>
                                        <div class="row">
                                            <?php if($enable_discount === "YES"): ?>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Discount</label>
                                                        <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                    <span class="help-inline">
                                                        <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                            Discount!</div>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Grace Period (Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21" selected>21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-md-6">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div style="width: 99%">
                                                        <label>Grace Period (Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period
                                                            </option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21" selected>21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <input type="hidden" id="price_cat" name="price_category_id">
                                            <input type="hidden" id="discount_value" name="discount_amount">
                                            <input type="hidden" id="paid_value" name="paid_amount">
                                            <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                            <input type="hidden" id="order_cart" name="cart">
                                            <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                                            <input type="hidden" value="<?php echo e($fixed_price); ?>" id="fixed_price">
                                            <input type="hidden" value="<?php echo e($enable_discount); ?>" id="enable_discount">
                                        </div>
                                    <?php else: ?>
                                        <div class="row">
                                            <?php if($enable_discount === "YES"): ?>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Sales Date<font color="red">*</font></label>
                                                        <input type="text" name="sale_date" class="form-control" id="credit_sale_date"
                                                            autocomplete="off" required="true" value="<?php echo e(date('Y-m-d')); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Discount</label>
                                                        <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                    <span class="help-inline">
                                                        <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                            Discount!</div>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Grace Period (Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21" selected>21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Sales Date<font color="red">*</font></label>
                                                        <input type="text" name="sale_date" class="form-control" id="credit_sale_date"
                                                            autocomplete="off" value="<?php echo e(date('Y-m-d')); ?>" required="true">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Grace Period (Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21" selected>21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <input type="hidden" id="price_cat" name="price_category_id">
                                            <input type="hidden" id="discount_value" name="discount_amount">
                                            <input type="hidden" id="paid_value" name="paid_amount">
                                            <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                            <input type="hidden" id="order_cart" name="cart">
                                            <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                                            <input type="hidden" value="<?php echo e($fixed_price); ?>" id="fixed_price">
                                            <input type="hidden" value="<?php echo e($enable_discount); ?>" id="enable_discount">
                                        </div>
                                    <?php endif; ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-8">
                                            
                                        </div>

                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Sub Total:</b>
                                                </div>
                                                <div class="sub-total col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>VAT:</b>
                                                </div>
                                                <div class="tax-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Total:</b>
                                                </div>
                                                <div class="total-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Balance:</b>
                                                </div>
                                                <div class="balance-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Max. Credit:</b>
                                                </div>
                                                <div id="max_credit" class="credit_max col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="total">
                                        <input type="hidden" id="sub_total">
                                        <input type="hidden" id="total_vat" value="0.00">
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6 d-flex">
                                            <div>
                                                <b>Total Items:</b>
                                                <span id="total_items">0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="btn-group" style="float: right;">
                                                <button type="button" class="btn btn-danger" id="deselect-all-credit-sale">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" value="" id="category">
                                    <input type="hidden" value="" id="customers">
                                    <input type="hidden" value="" id="print">

                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- ajax loading gif -->
                    <div id="loading">
                        <img id="loading-image" src="<?php echo e(asset('assets/images/spinner.gif')); ?>" />
                    </div>
                    

                </div>
            </div>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[name="payment-form"]');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                const btn = form.querySelector('#save_btn');
                // if already disabled, block double submit
                if (btn.dataset.saving === '1') {
                    e.preventDefault();
                    return;
                }
                // mark as saving and disable
                btn.dataset.saving = '1';
                btn.setAttribute('disabled', 'disabled');
                btn.setAttribute('aria-disabled', 'true');
                btn.originalText = btn.innerHTML;
                btn.innerHTML = 'Saving...';
            });
        });
    </script>

    <?php echo $__env->make('sales.customers.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>

    
    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <script type="text/javascript">

        var page_no = 1;//sales page
        var normal_search = 0;//search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                selectProducts: '<?php echo e(route('selectProducts')); ?>',
                storeCreditSale: '<?php echo e(route('credit-sales.storeCreditSale')); ?>',
                filterProductByWord: '<?php echo e(route('filter-product-by-word')); ?>',
                getCreditSale: '<?php echo e(route('getCreditSale')); ?>'
            }
        };
        var canAddCreditPayment = <?php echo e(auth()->user()->checkPermission('Add Credit Payment') ? 'true' : 'false'); ?>;

    </script>
    <script src="<?php echo e(asset("assets/plugins/moment/js/moment.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/notification.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/sales/credit.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/customer.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pages/ac-datepicker.js")); ?>"></script>

    
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () { $('#credit_barcode_input').focus(); }, 150);
            var start = moment();
            var end = moment();


            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sales_date').daterangepicker({
                startDate: moment().startOf("month"),
                endDate: moment().endOf("month"),
                maxDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

        });

    </script>
    
    <script>
        $('#fixed-header-main').DataTable({
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false
        });

        let payment_history_filter_table = $('#fixed-header-filter').DataTable({
            columns: [
                { 'data': 'receipt_number' },
                { 'data': 'name' },
                {
                    'data': 'created_at', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    'data': 'paid_amount', render: function (amount) {
                        return formatMoney(amount);
                    }
                }
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false,
            // aaSorting: [[1, "desc"]]
        });

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sales_date_payment').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);
            cb(start, end);

            $('input[name="date_of_sale"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
                filterPaymentHistory();
            });

            $('input[name="date_of_sale"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

        });

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
            }
        }

    </script>

    <script>
        $('#cust_id').on('change', function (e) {
            // e.preventDefault();

            const selectedValue = $(this).val();
            console.log("DataSelected", selectedValue);


            if (selectedValue === 'Select Customer') {
                credit_payment_table.column(1).search('').draw();
            }

            // Check if nothing is selected and reset the filter
            if (selectedValue && selectedValue !== 'Select Customer') {
                credit_payment_table.column(1).search(selectedValue).draw();
            } else {
                credit_payment_table.column(1).search('').draw();
            }
        });
    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/sales/credit_sales/index.blade.php ENDPATH**/ ?>