<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Akhir Risiko #{{ $riskReport->id }}</title>
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
            margin-top: 0;
            margin-bottom: 0;
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
            width: 100%;
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
        .section {
            margin-top: 0;
            margin-bottom: 0;
        }
        .pre {
            white-space: pre-wrap;
            margin-top: 0;
            margin-bottom: 0;
        }
        .footer {
            margin-top: 30pt;
            text-align: right;
        }
        p {
            margin-top: 0;
            margin-bottom: 0;
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
    </div>
    
    <h2>A. IDENTITAS LAPORAN</h2>
    <table class="info">
        <tr>
            <td>Nomor Laporan</td>
            <td>: {{ $riskReport->id }}</td>
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
    </table>
    
    <div class="section">
        <h3>Kronologi Kejadian:</h3>
        <div class="pre">{{ $riskReport->chronology }}</div>
    </div>
    
    <h2>C. ANALISIS RISIKO</h2>
    <table class="info">
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
    
    @if($riskReport->recommendation)
        <div class="section">
            <h3>Rekomendasi:</h3>
            <div class="pre">{{ $riskReport->recommendation }}</div>
        </div>
    @endif
    
    <h2>D. PENANGANAN DAN STATUS AKHIR</h2>
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