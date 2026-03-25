d<!DOCTYPE html>
<html>
<head>
    <title>Supplier Statement Report</title>

    <style>

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 6px;
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

        tr:nth-child(even) {
            background-color: #f2f2f2;
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Supplier Statement Report</h3>
            <h4 align="center" style="margin-top: -1%">Supplier: <b>{{$data['supplier']->name ?? 'N/A'}}</b></h4>
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
                    <th align="center" style="width: 5%;">S/N</th>
                    <th align="center" style="width: 12%;">Date</th>
                    <th align="left" style="width: 25%;">Invoice Reference</th>
                    <th align="right" style="width: 18%;">Debit (Invoiced)</th>
                    <th align="right" style="width: 18%;">Credit (Paid)</th>
                    <th align="right" style="width: 18%;">Balance</th>
                </tr>
                </thead>
                @php $serial = 1; $hasTransactions = false; @endphp
                @forelse($data['transactions'] as $transaction)
                    @php $hasTransactions = true; @endphp
                    <tr>
                        <td align="center">{{ $serial++ }}</td>
                        <td align="center">{{ date('Y-m-d', strtotime($transaction['date'])) }}</td>
                        <td align="left">{{ $transaction['reference'] }}</td>
                        <td align="right">{{ number_format($transaction['debit'], 2) }}</td>
                        <td align="right">
                            @if($transaction['credit'] > 0)
                                {{ number_format($transaction['credit'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td align="right">{{ number_format($transaction['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center" style="padding: 20px;">No transactions found for the selected period.</td>
                    </tr>
                @endforelse
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
                    <div class="col-50" align="right"><b>Opening Balance: </b></div>
                    <div class="col-50" align="right">{{ number_format($data['opening_balance'] ?? 0, 2) }}</div>
                </div>
                <div class="full-row">
                    <div class="col-50" align="right"><b>Total Invoiced: </b></div>
                    <div class="col-50" align="right">{{ number_format($data['total_invoiced'], 2) }}</div>
                </div>
                <div class="full-row">
                    <div class="col-50" align="right"><b>Total Paid: </b></div>
                    <div class="col-50" align="right">{{ number_format($data['total_paid'], 2) }}</div>
                </div>
                <div class="full-row">
                    <div class="col-50" align="right"><b>Outstanding Balance: </b></div>
                    <div class="col-50" align="right">{{ number_format($data['balance'], 2) }}</div>
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
