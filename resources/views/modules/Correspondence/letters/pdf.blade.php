<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $correspondence->document_title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            font-size: 12pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .letter-number {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .letter-date {
            text-align: right;
            margin-bottom: 20px;
        }
        .recipient {
            margin-bottom: 20px;
        }
        .subject {
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .body {
            text-align: justify;
            margin-bottom: 20px;
        }
        .signature {
            text-align: right;
            margin-top: 40px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            text-align: center;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.letter-info {
            margin-bottom: 20px;
        }
        table.letter-info td {
            vertical-align: top;
            padding: 3px;
        }
        .signature-img {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <p>{{ session('tenant_name', 'Sistem Informasi Arsip') }}</p>
    </div>

    <div class="letter-number">
        <strong>Nomor:</strong> {{ $correspondence->document_number ?? 'N/A' }}
    </div>

    <table class="letter-info">
        <tr>
            <td width="120">Tanggal</td>
            <td width="10">:</td>
            <td>{{ $correspondence->document_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>{{ $correspondence->subject }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>{{ $correspondence->file_path ? '1 berkas' : '-' }}</td>
        </tr>
    </table>

    <div class="recipient">
        <p>
            <strong>Kepada Yth.</strong><br>
            {{ $correspondence->recipient_name }}<br>
            {{ $correspondence->recipient_position }}<br>
            di Tempat
        </p>
    </div>

    <div class="subject">
        <p>{{ strtoupper($correspondence->document_title) }}</p>
    </div>

    <div class="body">
        {!! nl2br(e($correspondence->body)) !!}
    </div>

    <div class="signature">
        <p>
            {{ $correspondence->signed_at_location }}, {{ $correspondence->signed_at_date->format('d F Y') }}<br>
            {{ $correspondence->signatory_position }}<br>
            @if($correspondence->signature_file)
            <img src="{{ public_path('storage/' . $correspondence->signature_file) }}" alt="Tanda Tangan" class="signature-img"><br>
            @else
            <br><br><br>
            @endif
            <strong>{{ $correspondence->signatory_name }}</strong><br>
            @if($correspondence->signatory_rank)
            {{ $correspondence->signatory_rank }}<br>
            @endif
            @if($correspondence->signatory_nrp)
            NRP: {{ $correspondence->signatory_nrp }}
            @endif
        </p>
        
        <!-- QR Code untuk validasi surat -->
        <div style="margin-top: 10px; text-align: center;">
            @isset($qrCodeDataUri)
            <img src="{{ $qrCodeDataUri }}" alt="QR Code Validasi" style="width: 100px; height: 100px;"><br>
            <small style="font-size: 8pt;">Scan QR Code untuk validasi surat</small>
            @endisset
        </div>
    </div>

    @if($correspondence->cc_list)
    <div style="margin-top: 20px;">
        <p>
            <strong>Tembusan:</strong><br>
            {!! nl2br(e($correspondence->cc_list)) !!}
        </p>
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak melalui {{ config('app.name') }} pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html> 