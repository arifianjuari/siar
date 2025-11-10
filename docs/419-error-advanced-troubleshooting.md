# Advanced Troubleshooting Error 419 Page Expired

Dokumen ini menjelaskan langkah-langkah advanced troubleshooting untuk error 419 yang masih terjadi meskipun `SESSION_DRIVER=database` sudah di-set.

## ✅ Yang Sudah Benar

- `SESSION_DRIVER=database` sudah ditambahkan di custom variables ✅
- Custom variable override injected `cookie` ✅

## 🔍 Langkah-Langkah Debugging

### 1. Verifikasi Tabel Sessions Sudah Dibuat

**Jalankan di Laravel Cloud Artisan Commands:**

```bash
php artisan migrate:status
```

Cari migration `2025_04_26_211145_create_sessions_table` - harus status **Ran**.

Jika belum, jalankan:

```bash
php artisan migrate --force
```

**Verifikasi tabel ada:**

```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

Jika error "table doesn't exist", berarti tabel belum dibuat.

### 2. Verifikasi Session Configuration

**Cek konfigurasi session yang aktif:**

```bash
php artisan tinker
>>> config('session.driver');
>>> config('session.secure');
>>> config('session.domain');
>>> config('session.same_site');
```

**Harus return:**
- `driver`: `"database"`
- `secure`: `null` atau `true` (jika HTTPS)
- `domain`: `".laravel.cloud"` atau sesuai
- `same_site`: `"lax"`

### 3. Test Session Manual

**Test apakah session berfungsi:**

```bash
php artisan tinker
>>> session()->put('test', 'value');
>>> session()->get('test');
>>> session()->save();
```

Jika `get('test')` return `null`, berarti session tidak berfungsi.

### 4. Cek Environment Variables yang Aktif

**Cek environment variables yang benar-benar digunakan:**

```bash
php artisan tinker
>>> env('SESSION_DRIVER');
>>> env('SESSION_SECURE_COOKIE');
>>> env('APP_URL');
>>> env('SESSION_DOMAIN');
```

**Harus return:**
- `SESSION_DRIVER`: `"database"`
- `SESSION_SECURE_COOKIE`: `null`
- `APP_URL`: `"https://siar-main-bot1z9.laravel.cloud"`
- `SESSION_DOMAIN`: `".laravel.cloud"` atau sesuai

### 5. Clear Semua Cache (PENTING!)

Setelah mengubah environment variables, **WAJIB** clear semua cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear
```

Kemudian rebuild:

```bash
php artisan config:cache
php artisan route:cache
```

**PENTING:** Setelah mengubah environment variables, config cache harus di-clear karena Laravel cache config values.

### 6. Deploy Ulang Aplikasi

Setelah mengubah environment variables, **WAJIB** deploy ulang aplikasi:

1. Buka Laravel Cloud dashboard
2. Klik **Deploy** atau **Rebuild**
3. Tunggu proses build selesai

**Mengapa penting?**
- Environment variables di-load saat aplikasi start
- Perubahan env vars tidak langsung aktif tanpa restart/deploy

### 7. Verifikasi APP_DOMAIN dan SESSION_DOMAIN

Dari gambar, saya lihat masih ada:
- `APP_DOMAIN=laravelcloud.com` (salah)
- `SESSION_DOMAIN=.laravelcloud.com` (salah)

**Harus di-override dengan:**

```env
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud
```

**Tambahkan di custom variables untuk override injected values.**

### 8. Cek Session Cookie di Browser

1. Buka Developer Tools (F12)
2. Buka tab **Application** > **Cookies**
3. Setelah login, cek apakah ada session cookie:
   - **Nama**: `siar_session` (atau sesuai `SESSION_COOKIE`)
   - **Domain**: `.laravel.cloud` (atau sesuai)
   - **Secure**: ✅ checked (jika HTTPS)
   - **SameSite**: `Lax`
   - **Value**: Tidak kosong

**Jika session cookie tidak ada atau tidak ter-set:**
- Masalah di konfigurasi session
- Cek `SESSION_DOMAIN` dan `SESSION_SECURE_COOKIE`

### 9. Cek Network Request Headers

1. Buka Developer Tools > Network
2. Submit form login
3. Cek request headers:
   - **Cookie**: Harus ada session cookie
   - **X-CSRF-TOKEN**: Harus ada di request header
4. Cek response headers:
   - **Set-Cookie**: Harus ada session cookie di response

**Jika tidak ada:**
- Session tidak ter-set dengan benar
- Cek konfigurasi session

### 10. Cek Logs untuk Detail Error

Buka logs aplikasi di Laravel Cloud dashboard dan cari:

- `CSRF token mismatch`
- `Session not found`
- `Cookie not set`
- `Session driver error`
- `Database connection error`

## 🔧 Solusi Step-by-Step

### Step 1: Pastikan Environment Variables Benar

Di custom variables, pastikan ada:

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=null
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud
```

### Step 2: Deploy Ulang Aplikasi

**WAJIB** deploy ulang setelah mengubah env vars.

### Step 3: Jalankan Migration

```bash
php artisan migrate --force
```

### Step 4: Clear Semua Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear
php artisan config:cache
php artisan route:cache
```

### Step 5: Verifikasi dengan Tinker

```bash
php artisan tinker
>>> config('session.driver');  // Harus return "database"
>>> DB::table('sessions')->count();  // Harus return angka (bukan error)
```

### Step 6: Test Login

1. Clear cookies dan cache browser
2. Buka `https://siar-main-bot1z9.laravel.cloud/login`
3. Coba login
4. Cek session cookie di Developer Tools

## ⚠️ Masalah Umum

### 1. Config Cache Tidak Di-Clear

**Masalah:** Setelah mengubah env vars, config cache masih menggunakan nilai lama.

**Solusi:** 
```bash
php artisan config:clear
php artisan config:cache
```

### 2. Aplikasi Tidak Di-Deploy Ulang

**Masalah:** Environment variables di-load saat aplikasi start, tidak langsung aktif.

**Solusi:** Deploy ulang aplikasi setelah mengubah env vars.

### 3. APP_DOMAIN dan SESSION_DOMAIN Salah

**Masalah:** Domain tidak sesuai dengan domain yang sebenarnya.

**Solusi:** Override dengan nilai yang benar:
```env
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud
```

### 4. Tabel Sessions Belum Dibuat

**Masalah:** Migration belum dijalankan.

**Solusi:**
```bash
php artisan migrate --force
```

### 5. Session Cookie Tidak Ter-Set

**Masalah:** Cookie tidak ter-set karena domain atau secure flag salah.

**Solusi:** 
- Pastikan `SESSION_DOMAIN` benar
- Pastikan `SESSION_SECURE_COOKIE=null`
- Pastikan `APP_URL` menggunakan HTTPS

## 📋 Checklist Lengkap

- [ ] `SESSION_DRIVER=database` di custom variables
- [ ] `SESSION_SECURE_COOKIE=null` di custom variables
- [ ] `APP_DOMAIN=laravel.cloud` di custom variables (override)
- [ ] `SESSION_DOMAIN=.laravel.cloud` di custom variables (override)
- [ ] Aplikasi sudah di-deploy ulang setelah mengubah env vars
- [ ] Migration sudah dijalankan (`php artisan migrate --force`)
- [ ] Config cache sudah di-clear (`php artisan config:clear`)
- [ ] Config cache sudah di-rebuild (`php artisan config:cache`)
- [ ] Tabel sessions sudah dibuat (verifikasi dengan tinker)
- [ ] Session configuration benar (verifikasi dengan tinker)
- [ ] Session cookie ter-set di browser (cek di Developer Tools)
- [ ] Logs tidak menunjukkan error terkait session

## 🆘 Jika Masih Error

Jika masih error setelah semua langkah di atas:

1. **Share hasil dari tinker:**
   ```bash
   php artisan tinker
   >>> config('session.driver');
   >>> config('session.secure');
   >>> config('session.domain');
   >>> DB::table('sessions')->count();
   ```

2. **Share screenshot dari:**
   - Environment variables di Laravel Cloud
   - Session cookie di browser Developer Tools
   - Network request headers saat login

3. **Share error dari logs:**
   - Buka logs aplikasi
   - Cari error terkait session atau CSRF
   - Share error message lengkap

Dengan informasi ini, kita bisa debug lebih lanjut.

