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
        .small-table table td,
        .small-table table th {
            padding: 0.35rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-title'); ?>
    Stock Adjustment
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Adjustment</a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="current-stock-tablist"
                        href="<?php echo e(route('new-stock-adjustment')); ?>" aria-controls="current-stock" aria-selected="true">Stock
                        Adjustment</a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Adjustment')): ?>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="all-stock-tablist" href="<?php echo e(route('stock-adjustments-history')); ?>"
                        aria-controls="stock_list" aria-selected="false">Adjustment History
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

                    <div class="d-flex p-0" style="width: 245px; margin-right: -1px;">
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($allstock->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($allstock->id); ?>"
                                                                data-product-id="<?php echo e($allstock->product_id); ?>" data-product-name="<?php echo e($allstock->name
                                        . (!empty($allstock->brand) ? ' ' . $allstock->brand : '')
                                        . (!empty($allstock->pack_size) ? ' ' . $allstock->pack_size : '')
                                        . (!empty($allstock->sales_uom) ? $allstock->sales_uom : '')); ?>"
                                                                data-from-type="summary" data-current-stock="<?php echo e($allstock->quantity); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
                                        <?php echo e(smartFormat($allDet->quantity)); ?>

                                    </td>
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($allDet->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($allDet->id); ?>"
                                                                data-product-id="<?php echo e($allDet->product_id); ?>" data-product-name="<?php echo e($allDet->name
                                        . (!empty($allDet->brand) ? ' ' . $allDet->brand : '')
                                        . (!empty($allDet->pack_size) ? ' ' . $allDet->pack_size : '')
                                        . (!empty($allDet->sales_uom) ? $allDet->sales_uom : '')); ?>"
                                                                data-from-type="detailed" data-current-stock="<?php echo e(smartFormat($allDet->quantity)); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($stock->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($stock->id); ?>"
                                                                data-product-id="<?php echo e($stock->product_id); ?>" data-product-name="<?php echo e($stock->name
                                        . (!empty($stock->brand) ? ' ' . $stock->brand : '')
                                        . (!empty($stock->pack_size) ? ' ' . $stock->pack_size : '')
                                        . (!empty($stock->sales_uom) ? $stock->sales_uom : '')); ?>"
                                                                data-from-type="summary" data-current-stock="<?php echo e(smartFormat($stock->quantity)); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
                                        <?php echo e(smartFormat($data->quantity)); ?>

                                    </td>
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($data->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($data->id); ?>"
                                                                data-product-id="<?php echo e($data->product_id); ?>" data-product-name="<?php echo e($data->name
                                        . (!empty($data->brand) ? ' ' . $data->brand : '')
                                        . (!empty($data->pack_size) ? ' ' . $data->pack_size : '')
                                        . (!empty($data->sales_uom) ? $data->sales_uom : '')); ?>"
                                                                data-from-type="detailed" data-current-stock="<?php echo e(smartFormat($data->quantity)); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
                                        <?php echo e(smartFormat($out->quantity)); ?>

                                    </td>
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($out->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($out->id); ?>"
                                                                data-product-id="<?php echo e($out->product_id); ?>" data-product-name="<?php echo e($out->name
                                        . (!empty($out->brand) ? ' ' . $out->brand : '')
                                        . (!empty($out->pack_size) ? ' ' . $out->pack_size : '')
                                        . (!empty($out->sales_uom) ? $out->sales_uom : '')); ?>" data-from-type="summary"
                                                                data-current-stock="<?php echo e(smartFormat($out->quantity)); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
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
                                <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__currentLoopData = $outDetailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outDet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td id="o_detal_name_<?php echo e($outDet->product_id); ?>">
                                        <?php echo e($outDet->name); ?>

                                        <?php echo e($outDet->brand ? ' ' . $outDet->brand : ''); ?>

                                        <?php echo e($outDet->pack_size ?? ''); ?><?php echo e($outDet->sales_uom ?? ''); ?>

                                    </td>
                                    <td id="o_name_<?php echo e($outDet->product_id); ?>">
                                        <?php echo e($outDet->cat_name); ?>

                                    </td>
                                    <td id="o_detal_batch_<?php echo e($outDet->product_id); ?>"><?php echo e($outDet->batch_number ?? ''); ?></td>
                                    <?php if($expireEnabled): ?>
                                        <td id="o_detal_expiry_<?php echo e($outDet->product_id); ?>"><?php echo e($outDet->expiry_date ?? ''); ?></td>
                                    <?php endif; ?>
                                    <td id="o_detal_quantity_<?php echo e($outDet->product_id); ?>">
                                        <?php echo e(smartFormat($outDet->quantity)); ?>

                                    </td>
                                    <?php if(auth()->user()->checkPermission('Create Stock Adjustment')): ?>
                                                        <td id="actions_<?php echo e($outDet->product_id); ?>">
                                                            <!-- Adjustment Button -->
                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-adjust-stock"
                                                                data-toggle="modal" data-target="#adjustStockModal" data-id="<?php echo e($outDet->id); ?>"
                                                                data-product-id="<?php echo e($outDet->product_id); ?>" data-product-name="<?php echo e($outDet->name
                                        . (!empty($outDet->brand) ? ' ' . $outDet->brand : '')
                                        . (!empty($outDet->pack_size) ? ' ' . $outDet->pack_size : '')
                                        . (!empty($outDet->sales_uom) ? $outDet->sales_uom : '')); ?>"
                                                                data-from-type="detailed" data-current-stock="<?php echo e($outDet->quantity); ?>">
                                                                Adjust
                                                            </button>
                                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>

                    </table>
                </div>


            </div>
        </div>
    </div>
    </div>

    <?php echo $__env->make('stock_management.adjustments.adjust_stock_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
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
                            // console.log('Current Stock loading...', response);
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

        $(document).on('click', '.btn-adjust-stock', function () {
            const $btn = $(this);
            const product_name = $btn.data('product-name');
            const current_stock = $btn.data('current-stock');
            const id = $btn.data('id');
            const product_id = $btn.data('product-id');
            const from_type = $btn.data('from-type');
            let stock = Number(current_stock);
            let displayStock = (stock % 1 === 0) ? stock : stock;
            $('#show_product_name').text(product_name);
            $('#show_current_stock').text(smartFormat(displayStock));
            $('#confirmAdjustmentProductName').text(product_name);
            $('#product_id').val(product_id);
            $('#stock_id').val(id);
            $('#from_type').val(from_type);
            $('#current_stock_input').val(current_stock);

            $('#adjustStockModal').modal('show');
        });

        $(document).on('submit', '#adjustStockForm', function (e) {
            e.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            $('#confirmAdjustmentBtn').data('formData', formData);
            // console.log('Form Data:', formData);
            $('#adjustStockModal').off('hidden.bs.modal').one('hidden.bs.modal', function () {
                $('#confirmAdjustmentModal').modal('show');
            });

            $('#adjustStockModal').modal('hide');
        });

        $(document).on('click', '#confirmAdjustmentBtn', function () {
            const formData = $(this).data('formData');
            $.ajax({
                url: "<?php echo e(route('stock-adjustments.store')); ?>",
                method: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        // console.log('Success:', response);
                        notify("Stock adjusted successfully.", "top", "right", "success");
                        $('#confirmAdjustmentModal').modal('hide');
                        location.reload();
                    } else {
                        console.error('Error:', response);
                        notify('Error: ' + response.message, "top", "right", "danger");
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            errors[field].forEach(msg => {
                                notify(msg, "top", "right", "danger");
                            });
                        }
                    } else {
                        alert('An unexpected error occurred.');
                    }
                }
            });
        });

        function adjustStock(productId) {
            let qty = $("#quantity_" + productId).text();
            $("#stock_id").val(productId);
            $("#is_detailed").val(0);
            $("#current_stock_input").val(qty);
            $("#adjustStockModal").modal("show");
        }

        function adjustStockDetailed(stockId) {
            let qty = $("#d_quantity_" + stockId).text();
            $("#stock_id").val(stockId);
            $("#is_detailed").val(1);
            $("#current_qty").val(qty);
            $("#adjustStockModal").modal("show");
        }

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

        $(document).ready(function () {
            var savedStatus = localStorage.getItem('stock_status_id');
            var savedCategory = localStorage.getItem('category_id');

            if (savedStatus !== null) {
                $('#stock_status_id').val(savedStatus);
            }
            if (savedCategory !== null) {
                $('#category_id').val(savedCategory);
            }

            // Trigger change once to load the table using saved values
            $('#stock_status_id, #category_id').trigger('change');
        });

        $(document).on('change', '#stock_status_id, #category_id', function () {
            localStorage.setItem('stock_status_id', $('#stock_status_id').val());
            localStorage.setItem('category_id', $('#category_id').val());
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

        $('#new_qty_to_show').on('input', function () {
            let value = this.value;

            // Remove any non-numeric characters except decimal point
            value = value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (parts.length === 2 && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }

            this.value = value;

            // Update hidden field
            if (value !== '') {
                document.getElementById('new_quantity').value = parseFloat(value.replace(/\,/g, ''));
            } else {
                document.getElementById('new_quantity').value = '';
            }
        });

        $('#new_qty_to_show').on('blur', function () {
            var newValue = this.value;
            if (newValue !== '' && !isNaN(newValue)) {
                // Format with commas on blur
                this.value = numberWithCommas(parseFloat(newValue));
                document.getElementById('new_quantity').value = parseFloat(newValue);
            }
        });

        $('#new_qty_to_show').on('focus', function () {
            // Remove commas on focus for easier editing
            var value = this.value.replace(/\,/g, '');
            if (value !== '' && !isNaN(value)) {
                this.value = value;
            }
        });
        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function smartFormat(num) {
            let str = String(num);

            if (str.includes('.')) {
                let [whole, decimal] = str.split('.');

                decimal = decimal.replace(/0+$/, "");

                if (decimal === "") {
                    return Number(whole).toLocaleString();
                }

                let wholeFormatted = Number(whole).toLocaleString();

                return wholeFormatted + "." + decimal;

            } else {
                return Number(str).toLocaleString();
            }
        }

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/stock_management/adjustments/new_adjustment.blade.php ENDPATH**/ ?>