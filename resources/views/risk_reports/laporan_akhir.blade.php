<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Akhir Risiko #{{ $riskReport->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 1cm;
            line-height: 1.5;
        }
        h1 {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20pt;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-top: 20pt;
            margin-bottom: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30pt;
        }
        .header img {
            max-height: 80pt;
        }
        .logo {
            border: 0;
        }
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20pt;
        }
        table.info td {
            padding: 5pt;
            vertical-align: top;
        }
        table.info td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .section {
            margin-top: 20pt;
            margin-bottom: 20pt;
        }
        .pre {
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 50pt;
            text-align: right;
        }
        .status {
            font-weight: bold;
            padding: 5pt;
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
            margin-top: 30pt;
        }
        .qrcode img {
            width: 150pt;
            height: 150pt;
        }
        .signatures {
            margin-top: 50pt;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
            padding-top: 50pt;
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
            <td>Status</td>
            <td>: 
                @if($riskReport->status === 'open')
                    <span class="status status-open">Open</span>
                @elseif($riskReport->status === 'in_review')
                    <span class="status status-in-review">In Review</span>
                @else
                    <span class="status status-resolved">Resolved</span>
                @endif
            </td>
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
            <img src="data:image/png;base64,{{ $qrCodeData }}" alt="QR Code Tanda Tangan Digital">
            <p>Scan QR code ini untuk verifikasi tanda tangan digital</p>
        </div>
    @endif
    
    <table class="signatures">
        <tr>
            <td>
                <p>Dibuat oleh:</p>
                <p>{{ $riskReport->creator->name ?? 'Unknown' }}</p>
                <p>Tanggal: {{ $riskReport->created_at->format('d/m/Y') }}</p>
            </td>
            
            @if($riskReport->approved_by)
            <td>
                <p>Disetujui oleh:</p>
                <p>{{ $riskReport->approver->name ?? 'Unknown' }}</p>
                <p>Tanggal: {{ $riskReport->approved_at->format('d/m/Y') }}</p>
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