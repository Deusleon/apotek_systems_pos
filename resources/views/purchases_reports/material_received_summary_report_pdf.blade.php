<!DOCTYPE html>
<html>
<head>
    <title>Material Received Summary Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th {
            border-collapse: collapse;
            padding: 8px;
        }

        table,
        td {
            border-collapse: collapse;
            padding: 5px;
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
            width: 100%;
            margin-top: 10px;
            margin-bottom: -10px;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Material Received Summary Report ({{ $branch_name }})</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d',strtotime($data['dates'][0]))}}</b> To:
                <b>{{date('Y-m-d',strtotime($data['dates'][1]))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="center" style="width: 5%;">#</th>
                    <th align="left" style="width: 30%;">Supplier</th>
                    <th align="center" style="width: 15%;">Total Quantity</th>
                    <th align="right" style="width: 15%;">Total Buy Cost</th>
                    <th align="right" style="width: 15%;">Total Sell Value</th>
                    <th align="right" style="width: 15%;">Total Profit</th>
                </tr>
                </thead>

                @foreach($data['data'] as $item)
                    <tr>
                        <td align="center">{{$loop->iteration}}.</td>
                        <td align="left">{{$item['supplier']}}</td>
                        <td align="center">{{number_format($item['total_quantity'])}}</td>
                        <td align="right">{{number_format($item['total_buy'], 2)}}</td>
                        <td align="right">{{number_format($item['total_sell'], 2)}}</td>
                        <td align="right">{{number_format($item['total_profit'], 2)}}</td>
                    </tr>
                @endforeach

                <!-- Grand Total Row -->
                <tr style="background: #e8e8e8; font-weight: bold;">
                    <td align="center"></td>
                    <td align="left"><strong>GRAND TOTAL</strong></td>
                    <td align="center"><strong>{{number_format($data['grand_total_quantity'])}}</strong></td>
                    <td align="right"><strong>{{number_format($data['grand_total_buy'], 2)}}</strong></td>
                    <td align="right"><strong>{{number_format($data['grand_total_sell'], 2)}}</strong></td>
                    <td align="right"><strong>{{number_format($data['grand_total_profit'], 2)}}</strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script type="text/php">
if (isset($pdf)) {

    $width = $pdf->get_width();
    $height = $pdf->get_height();

    // Center horizontally, 30px from bottom
    $x = $width / 2 - 50;
    $y = $height - 30;

    $text = "{PAGE_NUM} of {PAGE_COUNT} pages";

    // Use a safe non-bold font
    $font = $fontMetrics->get_font("helvetica", "normal");

    $size = 10;
    $color = array(0,0,0);

    $pdf->page_text($x, $y, $text, $font, $size, $color);
}
</script>

</body>
</html>
