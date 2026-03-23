<!DOCTYPE html>
<html>
<head>
    <title>Invoice Payment Report</title>

    <style>

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
            width: 40%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 45%;
        }

        .col-15 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
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
    <!-- Header Section -->
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Invoice Payment Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d',strtotime($data['from']))}}</b> To:
                <b>{{date('Y-m-d',strtotime($data['to']))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.2%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 0.3%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th style="width: 4%">#</th>
                    <th align="left" style="width: 12%;">Payment Date</th>
                    <th align="left" style="width: 12%;">Invoice No.</th>
                    <th align="left" style="width: 15%;">Supplier</th>
                    <th align="right" style="width: 12%;">Invoice Amount</th>
                    <th align="right" style="width: 12%;">Paid Amount</th>
                    <th align="right" style="width: 12%;">Remaining Balance</th>
                    <th align="left" style="width: 10%;">Payment Method</th>
                    <th align="left" style="width: 10%;">Recorded By</th>
                </tr>
                </thead>
                @foreach($data['payments'] as $payment)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td align="left">{{ $payment->created_at ? date('Y-m-d', strtotime($payment->created_at)) : 'N/A' }}</td>
                        <td align="left">{{ $payment->invoice->invoice_no ?? 'N/A' }}</td>
                        <td align="left">{{ $payment->invoice->supplier->name ?? 'N/A' }}</td>
                        <td align="right">{{ number_format($payment->invoice->invoice_amount ?? 0, 2) }}</td>
                        <td align="right">{{ number_format($payment->amount, 2) }}</td>
                        <td align="right">{{ number_format(($payment->invoice->invoice_amount ?? 0) - ($payment->invoice->paid_amount ?? 0), 2) }}</td>
                        <td align="left">{{ ucfirst($payment->payment_method) }}</td>
                        <td align="left">{{ $payment->user->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <hr>

        <div class="full-row" style="padding-top: 1%">
            <div class="col-35">
                <div class="full-row">
                </div>

            </div>
            <div class="col-15"></div>
            <div class="col-50">
                <div class="full-row">
                    <div class="col-50" align="right"><b>Total Paid: </b></div>
                    <div class="col-50" align="right">{{ number_format($data['total_paid'], 2) }}</div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 280;
        $y = 820;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>

</body>
</html>
