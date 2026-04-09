@php
function customRound($num) {
    $whole = floor($num);
    $decimal = $num - $whole;

    if ($decimal > 0.5) {
        return number_format($whole + 1, 2);
    } else {
        return number_format($whole, 2);
    }
}
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
    <title>Delivery Note</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 18px;
            margin: 0;
            padding: 12px;
            padding-right: 30px;
        }

        * {
            font-family: Arial, Helvetica, sans-serif
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding-top: 2px;
            padding-bottom: 2px;
            word-wrap: break-word;
        }

        #table-detail thead th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        #table-detail tbody tr td {
            border-bottom: 1px dotted #000;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        #table-detail tbody tr:last-child td {
            border-bottom: none;
        }

        hr {
            border: none;
            border-bottom: 1px solid #000;
            margin: 3px 0;
        }

        h3,
        h4,
        h5,
        h6 {
            margin: 2px 0;
            font-weight: normal;
            text-align: center;
        }

        /* Align numeric columns */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Reduce spacing for thermal */
        #footer-detail td {
            padding: 2px 0;
        }
    </style>
</head>

<body>
    <div style="width: 100%;">
        <h3><b>DELIVERY NOTE</b></h3>
        <h4>{{$pharmacy['name']}}</h4>
        <h5>{{$pharmacy['address']}}</h5>
        <h5>{{$pharmacy['phone']}}</h5>
        <h5>TIN: {{$pharmacy['tin_number'] ?? 'N/A'}}</h5>
        <h5>VRN: {{$pharmacy['vrn_number'] ?? 'N/A'}}</h5>

        @foreach($data as $datas => $dat)
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> {{$datas}}<br>
                        <span>Date:</span> {{date('Y-m-d', strtotime($dat[0]['created_at']))}}<br>
                        <span>Customer:</span> {{$dat[0]['customer'] ?? 'N/A'}}<br>
                        <span>TIN:</span> {{$dat[0]['customer_tin'] ?? 'N/A'}}<br>
                        <span>Printed On:</span> {{date('Y-m-d H:i:s')}}
                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr>
                        <th align="left" style="width: 60%;">Description</th>
                        <th class="text-center" style="width: 20%;">Qty</th>
                        <th class="text-center" style="width: 20%;">UOM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dat as $item)
                        <tr>
                            <td>{{$item['name']}}</td>
                            <td class="text-center">{{smartFormat($item['quantity'])}}</td>
                            <td class="text-center">{{$item['sales_uom'] ?? ''}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <h5>Issued By: {{$dat[0]['sold_by']}}</h5>
            <h5 style="font-style: italic;">{{$pharmacy['slogan'] ?? 'Thank you for your business'}}</h5>
        @endforeach
    </div>
</body>

</html>