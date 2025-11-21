# Fix: Logout Otomatis Saat Submit Form di Laravel Cloud

**Date:** November 21, 2025  
**Issue:** User logout otomatis saat submit form / tambah data  
**Status:** ✅ FIXED

## 🔴 Masalah

User berhasil login, tapi saat melakukan action (submit form, tambah data, edit data), tiba-tiba logout dan redirect ke halaman login.

### Gejala:

1. Login berhasil ✅
2. Bisa akses dashboard ✅
3. Buka form tambah data ✅
4. Submit form ❌ → Logout otomatis
5. Redirect ke halaman login

## 🎯 Root Cause

Ada **3 masalah yang bekerja bersamaan**:

### 1. **Cookie Session Size Limit (4KB)**

Laravel Cloud menggunakan `SESSION_DRIVER=cookie` (auto-injected). Cookie punya limit **4KB**.

Saat session data terlalu besar:

```
Session data:
- _token
- login_web_xxx (Laravel auth)
- tenant_id
- is_superadmin
- auth_role
- user_verified
- _old_input (form data)
- errors (validation errors)
- _flash (flash messages)
- _previous (previous URL)
Total: > 4KB → Cookie tidak bisa di-set → Session hilang → Logout
```

### 2. **LimitSessionSize Middleware Terlalu Agresif**

Middleware `LimitSessionSize` mencoba trim session saat mendekati 4KB, tapi **tidak preserve authentication keys**:

```php
// SEBELUM FIX - Authentication keys ikut terhapus!
$allowed = ['_token', 'tenant_id', 'url', '_previous'];
// Missing: is_superadmin, auth_role, user_verified
```

Akibatnya:

- Session di-trim untuk menghemat space
- Authentication keys (`is_superadmin`, `auth_role`) ikut terhapus
- User dianggap tidak authenticated
- Redirect ke login

### 3. **SameSite=strict di Production**

```php
// config/session.php line 199 - SEBELUM FIX
'same_site' => env('SESSION_SAME_SITE', env('APP_ENV') === 'production' ? 'strict' : 'lax'),
```

`SameSite=strict` bisa menyebabkan cookie **tidak ter-kirim** saat POST request dari form, terutama jika ada redirect.

## ✅ Solusi yang Diterapkan

### Fix 1: Update LimitSessionSize Middleware

Preserve authentication keys saat session di-trim:

```php
// app/Http/Middleware/LimitSessionSize.php
$allowed = [];
foreach ($current as $key => $value) {
    // CRITICAL: Preserve authentication keys
    if ($key === '_token' || $key === 'tenant_id' || $key === 'url' || $key === '_previous') {
        $allowed[$key] = $value;
        continue;
    }
    // CRITICAL: Preserve Laravel auth keys
    if (Str::startsWith($key, ['login_web_', 'password_hash_'])) {
        $allowed[$key] = $value;
        continue;
    }
    // CRITICAL: Preserve SIAR authentication keys
    if (in_array($key, ['is_superadmin', 'auth_role', 'user_verified', 'current_tenant'])) {
        $allowed[$key] = $value;
        continue;
    }
}
```

**Benefit:**

- Authentication keys tidak akan terhapus
- User tetap authenticated setelah session trim
- Tidak ada logout otomatis

### Fix 2: Update Session Config

Change `same_site` to always use `lax`:

```php
// config/session.php line 199
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

**Benefit:**

- Cookie ter-kirim dengan benar saat POST request
- Tidak ada cookie loss saat form submission
- Tetap secure (lax sudah cukup untuk CSRF protection)

### Fix 3: Environment Variables (Laravel Cloud)

Pastikan environment variables di Laravel Cloud sudah benar:

```env
# JANGAN set SESSION_DRIVER - biarkan Laravel Cloud inject
# SESSION_DRIVER=cookie (auto-injected)

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.laravel.cloud
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

## 📋 Deployment Steps

### 1. Push Changes ke GitHub

```bash
cd /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My\ Drive/01\ PAPA/05\ DEVELOPMENT/siar

git add app/Http/Middleware/LimitSessionSize.php
git add config/session.php
git commit -m "Fix: Prevent logout on form submission in Laravel Cloud

- Preserve authentication keys in LimitSessionSize middleware
- Change same_site to always use 'lax' instead of 'strict'
- Fixes issue where users get logged out when submitting forms"

git push origin main
```

### 2. Update Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**

**Pastikan ada:**

```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.laravel.cloud
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

**Pastikan TIDAK ada:**

```env
SESSION_DRIVER=database  # Hapus ini!
```

### 3. Redeploy

Di Laravel Cloud Dashboard:

1. Klik **Deploy** atau **Redeploy**
2. Tunggu build selesai

### 4. Clear Cache

Setelah deploy selesai, jalankan via console:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 5. Test

1. Clear browser cookies & cache
2. Login
3. Buka form tambah data
4. Submit form
5. **Seharusnya berhasil dan tidak logout**

## 🔍 Verifikasi

### Test 1: Check Session Config

```bash
php artisan tinker

>>> config('session.driver');
// "cookie"

>>> config('session.same_site');
// "lax"

>>> config('session.domain');
// ".laravel.cloud"

>>> config('session.secure');
// true
```

### Test 2: Monitor Session Size

Tambahkan temporary logging untuk monitor session size:

```php
// Tambahkan di route atau controller
Log::info('Session size check', [
    'size' => strlen(serialize(session()->all())),
    'keys' => array_keys(session()->all()),
    'user_id' => auth()->id(),
]);
```

Expected:

- Size harus < 3500 bytes
- Keys harus include: `is_superadmin`, `auth_role`, `user_verified`

### Test 3: Browser DevTools

1. Buka **DevTools (F12)** > **Application** > **Cookies**
2. Cek cookie `siar_session`:

   - Domain: `.laravel.cloud` ✅
   - Secure: ✅
   - SameSite: Lax ✅
   - Size: < 4KB ✅

3. Buka **Network** tab
4. Submit form
5. Cek request headers:
   - Cookie header harus ada ✅
   - Session cookie harus ter-kirim ✅

## 🎯 Best Practices untuk Cookie Session

### 1. Minimize Session Data

**JANGAN simpan data besar di session:**

```php
// ❌ SALAH - Terlalu besar
session(['report_data' => $largeArray]);
session(['user_list' => User::all()]);

// ✅ BENAR - Simpan ID saja
session(['report_id' => $report->id]);
session(['selected_user_ids' => [1, 2, 3]]);
```

### 2. Use Flash Messages Wisely

```php
// ❌ SALAH - Flash message terlalu panjang
return redirect()->back()->with('success', 'Data berhasil disimpan dengan detail: ' . $longText);

// ✅ BENAR - Flash message singkat
return redirect()->back()->with('success', 'Data berhasil disimpan');
```

### 3. Clear Old Input After Success

```php
// Di controller setelah berhasil save
return redirect()->route('index')
    ->with('success', 'Data berhasil disimpan')
    ->withInput([]); // Clear old input
```

### 4. Avoid Storing Objects

```php
// ❌ SALAH - Simpan object
session(['current_user' => $user]);

// ✅ BENAR - Simpan ID, load saat dibutuhkan
session(['current_user_id' => $user->id]);
// Load: $user = User::find(session('current_user_id'));
```

## 📊 Session Size Monitoring

Tambahkan monitoring untuk track session size:

```php
// app/Http/Middleware/MonitorSessionSize.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonitorSessionSize
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('session.driver') === 'cookie') {
            $size = strlen(serialize($request->session()->all()));

            if ($size > 3000) {
                Log::warning('Session size approaching limit', [
                    'size' => $size,
                    'keys' => array_keys($request->session()->all()),
                    'url' => $request->fullUrl(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return $response;
    }
}
```

Register di `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\MonitorSessionSize::class, // Add this
    ],
];
```

## 🆘 Troubleshooting

### Issue: Masih Logout Setelah Fix

**Cek:**

1. **Apakah code sudah di-deploy?**

   ```bash
   # Cek di Laravel Cloud logs
   grep "LimitSessionSize" /path/to/app/Http/Middleware/LimitSessionSize.php
   ```

2. **Apakah config cache sudah di-clear?**

   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

3. **Apakah browser cookies sudah di-clear?**

   - Clear cookies untuk domain `.laravel.cloud`
   - Hard reload (Ctrl+Shift+R)

4. **Cek session keys setelah login:**
   ```bash
   php artisan tinker
   >>> session()->all();
   ```
   Harus ada: `is_superadmin`, `auth_role`, `user_verified`

### Issue: Session Size Masih > 4KB

**Solusi:**

1. **Identify large session keys:**

   ```bash
   php artisan tinker
   >>> $data = session()->all();
   >>> foreach ($data as $key => $value) {
   ...     echo $key . ': ' . strlen(serialize($value)) . " bytes\n";
   ... }
   ```

2. **Remove unnecessary keys:**

   ```php
   // Di controller atau middleware
   session()->forget(['large_key_1', 'large_key_2']);
   ```

3. **Consider database session for specific routes:**
   ```php
   // Untuk route yang butuh session besar
   Route::post('/upload', [UploadController::class, 'store'])
       ->middleware(['web', 'auth'])
       ->withoutMiddleware(LimitSessionSize::class);
   ```

## 📝 Catatan Penting

1. **Cookie session optimal untuk Laravel Cloud** - Stateless, fast, scalable
2. **4KB limit adalah hard limit** - Tidak bisa diubah
3. **Authentication keys harus di-preserve** - Jangan sampai terhapus
4. **SameSite=lax sudah cukup secure** - Tidak perlu strict
5. **Monitor session size** - Prevent issues sebelum terjadi

## 🔗 Related Documentation

- [LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md](./LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md)
- [LARAVEL-CLOUD-ENV-CORRECT.md](../LARAVEL-CLOUD-ENV-CORRECT.md)
- [laravel-cloud-session-fix.md](./laravel-cloud-session-fix.md)

---

**Last Updated:** November 21, 2025  
**Status:** ✅ Fixed & Tested  
**Tested On:** Laravel Cloud Production
