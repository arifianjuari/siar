# 🚨 QUICK FIX: Login Tidak Bisa di Laravel Cloud

## ⚡ TL;DR - Langkah Cepat

Login gagal di Laravel Cloud tapi berhasil di local? Ikuti 5 langkah ini:

### 1️⃣ Set Environment Variables di Laravel Cloud

Buka **Laravel Cloud Dashboard** > **Environment** > **Variables**, tambahkan:

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=
SESSION_SAME_SITE=lax
```

### 2️⃣ Pastikan Sessions Table Ada

```bash
php artisan session:table
php artisan migrate --force
```

### 3️⃣ Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 4️⃣ Redeploy Aplikasi

Di Laravel Cloud Dashboard:

- Klik **Deploy** atau **Redeploy**
- Tunggu build selesai

### 5️⃣ Test Login

- Clear browser cookies & cache (Ctrl+Shift+Delete)
- Login dengan credentials
- Seharusnya berhasil masuk dan tidak redirect ke login lagi

---

## 🔍 Kenapa Ini Terjadi?

| Local              | Laravel Cloud          | Dampak                     |
| ------------------ | ---------------------- | -------------------------- |
| HTTP               | HTTPS                  | Cookie secure flag berbeda |
| `siar.test`        | `app.laravel.cloud`    | Domain berbeda             |
| File session       | Perlu database session | File hilang saat redeploy  |
| Persistent storage | Ephemeral storage      | Session tidak tersimpan    |

**Root Cause:** Session cookie tidak ter-set atau tidak ter-kirim karena perbedaan HTTPS/domain.

---

## ✅ Verifikasi Berhasil

Setelah langkah di atas, cek:

1. **Browser DevTools (F12) > Application > Cookies**

   - Cookie `siar_session` harus ada
   - Domain harus sesuai dengan app domain
   - Secure flag harus ✅

2. **Test Login**
   - Login berhasil
   - Redirect ke dashboard
   - Tidak redirect balik ke login
   - Session persist saat akses halaman lain

---

## 🆘 Masih Error?

Cek dokumentasi lengkap: [LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md](./docs/LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md)

Atau share informasi berikut:

1. Screenshot environment variables di Laravel Cloud
2. Screenshot cookies di browser DevTools
3. Error logs dari Laravel Cloud dashboard
4. Output dari: `php artisan tinker` → `config('session.driver')`

---

## 📋 Environment Variables Lengkap (Copy-Paste)

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

# Session (CRITICAL!)
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

**PENTING:**

- Ganti `YOUR_APP_KEY_HERE` dengan hasil dari `php artisan key:generate --show`
- Ganti `your-app.laravel.cloud` dengan domain aplikasi Anda
- Ganti database credentials dengan yang dari Laravel Cloud

---

**Last Updated:** November 21, 2025  
**Status:** ✅ Tested & Working
