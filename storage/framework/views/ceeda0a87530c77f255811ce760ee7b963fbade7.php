<div class="modal fade" id="adjustStockModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <form id="adjustStockForm">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="stock_id" id="stock_id">
      <input type="hidden" name="product_id" id="product_id">
      <input type="hidden" name="current_stock" id="current_stock_input">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Adjust Stock</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row pl-3 mb-2">
              <strong class="">Product Name: </strong>
              <p id="show_product_name" class="text-body ml-1"></p>
            </div>
            <div class="row pl-3">
              <strong class="">Current Stock: </strong>
              <p id="show_current_stock" class="text-body ml-1"></p>
            </div>
            <hr>
          <div class="col pl-3">
            <div class="row">
                <label>New Quantity</label>
                <input type="hidden" class="form-control" name="new_quantity" id="new_quantity" required>
                <input type="text" class="form-control" name="new_qty" id="new_qty_to_show" required>
                <input type="hidden" name="from_type" id="from_type">
            </div>
            <div class="row mt-3">
                <label>Reason <font class="text-danger">*</font></label>
                <select name="reason" id="reason" class="form-control select2 <?php if ($errors->has('reason')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('reason'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>"
                  required>
                  <option value="">Select Reason</option>
                  <?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($reason->reason); ?>" <?php echo e(old('reason')==$reason->reason ? 'selected' : ''); ?>>
                    <?php echo e($reason->reason); ?>

                  </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if ($errors->has('reason')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('reason'); ?>
                  <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($message); ?></strong>
                  </span>
                <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
              </div>
            </div>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmAdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="confirmAdjustmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmAdjustmentModalLabel">Confirm Adjustment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to adjust stock for <span id="confirmAdjustmentProductName"></span>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="confirmAdjustmentBtn" class="btn btn-primary btn-sm">Yes</button>
      </div>
    </div>
  </div>
</div><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/stock_management/adjustments/adjust_stock_modal.blade.php ENDPATH**/ ?>