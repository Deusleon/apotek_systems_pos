<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
          <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('products.destroy','id')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field("DELETE"); ?>

                    <div class="modal-body">
                        <div id="message"></div>

                        <input type="hidden" name="product_id" id="product_id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                    </div>
                </form>

          </div>
        </div>
 </div>
<?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/masters/products/delete.blade.php ENDPATH**/ ?>