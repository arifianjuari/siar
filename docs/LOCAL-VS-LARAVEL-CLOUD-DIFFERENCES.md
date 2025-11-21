# Perbedaan Environment Local vs Laravel Cloud - Troubleshooting Guide

**Date:** November 21, 2025  
**Issue:** Login berhasil di local tapi gagal di Laravel Cloud  
**Status:** 🔍 Analysis & Solutions

## 🎯 Mengapa Berbeda?

Aplikasi Laravel yang berjalan sempurna di local environment bisa gagal di Laravel Cloud karena **perbedaan fundamental** dalam konfigurasi infrastructure dan environment.

## 📊 Perbandingan Environment

| Aspek              | Local (siar.test) | Laravel Cloud                    |
| ------------------ | ----------------- | -------------------------------- |
| **Protocol**       | HTTP              | HTTPS (wajib)                    |
| **Domain**         | siar.test         | your-app.laravel.cloud           |
| **Session Driver** | file              | **Harus database**               |
| **Session Secure** | false             | **Harus true/auto**              |
| **APP_DEBUG**      | true              | **Harus false**                  |
| **Cache**          | Persistent        | Ephemeral (hilang saat redeploy) |
| **Storage**        | Local disk        | S3/Cloud storage                 |
| **Database**       | Local MySQL       | Cloud MySQL                      |
| **Permissions**    | 777 (dev)         | 755 (production)                 |
| **Environment**    | .env file         | Cloud variables                  |

## 🔴 Masalah Umum & Penyebabnya

### 1. **Login Gagal / Redirect Loop**

#### Penyebab:

- ❌ Session cookie tidak ter-set karena HTTPS/domain mismatch
- ❌ `SESSION_SECURE_COOKIE` tidak dikonfigurasi dengan benar
- ❌ `SESSION_DOMAIN` tidak sesuai dengan domain cloud
- ❌ Session driver `file` tidak persistent di cloud

#### Gejala:

```
1. Login berhasil (log menunjukkan "Login: Superadmin authenticated")
2. Redirect ke dashboard
3. Langsung redirect balik ke /login
4. Session tidak persist antar request
```

#### Solusi:

```env
# Di Laravel Cloud Environment Variables
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=
SESSION_SAME_SITE=lax
```

### 2. **CSRF Token Mismatch (419 Error)**

#### Penyebab:

- ❌ Session tidak persist (lihat #1)
- ❌ Cookie tidak ter-set dengan benar
- ❌ Domain/subdomain mismatch

#### Solusi:

```env
# Pastikan session configuration benar
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Jangan set SESSION_DOMAIN kecuali perlu subdomain support
SESSION_DOMAIN=
```

### 3. **500 Internal Server Error**

#### Penyebab:

- ❌ `APP_KEY` tidak di-set
- ❌ Database credentials salah
- ❌ Migration belum dijalankan
- ❌ Config cache menggunakan nilai local

#### Solusi:

```bash
# Generate APP_KEY di local
php artisan key:generate --show

# Set di Laravel Cloud environment variables
APP_KEY=base64:YOUR_GENERATED_KEY

# Jalankan migration
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan config:cache
```

### 4. **File Upload / Storage Issues**

#### Penyebab:

- ❌ Storage menggunakan local disk
- ❌ Symlink tidak dibuat
- ❌ Permissions tidak sesuai

#### Solusi:

```env
# Gunakan S3 atau cloud storage
FILESYSTEM_DISK=s3

# Atau pastikan storage symlink dibuat di build script
php artisan storage:link
```

### 5. **Cache Tidak Berfungsi**

#### Penyebab:

- ❌ File cache hilang saat redeploy
- ❌ Cache driver tidak persistent

#### Solusi:

```env
# Gunakan Redis atau database untuk cache
CACHE_DRIVER=redis
# atau
CACHE_DRIVER=database
```

## ✅ Solusi Lengkap untuk Login Issue

### Step 1: Set Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Variables**:

```env
# Application
APP_NAME=SIAR
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.laravel.cloud

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Session (PENTING!)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=
SESSION_SAME_SITE=lax

# Cache
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Features
TELESCOPE_ENABLED=false
ACTIVITY_LOGGER_ENABLED=true
```

### Step 2: Pastikan Migration Sessions Table

Buat migration untuk sessions table jika belum ada:

```bash
php artisan session:table
php artisan migrate --force
```

Atau cek apakah sudah ada:

```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

### Step 3: Update Build Script

Pastikan `.laravel-cloud-build.sh` include:

```bash
#!/bin/bash
set -e

echo "🚀 Starting Laravel Cloud Build..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Clear old caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force --no-interaction

# Seed essential data
php artisan db:seed --class=ModuleSeeder --force --no-interaction || true

# Create storage symlink
php artisan storage:link || true

# Build assets
npm ci
npm run build

# Create optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set permissions
chmod -R 755 storage bootstrap/cache

echo "✅ Build completed successfully!"
```

### Step 4: Deploy & Clear Cache

```bash
# Push ke GitHub
git add .
git commit -m "Fix Laravel Cloud configuration"
git push origin main

# Di Laravel Cloud Dashboard:
# 1. Trigger redeploy
# 2. Tunggu build selesai
# 3. Jalankan di console:
php artisan config:clear
php artisan config:cache
```

### Step 5: Verifikasi

Buat route debug untuk verifikasi (hapus setelah testing):

```php
// routes/web.php
Route::get('/debug-cloud', function() {
    return response()->json([
        'environment' => app()->environment(),
        'app_url' => config('app.url'),
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_same_site' => config('session.same_site'),
        'db_connection' => config('database.default'),
        'cache_driver' => config('cache.default'),
        'sessions_table_exists' => Schema::hasTable('sessions'),
        'sessions_count' => DB::table('sessions')->count(),
    ]);
})->middleware('web');
```

Akses: `https://your-app.laravel.cloud/debug-cloud`

Expected output:

```json
{
  "environment": "production",
  "app_url": "https://your-app.laravel.cloud",
  "session_driver": "database",
  "session_domain": null,
  "session_secure": true,
  "session_same_site": "lax",
  "db_connection": "mysql",
  "cache_driver": "database",
  "sessions_table_exists": true,
  "sessions_count": 0
}
```

## 🔍 Debugging Checklist

Jika login masih gagal, cek satu per satu:

### Browser (Developer Tools - F12)

- [ ] **Application > Cookies**: Cookie `siar_session` ada dan ter-set
- [ ] **Cookie Domain**: Sesuai dengan domain aplikasi
- [ ] **Cookie Secure**: ✅ (karena HTTPS)
- [ ] **Cookie SameSite**: Lax atau Strict
- [ ] **Network > Request Headers**: Cookie ter-kirim di setiap request
- [ ] **Console**: Tidak ada JavaScript error

### Laravel Cloud Dashboard

- [ ] **Environment Variables**: Semua variable sudah di-set dengan benar
- [ ] **Database**: Status "Running" dan accessible
- [ ] **Logs**: Tidak ada error saat login
- [ ] **Build Logs**: Migration berhasil dijalankan
- [ ] **Deployment**: Status "Deployed" (bukan "Failed")

### Database

- [ ] **Sessions table**: Exists dan writable
- [ ] **Users table**: Ada data user untuk login
- [ ] **Roles table**: Role superadmin exists
- [ ] **Tenants table**: Minimal 1 tenant exists

### Application

```bash
# Via Laravel Cloud console atau tinker
php artisan tinker

# Cek session config
>>> config('session.driver');  // "database"
>>> config('session.domain');  // null
>>> config('session.secure');  // true atau null

# Cek user
>>> $user = \App\Models\User::where('email', 'superadmin@siar.com')->first();
>>> $user->isSuperadmin();  // true
>>> $user->role;  // Role object
>>> $user->tenant;  // Tenant object

# Test session
>>> session()->put('test', 'value');
>>> session()->save();
>>> session()->get('test');  // "value"

# Cek sessions table
>>> DB::table('sessions')->count();  // Angka (bukan error)
```

## 🎯 Common Mistakes

### ❌ JANGAN Lakukan Ini:

```env
# SALAH - Hardcode domain yang salah
SESSION_DOMAIN=.laravelcloud.com

# SALAH - Force secure di environment variable
SESSION_SECURE_COOKIE=true

# SALAH - Gunakan file driver di production
SESSION_DRIVER=file
CACHE_DRIVER=file

# SALAH - Debug mode di production
APP_DEBUG=true

# SALAH - Gunakan local database credentials
DB_HOST=127.0.0.1
DB_DATABASE=siar_dev
```

### ✅ LAKUKAN Ini:

```env
# BENAR - Biarkan Laravel auto-detect
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=

# BENAR - Gunakan persistent driver
SESSION_DRIVER=database
CACHE_DRIVER=database

# BENAR - Production mode
APP_DEBUG=false

# BENAR - Gunakan cloud database credentials
DB_HOST=your-cloud-db-host
DB_DATABASE=your-cloud-db-name
```

## 📝 Testing Workflow

### 1. Test di Local Dulu

```bash
# Simulasi production environment di local
cp .env .env.backup
nano .env

# Set seperti production
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=database
CACHE_DRIVER=database

# Test
php artisan migrate:fresh --seed
php artisan serve

# Login dan test semua fitur
# Jika berhasil, baru deploy ke cloud
```

### 2. Deploy ke Cloud

```bash
# Restore local .env
mv .env.backup .env

# Push ke GitHub
git add .
git commit -m "Production-ready configuration"
git push origin main

# Deploy via Laravel Cloud dashboard
```

### 3. Verify di Cloud

```bash
# Akses debug endpoint
curl https://your-app.laravel.cloud/debug-cloud

# Test login
# - Clear browser cache & cookies
# - Login dengan credentials
# - Cek session cookie ter-set
# - Cek tidak redirect ke login lagi
```

## 🆘 Jika Masih Error

Kumpulkan informasi berikut:

1. **Environment Variables** (screenshot dari Laravel Cloud dashboard)
2. **Debug Output** dari `/debug-cloud` endpoint
3. **Browser DevTools**:
   - Network tab (POST /login request & response)
   - Application > Cookies (screenshot)
   - Console errors (jika ada)
4. **Laravel Cloud Logs**:
   - Application logs (error terbaru)
   - Build logs (migration status)
5. **Database Status** (dari Laravel Cloud dashboard)

## 🔗 Related Documentation

- [laravel-cloud-session-fix.md](./laravel-cloud-session-fix.md) - Session persistence fix
- [LARAVEL-CLOUD-500-ERROR-FIX.md](./LARAVEL-CLOUD-500-ERROR-FIX.md) - 500 error troubleshooting
- [419-ERROR-CSRF-FIX.md](./419-ERROR-CSRF-FIX.md) - CSRF token issues
- [cookie-domain-fix.md](./cookie-domain-fix.md) - Cookie domain configuration

## 📌 Key Takeaways

1. **HTTPS vs HTTP**: Laravel Cloud selalu HTTPS, session cookies harus secure
2. **Session Driver**: File driver tidak persistent di cloud, gunakan database
3. **Environment Variables**: Harus di-set di cloud dashboard, bukan .env file
4. **Cache**: File cache hilang saat redeploy, gunakan database/redis
5. **Domain**: Jangan hardcode SESSION_DOMAIN, biarkan auto-detect
6. **Debug Mode**: Selalu false di production untuk security
7. **Migration**: Harus dijalankan otomatis via build script
8. **Testing**: Test dengan production config di local sebelum deploy

---

**Last Updated:** November 21, 2025  
**Tested On:** Laravel Cloud, Laravel 10.x  
**Status:** ✅ Verified Solution
