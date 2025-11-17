# Fix: Session Tidak Persist di Laravel Cloud (Login Berhasil tapi Redirect ke Login Lagi)

## 🔴 Masalah

- Login berhasil dan bisa masuk ke dashboard superadmin
- Saat mengakses halaman lain, langsung redirect ke login lagi
- Session tidak persist antar request
- Di local berfungsi dengan baik

## 🎯 Root Cause

Masalah ini disebabkan oleh **session cookie tidak ter-set atau tidak ter-kirim** oleh browser di Laravel Cloud. Penyebab umum:

1. **SESSION_DOMAIN** tidak sesuai dengan domain Laravel Cloud
2. **SESSION_SECURE_COOKIE** tidak di-set dengan benar untuk HTTPS
3. **Session driver** menggunakan `file` yang tidak persistent di Laravel Cloud
4. **Config cache** tidak di-clear setelah perubahan environment variables

## ✅ Solusi Lengkap

### Step 1: Set Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**, tambahkan:

```env
# Session Configuration
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=

# Optional: Jika perlu subdomain support
# SESSION_DOMAIN=.laravel.cloud
```

**Penjelasan:**

- `SESSION_DRIVER=database` - Gunakan database untuk session (persistent)
- `SESSION_SECURE_COOKIE=` (kosong) - Biarkan Laravel auto-detect HTTPS
- `SESSION_DOMAIN=` (kosong) - Cookie akan ter-set untuk domain yang sama persis

**PENTING:** Jangan gunakan nilai seperti:

- ❌ `SESSION_DOMAIN=.laravelcloud.com` (salah domain)
- ❌ `SESSION_SECURE_COOKIE=true` (bisa menyebabkan masalah)

### Step 2: Pastikan Tabel Sessions Ada

Jalankan migration untuk membuat tabel sessions:

```bash
php artisan migrate --force
```

Verifikasi tabel sudah dibuat:

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
php artisan route:cache
```

### Step 4: Deploy Ulang Aplikasi

**PENTING:** Environment variables di-load saat aplikasi start, jadi **WAJIB** deploy ulang:

1. Buka Laravel Cloud Dashboard
2. Klik **Deploy** atau **Redeploy**
3. Tunggu proses build selesai

### Step 5: Verifikasi Konfigurasi

Setelah deploy, akses endpoint debug untuk verifikasi:

**1. Cek konfigurasi session:**

```
https://your-app.laravel.cloud/debug-config
```

Pastikan:

- `session_driver: "database"`
- `session_domain: null` atau sesuai yang Anda set
- `session_secure: null` atau `true`
- `session_same_site: "lax"`
- `sessions_count: 0` atau lebih (bukan error)

**2. Cek authentication setelah login:**

```
https://your-app.laravel.cloud/debug-auth
```

Setelah login, pastikan:

- `is_authenticated: true`
- `has_session_cookie: true`
- `session_cookie_value: "exists"`
- `session_has_auth_key: true`

### Step 6: Test Login

1. **Clear cookies dan cache browser** (Ctrl+Shift+Delete)
2. Buka aplikasi di browser
3. Login dengan credentials yang valid
4. Cek di **Developer Tools** (F12) > **Application** > **Cookies**:
   - Harus ada cookie dengan nama `siar_session`
   - Domain harus sesuai dengan domain aplikasi
   - Secure flag harus ✅ (karena HTTPS)
   - SameSite harus `Lax`
5. Coba akses halaman lain - seharusnya tidak redirect ke login

## 🔍 Debugging Jika Masih Error

### 1. Cek Session Cookie di Browser

Buka **Developer Tools** (F12) > **Application** > **Cookies**

**Jika cookie tidak ada atau tidak ter-set:**

- Masalah di `SESSION_DOMAIN` atau `SESSION_SECURE_COOKIE`
- Coba set `SESSION_DOMAIN=` (kosong)
- Pastikan `SESSION_SECURE_COOKIE=` (kosong)

**Jika cookie ada tapi tidak ter-kirim:**

- Cek domain cookie harus match dengan domain aplikasi
- Cek Secure flag harus sesuai dengan HTTPS

### 2. Cek Network Request

Buka **Developer Tools** > **Network**

Saat akses halaman lain setelah login:

- Cek **Request Headers** > **Cookie** - harus ada session cookie
- Jika tidak ada, berarti browser tidak mengirim cookie

### 3. Cek Logs

Buka **Laravel Cloud Dashboard** > **Logs**

Cari error terkait:

- `CSRF token mismatch`
- `Session not found`
- `Unauthenticated`
- `Cookie not set`

### 4. Verifikasi dengan Tinker

```bash
php artisan tinker

# Cek konfigurasi
>>> config('session.driver');  // Harus "database"
>>> config('session.domain');  // Harus null atau sesuai
>>> config('session.secure');  // Harus null atau true

# Test session manual
>>> session()->put('test', 'value');
>>> session()->save();
>>> session()->get('test');  // Harus return "value"

# Cek tabel sessions
>>> DB::table('sessions')->count();  // Harus return angka
```

## 📋 Checklist Troubleshooting

- [ ] `SESSION_DRIVER=database` sudah di-set di custom variables
- [ ] `SESSION_SECURE_COOKIE=` (kosong) di custom variables
- [ ] `SESSION_DOMAIN=` (kosong) di custom variables
- [ ] Aplikasi sudah di-deploy ulang setelah mengubah env vars
- [ ] Migration sudah dijalankan (`php artisan migrate --force`)
- [ ] Config cache sudah di-clear (`php artisan config:clear`)
- [ ] Config cache sudah di-rebuild (`php artisan config:cache`)
- [ ] `/debug-config` menunjukkan konfigurasi yang benar
- [ ] `/debug-auth` menunjukkan `has_session_cookie: true` setelah login
- [ ] Session cookie ter-set di browser (cek di Developer Tools)
- [ ] Browser cookies dan cache sudah di-clear sebelum test

## 🎯 Solusi Alternatif

### Jika Masih Error dengan SESSION_DOMAIN Kosong

Coba set `SESSION_DOMAIN` dengan domain yang sama persis:

```env
SESSION_DOMAIN=your-app-name.laravel.cloud
```

Ganti `your-app-name` dengan nama aplikasi Anda di Laravel Cloud.

### Jika Perlu Subdomain Support

Jika aplikasi Anda menggunakan subdomain (misalnya `app1.laravel.cloud`, `app2.laravel.cloud`):

```env
SESSION_DOMAIN=.laravel.cloud
```

Dengan dot prefix (`.`), cookie akan ter-set untuk semua subdomain.

## 🆘 Jika Masih Tidak Berhasil

Jika setelah semua langkah di atas masih error, share informasi berikut:

1. **Output dari `/debug-config`**
2. **Output dari `/debug-auth` (setelah login)**
3. **Screenshot session cookie di browser Developer Tools**
4. **Screenshot environment variables di Laravel Cloud**
5. **Error logs dari Laravel Cloud dashboard**

Dengan informasi ini, kita bisa debug lebih lanjut untuk menemukan root cause yang spesifik.

## 📝 Catatan Penting

1. **Selalu deploy ulang** setelah mengubah environment variables
2. **Selalu clear config cache** setelah deploy
3. **Gunakan database session driver** untuk production (bukan file)
4. **Jangan hardcode domain** - biarkan Laravel auto-detect
5. **Test dengan browser yang clean** (clear cookies dan cache)

## 🔗 Referensi

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel Cloud Documentation](https://laravel.com/docs/cloud)
- Cookie Domain Fix: `docs/cookie-domain-fix.md`
- 419 Error Troubleshooting: `docs/419-error-advanced-troubleshooting.md`
