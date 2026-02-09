@php
    function smartFormat($num)
    {
        $str = (string) $num;

        if (strpos($str, '.') !== false) {

            list($whole, $decimal) = explode('.', $str);

            $decimal = rtrim($decimal, '0');

            if ($decimal === '') {
                return number_format((int) $whole);
            }

            $wholeFormatted = number_format((int) $whole);

            return $wholeFormatted . '.' . $decimal;

        } else {
            return number_format((int) $str);
        }
    }
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>Waste Collection Report</title>

    <style>
        body {
            font-size: 12px;
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
            width: 103%;
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 10px;
        }

        #table-detail tr:nth-child(even) {
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

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>

<body>
    <div class="row" style="padding-top: -2%">
        <!-- Header Section -->
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -2%;">
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
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Waste Collection Report</h3>
                <h4 align="center" style="margin-top: -1%">From: <b>{{$pharmacy['from_date']}}</b> To:
                    <b>{{$pharmacy['to_date']}}</b>
                </h4>
                <h4 align="center" style="margin-top: -2%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>
        <div class="row" style="">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center" style="width: 20px;">#</th>
                        <th align="left" style="width: 100px;">Receipt #</th>
                        <th align="left" style="width: 100px;">Date</th>
                        {{-- <th align="left" style="width: 120px;">Item Name</th> --}}
                        <th align="left" style="width: 200px;">Customer Name</th>
                        <th align="left" style="width: 200px">Collected By</th>
                        <th align="center">Weight (kg)</th>
                        <th align="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total_weight = 0; ?>
                    <?php $grand_total = 0; ?>
                    @foreach($data as $item)
                        <tr>
                            <td align="center">{{ $loop->iteration }}.</td>
                            <td align="left">{{ $item['receipt_number'] }}</td>
                            <td align="left">{{ $item['date'] }}</td>
                            {{-- <td align="left">{{ $item['item_name'] }}</td> --}}
                            <td align="left">{{ $item['customer_name'] }}</td>
                            <td align="left">{{ $item['collected_by'] }}</td>
                            <td align="center">{{ smartFormat($item['weight']) }}</td>
                            <td align="right">{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                        <?php $total_weight += $item['weight']; ?>
                        <?php $grand_total += $item['amount']; ?>
                    @endforeach
                </tbody>
            </table>
            <table style="width: 101%;">
                <tr>
                    <td colspan="7" align="right" style="padding-top: -3%; width: 85%;"><b>Total Weight (kg):</b></td>
                    <td align="right" style="padding-top: -3%;">
                        {{ smartFormat($total_weight) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="right" style="padding-top: -3%; width: 85%;"><b>Amount:</b></td>
                    <td align="right" style="padding-top: -3%">{{ number_format($grand_total, 2) }}</td>
                </tr>
            </table>
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
