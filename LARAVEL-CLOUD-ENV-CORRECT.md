# Laravel Cloud - Correct Environment Variables

**Date:** November 21, 2025  
**Issue:** SESSION_DRIVER conflict between custom and injected variables  
**Solution:** Use cookie session with proper configuration

## ⚠️ Masalah yang Ditemukan

```env
# Custom Variables (Anda set)
SESSION_DRIVER=database

# Injected Variables (Laravel Cloud auto-set)
SESSION_DRIVER=cookie  ← INI YANG MENANG!
```

**Injected variables selalu override custom variables!**

## ✅ Solusi: Custom Environment Variables yang Benar

Copy-paste ini ke **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**:

```env
# Application Key
APP_KEY=base64:S/oT2EoBYo4aynSl9g+6qAeFLVvSQ9FenZ9BvRmw80A=

# Session Configuration (PENTING!)
# JANGAN set SESSION_DRIVER - biarkan Laravel Cloud yang set
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.laravel.cloud
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# Logging
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Features
TELESCOPE_ENABLED=false
ACTIVITY_LOGGER_ENABLED=true

# Drivers (yang tidak di-override Laravel Cloud)
BROADCAST_DRIVER=log
QUEUE_CONNECTION=database

# Cache - Gunakan yang sudah di-inject
# CACHE_DRIVER=database  ← HAPUS, sudah ada CACHE_STORE=database dari inject

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Redis (optional, jika dipakai)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# AWS (optional, jika dipakai)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Pusher (optional, jika dipakai)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## 📋 Yang Dihapus dari Custom Variables

Hapus ini karena **sudah di-inject otomatis** oleh Laravel Cloud:

```env
# ❌ HAPUS - Sudah di-inject
SESSION_DRIVER=database  # Conflict! Laravel Cloud set ke "cookie"
CACHE_DRIVER=database    # Sudah ada CACHE_STORE=database
MEMCACHED_HOST=127.0.0.1 # Tidak dipakai di cloud
```

## 🔍 Penjelasan Injected Variables

Laravel Cloud otomatis inject variables ini (TIDAK PERLU di-set manual):

```env
# Application
APP_NAME="siar"
APP_ENV=production
APP_DEBUG=false
APP_URL="https://siar-beta-ctegvo.laravel.cloud"

# Logging (Laravel Cloud specific)
LOG_CHANNEL=laravel-cloud-socket
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter

# Database (Auto-configured)
DB_CONNECTION=mysql
DB_HOST=db-a0523851-f5e1-4149-84a3-42d156290586.ap-southeast-1.db.laravel.cloud
DB_PORT=3306
DB_DATABASE=main
DB_USERNAME=ipugqto1eo6kqgra
DB_PASSWORD=uEGDOge0X6eUQTgv1Giy

# Session (Auto-configured untuk cloud)
SESSION_DRIVER=cookie  ← Cookie session optimal untuk Laravel Cloud

# Cache
CACHE_STORE=database
SCHEDULE_CACHE_DRIVER=database

# Storage (Cloudflare R2)
FILESYSTEM_DISK=private
LARAVEL_CLOUD_DISK_CONFIG='...'
```

## ✅ Mengapa Cookie Session Lebih Baik di Laravel Cloud?

| Aspect            | Cookie Session            | Database Session                    |
| ----------------- | ------------------------- | ----------------------------------- |
| **Performance**   | ✅ Faster (no DB query)   | ❌ Slower (DB query setiap request) |
| **Scalability**   | ✅ Stateless              | ❌ Stateful (perlu DB)              |
| **Laravel Cloud** | ✅ Recommended            | ⚠️ Not recommended                  |
| **Security**      | ✅ Encrypted              | ✅ Encrypted                        |
| **Persistence**   | ✅ Persistent (di cookie) | ✅ Persistent (di DB)               |

**Cookie session di Laravel Cloud:**

- Encrypted dengan APP_KEY
- Secure flag otomatis (HTTPS)
- SameSite protection
- Tidak perlu DB query untuk session
- Lebih cepat dan scalable

## 🔧 Langkah-Langkah Fix

### 1. Update Custom Variables

Di **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**:

**Hapus:**

- `SESSION_DRIVER=database`
- `CACHE_DRIVER=database`
- `MEMCACHED_HOST=127.0.0.1`

**Update:**

```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.laravel.cloud
SESSION_SAME_SITE=lax
```

### 2. Redeploy

Klik **Deploy** atau **Redeploy** di Laravel Cloud Dashboard.

### 3. Clear Cache

Setelah deploy selesai, jalankan di console:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 4. Verifikasi

Buat route debug (temporary):

```php
// routes/web.php
Route::get('/debug-session', function() {
    return response()->json([
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_same_site' => config('session.same_site'),
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
    ]);
})->middleware('web');
```

Expected output:

```json
{
  "session_driver": "cookie",
  "session_domain": ".laravel.cloud",
  "session_secure": true,
  "session_same_site": "lax",
  "app_url": "https://siar-beta-ctegvo.laravel.cloud",
  "app_env": "production"
}
```

### 5. Test Login

1. Clear browser cookies & cache
2. Login dengan credentials
3. Cek di DevTools > Application > Cookies:
   - Cookie `siar_session` harus ada
   - Domain: `.laravel.cloud`
   - Secure: ✅
   - SameSite: Lax
4. Test navigasi ke halaman lain - tidak boleh redirect ke login

## 🎯 Konfigurasi Session yang Benar

### config/session.php

Pastikan file ini menggunakan environment variables:

```php
return [
    'driver' => env('SESSION_DRIVER', 'cookie'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production'),
    'http_only' => true,
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => false,
];
```

**Jangan hardcode values!** Selalu gunakan `env()`.

## ⚠️ Common Mistakes

### ❌ JANGAN:

```env
# SALAH - Override injected variable
SESSION_DRIVER=database

# SALAH - Domain tidak sesuai
SESSION_DOMAIN=siar.test

# SALAH - Hardcode secure
SESSION_SECURE_COOKIE=false
```

### ✅ LAKUKAN:

```env
# BENAR - Biarkan Laravel Cloud set SESSION_DRIVER
# (tidak perlu set di custom variables)

# BENAR - Domain untuk Laravel Cloud
SESSION_DOMAIN=.laravel.cloud

# BENAR - Auto-detect secure
SESSION_SECURE_COOKIE=true
```

## 📝 Catatan Penting

1. **Injected variables selalu menang** - Jangan set variable yang sudah di-inject
2. **Cookie session sudah aman** - Encrypted dengan APP_KEY
3. **Domain harus match** - Gunakan `.laravel.cloud` untuk semua subdomain
4. **Selalu redeploy** setelah ubah environment variables
5. **Clear browser cache** sebelum test

## 🔗 Related Files

- `config/session.php` - Session configuration
- `.laravel-cloud-build.sh` - Build script
- `routes/web.php` - Debug routes

---

**Last Updated:** November 21, 2025  
**Status:** ✅ Correct Configuration  
**Tested:** Laravel Cloud Production
