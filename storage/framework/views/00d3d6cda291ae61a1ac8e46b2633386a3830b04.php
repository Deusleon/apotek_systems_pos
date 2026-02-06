<?php $__env->startSection('content-title'); ?>
    Sales History
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales History</a></li>
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
        <div class="card-block">
            <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                <?php if(Auth::user()->checkPermission('View Sales History')): ?>
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="sales-history-tablist" data-toggle="pill"
                            href="<?php echo e(route('sale-histories.SalesHistory')); ?>" role="tab" aria-controls="sales_history"
                            aria-selected="true">Sales History</a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->checkPermission('View Sales Returns')): ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="sales-return-tablist" data-toggle="pill"
                            href="<?php echo e(route('sale-returns.index')); ?>" role="tab" aria-controls="sales_returns"
                            aria-selected="false">Returns
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->checkPermission('View Sales Returns Approvals')): ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="sales-approval-tablist" data-toggle="pill"
                            href="<?php echo e(route('sale-returns-approval.getSalesReturn')); ?>" role="tab" aria-controls="sales_returns"
                            aria-selected="false">Approvals
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="tab-content" id="myTabContent">

                
                <div class="tab-pane fade show active" id="sales-history" role="tabpanel"
                    aria-labelledby="sales_history-tab">
                    <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                    <div class="d-flex justify-content-end mb-3 align-items-center">
                        <label class="mr-2" for="">Date:</label>
                        <input type="text" id="daterange" class="form-control w-auto">
                    </div>
                    <input type="hidden" name="" value="<?php echo e($enable_discount); ?>" id="discount_enabled">
                    <form id="sale_receipt_reprint_form" action="<?php echo e(route('sale-reprint-receipt')); ?>" method="post"
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
                                        <th>Sub Total</th>
                                        <th>VAT</th>
                                        <?php if($enable_discount === 'YES'): ?>
                                        <th>Discount</th>
                                        <?php endif; ?>
                                        <th>Amount</th>
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
                

                
                <div class="tab-pane fade" id="sales-returns" role="tabpanel" aria-labelledby="sales_returns-tab">
                    <div class="col-sm-12">
                        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-uppercase" id="sales-return-tablist" data-toggle="pill"
                                    href="#sales-return" role="tab" aria-controls="sales_return"
                                    aria-selected="true">Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-uppercase" id="returns-approval-tablist" data-toggle="pill"
                                    href="#sales-return-approval" role="tab" aria-controls="sales_return_approval"
                                    aria-selected="false">Approval
                                </a>
                            </li>
                        </ul>
                        <div class="card-block">
                            <div class="tab-content" id="myTabContent">
                            </div>

                        </div>


                    </div>
                    <?php echo $__env->make('sales.sale_returns.return', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                


            </div>

        </div>


    </div>
    </div>
    <?php echo $__env->make('sales.sales_history.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>
    
    <script>
        //Functionalities Below will be able to show Sale Product List
        //Endpoints
        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                salesDetails: '<?php echo e(route('sale_detail')); ?>',
                getSalesHistory: '<?php echo e(route('getSalesHistory')); ?>',
                getSalesHistoryData: '<?php echo e(route('getSalesHistoryData')); ?>',
                receiptBaseUrl: "<?php echo e(route('sale-reprint-receipt-get', ['receipt' => ':receipt'])); ?>"
            }
        };
        var canPrintSalesHistory = <?php echo e(auth()->user()->checkPermission('Print Sales History') ? 'true' : 'false'); ?>;

    </script>

    <script src="<?php echo e(asset("assets/apotek/js/sales_history.js")); ?>"></script>
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
                // Display format tu
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#daterange').daterangepicker({
                // startDate: moment().startOf("month"),
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
                    // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

        });

    </script>

    
    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pages/ac-datepicker.js")); ?>"></script>


    
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sold_date').daterangepicker({
                startDate: moment().startOf("month"),
                endDate: moment().endOf("month"),
                maxDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);


        });

    </script>

    <script type="text/javascript">
        function getSales() {
            var range = document.getElementById('sold_date').value;
            range = range.split('-');

            $("#sale_list_return_table").dataTable().fnDestroy();

            $('#sale_list_return_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '<?php echo e(route('getSales')); ?>',
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "<?php echo e(csrf_token()); ?>",
                        range: range
                    }
                },
                "columns": [
                    { 'data': 'receipt_number' },
                    {
                        'data': 'date', render: function (date) {
                            return moment(date).format('D-M-YYYY');
                        }
                    },
                    {
                        'data': 'customer', render: function (customer) {
                            if (customer) {
                                return customer.name
                            }
                            return '';
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
                            }
                            return '';
                        }
                    },

                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount) * (cost.vat / cost.sub_total)));
                            }
                            return '';
                        }
                    },
                    {
                        'data': 'cost.discount', render: function (discount) {
                            return formatMoney(discount);
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount)));
                            }
                            return '';
                        }
                    },
                    {
                        'data': "action",
                        defaultContent: "<button type='button' id='open_btn' class='btn btn-sm btn-rounded btn-success'>Open</button>"
                    }
                ], aaSorting: [[1, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": [3, 4, 5, 6, 7] }
                ]

            });


        }

        $('#sale_list_return_table tbody').on('click', '#open_btn', function () {
            var row_data = $('#sale_list_return_table').DataTable().row($(this).parents('tr')).data();
            saleReturn(row_data.details);
        });

    </script>


    
    <script type="text/javascript">

        $(function () {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                // Display format
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#returned_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);


        });


        $('#return_table tbody').on('click', '#approve', function () {
            var product = return_table.row($(this).parents('tr')).data();
            getRetunedProducts('approve', product.item_returned)
        });

        $('#return_table tbody').on('click', '#reject', function () {
            var product = return_table.row($(this).parents('tr')).data();
            getRetunedProducts('reject', product.item_returned)
        });


        function getRetunedProducts(action, product) {
            var status = document.getElementById("retun_status").value;
            var range = document.getElementById("returned_date").value;
            var date = range.split('-');
            if (date) {
                $('#loading').show();
                $.ajax({
                    url: '<?php echo e(route('getRetunedProducts')); ?>',
                    data: {
                        "_token": '<?php echo e(csrf_token()); ?>',
                        "date": date,
                        "status": status,
                        "action": action,
                        "product": product
                    },
                    type: 'get',
                    dataType: 'json',
                    cache: false,
                    success: function (data) {
                        if (status == 3) {

                            return_table.column(6).visible(false);
                            data.forEach(function (data) {
                                if (data.status == 5) {
                                    data.item_returned.bought_qty += data.item_returned.rtn_qty;//This calculate the original bought qty
                                    data.item_returned.amount = (data.item_returned.amount / data.item_returned.rtn_qty) * data.item_returned.bought_qty;
                                }

                            });
                        } else if (status == 4) {
                            return_table.column(6).visible(false);
                        } else {
                            return_table.column(6).visible(true);
                        }
                        return_table.clear();
                        return_table.rows.add(data);
                        return_table.draw();

                    },
                    complete: function () {
                        $('#loading').hide();
                    }
                });
            }
        }

        var return_table = $('#return_table').DataTable({
            bPaginate: true,
            bInfo: true,
            // dom: 't',
            columns: [
                { data: 'item_returned.name' },
                {
                    data: 'item_returned.b_date', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'item_returned',
                    render: function (item_returned) {
                        return Math.floor(item_returned.bought_qty).toLocaleString();
                    }
                },
                {
                    data: 'date', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'item_returned',
                    render: function (item_returned) {
                        return Math.floor(item_returned.rtn_qty).toLocaleString();
                    }
                },
                {
                    data: 'item_returned', render: function (item_returned) {
                        return formatMoney((item_returned.rtn_qty / item_returned.bought_qty) * (item_returned.amount - item_returned.discount));
                    }
                },
                {
                    data: "action",
                    defaultContent: "<button type='button' id='approve' class='btn btn-sm btn-rounded btn-primary'>Approve</button><button type='button' id='reject' class='btn btn-sm btn-rounded btn-danger'>Reject</button>"
                }

            ], aaSorting: [[1, "desc"]]
        });

        $('#searching_returns').on('keyup', function () {
            return_table.search(this.value).draw();
        });

    </script>


    
    <script>
        $(document).ready(function () {
            // Listen for the click event on the Transfer History tab
            $('#sales-history-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-return-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-approval-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });


    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/sales/sales_history/index.blade.php ENDPATH**/ ?>