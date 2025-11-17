# Debug Session Persistence Issue - Laravel Cloud

## 🔴 Masalah Saat Ini

- ✅ Login berhasil, bisa masuk ke dashboard superadmin
- ❌ Saat akses halaman lain (misal: `/superadmin/tenants`), langsung redirect ke login
- ❌ Session tidak persist antar request

## 📋 Environment Variables (Sudah Benar)

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=
SESSION_LIFETIME=120
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

✅ Konfigurasi sudah benar!

## 🔍 Langkah Debugging

### Step 1: Verifikasi Config Cache Sudah Di-Clear

Di Laravel Cloud, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

**PENTING:** Setelah mengubah environment variables, WAJIB clear config cache!

### Step 2: Verifikasi Tabel Sessions Ada

```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

**Expected:** Return angka (0 atau lebih)
**Jika error:** Jalankan `php artisan migrate --force`

### Step 3: Test Session Persistence

Akses endpoint ini di browser:

```
https://siar-main-bot1z9.laravel.cloud/debug-session-test
```

**Refresh halaman ini 3-5 kali**, perhatikan:

1. **`session_id`** - Harus SAMA setiap refresh

   - ✅ Jika sama: Session persist dengan benar
   - ❌ Jika berubah: Session tidak persist (masalah!)

2. **`test.match`** - Harus `true`
   - ✅ Jika true: Session bisa write/read
   - ❌ Jika false: Session tidak bisa write/read (masalah!)

### Step 4: Cek Konfigurasi Session

Akses:

```
https://siar-main-bot1z9.laravel.cloud/debug-config
```

Pastikan:

- ✅ `session_config.driver`: `"database"`
- ✅ `session_config.domain`: `null` atau `""`
- ✅ `session_config.secure`: `null` atau `true`
- ✅ `session_config.same_site`: `"lax"`
- ✅ `sessions_table.count`: Angka (bukan error)

### Step 5: Login dan Cek Authentication

1. **Login** dengan credentials superadmin
2. **Setelah login berhasil**, akses:

```
https://siar-main-bot1z9.laravel.cloud/debug-auth
```

Perhatikan output:

#### A. Session Info

```json
"session_info": {
  "session_id": "...",
  "has_session_cookie": true,  // ✅ HARUS TRUE
  "session_cookie_value": "exists (length: 40)",  // ✅ HARUS EXISTS
  "session_has_auth_key": true,  // ✅ HARUS TRUE
  "all_cookies_count": 2  // Minimal ada 2 cookies
}
```

**Jika `has_session_cookie: false`:**

- Browser tidak mengirim session cookie
- Masalah di cookie domain atau secure flag

**Jika `session_has_auth_key: false`:**

- Session tidak menyimpan auth data
- Masalah di session driver atau database

#### B. Session Database

```json
"session_database": {
  "exists_in_db": true,  // ✅ HARUS TRUE
  "user_id": 1,  // ✅ HARUS ADA (ID user yang login)
  "ip_address": "...",
  "last_activity": "2025-11-17 11:00:00",
  "payload_length": 500  // ✅ HARUS > 0
}
```

**Jika `exists_in_db: false`:**

- Session tidak tersimpan di database
- Masalah di session driver atau database connection

**Jika `user_id: null`:**

- Session ada tapi tidak ada user_id
- Masalah di auth guard atau session

#### C. Auth Check

```json
"auth_check": true,  // ✅ HARUS TRUE
"user": {
  "id": 1,
  "name": "Superadmin",
  "email": "superadmin@siar.com",
  "role": {
    "slug": "superadmin"
  }
}
```

**Jika `auth_check: false`:**

- User tidak terotentikasi
- Session tidak persist atau cookie tidak terkirim

### Step 6: Test Akses Halaman Lain

Setelah login, coba akses:

```
https://siar-main-bot1z9.laravel.cloud/superadmin/tenants
```

**Jika redirect ke login:**

1. **Buka Developer Tools** (F12)
2. **Buka tab Network**
3. **Refresh halaman**
4. **Klik request** ke `/superadmin/tenants`
5. **Cek Request Headers** > **Cookie**
   - ✅ Harus ada `siar_session=...`
   - ❌ Jika tidak ada: Browser tidak mengirim cookie (masalah!)

### Step 7: Cek Browser Cookies

1. **Buka Developer Tools** (F12)
2. **Buka tab Application** > **Cookies**
3. **Pilih** `https://siar-main-bot1z9.laravel.cloud`

Harus ada cookie:

- **Name:** `siar_session`
- **Value:** String panjang (40 karakter)
- **Domain:** `siar-main-bot1z9.laravel.cloud` atau `.laravel.cloud`
- **Path:** `/`
- **Secure:** ✅ (checked)
- **HttpOnly:** ✅ (checked)
- **SameSite:** `Lax`

**Jika cookie tidak ada:**

- Session cookie tidak ter-set
- Masalah di session config (domain/secure)

**Jika cookie ada tapi tidak terkirim:**

- Domain cookie tidak match
- Secure flag tidak sesuai
- Browser block cookie

### Step 8: Cek Logs di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Logs**

Cari log dari `SuperadminMiddleware`:

**Log saat akses halaman:**

```
SuperadminMiddleware: Pengguna tidak terautentikasi
```

Ini berarti `auth()->check()` return `false` - session tidak persist!

**Atau:**

```
SuperadminMiddleware: Memeriksa akses
```

Ini berarti user terotentikasi, tapi mungkin ditolak karena role/tenant.

## 🎯 Kemungkinan Penyebab & Solusi

### Penyebab 1: Session ID Berubah Setiap Request

**Gejala:**

- `/debug-session-test` menunjukkan `session_id` berubah setiap refresh
- Session tidak persist

**Penyebab:**

- Session cookie tidak ter-set atau tidak terkirim
- Browser membuat session baru setiap request

**Solusi:**

1. **Cek cookie di browser** - Pastikan `siar_session` ada dan ter-set
2. **Cek domain cookie** - Harus match dengan domain aplikasi
3. **Clear browser cookies** - Hapus semua cookies dan login ulang
4. **Set SESSION_DOMAIN=** (kosong) di environment variables

### Penyebab 2: Session Cookie Tidak Terkirim

**Gejala:**

- `/debug-auth` menunjukkan `has_session_cookie: false`
- Cookie ada di browser tapi tidak terkirim

**Penyebab:**

- Domain cookie tidak match
- Secure flag tidak sesuai
- SameSite policy block

**Solusi:**

1. **Pastikan SESSION_DOMAIN kosong:**

   ```env
   SESSION_DOMAIN=
   ```

2. **Pastikan SESSION_SECURE_COOKIE kosong:**

   ```env
   SESSION_SECURE_COOKIE=
   ```

3. **Clear config cache:**

   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

4. **Clear browser cookies dan login ulang**

### Penyebab 3: Session Tidak Tersimpan di Database

**Gejala:**

- `/debug-auth` menunjukkan `exists_in_db: false`
- Session tidak ada di database

**Penyebab:**

- Session driver tidak menggunakan database
- Database connection error
- Tabel sessions tidak ada

**Solusi:**

1. **Verifikasi SESSION_DRIVER:**

   ```bash
   php artisan tinker
   >>> config('session.driver');  // Harus "database"
   ```

2. **Verifikasi tabel sessions:**

   ```bash
   >>> DB::table('sessions')->count();  // Harus return angka
   ```

3. **Jika tabel tidak ada:**

   ```bash
   php artisan migrate --force
   ```

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

### Penyebab 4: Auth Data Tidak Tersimpan di Session

**Gejala:**

- `/debug-auth` menunjukkan `session_has_auth_key: false`
- Session ada tapi tidak ada auth data

**Penyebab:**

- Login tidak menyimpan auth data ke session
- Session guard tidak bekerja dengan benar

**Solusi:**

1. **Logout dan login ulang:**

   - Akses `/logout`
   - Clear browser cookies
   - Login ulang

2. **Cek session setelah login:**

   - Langsung setelah login, akses `/debug-auth`
   - Pastikan `session_has_auth_key: true`

3. **Jika masih false, cek auth guard:**
   ```bash
   php artisan tinker
   >>> config('auth.guards.web');
   ```

### Penyebab 5: Config Cache Tidak Di-Clear

**Gejala:**

- Sudah set environment variables tapi tidak berubah
- `/debug-config` masih menunjukkan nilai lama

**Penyebab:**

- Config cache masih menggunakan nilai lama
- Environment variables tidak di-reload

**Solusi:**

1. **Clear semua cache:**

   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Rebuild cache:**

   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

3. **Deploy ulang aplikasi** (PENTING!)

## 📝 Checklist Debugging

Ikuti checklist ini secara berurutan:

- [ ] **Step 1:** Clear config cache (`php artisan config:clear && php artisan config:cache`)
- [ ] **Step 2:** Verifikasi tabel sessions ada (`DB::table('sessions')->count()`)
- [ ] **Step 3:** Test session persistence (`/debug-session-test` - session_id harus sama)
- [ ] **Step 4:** Cek konfigurasi (`/debug-config` - driver harus database)
- [ ] **Step 5:** Login dan cek auth (`/debug-auth` - auth_check harus true)
- [ ] **Step 6:** Cek session cookie di browser (harus ada `siar_session`)
- [ ] **Step 7:** Test akses halaman lain (`/superadmin/tenants`)
- [ ] **Step 8:** Cek logs di Laravel Cloud (cari error dari SuperadminMiddleware)

## 🆘 Jika Masih Error

Jika setelah semua langkah di atas masih error, **share informasi berikut:**

### 1. Output dari `/debug-session-test`

Refresh 3-5 kali dan catat:

- Apakah `session_id` berubah?
- Apakah `test.match` selalu `true`?

### 2. Output dari `/debug-config`

```json
{
  "session_config": { ... },
  "env_vars": { ... },
  "sessions_table": { ... }
}
```

### 3. Output dari `/debug-auth` (setelah login)

```json
{
  "auth_check": ...,
  "session_info": { ... },
  "session_database": { ... }
}
```

### 4. Screenshot Browser Cookies

Developer Tools > Application > Cookies > Screenshot

### 5. Screenshot Network Request

Developer Tools > Network > Request ke `/superadmin/tenants` > Request Headers > Screenshot

### 6. Logs dari Laravel Cloud

Copy logs yang berisi:

- `SuperadminMiddleware`
- `Redirect to login`
- Error terkait session

## 🔧 Quick Fix (Coba Ini Dulu)

Jika tidak mau ribet debugging, coba quick fix ini:

```bash
# 1. Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Migrate tabel sessions
php artisan migrate --force

# 3. Rebuild cache
php artisan config:cache
php artisan route:cache

# 4. Test session
# Akses: /debug-session-test
# Refresh 3x, pastikan session_id SAMA

# 5. Login ulang
# Clear browser cookies
# Login dengan superadmin@siar.com / asdfasdf
# Akses: /debug-auth
# Pastikan auth_check: true

# 6. Test akses halaman
# Akses: /superadmin/tenants
# Jika masih redirect, lanjut debugging detail
```

## 📚 Referensi

- Laravel Session Documentation: https://laravel.com/docs/session
- Laravel Cloud Documentation: https://laravel.com/docs/cloud
- Cookie Domain Fix: `docs/cookie-domain-fix.md`
- Session Fix: `docs/laravel-cloud-session-fix.md`
