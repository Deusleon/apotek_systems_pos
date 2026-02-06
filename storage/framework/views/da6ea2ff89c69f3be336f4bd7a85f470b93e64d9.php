<?php
    function smartFormat($num)
    {
        $str = (string) $num;

        if (strpos($str, '.') !== false) {

            list($whole, $decimal) = explode('.', $str);

            $decimal = rtrim($decimal, '0');

            if ($decimal === '') {
                return number_format((int) $whole);
            }

            $wholeFormatted = number_format((int) $whole);

            return $wholeFormatted . '.' . $decimal;

        } else {
            return number_format((int) $str);
        }
    }
?>


<?php $__env->startSection('page_css'); ?>
    <style>

    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-title'); ?>
    Current Stock
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Current Stock </a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>

    <?php if(auth()->user()->checkPermission('View Current Stock')): ?>
        <div class="col-sm-12">
            <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="current-stock-tablist" data-toggle="pill"
                        href="<?php echo e(route('current-stocks')); ?>" role="tab" aria-controls="current-stock"
                        aria-selected="true">Current Stock</a>
                </li>
                <?php if(auth()->user()->checkPermission('View Current Stock Value')): ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="all-stock-tablist" data-toggle="pill"
                            href="<?php echo e(route('all-stocks')); ?>" role="tab" aria-controls="stock_list"
                            aria-selected="false">Current Stock Value
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(auth()->user()->checkPermission('View OLd Stock Value')): ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="old-stock-tablist" data-toggle="pill"
                            href="<?php echo e(route('old-stocks')); ?>" role="tab" aria-controls="stock_list" aria-selected="false">Old
                            Stock Value
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pr-3 row d-flex justify-content-end">
                        <div class="d-flex mr-3" style="width: 245px;">
                            <label for="stock_status" class="col-form-label text-md-right mr-2">Status:</label>
                            <select name="stock_status" class="js-example-basic-single form-control" id="stock_status_id">
                                <option name="store_name" value="all">All</option>
                                <option name="store_name" value="1">In Stock</option>
                                <option name="store_name" value="0">Out Of Stock</option>
                            </select>
                        </div>

                        <div class="d-flex" style="width: 245px; margin-right: -1px;">
                            <label for="category" class="col-form-label text-md-left mr-2">Type:</label>
                            <select name="category" class="js-example-basic-single form-control" id="category_id">
                                <option name="store_name" value="1">Summary</option>
                                <option name="store_name" value="0">Detailed</option>
                            </select>
                        </div>
                    </div>
                    <!-- main table -->
                    
                    <div class="table-responsive" id="all_summary_stocks">
                        
                        <table id="all_summary" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th hidden>Pack Size</th>
                                    <th>Quantity</th>
                                    <th hidden>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $allStocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allstock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="name_<?php echo e($allstock->product_id); ?>">
                                            <?php echo e($allstock->name); ?>

                                            <?php echo e($allstock->brand ? ' ' . $allstock->brand : ''); ?>

                                            <?php echo e($allstock->pack_size ?? ''); ?><?php echo e($allstock->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="category_<?php echo e($allstock->product_id); ?>"><?php echo e($allstock->cat_name); ?></td>
                                        <td id="pack_size_<?php echo e($allstock->product_id); ?>" hidden><?php echo e($allstock->pack_size); ?></td>
                                        <td id="quantity_<?php echo e($allstock->product_id); ?>"><?php echo e(smartFormat($allstock->quantity)); ?></td>
                                        <td id="actions_<?php echo e($allstock->product_id); ?>" hidden>
                                            <?php if(auth()->user()->checkPermission('Manage Current Stock')): ?>
                                                <button type="button" class="btn btn-primary btn-rounded btn-sm"
                                                    onclick="editStock(<?php echo e($allstock->product_id); ?>)">
                                                    Edit
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>

                    </div>

                    
                    <div class="table-responsive" id="all_detailed_stock" style="display: none;">
                        
                        <table id="all_detailed" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Batch Number</th>
                                    <?php if($expireEnabled): ?>
                                        <th>Expiry Date</th>
                                    <?php endif; ?>
                                    <th>Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $allDetailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allDet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="d_name_<?php echo e($allDet->product_id); ?>">
                                            <?php echo e($allDet->name); ?>

                                            <?php echo e($allDet->brand ? ' ' . $allDet->brand : ''); ?>

                                            <?php echo e($allDet->pack_size ?? ''); ?><?php echo e($allDet->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="d_stock_value_<?php echo e($allDet->product_id); ?>">
                                            <?php echo e($allDet->cat_name); ?>

                                        </td>
                                        <td id="d_batch_<?php echo e($allDet->product_id); ?>"><?php echo e($allDet->batch_number ?? ''); ?></td>
                                        <?php if($expireEnabled): ?>
                                            <td id="d_expiry_<?php echo e($allDet->product_id); ?>"><?php echo e($allDet->expiry_date ?? ''); ?></td>
                                        <?php endif; ?>
                                        <td id="d_quantity_<?php echo e($allDet->product_id); ?>">
                                            <?php echo e(floor($allDet->quantity) == $allDet->quantity ? smartFormat($allDet->quantity) : smartFormat($allDet->quantity)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </div>

                    
                    <div class="table-responsive" id="summary" style="display: none;">
                        
                        <table id="current_stock" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th hidden>Pack Size</th>
                                    <th>Quantity</th>
                                    <th hidden>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="name_<?php echo e($stock->product_id); ?>">
                                            <?php echo e($stock->name); ?>

                                            <?php echo e($stock->brand ? ' ' . $stock->brand : ''); ?>

                                            <?php echo e($stock->pack_size ?? ''); ?><?php echo e($stock->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="category_<?php echo e($stock->product_id); ?>"><?php echo e($stock->cat_name); ?></td>
                                        <td id="pack_size_<?php echo e($stock->product_id); ?>" hidden><?php echo e($stock->pack_size); ?></td>
                                        <td id="quantity_<?php echo e($stock->product_id); ?>"><?php echo e(smartFormat($stock->quantity)); ?></td>
                                        <td id="actions_<?php echo e($stock->product_id); ?>" hidden>
                                            <?php if(auth()->user()->checkPermission('Manage Current Stock')): ?>
                                                <button type="button" class="btn btn-primary btn-rounded btn-sm"
                                                    onclick="editStock(<?php echo e($stock->product_id); ?>)">
                                                    Edit
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>

                    </div>

                    
                    <div class="table-responsive" id="detailed" style="display: none;">
                        
                        <table id="current_stock_detailed" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Batch Number</th>
                                    <?php if($expireEnabled): ?>
                                        <th>Expiry Date</th>
                                    <?php endif; ?>
                                    <th>Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $detailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="d_name_<?php echo e($data->product_id); ?>">
                                            <?php echo e($data->name); ?>

                                            <?php echo e($data->brand ? ' ' . $data->brand : ''); ?>

                                            <?php echo e($data->pack_size ?? ''); ?><?php echo e($data->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="d_stock_value_<?php echo e($data->product_id); ?>">
                                            <?php echo e($data->cat_name); ?>

                                        </td>
                                        <td id="d_batch_<?php echo e($data->product_id); ?>"><?php echo e($data->batch_number ?? ''); ?></td>
                                        <?php if($expireEnabled): ?>
                                            <td id="d_expiry_<?php echo e($data->product_id); ?>"><?php echo e($data->expiry_date ?? ''); ?></td>
                                        <?php endif; ?>
                                        <td id="d_quantity_<?php echo e($data->product_id); ?>">
                                            <?php echo e(floor($data->quantity) == $data->quantity ? smartFormat($data->quantity) : smartFormat($data->quantity)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </div>

                    
                    <div class="table-responsive" id="outstock" style="display: none;">
                        
                        <table id="current_stock_out" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $outstock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $out): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="o_name_<?php echo e($out->product_id); ?>">
                                            <?php echo e($out->name); ?>

                                            <?php echo e($out->brand ? ' ' . $out->brand : ''); ?>

                                            <?php echo e($out->pack_size ?? ''); ?><?php echo e($out->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="o_name_<?php echo e($out->product_id); ?>">
                                            <?php echo e($out->cat_name); ?>

                                        </td>

                                        <td id="o_quantity_<?php echo e($out->product_id); ?>">
                                            <?php echo e(floor($out->quantity) == $out->quantity ? smartFormat($out->quantity) : smartFormat($out->quantity)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </div>

                    <div class="table-responsive" id="outstock_detailed" style="display: none;">
                        
                        <table id="current_stock_out_detailed" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Batch Number</th>
                                    <?php if($expireEnabled): ?>
                                        <th>Expiry Date</th>
                                    <?php endif; ?>
                                    <th>Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $outDetailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $out): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td id="o_detal_name_<?php echo e($out->product_id); ?>">
                                            <?php echo e($out->name); ?>

                                            <?php echo e($out->brand ? ' ' . $out->brand : ''); ?>

                                            <?php echo e($out->pack_size ?? ''); ?><?php echo e($out->sales_uom ?? ''); ?>

                                        </td>
                                        <td id="o_name_<?php echo e($out->product_id); ?>">
                                            <?php echo e($out->cat_name); ?>

                                        </td>
                                        <td id="o_detal_batch_<?php echo e($out->product_id); ?>"><?php echo e($out->batch_number ?? ''); ?></td>
                                        <?php if($expireEnabled): ?>
                                            <td id="o_detal_expiry_<?php echo e($out->product_id); ?>"><?php echo e($out->expiry_date ?? ''); ?></td>
                                        <?php endif; ?>
                                        <td id="o_detal_quantity_<?php echo e($out->product_id); ?>">
                                            <?php echo e(floor($out->quantity) == $out->quantity ? smartFormat($out->quantity) : smartFormat($out->quantity)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </div>


                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        $(document).ready(function () {

            document.getElementById("detailed").style.display = "none";
            document.getElementById("outstock").style.display = "none";
            document.getElementById("outstock_detailed").style.display = "none";

            $('#all_summary').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#all_detailed').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_detailed').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_out').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_out_detailed').DataTable({
                responsive: false,
                order: [
                    [0, 'asc']
                ]
            });

            if (!$.fn.DataTable.isDataTable('#current_stock')) {
                $('#current_stock').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "<?php echo e(route('current-stocks-filter')); ?>",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": function (d) {
                            // Use dynamic data here
                            var es = document.getElementById("category_id");
                            var value_es = es.options[es.selectedIndex].value;
                            d._token = "<?php echo e(csrf_token()); ?>";
                            d.category = value_es;
                        },
                        success: function (response) {
                            console.log('Current Stock loading...', response);
                            for (var i = 0; i < response.length; i++) {
                                var data_returned = response[i];
                                $('#name_' + data_returned.id).text(data_returned.name);
                                $('#brand_' + data_returned.id).text(data_returned.brand);
                                $('#pack_size_' + data_returned.id).text(data_returned.pack_size);
                                $('#quantity_' + data_returned.id).text(data_returned.quantity);
                            }
                        },
                        error: function (error) {
                            console.error('Error fetching users:', error);
                        }
                    }
                });
            }

            $('#current-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#old-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#all-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });

        const $stockStatus = $('#stock_status_id');
        const $category = $('#category_id');

        function showStockView(status, type) {
            $('#all_summary, #all_detailed, #current_stock, #current_stock_detailed, #current_stock_out, #current_stock_out_detailed').hide();
            $('#all_summary_stocks, #all_detailed_stock, #summary, #detailed, #outstock, #outstock_detailed').hide();

            if (status === "all" && type == 1) {
                $('#all_summary_stocks').show();
                $('#all_summary').show();
            } else if (status === "all" && type == 0) {
                $('#all_detailed_stock').show();
                $('#all_detailed').show();
            } else if (status == 1 && type == 1) {
                $('#summary').show();
                $('#current_stock').show();
            } else if (status == 1 && type == 0) {
                $('#detailed').show();
                $('#current_stock_detailed').show();
            } else if (status == 0 && type == 1) {
                $('#outstock').show();
                $('#current_stock_out').show();
            } else if (status == 0 && type == 0) {
                $('#outstock_detailed').show();
                $('#current_stock_out_detailed').show();
            }
        }

        $(document).on('change', '#stock_status_id, #category_id', function () {
            showStockView($stockStatus.val(), $category.val());
        });

        showStockView($stockStatus.val(), $category.val());

        function formatNumber(num) {
            if (num === null || num === undefined || num === '') return '';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/stock_management/current_stock/current_stock.blade.php ENDPATH**/ ?>