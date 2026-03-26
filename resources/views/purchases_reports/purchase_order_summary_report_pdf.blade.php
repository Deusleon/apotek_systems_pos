<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order Summary Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 8px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: -6%;
            border-collapse: collapse;
        }

        #table-detail tr > {
            line-height: 13px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 13px;
            width: 50%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 35%;
        }

        .col-15 {
            display: inline-block;
            font-size: 13px;
            width: 15%;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 160px;
            max-height: 160px;
        }

    </style>

</head>
<body>

<div class="row" style="padding-top: -2%">
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;"
                src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Purchase Order Summary Report ({{ $branch_name }})</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d',strtotime($data->first()->date_range[0]))}}</b> To:
                <b>{{date('Y-m-d',strtotime($data->first()->date_range[1]))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: -2%;">
        <div class="col-md-12">

            <table id="table-detail" align="center">
                
                <!-- COLUMN WIDTH DEFINITIONS -->
                <colgroup>
                    <col style="width: 5%;">   <!-- # -->
                    <col style="width: 10%;">  <!-- Date -->
                    <col style="width: 12%;">  <!-- Order # -->
                    <col style="width: 20%;">  <!-- Supplier -->
                    <col style="width: 12%;">  <!-- Total Products -->
                    <col style="width: 15%;">  <!-- Amount -->
                    <col style="width: 15%;">  <!-- Created By -->
                </colgroup>

                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th align="center">#</th>
                    <th align="left">Date</th>
                    <th align="left">Order #</th>
                    <th align="left">Supplier</th>
                    <th align="center">Total Products</th>
                    <th align="right">Amount</th>
                    <th align="center">Created By</th>
                </tr>
                </thead>

                @php $total_amount = 0; $index = 1; @endphp
                @foreach($data as $order)
                <tr>
                    <td align="center">{{ $index++ }}</td>
                    <td align="left">{{date('Y-m-d', strtotime($order->ordered_at))}}</td>
                    <td align="left">{{$order->order_number}}</td>
                    <td align="left">{{$order->supplier->name ?? ''}}</td>
                    <td align="center">{{$order->total_products}}</td>
                    <td align="right">{{number_format($order->total_amount, 2)}}</td>
                    <td align="center">{{$order->user->name ?? ''}}</td>
                </tr>
                @php $total_amount += $order->total_amount; @endphp
                @endforeach

                <!-- Total Row -->
                <tr style="font-weight: bold;">
                    <td colspan="6" align="right" style="border-top: 2px solid #000; padding-top: 10px;"><b>Total Amount:</b></td>
                    <td align="right" style="border-top: 2px solid #000; padding-top: 10px;">{{number_format($total_amount, 2)}}</td>
                </tr>
            </table>


        </div>
    </div>
</div>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 400;
        $y = 560;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
     }
</script>

</body>
</html>
