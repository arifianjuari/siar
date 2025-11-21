# Laravel Cloud 500 Error - Troubleshooting & Fix

**Date:** November 20, 2025  
**Issue:** 500 Internal Server Error setelah deployment berhasil  
**Status:** 🔧 In Progress

## Problem

Setelah deployment berhasil di Laravel Cloud, website menampilkan error:

```
Oops! An Error Occurred
The server returned a "500 Internal Server Error".
```

## Common Causes

Error 500 di Laravel Cloud biasanya disebabkan oleh:

1. **APP_KEY tidak di-set** - Laravel membutuhkan APP_KEY untuk enkripsi
2. **Database belum dikonfigurasi** - Connection string atau database belum dibuat
3. **Migrasi belum dijalankan** - Tabel database belum ada
4. **Environment variables kurang** - Variabel penting belum di-set
5. **Cache configuration** - Config cache mungkin menggunakan nilai lokal

## Solutions

### 1. Cek dan Set Environment Variables di Laravel Cloud

Pastikan environment variables berikut sudah di-set di Laravel Cloud dashboard:

**Required Variables:**

```bash
APP_NAME=SIAR
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE  # Generate dengan: php artisan key:generate --show
APP_DEBUG=false                    # PENTING: Set false di production!
APP_URL=https://your-app.laravel.app

DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password

LOG_CHANNEL=stack
LOG_LEVEL=error

CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

TELESCOPE_ENABLED=false            # Disable di production untuk performa
ACTIVITY_LOGGER_ENABLED=true
```

**Cara Set Environment Variables:**

1. Buka Laravel Cloud Dashboard
2. Pilih project Anda
3. Pergi ke **Settings** > **Environment Variables**
4. Tambahkan setiap variabel dengan nilai yang sesuai
5. Klik **Save** dan **Redeploy**

### 2. Generate APP_KEY (Jika Belum Ada)

Jalankan di local untuk generate key baru:

```bash
php artisan key:generate --show
```

Copy output (misalnya: `base64:abcdef123456...`) dan set sebagai `APP_KEY` di Laravel Cloud.

### 3. Pastikan Database Sudah Dibuat

Di Laravel Cloud, pastikan database sudah dibuat dan connection string sudah benar:

1. Cek **Database** section di dashboard
2. Pastikan database sudah provisioned
3. Copy database credentials ke environment variables
4. Test connection

### 4. Deploy Ulang dengan Build Script yang Sudah Diupdate

Build script sekarang sudah include:

- ✅ Run migrations automatically
- ✅ Seed essential data (ModuleSeeder)
- ✅ Clear config cache before creating new cache
- ✅ Set proper permissions

### 5. Check Laravel Cloud Logs

Untuk melihat error detail:

1. Buka Laravel Cloud Dashboard
2. Pilih project > **Logs**
3. Lihat **Application Logs** untuk error detail
4. Cari error terbaru yang terjadi saat Anda mengakses website

Common log locations:

- Application errors: `/storage/logs/laravel.log`
- Build logs: Tersedia di deployment history
- Web server logs: Cek di Laravel Cloud logs section

## Updated Build Script Features

Script `.laravel-cloud-build.sh` sekarang sudah ditingkatkan:

### Database Migration & Seeding

```bash
# Run database migrations
php artisan migrate --force --no-interaction || true

# Seed essential data
php artisan db:seed --class=ModuleSeeder --force --no-interaction || true
```

### Cache Management

```bash
# Clear old cache first
php artisan config:clear

# Then create new cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Production Permissions

```bash
# More restrictive permissions for production
chmod -R 755 storage bootstrap/cache
```

## Quick Fix Checklist

Lakukan langkah-langkah ini secara berurutan:

- [ ] **Set APP_KEY** di Laravel Cloud environment variables
- [ ] **Set APP_DEBUG=false** (penting untuk security!)
- [ ] **Set Database credentials** (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [ ] **Set APP_URL** ke URL production Anda
- [ ] **Push updated build script** ke GitHub
- [ ] **Trigger redeploy** di Laravel Cloud
- [ ] **Check logs** jika masih error
- [ ] **Run migrations manually** jika perlu (via Laravel Cloud console)

## Manual Commands (Jika Diperlukan)

Jika Anda punya akses ke Laravel Cloud console/terminal:

```bash
# Generate APP_KEY
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=ModuleSeeder --force

# Clear all caches
php artisan optimize:clear

# Recreate caches
php artisan optimize

# Check application
php artisan about
```

## Debug Mode (Temporary)

**HANYA untuk debugging, kembalikan ke false setelah selesai!**

Untuk melihat error detail, temporary set di environment variables:

```bash
APP_DEBUG=true
LOG_LEVEL=debug
```

Kemudian access website lagi untuk melihat error detail.

**PENTING:** Jangan lupa kembalikan ke:

```bash
APP_DEBUG=false
LOG_LEVEL=error
```

## Specific Fixes for SIAR

### Module Seeder

Aplikasi SIAR membutuhkan data module awal. Pastikan ModuleSeeder berhasil dijalankan:

```bash
php artisan db:seed --class=ModuleSeeder --force
```

Module yang harus ada:

- Dashboard
- User Management
- Risk Management
- Document Management
- SPO Management
- Activity Management
- Correspondence
- Work Unit
- Performance Management
- Product Management
- Kendali Mutu Biaya

### Tenant Configuration

Untuk superadmin access, pastikan minimal ada 1 tenant dummy atau Anda bisa bypass tenant check untuk superadmin.

### Permission System

SIAR menggunakan custom RBAC. Pastikan:

- Role hierarchy tables ada
- Permission tables termigrasi
- PermissionService bisa diakses

## Prevention

Untuk mencegah error di future deployments:

1. **Selalu test di staging** sebelum production
2. **Gunakan APP_DEBUG=false** di production
3. **Monitor logs** secara berkala
4. **Backup database** sebelum deployment
5. **Test migrations** di local dulu
6. **Set proper error tracking** (Sentry, Bugsnag, dll)

## Next Steps

1. **Set environment variables** di Laravel Cloud
2. **Push updated build script** (sudah saya update)
3. **Redeploy application**
4. **Check logs** untuk error detail
5. **Share error logs** dengan saya jika masih error

## Error Reporting

Jika masih error setelah langkah di atas, saya butuh:

1. **Laravel Cloud application logs** (copy exact error message)
2. **Build logs** (untuk cek apakah migration berhasil)
3. **Environment variables list** (tanpa values sensitif)
4. **Database status** (apakah sudah provisioned)

---

**File Updated:**

- `.laravel-cloud-build.sh` - Enhanced with migration, seeding, and better cache management

**Commit:** Ready to push

**Action Required:**

1. Set environment variables di Laravel Cloud
2. Push changes ke GitHub
3. Trigger redeploy
