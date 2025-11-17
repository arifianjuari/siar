# Panduan Deploy SIAR ke Laravel Cloud

Dokumen ini menjelaskan langkah-langkah untuk menghubungkan aplikasi SIAR ke Laravel Cloud.

## Prasyarat

1. Repository GitHub sudah ditautkan ke Laravel Cloud
2. Akun Laravel Cloud aktif
3. Aplikasi sudah dibuat di Laravel Cloud dashboard

## Langkah 1: Konfigurasi Environment Variables

Di Laravel Cloud dashboard, buka bagian **Environment Variables** dan tambahkan konfigurasi berikut:

### Konfigurasi Dasar Aplikasi

```env
APP_NAME=SIAR
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app-name.laravelcloud.com
APP_TIMEZONE=Asia/Jakarta
```

**Catatan:**

- `APP_KEY` akan otomatis di-generate oleh Laravel Cloud saat pertama kali deploy
- `APP_URL` sesuaikan dengan domain yang diberikan Laravel Cloud

### Konfigurasi Database

Laravel Cloud menyediakan database MySQL secara otomatis. Gunakan environment variables yang disediakan:

```env
DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
```

**Catatan:** Laravel Cloud biasanya menyediakan environment variables untuk database dengan format `${DB_HOST}`, `${DB_DATABASE}`, dll. Gunakan format tersebut.

### Konfigurasi Multi-Tenant

Untuk aplikasi multi-tenant, tambahkan konfigurasi berikut:

```env
APP_URL_SCHEME=https://
APP_URL_BASE=your-app-name.laravelcloud.com
APP_DOMAIN=laravelcloud.com
SESSION_DOMAIN=.laravelcloud.com
```

**Catatan:** Sesuaikan dengan domain yang diberikan Laravel Cloud. Jika menggunakan custom domain, sesuaikan `APP_DOMAIN` dan `SESSION_DOMAIN`.

### Konfigurasi Session & Cache

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=null
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**Catatan:**

- `SESSION_SECURE_COOKIE=null` akan membuat Laravel otomatis mendeteksi HTTPS dan mengaktifkan secure cookie
- Jika menggunakan HTTPS, pastikan `SESSION_SECURE_COOKIE` tidak di-set ke `false`

### Konfigurasi Mail (Opsional)

Jika menggunakan email, konfigurasi sesuai provider email Anda:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Konfigurasi Logging

```env
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
```

## Langkah 2: Konfigurasi Build Settings

Di Laravel Cloud dashboard, pastikan build settings sudah benar:

### Build Command

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Deploy Script (Opsional)

Jika perlu menjalankan migrasi otomatis saat deploy:

```bash
php artisan migrate --force
```

**Peringatan:** Hati-hati dengan auto-migrate di production. Pastikan backup database sudah dilakukan.

## Langkah 3: Konfigurasi Storage

Laravel Cloud menggunakan storage yang persisten. Pastikan:

1. **Storage Link:** Tambahkan di build script:

   ```bash
   php artisan storage:link
   ```

2. **Permissions:** Pastikan folder storage memiliki permission yang benar:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

## Langkah 4: Konfigurasi Domain & Subdomain

Untuk aplikasi multi-tenant dengan subdomain:

### Opsi 1: Menggunakan Domain Laravel Cloud

Jika menggunakan domain default Laravel Cloud (misal: `your-app.laravelcloud.com`), konfigurasi subdomain di Laravel Cloud dashboard untuk setiap tenant.

### Opsi 2: Custom Domain

Jika menggunakan custom domain:

1. Tambahkan custom domain di Laravel Cloud dashboard
2. Konfigurasi DNS:
   - A Record: `@` → IP Laravel Cloud
   - CNAME Record: `*` → `your-app.laravelcloud.com` (untuk wildcard subdomain)
3. Update environment variables:
   ```env
   APP_URL=https://yourdomain.com
   APP_DOMAIN=yourdomain.com
   SESSION_DOMAIN=.yourdomain.com
   ```

## Langkah 5: Database Migration & Seeding

Setelah deploy pertama kali, jalankan migrasi dan seeding:

### ⚠️ PENTING: Flag --force Diperlukan di Production

Laravel memblokir migrasi di production untuk keamanan. Anda **HARUS** menggunakan flag `--force` untuk menjalankan migrasi di production.

### Via Laravel Cloud Dashboard

Gunakan fitur **"Run Command"** atau **"Artisan Commands"** di dashboard untuk menjalankan:

```bash
php artisan migrate --force
```

**Catatan:** Flag `--force` diperlukan karena aplikasi berjalan di environment `production`.

Setelah migrasi selesai, jalankan seeder (jika diperlukan):

```bash
php artisan db:seed --class=DatabaseSeeder --force
```

### Membuat Tabel Cache (Jika Menggunakan Database Cache)

Jika menggunakan `CACHE_DRIVER=database`, pastikan tabel cache sudah dibuat:

```bash
php artisan migrate --force
```

Migration `2025_11_10_125532_create_cache_table` akan membuat tabel `cache` dan `cache_locks`.

## Langkah 6: Konfigurasi Scheduler (Cron Jobs)

Untuk backup database otomatis, tambahkan cron job di Laravel Cloud:

### Via Environment Variables

Laravel Cloud biasanya memiliki fitur untuk menjalankan scheduler. Pastikan scheduler diaktifkan dan tambahkan:

```env
SCHEDULE_RUN=true
```

### Manual Cron (jika diperlukan)

Di Laravel Cloud dashboard, tambahkan cron job:

```bash
* * * * * cd /path-to-your-app && php artisan schedule:run >> /dev/null 2>&1
```

## Langkah 7: Konfigurasi File .gitignore

Pastikan file-file berikut tidak di-commit ke repository:

```
.env
.env.backup
.env.production
/storage/*.key
/storage/logs/*.log
/vendor
/node_modules
/public/hot
/public/storage
```

## Langkah 8: Verifikasi Deployment

Setelah deploy, verifikasi:

1. **Akses aplikasi:** Buka URL aplikasi di browser
2. **Cek logs:** Lihat logs di Laravel Cloud dashboard untuk error
3. **Test database:** Pastikan koneksi database berfungsi
4. **Test multi-tenant:** Coba akses dengan subdomain tenant

## Troubleshooting

### Error: Database Connection Failed

- Pastikan environment variables database sudah benar
- Cek apakah database sudah dibuat di Laravel Cloud
- Verifikasi credentials database

### Error: Storage Permission Denied

- Pastikan folder `storage` dan `bootstrap/cache` memiliki permission 775
- Jalankan `php artisan storage:link` di build script

### Error: Subdomain Tidak Berfungsi

- Pastikan wildcard subdomain sudah dikonfigurasi di DNS
- Cek konfigurasi `APP_DOMAIN` dan `SESSION_DOMAIN`
- Verifikasi middleware `TenantMiddleware` berfungsi dengan benar

### Error: Session Tidak Berfungsi

- Pastikan `SESSION_DRIVER=database`
- Jalankan migrasi untuk membuat tabel `sessions`:
  ```bash
  php artisan migrate
  ```
- Cek konfigurasi `SESSION_DOMAIN`

### Error: Class "Laravel\Telescope\TelescopeApplicationServiceProvider" not found

Error ini terjadi karena Laravel Telescope adalah dev dependency dan tidak tersedia di production.

**Solusi:** File `TelescopeServiceProvider` sudah diperbaiki untuk mengecek apakah class tersedia sebelum digunakan. Jika masih terjadi error:

1. Pastikan file `app/Providers/TelescopeServiceProvider.php` sudah di-update dengan versi terbaru
2. Clear cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Rebuild aplikasi di Laravel Cloud

### Error: 404 Not Found (nginx)

Jika Anda mendapatkan error **404 Not Found** dari nginx saat mengakses domain aplikasi:

**Solusi:**

1. **Clear Route Cache** - Jalankan di Laravel Cloud dashboard (Artisan Commands):

   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:cache
   php artisan config:cache
   ```

2. **Verifikasi Environment Variables** - Pastikan `APP_URL` sesuai dengan domain Laravel Cloud:

   ```env
   APP_URL=https://siar-main-bot1z9.laravel.cloud
   ```

3. **Test Route Spesifik** - Coba akses route spesifik:

   - `https://siar-main-bot1z9.laravel.cloud/login` - Harus menampilkan halaman login
   - `https://siar-main-bot1z9.laravel.cloud/register` - Harus menampilkan halaman register

4. **Rebuild Aplikasi** - Jika masih error, coba rebuild aplikasi di Laravel Cloud dashboard

5. **Cek Logs** - Buka bagian **Logs** di Laravel Cloud dashboard untuk melihat error detail

**Lihat dokumentasi lengkap:** [Troubleshooting Guide](laravel-cloud-troubleshooting.md)

## Catatan Penting

1. **Jangan commit file `.env`** ke repository
2. **Backup database** sebelum menjalankan migrasi di production
3. **Test di staging environment** terlebih dahulu jika tersedia
4. **Monitor logs** secara berkala untuk mendeteksi error
5. **Update dependencies** secara berkala untuk keamanan

## Referensi

- [Laravel Cloud Documentation](https://laravel.com/docs/cloud)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Multi-Tenant Architecture Best Practices](https://laravel.com/docs/multi-tenancy)
