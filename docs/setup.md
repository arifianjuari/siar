# Pengaturan Database

Dokumen ini menjelaskan pengaturan database untuk sistem multi-tenant multi-role berbasis shared database.

## Spesifikasi Database

- **Database yang digunakan**: MySQL (disarankan versi 5.7+ atau 8.x)
- **Jenis database**: Shared database (satu database untuk semua tenant)
- **Desain database**: Multi-tenant dengan isolasi data berbasis kolom tenant_id

## Lingkungan Pengembangan

Untuk pengembangan lokal, Anda dapat menggunakan salah satu dari berikut ini:

- **XAMPP**: Server Apache + MySQL + PHP untuk Windows/Linux/macOS
- **Laragon**: Alternatif XAMPP dengan fitur lebih lengkap (Windows)
- **Docker**: Containerized environment (semua platform)
- **Homebrew**: Package manager untuk macOS

## Konfigurasi Database

Konfigurasi database **harus** berasal dari file `.env`, bukan hard-coded dalam kode program. Ini memungkinkan konfigurasi yang berbeda antara lingkungan pengembangan dan produksi tanpa perlu mengubah kode.

Parameter konfigurasi utama meliputi:

- `DB_HOST`: Host database (biasanya 127.0.0.1 untuk pengembangan lokal)
- `DB_PORT`: Port database (default MySQL: 3306)
- `DB_DATABASE`: Nama database
- `DB_USERNAME`: Username untuk koneksi database
- `DB_PASSWORD`: Password untuk koneksi database

## Contoh Konfigurasi

Berikut adalah contoh konfigurasi di file `.env` untuk lingkungan pengembangan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant_dev
DB_USERNAME=root
DB_PASSWORD=
```

## Migrasi Database

Setelah mengatur koneksi database, jalankan migrasi untuk membuat skema:

```bash
php artisan migrate
```

Migrasi akan membuat tabel-tabel berikut:
- `tenants`: Tabel tenant (penyewa)
- `modules`: Tabel modul aplikasi
- `tenant_modules`: Relasi tenant-modul
- `roles`: Tabel role/peran
- `role_module_permissions`: Izin modul berdasarkan peran
- `users`: Tabel pengguna (dengan relasi ke tenant dan role)

## Pengaturan Database Produksi

Saat memindahkan aplikasi ke server produksi, Anda hanya perlu mengubah nilai dalam file `.env` sesuai dengan konfigurasi server hosting Anda, tanpa perlu mengubah kode program.

Contoh konfigurasi untuk lingkungan produksi:

```env
DB_CONNECTION=mysql
DB_HOST=nama_host_database_produksi
DB_PORT=3306
DB_DATABASE=nama_database_produksi
DB_USERNAME=username_produksi
DB_PASSWORD=password_yang_aman
```

## Keamanan

- Pastikan password database Anda kuat di lingkungan produksi
- Batasi akses database hanya dari server aplikasi
- Lakukan backup database secara berkala
- Jangan menyimpan kredensial database di repositori kode (file .env harus ada dalam .gitignore) 