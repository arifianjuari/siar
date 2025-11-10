# Environment Variables untuk Laravel Cloud

Dokumen ini menjelaskan environment variables yang **WAJIB** dikonfigurasi di Laravel Cloud untuk mengatasi error 419 Page Expired.

## ⚠️ MASALAH UTAMA: SESSION_DRIVER=cookie

**Environment variable `SESSION_DRIVER=cookie` adalah penyebab utama error 419 Page Expired di Laravel Cloud.**

### Mengapa `SESSION_DRIVER=cookie` Bermasalah?

1. **Load Balancer**: Laravel Cloud menggunakan load balancer/proxy di depan aplikasi
2. **HTTPS Detection**: Dengan `cookie` driver, Laravel mungkin tidak mendeteksi HTTPS dengan benar
3. **Secure Cookie**: Session cookie mungkin tidak ter-set sebagai `secure` dengan benar
4. **CSRF Token**: CSRF token tidak valid karena session cookie tidak ter-set dengan benar

### Solusi: Gunakan `SESSION_DRIVER=database`

Dengan `database` driver:

- Session disimpan di database (lebih reliable)
- Tidak bergantung pada cookie attributes
- Lebih aman untuk production environment
- Bekerja dengan baik di balik load balancer

## Environment Variables yang Perlu Diubah

### 1. UBAH: SESSION_DRIVER

**Dari:**

```env
SESSION_DRIVER=cookie
```

**Menjadi:**

```env
SESSION_DRIVER=database
```

### 2. TAMBAHKAN: SESSION_SECURE_COOKIE

**Tambahkan:**

```env
SESSION_SECURE_COOKIE=null
```

**Penjelasan:**

- `null` akan membuat Laravel otomatis mendeteksi HTTPS
- Jangan set ke `false` jika menggunakan HTTPS
- Penting untuk secure cookie di production

### 3. PERBAIKI: APP_DOMAIN dan SESSION_DOMAIN

**Dari:**

```env
APP_DOMAIN=laravelcloud.com
SESSION_DOMAIN=.laravelcloud.com
```

**Menjadi:**

```env
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud
```

**Penjelasan:**

- Domain yang benar adalah `laravel.cloud` (dengan titik), bukan `laravelcloud.com`
- `SESSION_DOMAIN` harus sesuai dengan domain yang sebenarnya
- Titik di depan (`.laravel.cloud`) penting untuk subdomain support

## Environment Variables Lengkap yang Direkomendasikan

```env
# Application
APP_NAME="siar"
APP_ENV=production
APP_DEBUG=false
APP_URL="https://siar-main-bot1z9.laravel.cloud"
APP_TIMEZONE=Asia/Jakarta
APP_KEY=base64:Oto5UTUNnofMt6xxnRG4XS9PgKcJzdkXAgI2VqJKxB0=

# URL Configuration
APP_URL_SCHEME=https://
APP_URL_BASE=siar-main-bot1z9.laravel.cloud
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud

# Session Configuration (PENTING!)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=null

# Cache Configuration
CACHE_DRIVER=database
CACHE_STORE=database
SCHEDULE_CACHE_DRIVER=database

# Queue Configuration
QUEUE_CONNECTION=database

# Database (disediakan oleh Laravel Cloud)
DB_CONNECTION=mysql
DB_HOST=db-a0523851-f5e1-4149-84a3-42d156290586.ap-southeast-1.db.laravel.cloud
DB_PORT=3306
DB_DATABASE=main
DB_USERNAME=ipugqto1eo6kqgra
DB_PASSWORD=uEGDOge0X6eUQTgv1Giy

# Logging
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
LOG_CHANNEL=laravel-cloud-socket
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter

# Vite
VITE_APP_NAME="${APP_NAME}"
```

## Langkah-Langkah Setelah Mengubah Environment Variables

### 1. Update Environment Variables di Laravel Cloud

1. Buka Laravel Cloud dashboard
2. Buka aplikasi Anda
3. Buka bagian **Environment Variables**
4. Di bagian **Custom Variables**:
   - **HAPUS** `SESSION_DRIVER=cookie` (jika ada)
   - **TAMBAHKAN** `SESSION_SECURE_COOKIE=null` (jika belum ada)
   - **OVERRIDE** `APP_DOMAIN=laravel.cloud` (jika injected menggunakan `laravelcloud.com`)
   - **OVERRIDE** `SESSION_DOMAIN=.laravel.cloud` (jika injected menggunakan `.laravelcloud.com`)
5. **Save** perubahan

**Catatan Penting:**
- Jangan tambahkan `SESSION_DRIVER` di custom variables
- Biarkan injected `SESSION_DRIVER=database` digunakan
- Jika perlu override injected variables, pastikan nilainya benar

### 2. Deploy Ulang Aplikasi

Setelah mengubah environment variables, deploy ulang aplikasi:

- Klik **Deploy** atau **Rebuild** di Laravel Cloud dashboard
- Tunggu proses build selesai

### 3. Jalankan Migration

Pastikan tabel `sessions` sudah dibuat:

```bash
php artisan migrate --force
```

Ini akan membuat tabel `sessions` jika belum ada (migration `2025_04_26_211145_create_sessions_table`).

### 4. Clear dan Rebuild Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

### 5. Test Login

1. Clear cookies dan cache browser (atau gunakan mode incognito)
2. Buka `https://siar-main-bot1z9.laravel.cloud/login`
3. Coba login
4. Verifikasi tidak ada error 419

## Checklist Environment Variables

Pastikan environment variables berikut sudah benar:

### Injected Variables (tidak bisa diubah, hanya bisa di-override):
- [ ] `SESSION_DRIVER=database` ✅ (biarkan seperti ini, jangan override dengan `cookie`)
- [ ] `CACHE_DRIVER=database` ✅
- [ ] `QUEUE_CONNECTION=database` ✅

### Custom Variables (yang perlu Anda set):
- [ ] **TIDAK ADA** `SESSION_DRIVER` di custom variables (biarkan injected digunakan)
- [ ] `SESSION_SECURE_COOKIE=null` (tambahkan jika belum ada)
- [ ] `APP_DOMAIN=laravel.cloud` (override jika injected menggunakan `laravelcloud.com`)
- [ ] `SESSION_DOMAIN=.laravel.cloud` (override jika injected menggunakan `.laravelcloud.com`)
- [ ] `APP_URL` menggunakan HTTPS
- [ ] Tabel `sessions` sudah dibuat (jalankan `php artisan migrate --force`)

### Verifikasi:
- [ ] Di custom variables, **TIDAK ADA** `SESSION_DRIVER=cookie`
- [ ] Injected `SESSION_DRIVER=database` digunakan (tidak di-override)

## Mengapa Perubahan Ini Penting?

### SESSION_DRIVER=database

- **Lebih Reliable**: Session disimpan di database, tidak bergantung pada cookie
- **Load Balancer Friendly**: Bekerja dengan baik di balik load balancer
- **Production Ready**: Standar untuk production environment
- **CSRF Token**: CSRF token akan valid karena session tersimpan dengan benar

### SESSION_SECURE_COOKIE=null

- **Auto Detection**: Laravel otomatis mendeteksi HTTPS
- **Secure Cookie**: Cookie akan ter-set sebagai `secure` jika HTTPS terdeteksi
- **TrustProxies**: Bekerja dengan baik bersama `TrustProxies` middleware

### APP_DOMAIN dan SESSION_DOMAIN yang Benar

- **Domain Match**: Domain harus sesuai dengan domain yang sebenarnya
- **Subdomain Support**: Titik di depan `SESSION_DOMAIN` penting untuk subdomain
- **Cookie Domain**: Browser akan mengirim cookie ke domain yang benar

## Troubleshooting

### Jika Masih Error 419 Setelah Perubahan

1. **Verifikasi Environment Variables:**

   - Pastikan semua environment variables sudah benar
   - Pastikan sudah di-save di Laravel Cloud dashboard

2. **Deploy Ulang:**

   - Deploy ulang aplikasi setelah mengubah environment variables
   - Tunggu proses build selesai

3. **Clear Browser:**

   - Clear cookies dan cache browser
   - Coba di browser lain atau mode incognito

4. **Cek Logs:**

   - Buka logs aplikasi di Laravel Cloud dashboard
   - Cari error terkait session atau CSRF

5. **Verifikasi Tabel Sessions:**
   ```bash
   php artisan tinker
   >>> DB::table('sessions')->count();
   ```
   Jika error, berarti tabel belum dibuat.

## Referensi

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel CSRF Protection](https://laravel.com/docs/csrf)
- [Laravel Cloud Documentation](https://laravel.com/docs/cloud)
