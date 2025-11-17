# Fix: Session Tidak Persist Setelah Login di Laravel Cloud

## 🔴 Masalah

Setelah login berhasil di Laravel Cloud (`https://siar-main-bot1z9.laravel.cloud/`), saat mengklik halaman lain atau refresh, selalu dikembalikan ke halaman login.

### Gejala:

- ✅ Login berhasil (tidak ada error)
- ✅ Redirect ke dashboard berhasil
- ❌ Saat akses halaman lain atau refresh → redirect ke login
- ❌ Session tidak persist antar request

### Hasil Uji:

**Uji A (Login via Form):**

```json
{
  "auth_check": false,
  "session_has_auth_key": false,
  "session_database": {
    "exists_in_db": false,
    "user_id": null
  }
}
```

**Uji B (Direct Login - Server Side):**

```json
{
  "auth_check": true,
  "session_has_auth_key": true,
  "session_database": {
    "exists_in_db": true,
    "user_id": 1
  }
}
```

**Kesimpulan:** Login via form tidak menyimpan session di database, sedangkan direct login berhasil.

## 🎯 Root Cause

### 1. Konflik SESSION_DRIVER

Laravel Cloud meng-inject `SESSION_DRIVER=cookie` yang **override** custom variable `SESSION_DRIVER=database`.

**Injected Variables (dari Laravel Cloud):**

```env
SESSION_DRIVER=cookie  # ❌ Ini yang digunakan, bukan database
```

**Custom Variables (yang Anda set):**

```env
SESSION_DRIVER=database  # ❌ Di-override oleh injected variable
```

**Masalah:**

- Dengan `SESSION_DRIVER=cookie`, session disimpan di cookie (bukan database)
- Cookie mungkin tidak ter-set dengan benar karena konfigurasi secure/domain
- Session tidak persist karena cookie tidak ter-kirim kembali ke server

### 2. SESSION_SECURE_COOKIE Tidak Diset

Dari environment variables Anda:

```env
SESSION_SECURE_COOKIE=  # ❌ Kosong, tidak diset ke true untuk HTTPS
```

**Masalah:**

- Di Laravel Cloud, aplikasi berjalan di HTTPS
- Jika `SESSION_SECURE_COOKIE` tidak diset ke `true`, cookie mungkin tidak ter-set sebagai secure
- Browser mungkin menolak cookie yang tidak secure di HTTPS

### 3. Session Cookie Tidak Ter-Baca

Dari log:

```
session_cookie_value: missing
```

Meskipun cookie ada di header request, Laravel tidak bisa membaca nilainya. Ini menunjukkan masalah dengan:

- Cookie domain mismatch
- Cookie secure flag
- Cookie same-site attribute

## ✅ Solusi Lengkap

### Step 1: Update Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**, pastikan ada:

```env
# ⚠️ WAJIB: Override injected SESSION_DRIVER=cookie
SESSION_DRIVER=database

# ⚠️ WAJIB: Set ke true untuk HTTPS
SESSION_SECURE_COOKIE=true

# ⚠️ WAJIB: Kosongkan untuk domain yang sama persis
SESSION_DOMAIN=

# Konfigurasi lainnya
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax

# Cache & Queue
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**PENTING:**

- `SESSION_DRIVER=database` **WAJIB** ada di custom variables untuk override injected `cookie`
- `SESSION_SECURE_COOKIE=true` **WAJIB** diset untuk HTTPS
- `SESSION_DOMAIN=` **WAJIB** kosong (bukan `null` atau `.laravel.cloud`)

### Step 2: Verifikasi Tabel Sessions

Pastikan tabel `sessions` sudah dibuat:

```bash
php artisan migrate --force
```

Verifikasi:

```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

Jika return angka (bukan error), berarti tabel sudah ada.

### Step 3: Clear Semua Cache

**WAJIB** clear cache setelah mengubah environment variables:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Kemudian rebuild cache:

```bash
php artisan config:cache
```

### Step 4: Verifikasi Konfigurasi

Akses endpoint debug:

```
https://siar-main-bot1z9.laravel.cloud/debug-auth
```

Pastikan:

- ✅ `session_config.driver`: `"database"`
- ✅ `session_config.secure`: `true` (bukan `null`)
- ✅ `session_config.domain`: `null` atau `""`
- ✅ `session_config.same_site`: `"lax"`

### Step 5: Test Login

1. **Clear cookies** di browser (Developer Tools > Application > Cookies > Clear All)
2. **Login** dengan credentials yang valid
3. **Setelah login**, akses `/debug-auth`
4. **Verifikasi**:

   - ✅ `auth_check`: `true`
   - ✅ `session_has_auth_key`: `true`
   - ✅ `session_database.exists_in_db`: `true`
   - ✅ `session_database.user_id`: ada (bukan `null`)

5. **Akses halaman lain** (misal: `/superadmin/users`)
6. **Verifikasi** tidak redirect ke login

## 🔍 Troubleshooting

### Jika Masih Redirect ke Login

1. **Cek Log Laravel Cloud:**

   - Buka logs aplikasi
   - Cari log "Redirect to login because unauthenticated"
   - Perhatikan `session_cookie_value` dan `session_has_auth`

2. **Cek Cookie di Browser:**

   - Developer Tools > Application > Cookies
   - Cek cookie `siar_session`
   - Verifikasi:
     - ✅ **Domain**: `siar-main-bot1z9.laravel.cloud` (tanpa titik di depan)
     - ✅ **Secure**: Checked (untuk HTTPS)
     - ✅ **SameSite**: `Lax`
     - ✅ **Value**: Ada (tidak kosong)

3. **Cek Session di Database:**

   ```bash
   php artisan tinker
   >>> DB::table('sessions')->latest('last_activity')->first();
   ```

   - Pastikan ada record dengan `user_id` yang sesuai
   - Pastikan `last_activity` ter-update saat request

4. **Cek TrustProxies Middleware:**
   Pastikan `app/Http/Middleware/TrustProxies.php`:
   ```php
   protected $proxies = '*';
   ```

### Jika Session Cookie Tidak Ter-Set

1. **Cek Response Headers:**

   - Developer Tools > Network
   - Pilih request login
   - Cek tab **Response Headers**
   - Pastikan ada `Set-Cookie: siar_session=...`

2. **Cek Konfigurasi Secure:**

   ```bash
   php artisan tinker
   >>> config('session.secure');
   ```

   Harus return `true` (bukan `null` atau `false`)

3. **Cek APP_URL:**
   Pastikan `APP_URL` menggunakan HTTPS:
   ```env
   APP_URL=https://siar-main-bot1z9.laravel.cloud
   ```

## 📋 Checklist

- [ ] `SESSION_DRIVER=database` ada di custom variables
- [ ] `SESSION_SECURE_COOKIE=true` (bukan `null` atau kosong)
- [ ] `SESSION_DOMAIN=` (kosong, bukan `.laravel.cloud`)
- [ ] Tabel `sessions` sudah dibuat
- [ ] Config cache sudah di-clear dan di-rebuild
- [ ] TrustProxies middleware sudah dikonfigurasi (`$proxies = '*'`)
- [ ] `APP_URL` menggunakan HTTPS
- [ ] Test login berhasil dan session persist

## 🎯 Environment Variables Final

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

# Session Configuration (PENTING!)
SESSION_DRIVER=database          # ⚠️ WAJIB: Override injected cookie
SESSION_SECURE_COOKIE=true       # ⚠️ WAJIB: Untuk HTTPS
SESSION_DOMAIN=                  # ⚠️ WAJIB: Kosong untuk domain yang sama
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# Cache & Queue
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Logging
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
```

## 📚 Referensi

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel Cookie Configuration](https://laravel.com/docs/requests#cookies)
- [Laravel TrustProxies](https://laravel.com/docs/requests#configuring-trusted-proxies)
