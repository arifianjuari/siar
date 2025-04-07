<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($spo->document_title); ?></title>
    <style>
        @page {
            margin: 1cm;
        }
        * {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .logo-wrapper {
            width: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
        }
        .logo {
            max-width: 100px;
            max-height: 60px;
            display: block;
            margin: 0 auto 10px;
        }
        .tenant-name {
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
            width: 100%;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            line-height: 1.2;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            line-height: 1.2;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .header-info {
            line-height: 1;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .footer {
            font-size: 10px;
            text-align: right;
            position: fixed;
            bottom: 0;
            right: 0;
            padding: 5px;
        }
        .signature-column {
            text-align: center;
            vertical-align: middle !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 50%;
        }
        .signature-title {
            margin-bottom: 20px;
            font-weight: bold;
        }
        .signature-content {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .qr-code {
            display: block;
            width: 90px;
            height: 90px;
            margin: 2px auto;
        }
        .info-row td {
            font-size: 12px;
            padding: 4px 8px;
        }
        .info-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 25%; text-align: center; vertical-align: middle;" rowspan="1">
                <div class="logo-wrapper">
                    <?php if($tenant && $tenant->logo): ?>
                        <img src="<?php echo e(public_path('storage/tenant_logos/' . basename($tenant->logo))); ?>" alt="<?php echo e($tenant->name); ?>" class="logo">
                    <?php else: ?>
                        <div class="text-center">[logo tenant]</div>
                    <?php endif; ?>
                </div>
                <div class="tenant-name">
                    <?php if($tenant): ?>
                        <?php echo e($tenant->name); ?>

                    <?php else: ?>
                        [nama tenant]
                    <?php endif; ?>
                </div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: middle;" colspan="1">
                <div class="title">STANDAR PROSEDUR OPERASIONAL</div>
                <div class="document-title"><?php echo e($spo->document_title); ?></div>
            </td>
            <td style="width: 25%; padding: 0px 0px 0 0px; vertical-align: middle; text-align: center;" rowspan="2">
                <div style="margin-bottom: 5px;">
                    <strong>Ditetapkan oleh,</strong>
                </div>
                
                <?php if(isset($qrCodeDataUri)): ?>
                    <img src="<?php echo e($qrCodeDataUri); ?>" alt="QR Code" class="qr-code">
                <?php else: ?>
                    <div style="width: 60px; height: 60px; border: 1px dashed #ccc; margin: 10px auto; text-align: center; line-height: 120px;">
                        [QR Code]
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 5px; text-align: center;">
                    <?php if($tenant && $tenant->ceo): ?>
                        <strong><?php echo e($tenant->ceo); ?></strong><br>
                        <?php echo e($tenant->ceo_rank); ?> / <?php echo e($tenant->ceo_nrp); ?>

                    <?php elseif($approver): ?>
                        <strong><?php echo e($approver->name); ?></strong><br>
                        <?php echo e($approver->position ?? '-'); ?> / <?php echo e($approver->employee_number ?? '-'); ?>

                    <?php else: ?>
                        <strong>[CEO]</strong><br>
                        [Jabatan] / [NRP]
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr class="info-row">
            <td style="width: 25%; text-align: left; vertical-align: middle; padding: 4px 8px;">
                <div class="header-info"><span class="info-label">Versi:</span> <?php echo e($spo->document_version); ?></div>
                <div class="header-info" style="margin-top: 8px;"><span class="info-label">Tgl. Berlaku:</span> <?php echo e(\Carbon\Carbon::parse($spo->document_date)->format('d/m/Y')); ?></div>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle; padding: 4px 8px;">
                <div class="header-info"><span class="info-label">No.:</span> <?php echo e($spo->document_number); ?></div>
                <div class="header-info" style="margin-top: 8px;"><span class="info-label"><?php echo e($workUnit ? $workUnit->unit_name : '[unit kerja]'); ?></span></div>
            </td>
        </tr>
        <tr>
            <td style="width: 25%;"><strong>Pengertian</strong></td>
            <td colspan="2"><?php echo nl2br(e($spo->definition)); ?></td>
        </tr>
        <tr>
            <td><strong>Tujuan</strong></td>
            <td colspan="2"><?php echo nl2br(e($spo->purpose)); ?></td>
        </tr>
        <tr>
            <td><strong>Kebijakan</strong></td>
            <td colspan="2"><?php echo nl2br(e($spo->policy)); ?></td>
        </tr>
        <tr>
            <td><strong>Prosedur</strong></td>
            <td colspan="2"><?php echo nl2br(e($spo->procedure)); ?></td>
        </tr>
        <tr>
            <td><strong>Referensi</strong></td>
            <td colspan="2"><?php echo nl2br(e($spo->reference)); ?></td>
        </tr>
        <tr>
            <td><strong>Unit Terkait</strong></td>
            <td colspan="2">
                <?php if($linkedUnits && $linkedUnits->count() > 0): ?>
                    <?php $__currentLoopData = $linkedUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($unit->unit_name); ?><?php if(!$loop->last): ?>, <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        Revisi terakhir: <?php echo e(\Carbon\Carbon::parse($spo->updated_at)->format('d/m/Y')); ?>

    </div>
</body>
</html> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/work-unit/spo/pdf.blade.php ENDPATH**/ ?>