@extends("layouts.master")
@section('content-title')
    Tax Invoice
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Tax Invoice / Payments</a></li>
@endsection

@section("content")

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
                            <a class="nav-link text-uppercase" id="credit-tracking-tablist"
                                href="{{ route('tax-invoice-tracking') }}" role="tab" aria-controls="credit_tracking"
                                aria-selected="false">Tracking
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->checkPermission('View Tax Invoice Payments'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase active" id="credit-payment-tablist" 
                                href="{{ url('sales/tax-invoice/payments') }}" role="tab" aria-controls="credit_payment" aria-selected="true">Payments
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    {{-- Start Tax Invoice Payment--}}
                        <div class="tab-pane fade show active" id="credit-payment" role="tabpanel" aria-labelledby="credit_payment-tab">
                            <div class="row d-flex justify-content-end mr-0">
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="d-flex align-items-center mr-4" style="width: 278px;">
                                        <label for="price_category" class="form-label mb-0"
                                            style="white-space: nowrap; margin-right: 8px;">Customer:</label>
                                        <select name="customer_id" id="customer_payment"
                                            class="js-example-basic-single form-control" onchange="filterPaymentHistory()">
                                            <option value="" selected="true" disabled style="display:none;">Select Customer
                                            </option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}" data-vat="{{ $customer->vat }}">
                                                    {{$customer->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-3 align-items-center">
                                    <label class="mr-2" for="">Date:</label>
                                    <input type="text" id="sales_date_payment" name="date_of_sale" autocomplete="off"
                                        class="form-control w-auto">
                                </div>
                            </div>
                            <div class="table-responsive" id="main_table">
                                <table id="fixed-header-main" class="display table nowrap table-striped table-hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Customer Name</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive" id="filter_history" style="display: none">
                                <table id="fixed-header-filter" class="display table nowrap table-striped table-hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Customer Name</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    {{-- End Tax Invoice Payment--}}
                </div>
            </div>
        </div>
    </div>

@endsection

@push("page_scripts")

    @include('partials.notification')

    <script src="{{asset("assets/plugins/moment/js/moment.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let payment_history_filter_table = $('#fixed-header-filter').DataTable({
            columns: [
                { 'data': 'receipt_number' },
                { 'data': 'name' },
                {
                    'data': 'created_at', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    'data': 'paid_amount', render: function (amount) {
                        return formatMoney(amount);
                    }
                }
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false,
            // aaSorting: [[1, "desc"]]
        });

        function filterPaymentHistory() {
            let customer_id = document.getElementById('customer_payment').value;
            let date = document.getElementById('sales_date_payment').value;

            if (customer_id === '') {
                customer_id = null;
            }

            if (date === '') {
                date = null;
            }

            /*make ajax call for more*/
            $.ajax({
                url: '{{route('payment-history-filter')}}',
                type: "get",
                dataType: "json",
                data: {
                    customer_id: customer_id,
                    date: date
                },
                success: function (data) {
                    console.log('This is data', data)
                    document.getElementById('main_table').style.display = 'none';
                    document.getElementById('filter_history').style.display = 'block';

                    data = data.filter(function (el) {
                        return Number(el.paid_amount) !== Number(0);
                    });

                    payment_history_filter_table.clear();
                    payment_history_filter_table.rows.add(data);
                    payment_history_filter_table.draw();


                }
            });


        }

        $(function () {
            $('#sales_date_payment').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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
            });

            $('input[name="date_of_sale"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
                filterPaymentHistory();
            });

            $('input[name="date_of_sale"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            // Load payment history on page load
            filterPaymentHistory();

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