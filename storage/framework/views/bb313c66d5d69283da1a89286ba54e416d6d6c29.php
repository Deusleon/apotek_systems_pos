<?php $__env->startSection('page_css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-title'); ?>
   Update Role

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> User Management / Roles / Edit Role </a> </li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection("content"); ?>

<div class="col-sm-12">

<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('roles.update')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo e($role->id); ?>">

        <div class="form-group row">
                <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Role:<font color="red">*</font> </label>
                <div class="col-md-8">
                        <input id="role" type="text" class="form-control" name="name" value="<?php echo e($role->name); ?>" required autofocus>

                </div>
        </div>
        <div class="form-group row">
            <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Description:<font color="red">*</font> </label>
            <div class="col-md-8">
                    <input id="description" type="text" class="form-control" name="description" value="<?php echo e($role->description); ?>" required>
            </div>
        </div>
        <div class="form-group row">
            <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Permissions:<font color="red">*</font> </label>
            <div class="col-md-10">
                    <div class="form-group row">
                            <div class="col-sm-2">
                                    <div class="checkbox checkbox-fill d-inline">
                                        <input type="checkbox" id="check_all">
                                        <label for="check_all" class="cr">Check All</label>
                                    </div>
                            </div>
                    </div>


                <div class="card">
                    
                    <?php $__currentLoopData = $permissionsAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <h5 class="card-header" style="background-color: #F4F7FA"><?php echo e($key ?? ''); ?></h5>
                        <div class="card-body">
                            <div class="form-group row">
                                <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-sm-4">
                                        <div class="checkbox checkbox-fill d-inline">
                                            <input type="checkbox" name="permissions[]" id="<?php echo e($permission->id ?? ''); ?>"
                                                   value="<?php echo e($permission->id ?? ''); ?>">
                                            <label for="<?php echo e($permission->id ?? ''); ?>" class="cr"> <?php echo e($permission->name ?? ''); ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>












                <hr>
                <div class="form-group row">
                        <div class="col-sm-8"></div>
                        <a href="<?php echo e(route('roles.index')); ?>">
                                <button type="button" class="btn btn-danger">Back</button>
                        </a>
                        <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </div>
        </div>

        </form>

    </div> 

</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
<?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>

$(document).ready(function() {

    $('#check_all').click(function() {
            var c = this.checked;
            $(':checkbox').prop('checked',c);
    });

    var permissions = <?php echo json_encode($permissionsAssigned, 15, 512) ?>;


    $('input[type=checkbox]').each(function () {
              var id = $(this).val();
              if (ValueExist(id,permissions)==1) {
                      $(this).attr('checked', true);
              }
    });

    function ValueExist(value,arr){
        var status = '0';

        for(var i=0; i<arr.length; i++){
                var name = arr[i];
                if(name == value){
                        status = '1';
                        break;
                }
        }
        return status;
    }

});

</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/roles/edit.blade.php ENDPATH**/ ?>