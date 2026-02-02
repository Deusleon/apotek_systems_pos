@extends("layouts.master")

@section('content-title')
    Customers
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Customers</a></li>
@endsection

@section("content")

    <style type="text/css">
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
    </style>

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                @if(auth()->user()->checkPermission('View Customers'))
                    <div class="tab-content" id="myTabContent">
                        @if(auth()->user()->checkPermission('Add Customers'))
                            <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-sm btn-secondary"
                                data-toggle="modal" data-target="#create">
                                Add New Customer
                            </button>
                        @endif
                        <div class="table-responsive">
                            <table id="fixed-header" class="display table nowrap table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                         <th>TIN</th>
                                        <th>Total Credit</th>
                                        <th>Credit Limit</th>
                                        <!-- <th>Email</th> -->
                                        @if(auth()->user()->checkPermission('Edit Customers') || auth()->user()->checkPermission('Delete Customers'))
                                            <th>Action</th>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->phone}}</td>
                                            <td>{{$customer->tin}}</td>
                                            <td>{{number_format($customer->total_credit, 2)}}</td>
                                            <td>{{number_format($customer->credit_limit, 2)}}</td>
                                            {{--

                                            @if($customer->email)
                                            <td>{{$customer->email}}</td>
                                            @else
                                            <td>
                                                <div class="text text-danger">{{"Empty"}}</div>
                                            </td>
                                            @endif

                                            --}}
                                            @if(auth()->user()->checkPermission('Edit Customers') || auth()->user()->checkPermission('Delete Customers') || auth()->user()->checkPermission('View Customers'))
                                                <td>
                                                    @if(auth()->user()->checkPermission('View Customers'))
                                                        <button class="btn btn-sm btn-rounded btn-success" data-id="{{$customer->id}}"
                                                            data-name="{{$customer->name}}"
                                                            data-email="{{$customer->email}}"
                                            
                                                            data-phone="{{$customer->phone}}"
                                                            data-address="{{$customer->address}}"
                                                            data-tin="{{$customer->tin}}"
                                                            data-vat="{{$customer->vat}}"
                                                            data-credit_limit="{{$customer->credit_limit}}"
                                                            data-grace_period="{{$customer->grace_period}}"
                                                            data-total_credit="{{$customer->total_credit}}"
                                                            type="button" data-toggle="modal" data-target="#show">Show
                                                        </button>
                                                    @endif
                                                    @if(auth()->user()->checkPermission('Edit Customers'))
                                                        <a href="#">
                                                            <button class="btn btn-sm btn-rounded btn-primary" data-id="{{$customer->id}}"
                                                                data-name="{{$customer->name}}"
                                                                data-credit_limit="{{$customer->credit_limit}}"
                                                                data-address="{{$customer->address}}" data-phone="{{$customer->phone}}"
                                                                data-email="{{$customer->email}}" data-tin="{{$customer->tin}}"
                                                                type="button" data-toggle="modal" data-target="#edit">Edit
                                                            </button>
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->checkPermission('Delete Customers'))
                                                        @if($customer->active_user != "has transactions")
                                                            <a href="#">
                                                                <button class="btn btn-sm btn-rounded btn-danger" data-id="{{$customer->id}}"
                                                                    data-name="{{$customer->name}}" type="button" data-toggle="modal"
                                                                    data-target="#delete">
                                                                    Delete
                                                                </button>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                    @endforeach
                                    </tr>
                                </tbody>
                            </table>

                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">


                        </div>

                    </div>
                @endif
                @if(!auth()->user()->checkPermission('View Customers'))
                    <div class="alert alert-danger">You don't have permission to access this page</div>
                @endif
            </div>
        </div>

        @include('sales.customers.create2')
        @include('sales.customers.delete')
        @include('sales.customers.edit')
        
         <!-- Show Customer Modal -->
        <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="showModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showModalCenterTitle">Customer Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Name:</label>
                                    <span class="flex-grow-1" id="show_name"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Email:</label>
                                    <span class="flex-grow-1" id="show_email"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Phone:</label>
                                    <span class="flex-grow-1" id="show_phone"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Address:</label>
                                    <span class="flex-grow-1" id="show_address"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">TIN:</label>
                                    <span class="flex-grow-1" id="show_tin"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">VAT Reg:</label>
                                    <span class="flex-grow-1" id="show_vat"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Credit Limit:</label>
                                    <span class="flex-grow-1" id="show_credit_limit"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Grace Period:</label>
                                    <span class="flex-grow-1" id="show_grace_period"></span>
                                </div>
                                <div class="form-group d-flex align-items-start">
                                    <label class="mr-2 text-right" style="min-width: 90px;">Total Credit:</label>
                                    <span class="flex-grow-1" id="show_total_credit"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

@endsection


    @push("page_scripts")
        @include('partials.notification')
        <script src="{{asset("assets/apotek/js/customer.js")}}"></script>
        <script>
            $('#show').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var name = button.data('name');
                var email = button.data('email');
                var phone = button.data('phone');
                var address = button.data('address');
                var tin = button.data('tin');
                var vat = button.data('vat');
                var credit_limit = button.data('credit_limit');
                var grace_period = button.data('grace_period');
                var total_credit = button.data('total_credit');

                $('#show_name').text(name || 'N/A');
                $('#show_email').text(email || 'N/A');
                $('#show_phone').text(phone || 'N/A');
                $('#show_address').text(address || 'N/A');
                $('#show_tin').text(tin || 'N/A');
                $('#show_vat').text(vat || 'N/A');
                $('#show_credit_limit').text(credit_limit ? parseFloat(credit_limit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                $('#show_grace_period').text(grace_period ? grace_period + ' days' : 'N/A');
                $('#show_total_credit').text(total_credit ? parseFloat(total_credit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
            });
        </script>
    @endpush