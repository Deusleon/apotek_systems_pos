<div class="modal fade" id="create_normal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
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

                    <form id="form_product_normal">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="row" id="form-layout-container">
                                
                                <div class="col-12 d-flex mb-3">
                                    <label for="name" class="col-md-3 col-form-label text-md-right">
                                        Name<span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="name_edit_normal" name="name"
                                            maxlength="100" minlength="2" required value="<?php echo e(old('name')); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-12 d-flex mb-3">
                                    <label for="barcode" class="col-md-3 col-form-label text-md-right">Barcode</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="barcode_edit_normal" name="barcode"
                                            value="<?php echo e(old('barcode')); ?>" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="col-12 d-flex mb-3">
                                    <label for="category" class="col-md-3 col-form-label text-md-right">Category
                                        <span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <select name="category" class="form-control" id="category_option_normal"
                                            required onchange="createOptionNormal()">
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
                                
                                
                                
                                <div class="col-12 d-flex mb-3">
                                    <label for="min_quantinty" class="col-md-3 col-form-label text-md-right">Min.
                                        Stock </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="min_stock_edits_normal"
                                            name="min_quantinty" value="<?php echo e(old('min_quantinty')); ?>"
                                            onkeypress="return isNumberKey(event,this)">
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
</div><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/masters/products/create_normal.blade.php ENDPATH**/ ?>