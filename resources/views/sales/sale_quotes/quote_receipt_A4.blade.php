@php
    function customRound($num)
    {
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
    <title>Proforma Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: -15px;
            margin-top: -25px;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        .receipt-header {
            text-align: right;
            margin-top: -5%;
        }

        .receipt-title {
            font-weight: bold;
            font-size: 15px;
            margin: 0;
        }

        /* Table styling */
        .customer-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .customer-table .index-col {
            width: auto;
            text-align: left;
            padding-left: 5px;
        }

        .customer-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 11px;
            height: 15px;
        }

        .items-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 15px;
            margin-left: -1px;
            margin-right: -1px;
            /* margin-bottom: 10px; */
        }

        .table-header {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .table-header th {
            padding: 4px 2px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            border-left: 1px solid #858484;
            font-size: 11px;
        }

        .items-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 11px;
            height: 15px;
            /* text-align: right; */
        }
        
        .summary-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 15px;
            margin-left: -1px;
            margin-right: -1px;
            margin-bottom: 10px;
        }
        
        .summary-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 11px;
            height: 15px;
        }

        /* Summary section */
        .summary-section {
            width: 40%;
            margin-left: auto;
            font-size: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 5px;
        }


        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            background-color: #f0f0f0;
        }

        .sold-by {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .slogan-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            font-style: italic;
            padding: 10px 0;
            background-color: white;
            border-top: 1px solid #ccc;
            z-index: 1000;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div style="width: 100%; text-align: center; align-items: center;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;" src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            <span>{{$pharmacy['email'] ? $pharmacy['email'].' |' : 'N/A'}}</span> 
            <span>{{$pharmacy['website'] ?? 'N/A'}}</span><br>
            <span>TIN: {{$pharmacy['tin_number'] ? $pharmacy['tin_number'].' |' : 'N/A'}}</span> 
            <span>VRN: {{$pharmacy['vrn_number'] ?? 'N/A'}}</span>
        </div>
    </div>
    <div style="font-weight: bold; margin-top: 5px; text-align: center;">
        PROFORMA INVOICE
    </div>

    @foreach($data as $datas => $dat)
        <table class="customer-table">
            <tbody>
                <tr style="width: 100%; position: relative;">
                    <td style="width: 22%; position: absolute; padding-left: 10px;">Proforma # : <span style="margin-left: 3px;">{{$datas ?? 'N/A'}}</span></td>
                    <td style="width: 54.5%; padding-left: 10px;">Phone : <span style="margin-left: 3px;">{{$dat[0]['customer_phone'] ?? 'N/A'}}</span></td>
                    <td style="width: 27.5%; padding-left: 10px;">TIN : <span style="margin-left: 3px;">{{!empty($dat[0]['customer_tin']) ? $dat[0]['customer_tin'] : 'N/A'}}</span></td>
                </tr>
                <tr style="width: 100%; position: relative;">
                    <td style="width: 22%; position: absolute; padding-left: 10px;">Proforma Date : <span style="margin-left: 3px;">{{date('Y-m-d', strtotime($dat[0]['created_at']))}}</span></td>
                    <td style="width: 54.5%; padding-left: 10px;">Address : <span style="margin-left: 3px;">{{ !empty($dat[0]['customer_address']) ? $dat[0]['customer_address'] : 'N/A' }}</span></td>
                    <td style="width: 27.5%; padding-left: 10px;">Ref # : <span style="margin-left: 3px;">{{$dat[0]['ref_no'] ?? 'N/A'}}</span></td>
                </tr>
                <tr style="width: 100%; position: relative;">
                    <td colspan="2" style="width: 42%; padding-left: 10px;">Bill To : <span style="margin-left: 3px;">{{$dat[0]['customer'] ?? 'CASH'}}</span></td>
                    <td style="width: 28%; padding-left: 10px;">Currency : <span style="margin-left: 3px;">TZS</span></td>
                </tr>
            </tbody>
        </table>
    @endforeach
    @php
        $subTotal = 0;
        $vat = 0;
        $discount = 0;
        $grandTotal = 0;
    @endphp

    <!-- Customer Information -->
    @foreach($data as $datas => $dat)
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr class="table-header" style="width: 100%; position: relative;">
                    <th style="width: 2%; position: absolute; text-align: center;">#</th>
                    <th style="width: 40%; position: absolute; text-align: left; padding-left: 7px;">Description</th>
                    <th style="width: 10%; position: absolute; text-align: center;">Qty</th>
                    <th style="width: 10%; position: absolute; text-align: right; padding-right: 3px;">Price</th>
                    <th style="width: 18%; position: absolute; text-align: right; padding-right: 3px;">VAT</th>
                    <th style="width: 18%; position: absolute; text-align: right; padding-right: 3px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dat as $item)
                    <tr>
                        <td style="width: 20px; text-align: center;">{{$loop->iteration}}.</td>
                        <td style="width: 355px; text-align: left; padding-left: 7px;">{{$item['name']}}
                            {{$item['brand'] ?? ''}}
                            {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}</td>
                        <td style="width: 49px; text-align: center;">{{smartFormat($item['quantity'])}}</td>
                        <td style="width: 70px; text-align: right; padding-right: 3px;">{{customRound($item['price'])}}</td>
                        <td style="width: 89px; text-align: right; padding-right: 3px;">{{customRound($item['vat'])}}</td>
                        <td style="width: 89px; text-align: right; padding-right: 3px;">{{customRound($item['price'] * $item['quantity'] + $item['vat'])}}
                        </td>
                    </tr>
                    @php
                        $subTotal += $item['sub_total'];
                        $vat += $item['vat'];
                        $discount += $item['discount'];
                    @endphp
                @endforeach
                    @php
                        $grandTotal = ($subTotal - $dat[0]['discount_total']) + $dat[0]['total_vat'];
                    @endphp

                @if(count($dat) < 5)
                    <!-- Empty rows for spacing -->
                    @for($i = 0; $i < 7 - count($dat); $i++)
                        <tr>
                            <td class="index-col"></td>
                            <td class="description-col">&nbsp;</td>
                            <td class="qty-col">&nbsp;</td>
                            <td class="price-col">&nbsp;</td>
                            <td class="vat-col">&nbsp;</td>
                            <td class="amount-col">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>
        <div style="display: flex; justify-content: space-between; width: 100%;">
            <div style="width: 70%; padding-top: 10px;">

                @if($generalSettings && $generalSettings->proforma_invoice_terms)
                    <div style="">
                        <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px;">Terms & Conditions:</div>
                        <div style="font-size: 11px; line-height: 1.4; text-align: justify;">
                            {!! nl2br(e($generalSettings->proforma_invoice_terms)) !!}
                        </div>
                    </div>
                @endif
            </div>
            <div class="summary-section" style="width: 26.7%; margin-top: 0px; padding-top: 0; float: right;">
                <table class="summary-table" style="margin-bottom: 60px;">
                    <tr>
                        <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">Sub Total : </td>
                        <td align="right" style="padding-right: 3px;">{{customRound($subTotal)}}</td>
                    </tr>
                    @if($dat[0]['discount_total'] > 0)
                        <tr>
                            <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">Discount : </td>
                            <td align="right" style="padding-right: 3px;">{{customRound($dat[0]['discount_total'])}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">VAT : </td>
                        <td align="right" style="padding-right: 3px;">{{customRound($dat[0]['total_vat'])}}</td>
                    </tr>
                    <tr>
                        <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">Total : </td>
                        <td align="right" style="padding-right: 3px;">{{customRound($grandTotal)}}</td>
                    </tr>
                    @if($page == -1)
                        <tr>
                            <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">Paid : </td>
                            <td align="right" style="padding-right: 3px;">{{customRound($dat[0]['paid'])}}</td>
                        </tr>
                        <tr>
                            <td align="right" style="width: 50%; font-weight: bold; padding-right: 5px;">Balance : </td>
                            <td align="right" style="padding-right: 3px;">{{customRound($grandTotal - $dat[0]['paid'])}}</td>
                        </tr>
                    @endif
                </table>
                {{-- <span style="">
                    Stamp: 
                </span> --}}
            </div>
        </div>
        @break
    @endforeach
    <div class="slogan-section">
        {{$pharmacy['slogan'] ?? 'Thank you for your business'}}
    </div>
</body>

</html>