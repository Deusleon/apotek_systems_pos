<?php $__env->startSection('page_css'); ?>
    <style>


    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-title'); ?>
    Sales Reports
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Sales Reports </a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>

    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #select1 {
            z-index: 10050;
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

        input[type=button]:focus {
            background-color: #748892;
            border-color: #748892;
            color: white;
        }
    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form id="inventory_report_form" action="<?php echo e(route('sale-report-filter')); ?>" method="get" target="_blank">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="report_option">Select Sales Report<font color="red">*</font></label>
                                    <select id="report_option" name="report_option" onchange="reportOption()"
                                        class="js-example-basic-single form-control drop" required>
                                        <option selected="true" value="" disabled="disabled">Select report</option>
                                        <?php if(auth()->user()->checkPermission('Sales Details Report')): ?>
                                            <option value="9">Sales Details Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Sales Summary Report')): ?>
                                            <option value="10">Sales Summary Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Sales Total Report')): ?>
                                            <option value="7">Sales Total Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Cash Sales Details Report')): ?>
                                            <option value="1">Cash Sales Details Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Cash Sales Summary Report')): ?>
                                            <option value="2">Cash Sales Summary Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Cash Sales Total Report')): ?>
                                            <option value="13">Cash Sales Total Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Credit Sales Details Report')): ?>
                                            <option value="3">Credit Sales Details Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Credit Sales Summary Report')): ?>
                                            <option value="4">Credit Sales Summary Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Credit Sales Total Report')): ?>
                                            <option value="14">Credit Sales Total Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Credit Payments Report')): ?>
                                            <option value="5">Credit Payments Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Customer Payment Statement')): ?>
                                            <option value="6">Customer Credit Payment Statement</option>
                                        <?php endif; ?>
                                        <!-- <option value="6">Bill Sales Details Report</option>
                                                                <option value="7">Company Billing Report</option> -->
                                        <?php if(auth()->user()->checkPermission('Price List Report')): ?>
                                            <option value="8">Price List Report</option>
                                        <?php endif; ?>
                                        <!--   <option value="10">Sales Trend Chart</option> -->
                                        <?php if(auth()->user()->checkPermission('Sales Returns Report')): ?>
                                            <option value="11">Sales Returns Report</option>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Sales Comparison Report')): ?>
                                            <option value="12">Sales Comparison Report</option>
                                        <?php endif; ?>
                                        <?php if($enable_discount === 'YES'): ?>
                                            <?php if(auth()->user()->checkPermission('Discount Report')): ?>
                                                <option value="15">Discount Report</option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->checkPermission('Waste Collection Report')): ?>
                                            <option value="16">Waste Collection Report</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div id="range">
                                <label for="filter">Date<font color="red">*</font></label>
                                <input type="text" class="form-control" name="date_range" id="daterange" readonly />
                            </div>
                            <div id="price_category" style="display: none">
                                <label for="product">Price Category<font color="red">*</font></label>
                                <select id="product" name="category" onchange=""
                                    class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select category</option>
                                    <option value="all">
                                        All</option>
                                    <?php $__currentLoopData = $price_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>">
                                            <?php echo e($category->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span id="warning" style="color: #ff0000; display: none">Please select a category</span>
                            </div>
                        </div>

                        <div class="col-md-4" id="selling_price" style="display: none">
                            <label for="code">Type<font color="red">*</font></label>
                            <select name="price_type" id="price_type" class="js-example-basic-single form-control">
                                <option value="">Select type</option>
                                <option value="1">With Buy Price</option>
                                <option value="2">Without Buy Price</option>
                            </select>
                            <span id="price-type-warning" style="color: #ff0000; display: none">Please select type</span>
                        </div>

                        <div class="col-md-4" id="customer_statement" style="display: none">
                            <label for="code">Customer<font color="red">*</font></label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-single form-control">
                                <option value="">Select Customer</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->customer_id); ?>"><?php echo e($customer->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-2">
                            
                                <button class="btn btn-secondary" style="width: 100%">
                                    Show
                                </button>
                                
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ajax loading image -->
        <div id="loading">
            <image id="loading-image" src="<?php echo e(asset('assets/images/spinner.gif')); ?>"></image>
        </div>


    </div>

<?php $__env->stopSection(); ?>


<?php $__env->startPush("page_scripts"); ?>
    <script src="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pages/ac-datepicker.js")); ?>"></script>

    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            if ((Number(report_option_index) === Number(1)) || (Number(report_option_index) === Number(2))
                || (Number(report_option_index) === Number(3)) || (Number(report_option_index) === Number(4))
                || (Number(report_option_index) === Number(5)) || (Number(report_option_index) === Number(11))
                || (Number(report_option_index) === Number(12))) {
                $('#customer_id').prop('required', false);
                $("#product").prop("required", false);
            }

            //if product ledger
            if (Number(report_option_index) === Number(8)) {
                document.getElementById('price_category').style.display = 'block';
                $("#product").prop("required", true);
                $('#customer_id').prop('required', false);
                $("#customer_id").val("");
                $("#customer_id").change();
                document.getElementById('range').style.display = 'none';
            } else {
                document.getElementById('range').style.display = 'block';
                document.getElementById('price_category').style.display = 'none';
                document.getElementById('warning').style.display = 'none';

            }

            if (Number(report_option_index) === Number(6)) {
                document.getElementById('customer_statement').style.display = 'block';
                $('#customer_id').prop('required', true);
            } else {
                document.getElementById('customer_statement').style.display = 'none';
                $('#customer_id').prop('required', false);
            }

            if (Number(report_option_index) === Number(8)) {
                document.getElementById('selling_price').style.display = 'block';
                $('#price_type').prop('required', true);
            } else {
                document.getElementById('selling_price').style.display = 'none';
                $('#price_type').prop('required', false);
            }

        }


        $('#inventory_report_form').on('submit', function () {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            var product_option = document.getElementById("product");
            var product_option_index = product_option.options[product_option.selectedIndex].value;
            var price_type = document.getElementById('price_type').value;

            if (Number(report_option_index) === Number(8) && Number(product_option_index) !== '') {
                document.getElementById('warning').style.display = 'none';

            } else if (Number(report_option_index) === Number(8) && Number(product_option_index) === '') {
                document.getElementById('warning').style.display = 'block';
                return false;
            }

            if (Number(report_option_index) === 8 && (price_type !== '' && price_type !== null)) {
                document.getElementById('price-type-warning').style.display = 'none';
            } else if (Number(report_option_index) === 8 && (price_type !== '' && price_type !== null)) {
                document.getElementById('price-type-warning').style.display = 'block';
                return false;
            }
        });

    </script>
    <script type="text/javascript">
        $(function () {

            var start = moment().startOf('month');
            var end = moment();

            function cb(start, end) {
                // Display format
                $('#reportrange span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
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

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/sale_reports/index.blade.php ENDPATH**/ ?>