<?php
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
?>

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
            table-layout: fixed;
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
            border: 1px solid #858484;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .items-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 11px;
            height: 15px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .summary-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 15px;
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
        <?php if($pharmacy['logo']): ?>
            <img style="max-width: 90px; max-height: 90px;" src="<?php echo e(public_path('fileStore/logo/' . $pharmacy['logo'])); ?>" />
        <?php endif; ?>
        <div style="font-weight: bold; font-size: 16px;"><?php echo e($pharmacy['name']); ?></div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            <?php echo e($pharmacy['address']); ?><br>
            <?php echo e($pharmacy['phone']); ?><br>
            <span><?php echo e($pharmacy['email'] ? $pharmacy['email'].' |' : 'N/A'); ?></span>
            <span><?php echo e($pharmacy['website'] ?? 'N/A'); ?></span><br>
            <span>TIN: <?php echo e($pharmacy['tin_number'] ? $pharmacy['tin_number'].' |' : 'N/A'); ?></span>
            <span>VRN: <?php echo e($pharmacy['vrn_number'] ?? 'N/A'); ?></span>
        </div>
    </div>
    <div style="font-weight: bold; margin-top: 5px; text-align: center;">
        DELIVERY NOTE
    </div>

    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas => $dat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table class="customer-table">
            <tbody>
                <tr>
                    <td style="width: 20%;"><span style="margin-left: 8px;">Delivery # :</span><span style="margin-left: 3px; "><?php echo e($datas ?? 'N/A'); ?></span></td>
                    <td style="padding-left: 8px;"><span>Delivery To : <?php echo e($dat[0]['customer'] ?? 'CASH'); ?></span></td>
                    <td style="width: 20%;"><span style="margin-left: 8px;">Phone : </span><span style="margin-left: 3px;"><?php echo e($dat[0]['customer_phone'] ?? 'N/A'); ?></span></td>
                </tr>
                <tr>
                    <td style="width: 20%;"><span style="margin-left: 8px;">Date : </span><span style="margin-left: 3px;"><?php echo e(date('Y-m-d', strtotime($dat[0]['created_at']))); ?></span></td>
                    <td style="padding-left: 8px;"><span>Address : <?php echo e($dat[0]['customer_address'] ?? 'N/A'); ?></span></td>
                    <td style="width: 20%;"><span style="margin-left: 8px;">Customer PO : </span><span style="margin-left: 3px;"><?php echo e($dat[0]['ref_no'] ?? 'N/A'); ?></span></td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php
        $subTotal = 0;
        $vat = 0;
        $discount = 0;
        $grandTotal = 0;
    ?>

    <!-- Customer Information -->
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas => $dat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr class="table-header">
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 65%; text-align: left; padding-left: 7px;">Product</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 10%; text-align: center;">Condition</th>
                    <th style="width: 10%; text-align: center;">Checked</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $dat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $name = $item['name'] ?? '';
                    ?>
                    <tr>
                        <td style="width: 5%; text-align: center;"><?php echo e($loop->iteration); ?>.</td>
                        <td style="width: 65%; text-align: left; padding-left: 7px;"><?php echo e($name); ?></td>
                        <td style="width: 10%; text-align: center;"><?php echo e(number_format($item['quantity'], 0)); ?></td>
                        <td style="width: 10%; text-align: center;">Good</td>
                        <td style="width: 10%; text-align: center;"></td>
                    </tr>
                    <?php
                        $subTotal += $item['sub_total'];
                        $vat += $item['vat'];
                        $discount += $item['discount'];
                        $grandTotal += ($item['sub_total'] - $item['discount']) + $item['vat'];
                    ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if(count($dat) < 5): ?>
                    <!-- Empty rows for spacing -->
                    <?php for($i = 0; $i < 7 - count($dat); $i++): ?>
                        <tr>
                            <td style="width: 5%;">&nbsp;</td>
                            <td style="width: 65%;">&nbsp;</td>
                            <td style="width: 10%;">&nbsp;</td>
                            <td style="width: 10%;">&nbsp;</td>
                            <td style="width: 10%;">&nbsp;</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Terms & Conditions -->
        <?php if($generalSettings && $generalSettings->delivery_note_terms): ?>
            <div style="margin: 20px 0;">
                <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px;">Terms & Conditions:</div>
                <div style="font-size: 11px; line-height: 1.4; text-align: justify;">
                    <?php echo nl2br(e($generalSettings->delivery_note_terms)); ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- Two Column Grid Layout -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 10px; vertical-align: top;">
                    <!-- Left Column - Customer -->
                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">Goods Received By:</div>
                    <div style="margin-top: 20px;">
                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 11px;">Name: ____________________________________</span>
                        </div>
                        <br>
                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 11px;">Sign: _____________________________________</span>
                        </div>
                        <br>
                        <div style="margin-bottom: 10px;">
                            <span style="font-size: 11px;">Date: _____________________________________</span>
                        </div>
                        <br>
                    </div><br>    
                    <div style="margin-bottom: 5px;">
                        <span style="font-weight: bold; font-size: 11px;">CUSTOMER STAMP</span>
                    </div>
                    <br><br><br>
                        <span style="font-size: 11px;">__________________________________________</span>
                </td>
                <td style="width: 50%; padding: 10px; vertical-align: top;">
                    <!-- Right Column - Company -->
                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">Goods Delivered By:</div>
                    <div style="margin-top: 20px;">
                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 11px;">Name: ____________________________________</span>
                        </div>
                        <br>
                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 11px;">Sign: _____________________________________</span>
                        </div>
                        <br>
                        <div style="margin-bottom: 10px;">
                            <span style="font-size: 11px;">Date: _____________________________________</span>
                        </div>
                    </div>    
                    <br><br>
                    <div style="margin-bottom: 5px;">
                        <span style="font-weight: bold; font-size: 11px;"><?php echo e($pharmacy['name']); ?> STAMP</span>
                    </div>
                    <br><br><br>
                        <span style="font-size: 11px;">__________________________________________</span>
                </td>
            </tr>
        </table>
        <?php break; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="slogan-section">
        <?php echo e($pharmacy['slogan'] ?? 'Thank you for your business'); ?>

    </div>
</body>

</html><?php /**PATH D:\MY DOCUMENTS\PROJECTS\LARAVEL\APOTEk\Repo-project\apotek_systems_pos\resources\views/sales/delivery_notes/delivery_note_pdf.blade.php ENDPATH**/ ?>