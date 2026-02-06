<!DOCTYPE html>
<html lang="en">

<head>
    <title>APOTEk System</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="author" content="CodedThemes"/>

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset("APOTEk2.ico")); ?>">

    <!-- fontawesome icon -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/fonts/fontawesome/css/fontawesome-all.min.css")); ?>">

    <!-- animation css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/animation/css/animate.min.css")); ?>">
    <!-- vendor css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/css/style.css")); ?>">


</head>

<body>
<div class="auth-wrapper">
    <div class="auth-content">

        <div class="card">
            <div class="card-body text-center">

                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <i class="feather icon-unlock auth-icon"></i>
                    </div>

                    <h3 class="mb-4">Login</h3>
                    <div class="input-group mb-4">
                        <input id="email" type="email" class="form-control <?php if ($errors->has('email')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('email'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>"
                               name="email" value="<?php echo e(old('email')); ?>" required placeholder="Email" autofocus>

                        <?php if ($errors->has('email')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('email'); ?>
                        <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                        <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
                    </div>
                    <div class="input-group mb-4">
                        <input id="password" type="password"
                               class="form-control <?php if ($errors->has('password')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('password'); ?> is-invalid <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>" name="password" required
                               autocomplete="current-password" placeholder="Password">

                        <?php if ($errors->has('password')) :
if (isset($message)) { $messageCache = $message; }
$message = $errors->first('password'); ?>
                        <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                        <?php unset($message);
if (isset($messageCache)) { $message = $messageCache; }
endif; ?>
                    </div>
                    <div class="input-group mb-4">
                        <input id="facility" type="text"
                               class="form-control" name="facility" required placeholder="Business Name" value="demo" hidden>
                    </div>
                    <div class="form-group text-left">
                        <div class="checkbox checkbox-fill d-inline">
                            <input class="form-check-input" type="checkbox" name="remember"
                                   id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>

                            <label class="cr" for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary shadow-2 mb-4">
                        <?php echo e(__('Login')); ?>

                    </button>

                    <?php if(Route::has('password.request')): ?>

                        
                        <p class="mb-2 text-muted">Forgot password? <a href="#">Reset</a>
                        </p>

                    <?php endif; ?>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- Required Js -->





</body>
</html>
<?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/auth/login.blade.php ENDPATH**/ ?>