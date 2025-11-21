<!DOCTYPE html>
<html>
<head>
    <title>QR Code Test (SVG)</title>
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
        }
    </style>
</head>
<body>
    <h1>QR Code Test (SVG Format)</h1>
    
    <div class="qr-container">
        {!! QrCode::format('svg')->size(200)->generate('Test QR Code Content') !!}
    </div>
    
    <div class="info">
        <p>Jika QR code terlihat di atas, berarti library QR code dengan format SVG sudah berfungsi dengan baik.</p>
        <p>Ini adalah halaman test untuk memastikan library QR code berjalan dengan benar.</p>
    </div>
</body>
</html> 