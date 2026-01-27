@extends("layouts.master")
@section('content-title')
    Tax Invoice
@endsection

@php
    // Get the active tab from the session or default to "new"
    $activeTab = session('alert-success', '');
    $activeTabView = session('activeTabView', '');
@endphp

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Tax Invoice / Tracking</a></li>
@endsection

@section("content")

    <style>
        .iti__flag {
            background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags.png")}}");
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags@2x.png")}}");
            }
        }

        .iti {
            width: 100%;
        }

        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
        }

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

    <div class="col-sm-12 p-0">
        <div class="card-block">

            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    @if(auth()->user()->checkPermission('View Tax Invoice'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-sale-receiving-tablist"
                                href="{{ route('tax-invoice.creditSale') }}" role="tab" aria-controls="credit_sales" aria-selected="false">New
                                Invoice</a>
                        </li>
                    @endif

                    @if(auth()->user()->checkPermission('View Tax Invoice Tracking'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase active" id="credit-tracking-tablist"
                                href="{{ route('tax-invoice-tracking') }}" role="tab" aria-controls="credit_tracking"
                                aria-selected="true">Tracking
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->checkPermission('View Tax Invoice Payments'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-payment-tablist" 
                                href="{{ route('tax-invoice-payments') }}" role="tab" aria-controls="credit_payment" aria-selected="false">Payments
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    {{-- Tax Invoice Tracking--}}
                    @if(auth()->user()->checkPermission('View Tax Invoice Tracking'))
                        <div class="tab-pane fade show active" id="credit-tracking" role="tabpanel" aria-labelledby="credit_tracking-tab">
                            <div class="row ">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label id="cat_label">Customer<font color="red">*</font></label>
                                        <select name="customer_id" id="cust_id" class="js-example-basic-single form-control">
                                            <option value="" selected="true" disabled style="display:none;">Select Customer
                                            </option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="cat_label">Status:<font color="red">*</font></label>
                                        <select name="status" id="payment-status" class="js-example-basic-single form-control">
                                            <option value="" selected="true" disabled>Select Status</option>
                                            <option value="all">All</option>
                                            <option value="not_paid">Not Paid</option>
                                            <option value="partial_paid">Partial Paid</option>
                                            <option value="full_paid">Full Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="cat_label">Date <font color="red">*</font></label>
                                        <input type="text" name="date_of_sale" class="form-control" id="sales_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="track" value="1">
                            <input type="hidden" id="vat" value="">
                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">
                            <input type="hidden" value="" id="fixed_price">

                            <div class="row" id="detail">
                                <hr>
                                @if(auth()->user()->checkPermission('View Tax Invoice Paymentss'))
                                    <div id="can_pay"></div>
                                @endif
                                <div class="table teble responsive p-3" style="width: 100%;">
                                    <table id="credit_payment_table" class="display table nowrap table-striped table-hover"
                                        style="width:100%">

                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Customer</th>
                                                <th>Sales Date</th>
                                                <th>Total</th>
                                                <th>Paid</th>
                                                <th>Balance</th>
                                                @if(auth()->user()->checkPermission('Add Tax Invoice Payment'))
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                            @include('sales.credit_sales.create_payment')
                        </div>
                    @endif

                    @if(!auth()->user()->checkPermission('View Tax Invoice Tracking'))
                        <div class="tab-pane fade" id="credit-sale-receiving" role="tabpanel"
                            aria-labelledby="credit_sales-tab">
                            <div class="row">

                                {{-- <p>You do not have permission to View Tax Invoice Tracking</p> --}}

                            </div>
                        </div>
                    @endif
                    {{-- End Tax Invoice Tracking--}}

                </div>
            </div>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[name="payment-form"]');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                const btn = form.querySelector('#save_btn');
                // if already disabled, block double submit
                if (btn.dataset.saving === '1') {
                    e.preventDefault();
                    return;
                }
                // mark as saving and disable
                btn.dataset.saving = '1';
                btn.setAttribute('disabled', 'disabled');
                btn.setAttribute('aria-disabled', 'true');
                btn.originalText = btn.innerHTML;
                btn.innerHTML = 'Saving...';
            });
        });

    </script>


    @include('sales.customers.create')

@endsection

@push("page_scripts")

    {{-- For tax invoice --}}
    @include('partials.notification')


    <script type="text/javascript">



        var page_no = 1;//sales page
        var normal_search = 0;//search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                filterProductByWord: '{{route('filter-product-by-word')}}',
                getCreditSale: '{{route('getCreditSale')}}'
            }
        };
        var canAddCreditPayment = {{ auth()->user()->checkPermission('Add Tax Invoice Payment') ? 'true' : 'false' }};

    </script>
    <script src="{{asset("assets/plugins/moment/js/moment.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{asset("assets/apotek/js/customer.js")}}"></script>
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    {{-- For credit tracking --}}
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () { $('#credit_barcode_input').focus(); }, 150);
            var start = moment();
            var end = moment();


            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sales_date').daterangepicker({
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
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

            // Load credit sales data on page load
            setTimeout(function() {
                getCredits();
            }, 500);

        });

var credit_payment_table = $("#credit_payment_table").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [
        { data: "receipt_number" },
        { data: "name" },
        {
            data: "date",
            render: function (date) {
                return moment(date).format("YYYY-MM-DD");
            },
        },
        {
            data: "total_amount",
            render: function (total_amount) {
                return formatMoney(total_amount);
            },
        },
        {
            data: "paid_amount",
            render: function (paid_amount) {
                return formatMoney(paid_amount);
            },
        },
        {
            data: "balance",
            render: function (balance) {
                return formatMoney(balance);
            },
        },
        ...(typeof canAddCreditPayment !== "undefined" && canAddCreditPayment
            ? [
                  {
                      data: "action",
                      defaultContent:
                          "<button type='button' id='pay_btn' class='btn btn-sm btn-rounded btn-primary'>Pay</button>",
                  },
              ]
            : []),
    ],
    aaSorting: [[2, "desc"]],
});
        
function getCredits() {
    if ($("#track").length) {
        var status = document.getElementById("payment-status").value;
        var dates = document.querySelector("input[name=date_of_sale]").value;
        dates = dates.split("-");
    }
    var id = document.getElementById("cust_id").value || null;
    if (id || status || dates) {
        $.ajax({
            url: config.routes.getCreditSale,
            data: {
                _token: config.token,
                id: id,
                date: dates,
            },
            type: "get",
            dataType: "json",
            success: function (data) {
                // console.log('Data', data);
                //Remove Pay Button for Balance < 1
                data.forEach(function (data) {
                    if (data.balance < 1) {
                        data.action =
                            " <button class='btn btn-sm btn-rounded badge-success' disabled style=' color: #FFFFFF;'>Paid</button>";
                    }
                });
                if (status == "all") {
                    data = data;
                } else if (status == "full_paid") {
                    data = data.filter(function (el) {
                        return el.balance < 1;
                    });
                } else if (status == "not_paid") {
                    data = data.filter(function (el) {
                        return el.balance == el.total_amount;
                    });
                } else if (status == "partial_paid") {
                    data = data.filter(function (el) {
                        return el.paid_amount > 0 && el.balance > 0;
                    });
                } else {
                    data = data.filter(function (el) {
                        return el.balance > 0;
                    });
                }

                credit_payment_table.clear();
                credit_payment_table.rows.add(data);
                credit_payment_table.draw();
            },
            complete: function () {},
        });
    }
}

        $("#payment-status").change(function () {
            getCredits();
        });
        
        $("#sales_date").change(function () {
            getCredits();
        });

        $('#cust_id').on('change', function (e) {
            getCredits();
        });

        $("#credit_payment_table tbody").on("click", "#pay_btn", function () {
            var index = credit_payment_table.row($(this).parents("tr")).index();
            var data = credit_payment_table.row($(this).parents("tr")).data();
            $("#credit-sale-payment").modal("show");
            $("#credit-sale-payment").find(".modal-body #id_of_sale").val(data.sale_id);
            $("#credit-sale-payment")
                .find(".modal-body #customer-id")
                .val(data.customer_id);
            $("#credit-sale-payment").find(".modal-body #customer_name").val(data.name);
            $("#credit-sale-payment")
                .find(".modal-body #receipt-number")
                .val(data.receipt_number);
            $("#credit-sale-payment")
                .find(".modal-body #balance-amount")
                .val(data.balance);
            $("#credit-sale-payment")
                .find(".modal-body #outstanding")
                .val(formatMoney(data.balance));
            document.getElementById("save_btn").style.display = "block";
            $("#credit-sale-payment").on("change", "#rtn_qty", function () {
                var quantity = document.getElementById("rtn_qty").value;
                if (quantity > data[2]) {
                    document.getElementById("save_btn").disabled = "true";
                    document.getElementById("qty_error").style.display = "block";
                    $("#credit-sale-payment")
                        .find(".modal-body #qty_error")
                        .text("The maximum quantity is " + Math.floor(data[2]));
                } else {
                    document.getElementById("qty_error").style.display = "none";
                    $("#save_btn").prop("disabled", false);
                }
            });
        });


        $("#paying").on("change", function (evt) {
            if (evt.which != 110) {
                //not a fullstop
                var n = Math.abs(parseFloat($(this).val().replace(/\,/g, ""), 10) || 0);
                $(this).val(
                    n.toLocaleString("en", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    })
                );
            }
            var paid = document.getElementById("paying").value;
            paid_amount = parseFloat(paid.replace(/\,/g, ""), 10) || 0;
            $("#credit-sale-payment").find(".modal-body #paid-amount").val(paid_amount);
        });

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
            }
        }
    </script>

@endpush