<?php $__env->startSection('page_css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-title'); ?>
    Roles

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Security / Roles </a></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection("content"); ?>

    <div class="col-sm-12">

        <div class="card">


            <div class="card-body">
                <?php if(auth()->user()->checkPermission('Add Roles')): ?>
                    <a href="<?php echo e(route('roles.create')); ?>">
                        <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm">
                            Add Role
                        </button>
                    </a>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="fixed-header" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <?php if(auth()->user()->checkPermission('Edit Roles') || auth()->user()->checkPermission('Delete Roles')): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($role->name); ?></td>
                                    <td><?php echo e($role->description); ?></td>
                                    <?php if(auth()->user()->checkPermission('Edit Roles') || auth()->user()->checkPermission('Delete Roles')): ?>
                                        <td>
                                            <?php if(auth()->user()->checkPermission('Edit Roles')): ?>
                                                <a href="<?php echo e(route('roles.edit', $role->id)); ?>">
                                                    <button class="btn btn-primary btn-rounded btn-sm" type="button">Edit
                                                    </button>
                                                </a>
                                            <?php endif; ?>
                                            <?php if(auth()->user()->checkPermission('Delete Roles')): ?>
                                                <?php if($role->is_used === 'no'): ?>
                                                    <a href="#">
                                                        <button class="btn btn-danger btn-rounded btn-sm" data-id="<?php echo e($role->id); ?>"
                                                            data-name="<?php echo e($role->name); ?>" type="button" data-toggle="modal"
                                                            data-target="#deleteModal"> Delete
                                                        </button>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
    <?php echo $__env->make('roles.delete', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>

        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var message = "Are you sure you want to delete role '".concat(button.data('name'), "'?");
            var modal = $(this);
            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #role_id').val(id)
        })

    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/roles/index.blade.php ENDPATH**/ ?>