# 🚀 QUICK FIX: Session Tidak Persist Setelah Login

## ⚠️ MASALAH UTAMA

Laravel Cloud meng-inject `SESSION_DRIVER=cookie` yang **override** custom variable `SESSION_DRIVER=database`, sehingga session tidak tersimpan di database saat login via form.

## ✅ SOLUSI CEPAT (5 Menit)

### Step 1: Update Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Custom Variables**

**HAPUS** atau **UPDATE** variable berikut jika sudah ada:
- ❌ `SESSION_SECURE_COOKIE=` (kosong)
- ❌ `SESSION_DOMAIN=.laravel.cloud` (jika ada)

**TAMBAHKAN** atau **UPDATE** dengan nilai berikut:

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax
```

**PENTING:**
- `SESSION_DRIVER=database` **WAJIB** ada untuk override injected `cookie`
- `SESSION_SECURE_COOKIE=true` **WAJIB** diset (bukan `null` atau kosong)
- `SESSION_DOMAIN=` **WAJIB** kosong (bukan `.laravel.cloud`)

### Step 2: Clear Config Cache

Di Laravel Cloud, jalankan command:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Step 3: Test Login

1. **Clear cookies** di browser (F12 > Application > Cookies > Clear All)
2. **Login** dengan credentials yang valid
3. **Akses halaman lain** (misal: `/superadmin/users`)
4. **Verifikasi** tidak redirect ke login

## 🔍 Verifikasi

Akses endpoint debug setelah login:
```
https://siar-main-bot1z9.laravel.cloud/debug-auth
```

Pastikan:
- ✅ `auth_check`: `true`
- ✅ `session_has_auth_key`: `true`
- ✅ `session_database.exists_in_db`: `true`
- ✅ `session_config.secure`: `true` (bukan `null`)
- ✅ `session_config.domain`: `null` atau `""`

## 📋 Environment Variables Final

```env
# Session Configuration (di Custom Variables)
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax

# Cache & Queue
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

## ❓ Masih Error?

1. **Cek Log Laravel Cloud** untuk error terkait session
2. **Cek Cookie di Browser** (F12 > Application > Cookies > `siar_session`)
   - Domain: `siar-main-bot1z9.laravel.cloud` (tanpa titik)
   - Secure: ✅ Checked
   - SameSite: `Lax`
3. **Verifikasi Tabel Sessions**:
   ```bash
   php artisan tinker
   >>> DB::table('sessions')->count();
   ```

