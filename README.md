# SIAR - Sistem Informasi Aplikasi Rumah Sakit (Multi-Tenant)

## Tentang Aplikasi

SIAR adalah sistem informasi multi-tenant yang dirancang untuk rumah sakit dan fasilitas kesehatan. Aplikasi ini memungkinkan beberapa organisasi kesehatan (tenant) untuk mengakses sistem melalui domain unik mereka dengan data yang terisolasi antar tenant.

## Fitur Utama

- **Multi-Tenant Architecture**: Setiap tenant (rumah sakit) memiliki data yang terisolasi dan aman.
- **Manajemen Modul**: Aktivasi dan nonaktivasi fitur spesifik per tenant.
- **Manajemen Pengguna & Hak Akses**: Kontrol akses pengguna berdasarkan role dan permission.
- **Backup Database Otomatis**: Jadwal backup harian untuk keamanan data.
- **Desain Modular**: Struktur kode yang memudahkan pengembangan modul baru.

## Struktur Modul

Setiap modul dalam aplikasi ini terdiri dari:

1. **Controller**: Menangani permintaan HTTP dan logika aplikasi.
2. **Model**: Representasi data dan relasi database.
3. **View (Blade)**: Tampilan antarmuka pengguna.
4. **Routes**: Pendefinisian endpoint HTTP.
5. **Permissions**: Kontrol akses berdasarkan role.

## Sistem Tenant & Isolasi Data

Aplikasi menggunakan beberapa teknik untuk isolasi data:

- **Global Scopes**: Filter data secara otomatis berdasarkan tenant_id.
- **Middleware Tenant**: Validasi akses per-tenant dan pengaturan konteks tenant.
- **Subdomain Routing**: Akses aplikasi melalui subdomain khusus per tenant.

## Pengembangan di macOS

Proyek dikembangkan menggunakan lingkungan macOS dengan konfigurasi sebagai berikut:

- **Server**: PHP built-in server pada port 8080 (`php -S localhost:8080 -t public/`)
- **Database**: MySQL lokal (bawaan macOS)
- **URL Aplikasi**: http://localhost:8080
- **Path Proyek**: /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar

## Keamanan

- **Authentication**: Sistem otentikasi pengguna dengan Laravel Fortify.
- **Authorization**: Kontrol akses berdasarkan role dan permission per modul.
- **Database Backup**: Jadwal backup otomatis setiap hari pukul 02:00 WIB.

## Pengembangan Modul Baru

Untuk menambahkan modul baru, ikuti langkah-langkah berikut:

1. Buat MVC di direktori `app/Http/Controllers/Modules/[NamaModul]/`.
2. Registrasi route di `routes/web.php` dalam grup `modules`.
3. Tambahkan view di `resources/views/modules/[NamaModul]/`.
4. Update database dengan menambahkan modul baru di tabel `modules`.
5. Pastikan setiap model menggunakan trait `BelongsToTenant` untuk isolasi data.

## Pengaturan Backup Database

Backup database otomatis dikonfigurasi menggunakan Laravel Scheduler:

- File backup disimpan di `storage/app/backups/`.
- Format nama file: `Y-m-d_His.sql` (contoh: 2023-05-15_020000.sql).
- Maksimal menyimpan 10 backup terbaru, file lama dihapus otomatis.

Untuk menjalankan scheduler secara manual:

```bash
./run_scheduler.sh
```

## Menjalankan Aplikasi di Berbagai Lingkungan

SIAR dapat dijalankan di tiga lingkungan berbeda:

### 1. Laravel Valet (Development)

Untuk menjalankan dengan Laravel Valet:

1. Pastikan Laravel Valet sudah terinstal dan berjalan
2. Link direktori proyek: `valet link siar`
3. Konfigurasi .env:
   ```
   APP_URL=http://siar.test
   APP_URL_SCHEME=http://
   APP_URL_BASE=siar.test
   APP_DOMAIN=siar.test
   SESSION_DOMAIN=.siar.test
   ```
4. Akses aplikasi di browser: `http://siar.test`
5. Untuk tenant: `http://[tenant-domain].siar.test`

### 2. Localhost (Development)

Untuk menjalankan di localhost:

1. Konfigurasi .env:
   ```
   APP_URL=http://127.0.0.1:8000
   APP_URL_SCHEME=http://
   APP_URL_BASE=127.0.0.1:8000
   APP_DOMAIN=127.0.0.1
   SESSION_DOMAIN=127.0.0.1
   ```
2. Jalankan server development: `php artisan serve`
3. Akses aplikasi di browser: `http://127.0.0.1:8000`
4. Tambahkan entri di file hosts untuk tenant:
   ```
   127.0.0.1 [tenant-domain].127.0.0.1
   ```
5. Akses tenant di browser: `http://[tenant-domain].127.0.0.1:8000`

### 3. Laravel Cloud (Production)

Untuk deploy ke Laravel Cloud:

1. Pastikan repository GitHub sudah ditautkan ke Laravel Cloud
2. Ikuti panduan lengkap di [docs/laravel-cloud-setup.md](docs/laravel-cloud-setup.md)
3. Konfigurasi environment variables di Laravel Cloud dashboard
4. Pastikan build script sudah dikonfigurasi
5. Jalankan migrasi dan seeder setelah deploy pertama
6. Gunakan checklist di [docs/laravel-cloud-checklist.md](docs/laravel-cloud-checklist.md)

**Panduan Lengkap:** Lihat [docs/laravel-cloud-setup.md](docs/laravel-cloud-setup.md)

### 4. Server Online Lainnya (Production)

Untuk menjalankan di server online (VPS/Shared Hosting):

1. Konfigurasi .env:
   ```
   APP_URL=https://siar.example.com
   APP_URL_SCHEME=https://
   APP_URL_BASE=siar.example.com
   APP_DOMAIN=example.com
   SESSION_DOMAIN=.example.com
   ```
2. Konfigurasi web server (Apache/Nginx) untuk domain dan subdomain
3. Pastikan subdomain wildcard dikonfigurasi untuk tenant
4. Akses aplikasi di browser: `https://siar.example.com`
5. Akses tenant di browser: `https://[tenant-domain].example.com`

## Pengembangan Lanjutan

Sistem ini dirancang untuk terus berkembang dengan modul-modul baru. Beberapa modul yang direncanakan:

- Manajemen Pasien
- Rekam Medis Elektronik
- Inventory & Farmasi
- Billing & Keuangan
- Integrasi BPJS

## Lisensi

Hak Cipta © 2023 SIAR. Seluruh hak dilindungi undang-undang. 