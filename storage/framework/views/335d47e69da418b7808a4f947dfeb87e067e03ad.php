<?php $__env->startSection('content-title'); ?>
    Cash Sales
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Cash Sales</a></li>
<?php $__env->stopSection(); ?>


<?php $__env->startSection("content"); ?>
    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
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
                <div class="tab-content" id="myTabContent">
                    <form id="sales_form">
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
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="" id="is_all_store" value="<?php echo e(current_store()->name); ?>">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label id="cat_label">Price Category<font color="red">*</font></label>
                                    <select id="price_category" class="js-example-basic-single form-control" required>
                                        <option value="" selected="true" disabled>Select Price Category</option>
                                        <?php $__currentLoopData = $price_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($price->id); ?>" <?php echo e($default_sale_type === $price->id ? 'selected' : ''); ?>><?php echo e($price->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <input type="text" id="barcode_input" style="position:absolute; left:-9999px;" autofocus>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Products<font color="red">*</font></label>
                                    <select id="products" class="form-control">
                                        <option value="" disabled selected style="display:none;">Select Product</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="code">Payment Type</label>
                                    <select name="payment_type" id="payment_type"
                                        class="js-example-basic-single form-control">
                                        <?php $__currentLoopData = $payment_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            
                                            <option value="<?php echo e($payment->id); ?>"><?php echo e($payment->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="code">Customer Name<font color="red">*</font></label>
                                    <select name="customer_id" id="customer_id"
                                        class="js-example-basic-single form-control">
                                        <option value="" disabled>Select Customer</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <!-- <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option> -->
                                            <option value="<?php echo e($customer->id); ?>" <?php echo e($default_customer === $customer->id ? 'selected' : ''); ?>><?php echo e($customer->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading" style="display: none; z-index: 60;">
                            <img id="loading-image" src="<?php echo e(asset('assets/images/spinner.gif')); ?>" />
                        </div>

                        <div class="row" id="detail">
                            <hr>
                            <div class="table teble responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover pl-3 pr-3" width="100%"></table>
                            </div>

                        </div>
                        <input type="hidden" name="" id="is_backdate_enabled" value="<?php echo e($back_date); ?>">
                        <?php if($back_date == "NO"): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <?php if($enable_discount === "YES"): ?>
                                        <div style="width: 99%">
                                            <label>Discount</label>
                                            <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                value="0.00" />
                                            <span class="help-inline">
                                                <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                    Discount</div>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <div style="width: 99%" hidden>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="sub_total" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total_vat" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Total
                                                Amount:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                                value="0.00" />

                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="price_cat" name="price_category_id">
                                <input type="hidden" id="discount_value" name="discount_amount">
                                <input type="hidden" id="order_cart" name="cart">
                                <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div style="width: 99%">
                                        <label>Sales Date<font color="red">*</font></label>
                                        <input type="text" name="sale_date" class="form-control" id="cash_sale_date"
                                            autocomplete="off" required="true" value="<?php echo e(date('Y-m-d')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php if($enable_discount === "YES"): ?>
                                        <div style="width: 99%">
                                            <label>Discount</label>
                                            <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                value="0.00" />
                                        </div>
                                        <span class="help-inline">
                                            <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                Discount</div>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="sub_total" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total_vat" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Total
                                                Amount:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                                value="0.00" />

                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="price_cat" name="price_category_id">
                                <input type="hidden" id="discount_value" name="discount_amount">
                                <input type="hidden" id="order_cart" name="cart">
                                <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                                <input type="hidden" value="" id="total_vat">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" value="<?php echo e($price_category); ?>" id="category">
                        <input type="hidden" value="<?php echo e($customers); ?>" id="customers">
                        <input type="hidden" value="<?php echo e($fixed_price); ?>" id="fixed_price">
                        <input type="hidden" value="<?php echo e($enable_discount); ?>" id="enable_discount">
                        <?php if($enable_paid === "YES"): ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div style="width: 99%">
                                        <label><b>Paid</b></label>
                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                            value="0.00" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="width: 99%">
                                        <label><b>Change</b></label>
                                        <input type="text" id="change_amount" class="form-control" value="0.00" readonly />
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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
                                    <button class="btn btn-danger" id="deselect-all" onclick="return false">Cancel
                                    </button>
                                    <button class="btn btn-primary" id="save_btn">Save</button>
                                </div>
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('sales.customers.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush("page_scripts"); ?>
    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script type="text/javascript">

        // Connect to QZ Tray when page loads
        // qz.websocket.connect().then(function() {
        //     console.log("Connected to QZ Tray");
        // }).catch(function(err) {
        //     console.error("Error connecting to QZ Tray:", err);
        // });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                selectProducts: '<?php echo e(route('selectProducts')); ?>',
                storeCashSale: '<?php echo e(route('cash-sales.storeCashSale')); ?>',
                filterProductByWord: '<?php echo e(route('filter-product-by-word')); ?>'

            }
        };

        // Load cart from localStorage on page load
        var cart = JSON.parse(localStorage.getItem('cart')) || [];
        var default_cart = JSON.parse(localStorage.getItem('default_cart')) || [];
        var order_cart = JSON.parse(localStorage.getItem('order_cart')) || [];

    </script>
    <script src="<?php echo e(asset('assets/apotek/js/notification.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/apotek/js/sales.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/apotek/js/customer.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pages/ac-datepicker.js')); ?>"></script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/sales/cash_sales/index.blade.php ENDPATH**/ ?>