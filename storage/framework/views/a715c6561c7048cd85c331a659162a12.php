<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo e($correspondence->document_title); ?></title>
    <style>
        @page {
            margin: 1.5cm; /* Sesuaikan margin halaman */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.15;
        }
        p {
            margin-top: 0;
            margin-bottom: 0;
        }
        .letter-head {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid black; /* Garis bawah kop surat */
            padding-bottom: 10px;
            /* Anda mungkin perlu menambahkan style lebih spesifik untuk kop surat */
            /* Contoh: */
            /* font-size: 14pt; */
            /* font-weight: bold; */
        }
        .nota-dinas-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px; /* Kurangi jarak bawah */
            font-size: 13pt;
        }
        .nomor-surat {
            text-align: center;
            margin-bottom: 25px; /* Tambah jarak bawah */
            font-weight: bold;
        }
        .metadata {
            margin-bottom: 20px;
            line-height: 1.6; /* Sedikit renggangkan baris metadata */
        }
        .metadata td {
            vertical-align: top; /* Pastikan label dan isi sejajar di atas */
            padding-bottom: 1px; /* Atur jarak minimal antar baris metadata */
        }
        .metadata .label {
            width: 80px; /* Lebar kolom label */
        }
        .metadata .separator {
            width: 10px; /* Lebar kolom pemisah (:) */
            text-align: center;
        }
        .body-content {
            text-align: justify;
            margin-top: 20px; /* Jarak dari metadata */
            margin-bottom: 30px;
        }
        .body-content ol {
            padding-left: 25px; /* Indentasi untuk list */
            margin-top: 10px;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }
        .body-content li {
            margin-bottom: 5px; /* Atur margin bawah list item */
        }
        .signature-section {
            margin-top: 30px; /* Kurangi jarak atas */
            width: 40%; /* Lebar area tanda tangan */
            margin-left: 60%; /* Posisikan ke kanan */
            text-align: center; /* Teks rata tengah di area tanda tangan */
        }
        .signature-section p {
            /* Pastikan margin p di sini juga 0, line-height warisan dari body */
            /* margin-bottom: 2px; */ 
            /* line-height: 1.4; */
        }
        .signature-qr {
            margin-top: 5px; /* Kurangi jarak atas QR */
            margin-bottom: 5px; /* Kurangi jarak bawah QR */
            text-align: center; /* Pastikan QR Code di tengah */
        }
        .signature-qr img {
            width: 80px; /* Ukuran QR Code */
            height: 80px;
            text-decoration: underline;
        }
        .signatory-name {
            margin-top: 5px; /* Jarak setelah QR Code dikurangi */
            font-weight: bold;
            text-decoration: underline;
        }
        .signatory-details {
            /* Ukuran font sudah 11pt (default body), tidak perlu override */
            /* font-size: 11pt; */
            /* Atur line-height spesifik jika perlu, atau biarkan mewarisi 1.15 */
            /* line-height: 1.4; */ 
        }
        .tembusan-section {
            /* Hapus positioning absolut */
            /* position: absolute; */
            /* bottom: 1.5cm; */
            /* left: 1.5cm; */
            /* Tambahkan margin atas untuk jarak */
            margin-top: 30px;
            /* Ukuran font sudah 11pt (default body), tidak perlu override */
            /* font-size: 11pt; */
            /* Biarkan line-height mewarisi dari body (1.15) */
            /* line-height: 1.4; */
        }
        .tembusan-section ul {
            list-style: decimal;
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }
        hr { /* Style untuk garis pemisah jika diperlukan */
            border: 0;
            border-top: 1px solid black;
            margin: 3px 0; /* Kurangi margin garis */
        }

    </style>
</head>
<body>
    
    <div class="letter-head">
        
        
        <?php echo nl2br(e(auth()->user()->tenant->letter_head ?? 'KOP SURAT BELUM DIATUR')); ?>

    </div>

    
    <div class="nota-dinas-title">NOTA DINAS</div>
    <div class="nomor-surat">Nomor : <?php echo e($correspondence->document_number ?? '...'); ?></div>

    
    <table class="metadata">
        <tr>
            <td class="label">Kepada</td>
            <td class="separator">:</td>
            <td>Yth. <?php echo e($correspondence->recipient_position ?? '...'); ?></td>
        </tr>
        <tr>
            <td class="label">Dari</td>
            <td class="separator">:</td>
            <td><?php echo e($correspondence->sender_position ?? '...'); ?></td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="separator">:</td>
            <td><?php echo e($correspondence->subject ?? '...'); ?></td>
        </tr>
    </table>
    <hr style="border-top: 1px solid black; margin-bottom: 20px;"> 


    
    <div class="body-content">
        <ol>
            <?php if($correspondence->reference_to): ?>
            <li>
                Rujukan:<br>
                <?php echo nl2br(e($correspondence->reference_to)); ?>

            </li>
            <?php endif; ?>
            <li>
                
                <?php echo nl2br(e($correspondence->body ?? 'Isi surat belum dimasukkan.')); ?>

            </li>
            <li>
                Demikian untuk menjadi maklum.
            </li>
        </ol>
    </div>

    
    <div class="signature-section">
        
        
        <p><?php echo e(auth()->user()->tenant->city ?? 'Kota'); ?>, <?php echo e($correspondence->signed_at_date ? $correspondence->signed_at_date->isoFormat('D MMMM Y') : '...'); ?></p>
        <p><?php echo e($correspondence->signatory_position ?? '...'); ?></p>
        
        <p>RS BHAYANGKARA TK.III HASTA BRATA BATU</p>
        <div class="signature-qr">
            
            
            <?php if(isset($qrCodeDataUri)): ?>
            <img src="<?php echo e($qrCodeDataUri); ?>" alt="QR Code">
            <?php else: ?>
            <p>(QR Code)</p> 
            <?php endif; ?>
        </div>
        <p class="signatory-name"><?php echo e($correspondence->signatory_name ?? '...'); ?></p>
        <p class="signatory-details">
            <?php echo e($correspondence->signatory_rank ?? ''); ?>

            <?php if($correspondence->signatory_rank && $correspondence->signatory_nrp): ?>/<?php endif; ?>
            <?php if($correspondence->signatory_nrp): ?>NRP <?php echo e($correspondence->signatory_nrp); ?><?php endif; ?>
        </p>
    </div>

    
    <?php if($correspondence->cc_list): ?>
    <div class="tembusan-section">
        <strong>Tembusan :</strong>
        
        <ul>
            <?php $__currentLoopData = explode("\n", $correspondence->cc_list); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(trim($item)): ?>
                <li><?php echo e(trim($item)); ?></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    

</body>
</html> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/letters/pdf.blade.php ENDPATH**/ ?>