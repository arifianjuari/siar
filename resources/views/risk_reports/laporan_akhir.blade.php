<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Akhir Risiko #{{ $riskReport->riskreport_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 30;
        }
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
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin: 10px 0 0 0;
            padding: 0;
        }
        h3 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }
        h4 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin: 0;
            padding: 10;
        }
        .header img {
            max-height: 70pt;
        }
        .logo {
            border: 0;
        }
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        table.info td {
            padding: 0;
            vertical-align: top;
            line-height: 1.2;
        }
        table.info td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .section {
            margin: 0;
            padding: 0;
        }
        .pre {
            white-space: pre-wrap;
            margin: 0;
            padding: 0;
        }
        .footer {
            margin-top: 30pt;
            text-align: right;
        }
        p {
            margin: 0;
            padding: 0;
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
        .qrcode {
            text-align: center;
            margin-top: 5pt;
        }
        .qrcode img {
            width: 90pt;
            height: 90pt;
        }
        .signatures {
            margin-top: 5pt;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
            padding-top: 5pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN FINAL INSIDEN DAN MANAJEMEN RISIKO</h1>
        <p style="text-align: center; margin: 5px 0 15px 0; font-weight: bold; font-size: 14pt;">{{ strtoupper($riskReport->creator->tenant->name ?? 'Unknown Tenant') }}</p>
    </div>
    
    <h2>A. IDENTITAS LAPORAN</h2>
    <table class="info">
        <tr>
            <td>Nomor Laporan</td>
            <td>: {{ $riskReport->riskreport_number }}</td>
        </tr>
        <tr>
            <td>Tanggal Laporan</td>
            <td>: {{ $riskReport->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Unit Pelapor</td>
            <td>: {{ $riskReport->reporter_unit }}</td>
        </tr>
        <tr>
            <td>Judul Risiko</td>
            <td>: {{ $riskReport->risk_title }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ ucfirst($riskReport->status) }}</td>
        </tr>
    </table>
    
    <h2>B. DESKRIPSI KEJADIAN</h2>
    <table class="info">
        <tr>
            <td>Tanggal Kejadian</td>
            <td>: {{ $riskReport->occurred_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Kategori Risiko</td>
            <td>: {{ $riskReport->risk_category }}</td>
        </tr>
        <tr>
            <td>Tipe Risiko</td>
            <td>: {{ $riskReport->risk_type ?: 'Tidak Ditentukan' }}</td>
        </tr>
        <tr>
            <td>Dampak</td>
            <td>: {{ $riskReport->impact }}</td>
        </tr>
        <tr>
            <td>Probabilitas</td>
            <td>: {{ $riskReport->probability }}</td>
        </tr>
        <tr>
            <td>Tingkat Risiko</td>
            <td>: {{ $riskReport->risk_level }}</td>
        </tr>
    </table>
    
    <div class="section">
        <h3>Kronologi Kejadian:</h3>
        <div class="pre">{{ $riskReport->chronology }}</div>
    </div>

    <div class="section">
        <h3>Detail Kejadian:</h3>
        <div class="pre">{{ $riskReport->description }}</div>
    </div>

    <div class="section">
        <h3>Tindakan Segera:</h3>
        <div class="pre">{{ $riskReport->immediate_action }}</div>
    </div>

    <div class="section">
        <h3>Rekomendasi Awal:</h3>
        <div class="pre">{{ $riskReport->recommendation }}</div>
    </div>
    
    <h2>C. ANALISIS RISIKO</h2>
    @if($riskReport->analysis)
    <div class="section">
        <h3>Penyebab Langsung:</h3>
        <div class="pre">{{ $riskReport->analysis->direct_cause }}</div>
    </div>

    <div class="section">
        <h3>Akar Masalah:</h3>
        <div class="pre">{{ $riskReport->analysis->root_cause }}</div>
    </div>

    <h3>Faktor Kontributor:</h3>
    <table class="info">
        <tr>
            <td>Faktor Manusia</td>
            <td>: @if(isset($riskReport->analysis->contributor_factors['human_factors']))
                @php
                    $humanFactors = [
                        'knowledge' => 'Pengetahuan',
                        'fatigue' => 'Kelelahan',
                        'stress' => 'Stres',
                        'communication' => 'Komunikasi',
                        'teamwork' => 'Kerja Tim',
                        'supervision' => 'Pengawasan',
                        'experience' => 'Pengalaman',
                        'attitude' => 'Sikap'
                    ];
                @endphp
                {{ is_array($riskReport->analysis->contributor_factors['human_factors']) 
                    ? implode(', ', array_map(function($item) use ($humanFactors) { 
                        return $humanFactors[$item] ?? $item; 
                    }, $riskReport->analysis->contributor_factors['human_factors']))
                    : ($humanFactors[$riskReport->analysis->contributor_factors['human_factors']] ?? $riskReport->analysis->contributor_factors['human_factors']) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td>Faktor Lingkungan</td>
            <td>: @if(isset($riskReport->analysis->contributor_factors['environmental']))
                @php
                    $environmentalFactors = [
                        'temperature' => 'Suhu',
                        'lighting' => 'Pencahayaan',
                        'noise' => 'Kebisingan',
                        'space_constraints' => 'Keterbatasan Ruang',
                        'cleanliness' => 'Kebersihan',
                        'ventilation' => 'Ventilasi',
                        'workplace_layout' => 'Tata Letak Tempat Kerja'
                    ];
                @endphp
                {{ is_array($riskReport->analysis->contributor_factors['environmental'])
                    ? implode(', ', array_map(function($item) use ($environmentalFactors) { 
                        return $environmentalFactors[$item] ?? $item; 
                    }, $riskReport->analysis->contributor_factors['environmental']))
                    : ($environmentalFactors[$riskReport->analysis->contributor_factors['environmental']] ?? $riskReport->analysis->contributor_factors['environmental']) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td>Faktor Teknis</td>
            <td>: @if(isset($riskReport->analysis->contributor_factors['technical']))
                @php
                    $technicalFactors = [
                        'equipment_failure' => 'Kegagalan Peralatan',
                        'software_issues' => 'Masalah Perangkat Lunak',
                        'maintenance' => 'Pemeliharaan',
                        'design_issues' => 'Masalah Desain',
                        'technical_documentation' => 'Dokumentasi Teknis',
                        'calibration' => 'Kalibrasi',
                        'compatibility' => 'Kompatibilitas'
                    ];
                @endphp
                {{ is_array($riskReport->analysis->contributor_factors['technical'])
                    ? implode(', ', array_map(function($item) use ($technicalFactors) { 
                        return $technicalFactors[$item] ?? $item; 
                    }, $riskReport->analysis->contributor_factors['technical']))
                    : ($technicalFactors[$riskReport->analysis->contributor_factors['technical']] ?? $riskReport->analysis->contributor_factors['technical']) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td>Faktor Organisasi</td>
            <td>: @if(isset($riskReport->analysis->contributor_factors['organizational']))
                @php
                    $organizationalFactors = [
                        'policies_procedures' => 'Kebijakan & Prosedur',
                        'staffing' => 'Kepegawaian',
                        'training' => 'Pelatihan',
                        'leadership' => 'Kepemimpinan',
                        'resource_allocation' => 'Alokasi Sumber Daya',
                        'organizational_culture' => 'Budaya Organisasi',
                        'communication_systems' => 'Sistem Komunikasi'
                    ];
                @endphp
                {{ is_array($riskReport->analysis->contributor_factors['organizational'])
                    ? implode(', ', array_map(function($item) use ($organizationalFactors) { 
                        return $organizationalFactors[$item] ?? $item; 
                    }, $riskReport->analysis->contributor_factors['organizational']))
                    : ($organizationalFactors[$riskReport->analysis->contributor_factors['organizational']] ?? $riskReport->analysis->contributor_factors['organizational']) }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <h3>Rekomendasi Hasil Analisis:</h3>
    <div class="section">
        <h4>Rekomendasi Jangka Pendek (0-3 bulan):</h4>
        <div class="pre">{{ $riskReport->analysis->recommendation_short }}</div>
    </div>

    <div class="section">
        <h4>Rekomendasi Jangka Menengah (3-6 bulan):</h4>
        <div class="pre">{{ $riskReport->analysis->recommendation_medium ?: '-' }}</div>
    </div>

    <div class="section">
        <h4>Rekomendasi Jangka Panjang (6+ bulan):</h4>
        <div class="pre">{{ $riskReport->analysis->recommendation_long ?: '-' }}</div>
    </div>
    @else
    <div class="section">
        <p>Analisis risiko belum dilakukan.</p>
    </div>
    @endif
    
    <h2></h2>
    <table class="info">
        <tr>
            <td>Dibuat oleh</td>
            <td>: {{ $riskReport->creator->name ?? 'Unknown' }}</td>
        </tr>
        <tr>
            <td>Tanggal pembuatan</td>
            <td>: {{ $riskReport->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        
        @if($riskReport->reviewed_by)
        <tr>
            <td>Ditindaklanjuti oleh</td>
            <td>: {{ $riskReport->reviewer->name ?? 'Unknown' }}</td>
        </tr>
        <tr>
            <td>Tanggal tindak lanjut</td>
            <td>: {{ $riskReport->reviewed_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endif
        
        @if($riskReport->approved_by)
        <tr>
            <td>Disetujui oleh</td>
            <td>: {{ $riskReport->approver->name ?? 'Unknown' }}</td>
        </tr>
        <tr>
            <td>Tanggal persetujuan</td>
            <td>: {{ $riskReport->approved_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endif
    </table>
    
    @if($riskReport->status === 'resolved' && $qrCodeData)
        <div class="qrcode">
            <h3>Tanda Tangan Digital</h3>
            <img src="{{ $qrCodeData }}" alt="QR Code Tanda Tangan Digital">
            <p>Scan QR code ini untuk verifikasi tanda tangan digital</p>
        </div>
    @elseif($riskReport->analysis && $riskReport->analysis->analysis_status === 'completed' && $qrCodeAnalysis)
        <div class="qrcode">
            <h3>Tanda Tangan Analisis</h3>
            <img src="{{ $qrCodeAnalysis }}" alt="QR Code Analisis">
            <p>Scan QR code ini untuk verifikasi analisis</p>
        </div>
    @endif
    
    <table class="signatures">
        <tr>
            <td>
                <p>Dibuat oleh:</p>
                <p>{{ $riskReport->creator->name ?? 'Unknown' }}</p>
                <p>Tanggal: {{ $riskReport->created_at->format('d/m/Y') }}</p>
            </td>
            
            @if($riskReport->analysis && $riskReport->analysis->analyst)
            <td>
                <p>Disetujui oleh:</p>
                <p>{{ $riskReport->analysis->analyst->name }}</p>
                <p>Tanggal: {{ $riskReport->analysis->analyzed_at ? $riskReport->analysis->analyzed_at->format('d/m/Y') : $riskReport->analysis->updated_at->format('d/m/Y') }}</p>
            </td>
            @else
            <td>
                <p>Disetujui oleh:</p>
                <p>________________</p>
                <p>Tanggal: ________</p>
            </td>
            @endif
        </tr>
    </table>
</body>
</html> 