<?php $__env->startSection('content-title'); ?>
    Delivery Notes
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Delivery Note / Delivery List</a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>

<style>
    #loading {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        display: none;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
    }

    #loading-image {
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 100;
    }
</style>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3 align-items-center">
                <label class="mr-2" for="">Date:</label>
                <input type="text" id="daterange" class="form-control w-auto">
            </div>
            <form id="delivery_note_reprint_form" action="<?php echo e(route('sale-reprint-receipt')); ?>" method="post"
                enctype="multipart/form-data" target="_blank">
                <?php echo csrf_field(); ?>

                <div class="table-responsive" id="sales">
                    <table id="sale_history_dataTable"
                        class="display table nowrap table-striped table-hover dataTable no-footer"
                        style="width:100%">

                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>

                    </table>

                </div>

                <!-- ajax loading gif -->
                <div id="loading">
                    <image id="loading-image" src="<?php echo e(asset('assets/images/spinner.gif')); ?>"></image>
                </div>

                <input type="hidden" value="" id="category">
                <input type="hidden" value="" id="customers">
                <input type="hidden" value="" id="print" name="reprint_receipt">
                <input type="hidden" value="" id="fixed_price">

            </form>
        </div>
    </div>    
</div>

<?php echo $__env->make('sales.delivery_notes.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
    
    <script>
        //Functionalities Below will be able to show Delivery Note Product List
        //Endpoints
        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                salesDetails: '<?php echo e(route('sale_detail')); ?>',
                getSalesHistory: '<?php echo e(route('getSalesHistory')); ?>',
                getSalesHistoryData: '<?php echo e(route('getSalesHistoryData')); ?>',
                receiptBaseUrl: "<?php echo e(route('sale-reprint-receipt-get', ['receipt' => ':receipt'])); ?>",
                deliveryNoteUrl: "<?php echo e(route('delivery-note-pdf', ':receipt')); ?>"
            }
        };
        var canPrintSalesHistory = <?php echo e(auth()->user()->checkPermission('Print Sales History') ? 'true' : 'false'); ?>;

    </script>

    <script src="<?php echo e(asset("assets/apotek/js/delivery_notes.js")); ?>"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>
    <script type="text/javascript">
        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#daterange').daterangepicker({
                endDate: moment().endOf("month"),
                maxDate: end,
                autoUpdateInput: true,
                alwaysShowCalendars: false,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

        });

    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\MY DOCUMENTS\PROJECTS\LARAVEL\APOTEk\Repo-project\apotek_systems_pos\resources\views/sales/delivery_notes/index.blade.php ENDPATH**/ ?>