<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Awal Risiko #{{ $riskReport->id }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INSIDEN DAN MANAJEMEN RISIKO</h1>
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
            <h3>Rekomendasi Awal:</h3>
            <div class="pre">{{ $riskReport->recommendation }}</div>
        </div>
    @endif
    
    <div class="footer">
        <p>Dibuat oleh: {{ $riskReport->creator->name ?? 'Unknown' }}<br>
        Tanggal: {{ $riskReport->created_at->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html> 