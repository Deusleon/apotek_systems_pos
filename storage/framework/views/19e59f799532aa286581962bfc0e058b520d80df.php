<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="form_product">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="row" id="form-layout-container">
                                <!-- Left Column - Will be converted to full width in Normal mode -->
                                <div class="col-md-6" id="left-column">
                                    
                                    <div class="form-group row">
                                        <label for="name" class="col-md-4 col-form-label text-md-right">
                                            Name<span class="text-danger">*</span>
                                        </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="name_edit" name="name"
                                                maxlength="100" minlength="2" required value="<?php echo e(old('name')); ?>">
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row">
                                        <label for="barcode"
                                            class="col-md-4 col-form-label text-md-right">Barcode</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="barcode_edit" name="barcode"
                                                value="<?php echo e(old('barcode')); ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row product-detail-field" id="pack_size_field">
                                        <label for="pack_size"
                                            class="col-md-4 col-form-label text-md-right">Brand</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="brand_edits" name="brand"
                                                value="<?php echo e(old('brand')); ?>">
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row product-detail-field" id="pack_size_field">
                                        <label for="pack_size" class="col-md-4 col-form-label text-md-right">Pack
                                            Size</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="pack_size_edits"
                                                name="pack_size" value="<?php echo e(old('pack_size')); ?>"
                                                placeholder="e.g. 6, 12, 24"
                                                onkeypress="return isNumberKey(event,this)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Will be hidden in Normal mode -->
                                <div class="col-md-6" id="right-column">
                                    
                                    <div class="form-group row">
                                        <label for="category" class="col-md-4 col-form-label text-md-right">Category
                                            <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <select name="category" class="form-control" id="category_option" required
                                                onchange="createOption()">
                                                <option selected value="">Select Category</option>
                                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category->id); ?>" <?php echo e(old('category') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <span id="category_border"
                                                style="display: none; color: red; font-size: 0.9em">category
                                                required</span>
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row">
                                        <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="saleUoM_edit" name="sales_uom"
                                                placeholder="e.g. pcs, kg, ml" value="<?php echo e(old('sales_uom')); ?>">
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row">
                                        <label for="min_quantinty" class="col-md-4 col-form-label text-md-right">Min.
                                            Stock </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="min_stock_edits"
                                                name="min_quantinty" value="<?php echo e(old('min_quantinty')); ?>"
                                                onkeypress="return isNumberKey(event,this)">
                                        </div>
                                    </div>

                                    
                                    <div class="form-group row product-detail-field" id="max_stock_field">
                                        <label for="max_quantinty" class="col-md-4 col-form-label text-md-right">Max.
                                            Stock</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="max_stock_edits"
                                                name="max_quantinty" value="<?php echo e(old('max_quantinty')); ?>"
                                                onkeypress="return isNumberKey(event,this)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" id="id">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/masters/products/create.blade.php ENDPATH**/ ?>