<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $correspondence->document_title }}</title>
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
            padding-left: 15px;
            margin-top: 10px;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            list-style-type: decimal;
        }
        .body-content ul {
            padding-left: 15px;
            margin-top: 10px;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            list-style-type: none;
        }
        .body-content li {
            margin-bottom: 5px;
            margin-left: 10px;
        }
        .body-content ul > li {
            position: relative;
            list-style-type: none !important;
        }
        .body-content ul > li::before {
            content: '- ';
            position: absolute;
            left: -10px;
        }
        .body-content ol > li {
            list-style-type: decimal !important;
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
    {{-- 1. Kop Surat --}}
    <div class="letter-head">
        {{-- Asumsi data tenant ada di auth()->user()->tenant --}}
        {{-- Ganti 'letter_head' dengan nama kolom yang benar di tabel tenants --}}
        {!! nl2br(e(auth()->user()->tenant->letter_head ?? 'KOP SURAT BELUM DIATUR')) !!}
    </div>

    {{-- 2. Judul Nota Dinas & Nomor --}}
    <div class="nota-dinas-title">NOTA DINAS</div>
    <div class="nomor-surat">Nomor : {{ $correspondence->document_number ?? '...' }}</div>

    {{-- 3. Metadata Surat --}}
    <table class="metadata">
        <tr>
            <td class="label">Kepada</td>
            <td class="separator">:</td>
            <td>Yth. {{ $correspondence->recipient_position ?? '...' }}</td>
        </tr>
        <tr>
            <td class="label">Dari</td>
            <td class="separator">:</td>
            <td>{{ $correspondence->sender_position ?? '...' }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="separator">:</td>
            <td>{{ $correspondence->subject ?? '...' }}</td>
        </tr>
    </table>
    <hr style="border-top: 1px solid black; margin-bottom: 20px;"> {{-- Garis di bawah Perihal --}}


    {{-- 4. Isi Surat --}}
    <div class="body-content">
        <ol>
            @if($correspondence->reference_to)
            <li>
                Rujukan:<br>
                {!! nl2br(e($correspondence->reference_to)) !!}
            </li>
            @endif
            <li>
                {{-- Isi utama surat --}}
                {!! $correspondence->body ?? 'Isi surat belum dimasukkan.' !!}
            </li>
            <li>
                Demikian untuk menjadi maklum.
            </li>
        </ol>
    </div>

    {{-- 5. Tanda Tangan --}}
    <div class="signature-section">
        {{-- Asumsi data city ada di auth()->user()->tenant --}}
        {{-- Ganti 'city' dengan nama kolom yang benar di tabel tenants --}}
        <p>{{ auth()->user()->tenant->city ?? 'Kota' }}, {{ $correspondence->signed_at_date ? $correspondence->signed_at_date->isoFormat('D MMMM Y') : '...' }}</p>
        <p>{{ $correspondence->signatory_position ?? '...' }}</p>
        {{-- Ganti nama institusi sesuai kebutuhan --}}
        <p>RS BHAYANGKARA TK.III HASTA BRATA BATU</p>
        <div class="signature-qr">
            {{-- Tampilkan QR Code yang sudah di-generate di controller --}}
            {{-- Pastikan $qrCodeDataUri dikirim dari controller --}}
            @isset($qrCodeDataUri)
            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
            @else
            <p>(QR Code)</p> {{-- Fallback jika URI tidak ada --}}
            @endisset
        </div>
        <p class="signatory-name">{{ $correspondence->signatory_name ?? '...' }}</p>
        <p class="signatory-details">
            {{ $correspondence->signatory_rank ?? '' }}
            @if($correspondence->signatory_rank && $correspondence->signatory_nrp)/@endif
            @if($correspondence->signatory_nrp)NRP {{ $correspondence->signatory_nrp }}@endif
        </p>
    </div>

    {{-- 6. Tembusan --}}
    @if($correspondence->cc_list)
    <div class="tembusan-section">
        <strong>Tembusan :</strong>
        {{-- Gunakan format list untuk baris baru --}}
        <ul>
            @foreach(explode("\n", $correspondence->cc_list) as $item)
                @if(trim($item))
                <li>{{ trim($item) }}</li>
                @endif
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Hapus Footer Lama --}}
    {{-- <div class="footer"> ... </div> --}}

</body>
</html> 