# 🚨 EVALUASI KRITIS: Masalah Session/Login di Laravel Cloud

**Tanggal:** 3 Desember 2025  
**Status:** KRITIS - Perlu Diperbaiki Segera

---

## 📋 Executive Summary

Setelah evaluasi menyeluruh, **masalah utama** yang menyebabkan user sering keluar ke halaman login adalah:

### 🔴 ROOT CAUSE: SESSION_DRIVER=cookie

Laravel Cloud meng-inject `SESSION_DRIVER=cookie` secara default, yang **TIDAK COCOK** untuk aplikasi multitenant kompleks seperti SIAR karena:

1. **Batas ukuran cookie ~4KB** - Session data SIAR (auth, tenant, permissions) sering melebihi batas ini
2. **Middleware `LimitSessionSize` terlalu agresif** - Mencoba memotong session untuk muat di cookie, tapi berisiko menghapus data auth
3. **Cookie session tidak reliable** untuk aplikasi dengan banyak state

---

## 🔍 Analisis Environment Variables

### Environment yang Di-Inject Laravel Cloud:

```env
SESSION_DRIVER=cookie        ← MASALAH UTAMA!
CACHE_STORE=database
```

### Custom Environment Variables Anda:

```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.laravel.cloud
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

### Konflik yang Terjadi:

| Variable              | Custom         | Injected | Prioritas     |
| --------------------- | -------------- | -------- | ------------- |
| SESSION_DRIVER        | -              | cookie   | Injected wins |
| SESSION_DOMAIN        | .laravel.cloud | -        | Custom        |
| SESSION_SECURE_COOKIE | true           | -        | Custom        |

**Masalah:** `SESSION_DRIVER=cookie` di-inject oleh Laravel Cloud dan tidak bisa di-override dengan custom env variable karena Laravel Cloud memprioritaskan injected variables.

---

## 🔧 Analisis Middleware LimitSessionSize

File: `app/Http/Middleware/LimitSessionSize.php`

### Potensi Masalah:

```php
// Line 32-33: Threshold terlalu rendah
if ($sessionSize > 3000) {
    // Mulai menghapus data session
}

// Line 70-106: Hard-trim SANGAT BERISIKO
if ($sessionSizeAfter > 3500) {
    // Flush lalu restore hanya key yang diizinkan
    $request->session()->flush();  // ← BERBAHAYA!
}
```

### Risiko:

1. `session()->flush()` bisa menghapus data authentication
2. Whitelist key mungkin tidak lengkap
3. Race condition antara auth dan session trim

---

## ✅ SOLUSI YANG DIREKOMENDASIKAN

### OPSI 1: Override SESSION_DRIVER di Laravel Cloud (REKOMENDASI)

1. Buka **Laravel Cloud Dashboard**
2. Pergi ke **Environment** > **Variables**
3. **Hapus** semua custom session variables yang sudah ada
4. **Tambahkan** dengan prioritas tinggi (di bagian ATAS):

```env
# SESSION - CRITICAL (harus di atas)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Biarkan Laravel otomatis deteksi
# SESSION_DOMAIN= (kosongkan)
# SESSION_SECURE_COOKIE= (kosongkan - Laravel akan otomatis true untuk HTTPS)
```

**PENTING:** Di Laravel Cloud, pastikan variable `SESSION_DRIVER=database` ditambahkan di bagian **custom environment variables** dengan memastikan tidak ada konflik dengan injected variables.

### OPSI 2: Nonaktifkan LimitSessionSize Middleware

Jika OPSI 1 tidak bisa dilakukan, nonaktifkan middleware yang bermasalah:

File: `app/Http/Kernel.php`, ubah:

```php
'web' => [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    // \App\Http\Middleware\LimitSessionSize::class,  // ← COMMENT OUT
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### OPSI 3: Perbaiki LimitSessionSize Middleware (Paling Aman)

Ubah middleware untuk lebih berhati-hati dengan auth data.

---

## 📊 Verifikasi Masalah

Akses URL debugging berikut setelah login:

```
https://siar-beta-ctegvo.laravel.cloud/debug-config
https://siar-beta-ctegvo.laravel.cloud/debug-auth
https://siar-beta-ctegvo.laravel.cloud/debug-session-test
```

**Perhatikan:**

- `session_config.driver` - harus `database` bukan `cookie`
- `auth_check` - harus `true` jika sudah login
- Session ID - tidak boleh berubah setiap refresh

---

## 🔄 Langkah Implementasi

### Langkah 1: Backup

```bash
git add -A
git commit -m "Backup sebelum fix session Laravel Cloud"
```

### Langkah 2: Update Environment di Laravel Cloud

1. Dashboard → Environment → Variables
2. Tambahkan: `SESSION_DRIVER=database`
3. Hapus: `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE` (biarkan auto-detect)

### Langkah 3: Clear Cache

Di Laravel Cloud Console atau deploy hook:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Langkah 4: Test

1. Clear browser cookies
2. Login
3. Navigate ke beberapa halaman
4. Refresh beberapa kali
5. Session harus persist

---

## 📝 Catatan Tambahan

### Mengapa Database Session Lebih Baik?

| Aspek       | Cookie Session    | Database Session   |
| ----------- | ----------------- | ------------------ |
| Size Limit  | ~4KB              | Unlimited          |
| Security    | Exposed di client | Server-side        |
| Scalability | Single server     | Multi-server       |
| Persistence | Browser dependent | Server controlled  |
| Debug       | Difficult         | Easy (query table) |

### Tabel sessions di database

Pastikan tabel `sessions` sudah ada:

```bash
php artisan session:table
php artisan migrate
```

---

**Last Updated:** 3 Desember 2025
**Status:** WAITING FOR IMPLEMENTATION
