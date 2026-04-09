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
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Delivery Note</title>
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
            width: 10%;
        }

        .customer-table .name-col {
            width: 60%;
        }

        .customer-table .value-col {
            width: 30%;
        }

        .customer-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .customer-table .label {
            font-weight: bold;
            padding-right: 10px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .products-table th {
            background-color: #f0f0f0;
        }

        .products-table .quantity-col {
            width: 15%;
            text-align: center;
        }

        .products-table .name-col {
            width: 50%;
        }

        .products-table .uom-col {
            width: 20%;
            text-align: center;
        }

        .products-table .amount-col {
            width: 15%;
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .total-value {
            display: inline-block;
            width: 100px;
            text-align: right;
            font-weight: bold;
        }

        .footer-section {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }

        .signature-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .logo {
            max-width: 100px;
            max-height: 50px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .company-address {
            font-size: 12px;
            margin: 5px 0;
        }

        .company-contact {
            font-size: 12px;
            margin: 2px 0;
        }
    </style>
</head>

<body>
    @foreach ($data as $receiptNumber => $items)
        @php
            $firstItem = $items[0] ?? null;
        @endphp

        <div class="company-info">
            @if ($pharmacy['logo'])
                <img src="{{ public_path('images/' . $pharmacy['logo']) }}" alt="Logo" class="logo">
            @endif
            <h1 class="company-name">{{ $pharmacy['name'] }}</h1>
            <p class="company-address">{{ $pharmacy['address'] }}</p>
            <p class="company-contact">Phone: {{ $pharmacy['phone'] }} | Email: {{ $pharmacy['email'] }}</p>
            @if ($pharmacy['tin_number'])
                <p class="company-contact">TIN: {{ $pharmacy['tin_number'] }}</p>
            @endif
            @if ($pharmacy['vrn_number'])
                <p class="company-contact">VRN: {{ $pharmacy['vrn_number'] }}</p>
            @endif
        </div>

        <div class="receipt-header">
            <h2 class="receipt-title">DELIVERY NOTE</h2>
            <p><strong>Receipt No:</strong> {{ $receiptNumber }}</p>
            @if ($firstItem && isset($firstItem['ref_no']) && $firstItem['ref_no'])
                <p><strong>Ref No:</strong> {{ $firstItem['ref_no'] }}</p>
            @endif
            <p><strong>Date:</strong> {{ $firstItem ? date('d/m/Y', strtotime($firstItem['created_at'])) : '' }}</p>
        </div>

        <table class="customer-table">
            <tr>
                <td class="label">Customer:</td>
                <td>{{ $firstItem ? $firstItem['customer'] : '' }}</td>
            </tr>
            @if ($firstItem && $firstItem['customer_tin'])
                <tr>
                    <td class="label">TIN:</td>
                    <td>{{ $firstItem['customer_tin'] }}</td>
                </tr>
            @endif
            @if ($firstItem && $firstItem['customer_phone'])
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $firstItem['customer_phone'] }}</td>
                </tr>
            @endif
            @if ($firstItem && $firstItem['customer_address'])
                <tr>
                    <td class="label">Address:</td>
                    <td>{{ $firstItem['customer_address'] }}</td>
                </tr>
            @endif
        </table>

        <table class="products-table">
            <thead>
                <tr>
                    <th class="name-col">Product</th>
                    <th class="uom-col">UOM</th>
                    <th class="quantity-col">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="name-col">{{ $item['name'] }}</td>
                        <td class="uom-col">{{ $item['sales_uom'] }}</td>
                        <td class="quantity-col">{{ $item['quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-section">
            <p>Thank you for your business!</p>
            @if ($generalSettings && $generalSettings->terms_conditions)
                <p><strong>Terms & Conditions:</strong> {{ $generalSettings->terms_conditions }}</p>
            @endif
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Delivered By</p>
            </div>
            <div class="signature-box">
                <p>Received By</p>
            </div>
        </div>
    @endforeach
</body>

</html>