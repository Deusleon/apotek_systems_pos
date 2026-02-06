<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Configuration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('configurations.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Name')); ?> <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name"
                                   aria-describedby="emailHelp"
                                   placeholder="Enter Name" maxlength="100" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Value')); ?> <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="display_name"
                                   aria-describedby="emailHelp"
                                   placeholder="Enter Value" maxlength="100" required>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id_edit">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/configurations/create.blade.php ENDPATH**/ ?>