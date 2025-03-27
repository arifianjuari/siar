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

## Menjalankan Aplikasi

```bash
# Instalasi dependencies
composer install

# Buat database
./create_db.sh

# Migrasi database
php artisan migrate

# Menjalankan seeder
php artisan db:seed

# Menjalankan aplikasi pada port 8080
./serve.sh

# Mengakses aplikasi via browser
http://localhost:8080
```

## Pengujian Domain Tenant

Untuk menguji fitur multi-tenant dengan subdomain di macOS, tambahkan entri berikut di file hosts:

```bash
sudo nano /etc/hosts
```

Tambahkan baris:
```
127.0.0.1    rs-contoh.localhost
```

Kemudian akses aplikasi melalui browser:
```
http://rs-contoh.localhost:8080
```

## Pengembangan Lanjutan

Sistem ini dirancang untuk terus berkembang dengan modul-modul baru. Beberapa modul yang direncanakan:

- Manajemen Pasien
- Rekam Medis Elektronik
- Inventory & Farmasi
- Billing & Keuangan
- Integrasi BPJS

## Lisensi

Hak Cipta © 2023 SIAR. Seluruh hak dilindungi undang-undang. 