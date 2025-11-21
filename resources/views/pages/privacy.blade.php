@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h1 class="m-0 font-weight-bold text-primary h3">Kebijakan Privasi</h1>
        </div>
        <div class="card-body">
            <p class="mb-4">
                Kebijakan Privasi ini menjelaskan bagaimana Sistem Informasi Administrasi Rumah Sakit (SIAR) mengumpulkan, menggunakan, 
                dan melindungi informasi yang Anda berikan saat menggunakan layanan kami.
            </p>

            <h4 class="mb-3">Informasi yang Kami Kumpulkan</h4>
            <p>
                Kami mengumpulkan beberapa jenis informasi berikut:
            </p>
            <ul class="mb-4">
                <li>Informasi identitas pribadi (nama, alamat email, jabatan, unit kerja)</li>
                <li>Informasi log sistem (waktu akses, halaman yang dikunjungi, alamat IP)</li>
                <li>Informasi yang berkaitan dengan dokumen dan data rumah sakit sesuai izin akses</li>
                <li>Informasi lain yang disediakan secara sukarela</li>
            </ul>

            <h4 class="mb-3">Penggunaan Informasi</h4>
            <p>
                Informasi yang kami kumpulkan digunakan untuk:
            </p>
            <ul class="mb-4">
                <li>Menyediakan, mengelola, dan meningkatkan fitur dan layanan SIAR</li>
                <li>Memahami penggunaan layanan untuk pengembangan sistem</li>
                <li>Berkomunikasi dengan pengguna terkait pemberitahuan sistem</li>
                <li>Menjaga keamanan dan integritas sistem</li>
                <li>Memenuhi kewajiban hukum dan peraturan yang berlaku</li>
            </ul>

            <h4 class="mb-3">Keamanan Data</h4>
            <p class="mb-4">
                Kami menerapkan tindakan keamanan teknis dan organisasi yang dirancang untuk melindungi informasi 
                pengguna dari akses yang tidak sah, termasuk enkripsi, pembatasan akses, dan prosedur keamanan fisik.
            </p>

            <h4 class="mb-3">Berbagi Data</h4>
            <p class="mb-4">
                Informasi pengguna tidak akan dibagikan kepada pihak ketiga kecuali dalam kondisi berikut:
            </p>
            <ul class="mb-4">
                <li>Dengan persetujuan pengguna</li>
                <li>Untuk memenuhi kewajiban hukum</li>
                <li>Untuk melindungi hak, keamanan atau properti SIAR dan penggunanya</li>
                <li>Dalam kasus merger, akuisisi, atau penjualan aset dimana data pribadi termasuk di dalamnya</li>
            </ul>

            <h4 class="mb-3">Retensi Data</h4>
            <p class="mb-4">
                Kami menyimpan informasi pengguna selama diperlukan untuk tujuan yang dijelaskan dalam kebijakan ini 
                atau sesuai dengan persyaratan hukum atau peraturan yang berlaku.
            </p>

            <h4 class="mb-3">Hak Pengguna</h4>
            <p class="mb-4">
                Pengguna memiliki hak untuk:
            </p>
            <ul class="mb-4">
                <li>Mengakses dan memperbaiki informasi pribadi mereka</li>
                <li>Meminta penghapusan data pribadi (dengan batasan)</li>
                <li>Membatasi atau menolak pengolahan data tertentu</li>
                <li>Menerima informasi dalam format yang dapat dibaca mesin</li>
            </ul>

            <h4 class="mb-3">Perubahan Kebijakan</h4>
            <p class="mb-4">
                Kami dapat memperbarui kebijakan ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan 
                kepada pengguna melalui sistem atau email.
            </p>

            <h4 class="mb-3">Kontak</h4>
            <p class="mb-4">
                Jika Anda memiliki pertanyaan atau kekhawatiran tentang Kebijakan Privasi kami, silakan hubungi 
                administrator sistem melalui email <a href="mailto:support@siar.id">support@siar.id</a>.
            </p>

            <p>
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>
    </div>
</div>
@endsection 