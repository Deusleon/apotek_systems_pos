<?php
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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        @page  {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 10px;
            margin: 0;
            padding: 10px 30px 10px 10px;
            /* font-weight: bold; */
        }

        * {
            font-family: Arial, Helvetica, sans-serif
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
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
            padding-top: 4px;
            padding-bottom: 4px;
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
    </style>
</head>

<body>
    <div style="width: 100%;">
        <h3><b>CREDIT RECEIPT</b></h3>
        <h4><?php echo e($pharmacy['name']); ?></h4>
        <h5><?php echo e($pharmacy['address']); ?></h5>
        <h5><?php echo e($pharmacy['phone']); ?></h5>
        <h5>TIN: <?php echo e($pharmacy['tin_number'] ?? 'N/A'); ?></h5>

        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas => $dat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> <?php echo e($datas); ?><br>
                        <span>Sales Date:</span> <?php echo e(date('Y-m-d', strtotime($dat[0]['created_at']))); ?><br>
                        <span>Customer:</span> <?php echo e($dat[0]['customer'] ?? 'CASH'); ?><br>
                        <span>TIN:</span> <?php echo e($dat[0]['customer_tin'] ?? 'N/A'); ?><br>
                        <span>VRN:</span> <?php echo e($pharmacy['vrn_number'] ?? 'N/A'); ?><br>
                        <span>Printed On:</span> <?php echo e(date('Y-m-d H:i:s')); ?>

                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr>
                        <th align="left" style="width: 50%;">Description</th>
                        <th align="center" style="width: 15%;">Qty</th>
                        <th align="right" style="width: 35%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $dat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item['name']); ?> <?php echo e($item['brand'] ?? ''); ?> <?php echo e($item['pack_size'] ?? ''); ?><?php echo e($item['sales_uom'] ?? ''); ?></td>
                            <td align="center"><?php echo e(smartFormat($item['quantity'])); ?></td>
                            <td align="right"><?php echo e(customRound($item['price'] * $item['quantity'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <hr>
            <table id="footer-detail">
                <tbody>
                        <tr>
                            <td align="left">Sub Total</td>
                            <td align="right"><?php echo e(customRound($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total'])); ?></td>
                        </tr>
                        <?php if($dat[0]['discount_total'] > 0): ?>
                            <tr>
                                <td align="left">Discount</td>
                                <td align="right"><?php echo e(customRound($dat[0]['discount_total'])); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td align="left">VAT</td>
                            <td align="right"><?php echo e(customRound($dat[0]['total_vat'])); ?></td>
                        </tr>
                        <tr>
                            <td align="left"><b>Total</b></td>
                            <td align="right"><b><?php echo e(customRound($dat[0]['grand_total'])); ?></b></td>
                        </tr>
                </tbody>
            </table>
            <hr>
            <?php if($page == -1): ?>
            <table id="footer-detail">
                <tbody>
                        <tr>
                            <td align="left">Paid</td>
                            <td align="right"><?php echo e(customRound($dat[0]['paid'])); ?></td>
                        </tr>
                            <tr>
                                <td align="left">Balance</td>
                                <td align="right"><?php echo e(customRound($dat[0]['grand_total'] - $dat[0]['paid'])); ?></td>
                            </tr>
                </tbody>
            </table>
            <hr>
                <div class="summary-row" style="font-size: 9px;">
                    
                </div>
            <?php endif; ?>

            <h5>Issued By: <?php echo e($dat[0]['sold_by']); ?></h5>
            <h5 style="font-style: italic"><?php echo e($pharmacy['slogan']); ?></h5>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>

</html><?php /**PATH C:\Users\Little Pro\Desktop\APOTEk\apotek_systems_pos\resources\views/sales/cash_sales/credit_receipt_thermal.blade.php ENDPATH**/ ?>