<script>
    $(document).ready(function () {

        function notify(message, from, align, type) {
            $.growl({
                message: message,
                url: ''
            }, {
                element: 'body',
                type: type,
                allow_dismiss: true,
                placement: {
                    from: from,
                    align: align
                },
                offset: {
                    x: 30,
                    y: 30
                },
                spacing: 10,
                z_index: 999999,
                delay: 2500,
                timer: 3000,
                url_target: '_blank',
                mouse_over: false,

                icon_type: 'class',
                template: '<div data-growl="container" class="alert" role="alert">' +
                    '<button type="button" class="close" data-growl="dismiss">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '<span class="sr-only">Close</span>' +
                    '</button>' +
                    '<span data-growl="icon"></span>' +
                    '<span data-growl="title"></span>' +
                    '<span data-growl="message"></span>' +
                    '<a href="#!" data-growl="url"></a>' +
                    '</div>'
            });
        }

        // Helper function to show notifications with proper styling
        function showNotification(message, type) {
            notify(message, 'top', 'right', type);
        }
        <?php if($flash = session("alert-success")): ?>
            notify('<?php echo e(session("alert-success")); ?>', 'top', 'right', 'success');
        <?php endif; ?>

        <?php if($flash = session("success")): ?>
            notify('<?php echo e(session("success")); ?>', 'top', 'right', 'success');
        <?php endif; ?>

        <?php if($flash = session("alert-danger")): ?>
            notify('<?php echo e(session("alert-danger")); ?>', 'top', 'right', 'danger');
        <?php endif; ?>

        <?php if($flash = session("danger")): ?>
            notify('<?php echo e(session("danger")); ?>', 'top', 'right', 'danger');
        <?php endif; ?>

        <?php if($flash = session("alert-warning")): ?>
            notify('<?php echo e(session("alert-warning")); ?>', 'top', 'right', 'warning');
        <?php endif; ?>

        <?php if($flash = session("warning")): ?>
            notify('<?php echo e(session("warning")); ?>', 'top', 'right', 'warning');
        <?php endif; ?>

        <?php if($flash = session("alert-info")): ?>
            notify('<?php echo e(session("alert-info")); ?>', 'top', 'right', 'info');
        <?php endif; ?>

        <?php if($flash = session("info")): ?>
            notify('<?php echo e(session("info")); ?>', 'top', 'right', 'info');
        <?php endif; ?>

        <?php if(isset($alert_success)): ?>
            notify($alert_success, 'top', 'right', 'success');
        <?php endif; ?>

            <?php if($errors->any()): ?>
                var delay = 5000;
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    notify('<?php echo e($error); ?>', 'top', 'right', 'danger');

                    delay = delay + 1000;
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>


});


</script><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/partials/notification.blade.php ENDPATH**/ ?>