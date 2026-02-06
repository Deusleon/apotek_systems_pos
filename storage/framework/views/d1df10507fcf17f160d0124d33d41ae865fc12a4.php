<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form action="<?php echo e(route('price-list.update', 'id')); ?>" method="post" id="editPriceForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field("PUT"); ?>

                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Product Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="name" id="name" required
                                        value="<?php echo e(old('product_name')); ?>" readonly>

                                    <span class="text-danger">
                                        <strong id="name-error1"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Buy Price</label>
                                <div class="col-md-8">
                                    <input type="hidden" class="form-control" name="unit_cost" id="unit_cost_edit"
                                        value="<?php echo e(old('unit_cost')); ?>" required autofocus>
                                    <input type="text" class="form-control" name="unit_cost_show" id="unit_cost_edit_to_show"
                                        value="<?php echo e(old('unit_cost')); ?>" required autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Sell Price</label>
                                <div class="col-md-8">
                                    <input type="hidden" class="form-control" name="sell_price" id="sell_price_edit"
                                        value="<?php echo e(old('sell_price')); ?>" required autofocus>
                                    <input type="text" class="form-control" name="sell_price_show" id="sell_price_edit_to_show"
                                        value="<?php echo e(old('sell_price')); ?>" required autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Price Category</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="price_category" id="price_category"
                                        value="<?php echo e(old('price_category')); ?>" readonly>
                                </div>
                            </div>
                        </div>
                </div>

                <input type="hidden" name="id" id="id">
                <input type="hidden" name="product_id" id="product_id">
                <input type="hidden" name="selected_type" id="selected_type">
                <input type="hidden" name="price_category_id" id="price_category_id">
                <input type="hidden" name="stock_id" id="stock_id">

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/stock_management/price_list/edit.blade.php ENDPATH**/ ?>