# Quick Start: Deploy SIAR ke Laravel Cloud

Panduan cepat untuk menghubungkan aplikasi SIAR ke Laravel Cloud.

## Langkah Cepat (5 Menit)

### 1. Di Laravel Cloud Dashboard

1. Buka aplikasi Anda di Laravel Cloud dashboard
2. Pastikan repository GitHub sudah terhubung
3. Buka bagian **Environment Variables**

### 2. Tambahkan Environment Variables

Copy dan paste environment variables berikut ke Laravel Cloud:

```env
# Aplikasi
APP_NAME=SIAR
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.laravelcloud.com
APP_TIMEZONE=Asia/Jakarta

# Database (gunakan variable dari Laravel Cloud)
DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

# Multi-Tenant (sesuaikan dengan domain Anda)
APP_URL_SCHEME=https://
APP_URL_BASE=your-app-name.laravelcloud.com
APP_DOMAIN=laravelcloud.com
SESSION_DOMAIN=.laravelcloud.com

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Penting:** Ganti `your-app-name.laravelcloud.com` dengan domain aplikasi Anda di Laravel Cloud.

### 3. Konfigurasi Build Script (Opsional)

Jika Laravel Cloud mendukung custom build script, gunakan file `.laravel-cloud-build.sh` yang sudah disediakan.

### 4. Deploy

1. Klik **Deploy** di Laravel Cloud dashboard
2. Tunggu proses build selesai
3. Cek logs jika ada error

### 5. Setup Database

Setelah deploy pertama, jalankan migrasi:

1. Buka **Artisan Commands** atau **Run Command** di dashboard
2. Jalankan dengan flag `--force` (wajib di production):
   ```bash
   php artisan migrate --force
   ```
3. Jika perlu seeder:
   ```bash
   php artisan db:seed --class=DatabaseSeeder --force
   ```

**⚠️ PENTING:** Flag `--force` **WAJIB** digunakan karena aplikasi berjalan di environment production. Tanpa flag ini, migrasi akan dibatalkan.

### 6. Verifikasi

1. Buka URL aplikasi di browser
2. Test login
3. Cek apakah aplikasi berfungsi dengan baik

## Troubleshooting Cepat

### Error: Database Connection Failed

- Pastikan environment variables database sudah benar
- Cek apakah database sudah dibuat di Laravel Cloud

### Error: 500 Internal Server Error

- Cek logs di Laravel Cloud dashboard
- Pastikan `APP_KEY` sudah di-generate
- Pastikan migrasi sudah dijalankan

### Error: Storage Permission Denied

- Pastikan build script menjalankan `php artisan storage:link`
- Cek permission folder storage

## Dokumentasi Lengkap

Untuk panduan lengkap, lihat:

- [Panduan Lengkap](laravel-cloud-setup.md)
- [Checklist Deployment](laravel-cloud-checklist.md)

## Butuh Bantuan?

Jika mengalami masalah, cek:

1. Logs di Laravel Cloud dashboard
2. Dokumentasi Laravel Cloud: https://laravel.com/docs/cloud
3. Error messages di browser console
