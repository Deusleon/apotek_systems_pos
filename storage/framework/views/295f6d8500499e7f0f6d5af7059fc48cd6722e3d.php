<!DOCTYPE html>
<html lang="en">

<?php

use App\Store;
use App\Setting;
use Illuminate\Support\Facades\Auth;

$all_stores = Store::all();

$store_id = Auth::user()->store_id;

?>

<head>
    <title>APOTEk System</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Favicon icon -->
    
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset("APOTEk2.ico")); ?>">
    <!-- range slider -->
    <link rel="stylesheet" href="<?php echo e(asset("/assets/plugins/range-slider/css/bootstrap-slider.min.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("/assets/css/pages/rangeslider.css")); ?>">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/fonts/fontawesome/css/fontawesome-all.min.css")); ?>">
    <!-- animation css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/animation/css/animate.min.css")); ?>">
    <!-- notification css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/notification/css/notification.min.css")); ?>">
    <!-- data tables css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/data-tables/css/datatables.min.css")); ?>">
    <!-- vendor css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/css/style.css")); ?>">
    <!-- select2 css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/select2/css/select2.min.css")); ?>">
    <!-- multi-select css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/multi-select/css/multi-select.css")); ?>">
    <!-- tel-input css -->
    <link rel="stylesheet" href="<?php echo e(asset("assets/plugins/intl-tel-input/css/intlTelInput.css")); ?>">
    <!-- Datepicker css -->
    <link href="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/css/prettify.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/css/bootstrap-datepicker3.min.css")); ?>"
        rel="stylesheet">
    <link href="<?php echo e(asset("assets/plugins/daterangepicker-master/css/daterangepicker.css")); ?>" rel="stylesheet">

    <link href="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css")); ?>"
        rel="stylesheet">

    <?php echo $__env->yieldContent("page_css"); ?>


    <style>
        .select2-container .select2-selection--single {
            height: 43px !important;
            background-color: #f4f7fa !important;
            border: 1px solid #ced4da;
        }

        .select2-selection__rendered {
            line-height: 43px !important;
        }

        .select2-selection__arrow {
            height: 43px !important;
        }

        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 4px 10px;
            /* e.g. change 8x to 4px here */
        }

        a.dfn-hover {
            color: #333;
            text-decoration: none;
        }

        /** Code for hover info **/

        dfn {
            /*background: #e9e9e9;*/
            border-bottom: dashed 0px rgba(0, 0, 0, 0.8);
            padding: 0 0.4em;
            cursor: help;
            font-style: normal;
            position: relative;

        }

        dfn::after {
            content: attr(data-info);
            display: inline;
            position: absolute;
            top: 22px;
            left: 0;
            opacity: 0;
            width: 230px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5em;
            padding: 0.5em 0.8em;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            pointer-events: none;
            /* This prevents the box from apearing when hovered. */
            transition: opacity 250ms, top 250ms;
        }

        dfn::before {
            content: '';
            display: block;
            position: absolute;
            top: 12px;
            left: 20px;
            opacity: 0;
            width: 0;
            height: 0;
            border: solid transparent 5px;
            border-bottom-color: rgba(0, 0, 0, 0.8);
            transition: opacity 250ms, top 250ms;
        }

        dfn:hover {
            z-index: 2;
        }

        /* Keeps the info boxes on top of other elements */
        dfn:hover::after,
        dfn:hover::before {
            opacity: 1;
        }

        dfn:hover::after {
            top: 30px;
        }

        dfn:hover::before {
            top: 20px;
        }

        .badge-notify {
            text-decoration-color: #FFFFFF;
            background: red;
            border-radius: 50%;
            position: relative;
            top: -15px;
            left: 30px;
        }

        .alert-top-right {
            position: fixed;
            top: 20px;
            /* Adjust this value to move the alert up or down */
            right: 20px;
            /* Adjust this value to move the alert left or right */
            z-index: 9999;
            /* Ensures the alert is above other content */
            width: auto;
            max-width: 400px;
            /* Increased width for better readability */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            /* Optional: Adds a shadow for better visibility */
            border-radius: 8px;
            border: none;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes  slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .alert-top-right .close {
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .alert-top-right .close:hover {
            opacity: 1;
        }

        .alert-top-right i {
            margin-right: 8px;
        }
    </style>


</head>

<body>


    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar brand-red  active-red menu-item-icon-style4 icon-colored">
        <div class="navbar-wrapper">
            <div class="navbar-brand header-logo">
                <a href="<?php echo e(route('home')); ?>" class="b-brand">
                    
                        <img style="width: 2vw;" src="<?php echo e(asset('APOTEk2.ico')); ?>" alt="Apotek">
                        
                    <span class="b-title">APOTEk System</span>
                </a>
                <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            </div>
            <div class="navbar-content scroll-div">
                <ul class="nav pcoded-inner-navbar">


                    <?php echo $__env->make('layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </ul>
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    <header class="navbar pcoded-header navbar-expand-lg navbar-light">
        
        
        
        
        
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>
            <a href="index.html" class="b-brand">
                <div class="b-bg">
                    <i class="feather icon-trending-up"></i>
                </div>
                <span class="b-title">APOTEk</span>
            </a>
        </div>
        <a class="mobile-menu" id="mobile-header" href="#!">
            <i class="feather icon-more-horizontal"></i>
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li><a href="#!" class="full-screen" onclick="toggleFullScreen()"><i
                            class="feather icon-maximize"></i></a>
                </li>

            </ul>
            <ul class="navbar-nav ml-auto">
                <?php
                    $userStore = Auth::user()->store;
                    $canAccessAll = Auth::user()->store->name === "ALL";
                    $multiStore = Setting::where('id', 121)->value('value');
                    $multiStoreEnabled = $multiStore === 'YES';
                    $expiry = Setting::where('id', 123)->value('value');
                    $expiryEnabled = $expiry === 'YES';
                    $defaultStore = Setting::where('id', 122)->value('value');
                ?>

                <?php if($canAccessAll): ?>
                    <?php if($multiStoreEnabled): ?>
                        <li>
                            <div class="form-group" style="width: 200px!important; max-width: 100%; text-overflow: ellipsis;">
                                <form method="POST" action="<?php echo e(route('change_store')); ?>" id="storeForm">
                                    <?php echo csrf_field(); ?>
                                    <select name="store_id" id="store_id" class="js-example-basic-single form-control"
                                        onchange="this.form.submit()">
                                        <option value="" disabled <?php echo e(!session('current_store_id') ? 'selected' : ''); ?>>Select
                                            Branch</option>
                                        <?php $__currentLoopData = $all_stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($store->id); ?>" <?php echo e(session('current_store_id') == $store->id ? 'selected' : ''); ?>>
                                                <?php echo e($store->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </form>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li>
                    <div class="dropdown">
                        <?php if(auth()->user()->checkPermission('View Settings')): ?>
                            <dfn data-info="This is the default branch, where all activities will be based on."><a
                                    href="<?php echo e(route('configurations.index')); ?>">
                                    <?php if(session()->get('store') !== "Please Set Store"): ?>
                                    <span class="badge badge-info">Branch: <?php echo e(current_store()->name); ?></span> <?php else: ?>
                                    <span class="badge badge-danger">Please Set Default Branch</span> <?php endif; ?></a></dfn>
                        <?php else: ?>
                            <dfn data-info="This is the default branch, where all activities will be based on."><a href="#">
                                    <?php if(session()->get('store') !== "Please Set Store"): ?>
                                    <span class="badge badge-info">Branch: <?php echo e(current_store()->name); ?></span> <?php else: ?>
                                    <span class="badge badge-danger">Please Set Default Branch</span> <?php endif; ?></a></dfn>
                        <?php endif; ?>
                    </div>
                </li>
                <li>
                    <div class="dropdown">
                        <span class="badge text-white badge-pill badge-notify" id="span_counter"></span>
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i
                                class="icon feather icon-bell"></i></a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0">Notifications</h6>
                                <div class="float-right">
                                    
                                    
                                </div>
                            </div>
                            <ul class="noti-body" id="notification">
                                <li class="notification">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted p-3 text-sm">Loading...</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon feather icon-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                                
                                <span>
                                    <?php echo e(Auth::user()->name); ?>

                                </span>

                                <a href="<?php echo e(route('logout')); ?>" class="dud-logout" title="Logout" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="feather icon-log-out"></i>
                                </a>

                            </div>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                            <ul class="pro-body">
                                
                                
                                <li><a href="<?php echo e(route('showProfile')); ?>" class="dropdown-item"><i
                                            class="feather icon-user"></i> Profile</a></li>
                                <li><a href="<?php echo e(route('changePasswordForm')); ?>" class="dropdown-item"><i
                                            class="feather icon-x-circle"></i> Change Password</a></li>
                                <li><a href="<?php echo e(route('logout')); ?>" class="dropdown-item">
                                        <i class="feather icon-log-out"></i> Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>
    <!-- [ Header ] end -->
    <!-- [ chat user list ] start -->
    <section class="header-user-list">
        <div class="h-list-header">
            <div class="input-group">
                <input type="text" id="search-friends" class="form-control" placeholder="Search Friend . . .">
            </div>
        </div>
        <div class="h-list-body">
            <a href="#!" class="h-close-text"><i class="feather icon-chevrons-right"></i></a>
            <div class="main-friend-cont scroll-div">
                <div class="main-friend-list">

                </div>
            </div>
        </div>
    </section>
    <!-- [ chat user list ] end -->

    <!-- [ chat message ] start -->
    <section class="header-chat">
        <div class="h-list-header">
            <h6>Beatus K</h6>
            <a href="#!" class="h-back-user-list"><i class="feather icon-chevron-left"></i></a>
        </div>
        <div class="h-list-body">
            <div class="main-chat-cont scroll-div">

            </div>
        </div>

    </section>
    <!-- [ chat message ] end -->


    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container" style="margin-top: -4%">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- [ breadcrumb ] start -->
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10">
                                            <?php echo $__env->yieldContent("content-title"); ?>
                                        </h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <?php echo $__env->yieldContent("content-sub-title"); ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-wrapper">
                        <!-- [ Main Content ] start -->
                        <div class="row">
                            <!-- [ static-layout ] start -->

                            <!-- Session Messages -->
                            <?php if(session('success')): ?>
                                <div class="col-12">
                                    <div class="alert alert-success alert-dismissible fade show alert-top-right"
                                        role="alert">
                                        <i class="feather icon-check-circle"></i>
                                        <strong>Success!</strong> <?php echo e(session('success')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(session('error')): ?>
                                <div class="col-12">
                                    <div class="alert alert-danger alert-dismissible fade show alert-top-right"
                                        role="alert">
                                        <i class="feather icon-alert-circle"></i>
                                        <strong>Error!</strong> <?php echo e(session('error')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(session('warning')): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning alert-dismissible fade show alert-top-right"
                                        role="alert">
                                        <i class="feather icon-alert-triangle"></i>
                                        <strong>Warning!</strong> <?php echo e(session('warning')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(session('info')): ?>
                                <div class="col-12">
                                    <div class="alert alert-info alert-dismissible fade show alert-top-right" role="alert">
                                        <i class="feather icon-info"></i>
                                        <strong>Info!</strong> <?php echo e(session('info')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php echo $__env->yieldContent("content"); ?>
                            <!-- [ static-layout ] end -->
                        </div>
                        <!-- [ Main Content ] end -->
                    </div>
                    <!-- [ breadcrumb ] end -->

                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->


    <!-- Required Js -->
    <script src="<?php echo e(asset("assets/plugins/jquery/js/jquery.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/vendor-all.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/plugins/bootstrap/js/bootstrap.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pcoded.min.js")); ?>"></script>
    <!-- notification Js -->
    <script src="<?php echo e(asset("assets/plugins/notification/js/bootstrap-growl.min.js")); ?>"></script>

    <!-- datatable Js -->
    <script src="<?php echo e(asset("assets/plugins/data-tables/js/datatables.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pages/tbl-datatable-custom.js")); ?>"></script>

    <!-- select2 Js -->
    <script src="<?php echo e(asset("assets/plugins/select2/js/select2.full.min.js")); ?>"></script>

    <!-- multi-select Js -->
    <script src="<?php echo e(asset("assets/plugins/multi-select/js/jquery.quicksearch.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/plugins/multi-select/js/jquery.multi-select.js")); ?>"></script>

    <!-- form-select-custom Js -->
    <script src="<?php echo e(asset("assets/js/pages/form-select-custom.js")); ?>"></script>

    <!-- Input mask Js -->
    <script src="<?php echo e(asset("assets/plugins/inputmask/js/inputmask.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/plugins/inputmask/js/jquery.inputmask.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/plugins/inputmask/js/autoNumeric.js")); ?>"></script>
    <!-- tel-input js -->
    <script src="<?php echo e(asset("assets/plugins/intl-tel-input/js/intlTelInput.js")); ?>"></script>
    <!-- moment js -->
    <script src="<?php echo e(asset("assets/plugins/moment/js/moment.js")); ?>"></script>
    <!-- daterangepicker js -->
    <script src="<?php echo e(asset("assets/plugins/daterangepicker-master/js/daterangepicker.js")); ?>"></script>

    <script src="<?php echo e(asset('assets/plugins/intl-tel-input/js/intlTelInput.js')); ?>"></script>
    <script>
        var input = document.querySelector("#phone");
        if (input) {
            window.intlTelInput(input, {
                utilsScript: "<?php echo e(asset('assets/plugins/intl-tel-input/js/utils.js')); ?>", // <-- Hapa
            });
        }
    </script>

    

    
    <script src="<?php echo e(asset("assets/apotek/js/notification.js")); ?>"></script>
    <script>

        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                task: '<?php echo e(route('task')); ?>'
            }
        };

        // Fetch inventory notifications on page load
        $(document).ready(function() {
            loadInventoryNotifications();
        });

        function loadInventoryNotifications() {
            $.ajax({
                url: '<?php echo e(route("inventory.notifications")); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    updateNotificationBadge(data);
                    updateNotificationDropdown(data);
                },
                error: function() {
                    $('#notification').html('<li class="notification"><div class="media"><div class="media-body"><p class="text-muted p-3 text-sm">Failed to load notifications</p></div></div></li>');
                }
            });
        }

        function updateNotificationBadge(data) {
            if (data.notificationCount > 0) {
                $('#span_counter').text(data.notificationCount);
            } else {
                $('#span_counter').text('');
            }
        }

        function updateNotificationDropdown(data) {
            var html = '';
            var hasNotifications = false;

            if (data.outOfStockCount > 0) {
                hasNotifications = true;
                html += '<li class="notification" style="font-size: 14px; padding: 10px;"><span class="pl-1">Out of Stock:</span> <span class="text-c-red"><strong>' + data.outOfStockCount.toLocaleString() + '</strong></span></li>';
            }

            if (data.belowMinLevelCount > 0) {
                hasNotifications = true;
                html += '<li class="notification" style="font-size: 14px; padding: 10px;"><span class="pl-1">Below Min Level:</span> <span class="text-c-yellow"><strong>' + data.belowMinLevelCount.toLocaleString() + '</strong></span></li>';
            }

            if (data.expiryEnabled && data.expiredCount > 0) {
                hasNotifications = true;
                html += '<li class="notification" style="font-size: 14px; padding: 10px;"><span class="pl-1">Expired:</span> <span class="text-c-red"><strong>' + data.expiredCount.toLocaleString() + '</strong></span></li>';
            }

            if (data.expiryEnabled && data.expiringSoonCount > 0) {
                hasNotifications = true;
                html += '<li class="notification" style="font-size: 14px; padding: 10px;"><span class="pl-1">Expire in 3 Months:</span> <span class="text-c-yellow"><strong>' + data.expiringSoonCount.toLocaleString() + '</strong></span></li>';
            }

            if (!hasNotifications) {
                html = '<li class="notification" style="font-size: 15px;"><div class="media"><div class="media-body">No new notifications</div></div></li>';
            }

            $('#notification').html(html);
        }

        $('#mark_as_read').on('click', function () {
            $.ajax({
                url: '<?php echo e(route('task-read')); ?>',
                type: "get",
                dataType: "json",
                success: function (data) {
                    $('#notification').empty();
                    $("#span_counter").empty();
                },
                complete: function () {
                    $('#notification').empty();
                    $("#span_counter").empty();

                }
            });
        });

        var xs;

        $(document).ready(function () {
            try {
                xs = page_no;
            } catch (e) {
                console.log('error')
            }
        });

        $('.pcoded-main-container').on('click', function () {

            if (xs) {
                var xs_flag = 0;//default

                if (xs_flag === 0) {

                    try {
                        if (Number(normal_search) === Number(1)) {
                            xs_flag = 1;
                        }
                    } catch (e) {
                        console.log('normal search error')
                    }


                    if ($('#sale_paid').is(':focus')) {
                        xs_flag = 1;//disable
                    }

                    if ($('#sale_discount').is(':focus')) {
                        xs_flag = 1; //disable
                    }

                    if ($('#cash_sale_date').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#credit_sale_date').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#edit_quantity').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#edit_price').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#remark').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#name').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#email').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#phone-number').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#address').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#tin').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#credit_input').is(':focus')) {
                        xs_flag = 1;
                    }

                    if ($('#edit_expire_date').is(':focus')) {
                        xs_flag = 1;
                    }

                }

                if (xs_flag === 0) {
                    setTimeout(function () {
                        $('input[name="input_products_b"]').focus()
                    }, 30);
                }

                $('#cash_sale_date').on('change', function () {
                    setTimeout(function () {
                        $('input[name="input_products_b"]').focus()
                    }, 30);
                });

            }
        });




    </script>

    <?php echo $__env->yieldPushContent("page_scripts"); ?>

</body>

</html><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/layouts/master.blade.php ENDPATH**/ ?>