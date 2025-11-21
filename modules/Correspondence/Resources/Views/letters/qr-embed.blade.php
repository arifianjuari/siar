<!DOCTYPE html>
<html>
<head>
    <title>QR Code - {{ $correspondence->document_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        .qr-container {
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px auto;
            max-width: 300px;
            background: #fff;
        }
        .info {
            margin-top: 20px;
            text-align: left;
            padding: 10px;
            border: 1px solid #eee;
            background: #f9f9f9;
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h2>QR Code Surat / Nota Dinas</h2>
    <h3>{{ $correspondence->document_number }}</h3>
    
    <div class="qr-container">
        <img src="{{ $dataUri }}" alt="QR Code" style="width: 200px; height: 200px;">
    </div>
    
    <div class="info">
        <p><strong>Informasi QR Code:</strong></p>
        <ul style="text-align: left;">
            <li><strong>Nomor Surat:</strong> {{ $correspondence->document_number }}</li>
            <li><strong>Tanggal:</strong> {{ $correspondence->document_date->format('d-m-Y') }}</li>
            <li><strong>Perihal:</strong> {{ $correspondence->subject }}</li>
            <li><strong>Penandatangan:</strong> {{ $correspondence->signatory_name }}</li>
        </ul>
    </div>
</body>
</html> 