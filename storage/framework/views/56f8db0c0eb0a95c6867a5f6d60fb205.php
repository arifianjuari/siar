<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Awal Risiko #<?php echo e($riskReport->document_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 0.5cm;
            line-height: 1.2;
        }
        h1 {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-top: 0;
            margin-bottom: 40;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-top: 10;
            margin-bottom: 0;
            padding-top: 3pt;
        }
        h3 {
            margin-top: 10pt;
            margin-bottom: 5pt;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 5pt;
        }
        .header img {
            max-height: 70pt;
        }
        .logo {
            border: 0;
        }
        table.info {
            width: 75%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 0;
        }
        table.info td {
            padding: 0;
            vertical-align: top;
            line-height: 1;
        }
        table.info td:first-child {
            width: 30%;
            font-weight: bold;
        }
        table.info td.separator {
            width: 5%;
            text-align: center;
            padding: 0 5pt;
        }
        table.info td.value {
            width: 65%;
        }
        .section {
            margin-top: 0;
            margin-bottom: 0;
        }
        .pre {
            white-space: pre-line;
            margin: 5pt 0;
            line-height: 1;
            text-align: justify;
        }
        .footer {
            margin-top: 30pt;
            text-align: right;
            line-height: 1;
        }
        p {
            margin: 5pt 0;
        }
        .status {
            font-weight: bold;
            padding: 0;
            display: inline-block;
        }
        .status-open {
            color: red;
        }
        .status-in-review {
            color: orange;
        }
        .status-resolved {
            color: green;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN AWAL INSIDEN DAN MANAJEMEN RISIKO</h1>
    </div>
    
    <h2>A. IDENTITAS LAPORAN</h2>
    <table class="info">
        <tr>
            <td>Nomor Laporan</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->document_number); ?></td>
        </tr>
        <tr>
            <td>Tanggal Laporan</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->document_date ? $riskReport->document_date->format('d/m/Y') : $riskReport->created_at->format('d/m/Y')); ?></td>
        </tr>
        <tr>
            <td>Unit Pelapor</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->reporter_unit); ?></td>
        </tr>
        <tr>
            <td>Judul Risiko</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->document_title); ?></td>
        </tr>
    </table>
    
    <h2>B. DESKRIPSI KEJADIAN</h2>
    <table class="info">
        <tr>
            <td>Tanggal Kejadian</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->occurred_at->format('d/m/Y')); ?></td>
        </tr>
        <tr>
            <td>Kategori Risiko</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->risk_category); ?></td>
        </tr>
        <tr>
            <td>Tipe Risiko</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->risk_type ?: 'Tidak Ditentukan'); ?></td>
        </tr>
    </table>
    
    <div class="section">
        <h3>Kronologi Singkat:</h3>
        <div class="pre"><?php echo e($riskReport->chronology); ?></div>
    </div>

    <div class="section">
        <h3>Detil Kejadian:</h3>
        <div class="pre"><?php echo e($riskReport->description); ?></div>
    </div>

    <div class="section">
        <h3>Tindakan Segera:</h3>
        <div class="pre"><?php echo e($riskReport->immediate_action ?: '-'); ?></div>
    </div>
    
    <h2>C. ANALISIS RISIKO</h2>
    <table class="info">
        <tr>
            <td>Dampak</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->impact); ?></td>
        </tr>
        <tr>
            <td>Probabilitas</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->probability); ?></td>
        </tr>
        <tr>
            <td>Tingkat Risiko</td>
            <td class="separator">:</td>
            <td class="value"><?php echo e($riskReport->risk_level); ?></td>
        </tr>
    </table>
    
    <?php if($riskReport->recommendation): ?>
        <div class="section">
            <h3>Rekomendasi Awal:</h3>
            <div class="pre"><?php echo e($riskReport->recommendation); ?></div>
        </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Dibuat oleh: <?php echo e($riskReport->creator->name); ?></p>
        <p>Tanggal: <?php echo e($riskReport->document_date ? $riskReport->document_date->format('d/m/Y H:i') : $riskReport->created_at->format('d/m/Y H:i')); ?></p>
    </div>
</body>
</html> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/modules/risk_management/laporan_awal.blade.php ENDPATH**/ ?>