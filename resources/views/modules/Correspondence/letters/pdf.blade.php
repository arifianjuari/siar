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
        <div class="signature-section">
            <p>{{ $correspondence->signed_at_location }}, {{ $correspondence->signed_at_date->format('d F Y') }}</p>
            <p>{{ $correspondence->signatory_position }}</p>
            <div class="qr-code-container">
                {!! QrCode::size(100)->generate(route('modules.correspondence.letters.show', $correspondence->id)) !!}
            </div>
            <p class="signatory-name">{{ $correspondence->signatory_name }}</p>
            @if($correspondence->signatory_rank)
            <p class="signatory-rank">{{ $correspondence->signatory_rank }}</p>
            @endif
            @if($correspondence->signatory_nrp)
            <p class="signatory-nrp">NRP: {{ $correspondence->signatory_nrp }}</p>
            @endif
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