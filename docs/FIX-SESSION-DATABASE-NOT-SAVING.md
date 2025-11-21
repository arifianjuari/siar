# Fix: Session Database Driver Tidak Menyimpan Session

## 🔴 Masalah

- Dengan `SESSION_DRIVER=database`: Session tidak tersimpan, selalu terlempar ke login
- Dengan `SESSION_DRIVER=cookie`: Session bekerja tapi cookie terlalu besar → **400 Bad Request - Request Header Or Cookie Too Large**

## 🎯 Root Cause

**Masalah utama:** Session tidak ter-save secara eksplisit ke database setelah `regenerate()` pada controller login.

Laravel secara default akan save session di akhir request lifecycle, tapi dengan `regenerate()` dan redirect, session mungkin tidak ter-save sebelum redirect terjadi, terutama di environment production dengan load balancer.

## ✅ Solusi

### 1. Update AuthenticatedSessionController

Telah ditambahkan `$request->session()->save()` secara eksplisit setelah `regenerate()` dan sebelum redirect untuk memastikan session tersimpan di database.

**Perubahan:**
- Tambahkan `$request->session()->save()` setelah `regenerate()`
- Tambahkan `$request->session()->save()` sebelum redirect
- Update cookie secure detection untuk menggunakan config jika tersedia

### 2. Environment Variables di Laravel Cloud

Pastikan di **Custom Variables**:

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax
```

**PENTING:**
- `SESSION_DRIVER=database` **WAJIB** ada untuk override injected `cookie`
- `SESSION_SECURE_COOKIE=true` **WAJIB** diset untuk HTTPS
- `SESSION_DOMAIN=` **WAJIB** kosong (bukan `.laravel.cloud`)

### 3. Verifikasi Tabel Sessions

Pastikan tabel `sessions` sudah dibuat:

```bash
php artisan migrate --force
```

Verifikasi:
```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

### 4. Clear Config Cache

**WAJIB** setelah update code:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 5. Deploy dan Test

1. **Deploy** perubahan ke Laravel Cloud
2. **Clear cookies** di browser
3. **Login** dengan credentials yang valid
4. **Akses** `/debug-auth` untuk verifikasi:
   - ✅ `auth_check`: `true`
   - ✅ `session_has_auth_key`: `true`
   - ✅ `session_database.exists_in_db`: `true`
5. **Akses halaman lain** (misal: `/superadmin/users`)
6. **Verifikasi** tidak redirect ke login

## 🔍 Debugging

### Cek Log Setelah Login

Setelah login, cek log Laravel Cloud untuk melihat:
- `session_in_database`: Harus `true` jika menggunakan database driver
- `session_config.driver`: Harus `"database"`
- `session_config.secure`: Harus `true` (bukan `null`)

### Cek Session di Database

```bash
php artisan tinker
>>> DB::table('sessions')
    ->where('user_id', 1)
    ->latest('last_activity')
    ->first();
```

Pastikan:
- ✅ Ada record dengan `user_id` yang sesuai
- ✅ `last_activity` ter-update saat request
- ✅ `payload` tidak kosong

### Jika Masih Tidak Tersimpan

1. **Cek Session Connection:**
   ```bash
   php artisan tinker
   >>> config('session.connection');
   ```
   Harus `null` (menggunakan default connection) atau connection yang valid.

2. **Cek Database Connection:**
   ```bash
   >>> DB::connection()->getPdo();
   ```
   Harus return PDO object (bukan error).

3. **Test Manual Save:**
   ```bash
   >>> session()->put('test', 'value');
   >>> session()->save();
   >>> DB::table('sessions')->where('id', session()->getId())->first();
   ```
   Harus return record dengan payload yang berisi 'test'.

## 📋 Checklist

- [ ] Code sudah di-update dengan `session()->save()`
- [ ] `SESSION_DRIVER=database` ada di custom variables
- [ ] `SESSION_SECURE_COOKIE=true` diset
- [ ] `SESSION_DOMAIN=` kosong
- [ ] Tabel `sessions` sudah dibuat
- [ ] Config cache sudah di-clear
- [ ] Deploy ke Laravel Cloud
- [ ] Test login dan verifikasi session persist
- [ ] Cek log untuk `session_in_database: true`

## 🎯 Mengapa Cookie Driver Menyebabkan 400 Bad Request?

Dengan `SESSION_DRIVER=cookie`, semua data session disimpan di cookie. Jika session data besar (misal: user data, tenant data, permissions, dll), cookie bisa melebihi batas nginx (biasanya 4KB-8KB), menyebabkan error:

```
400 Bad Request
Request Header Or Cookie Too Large
```

**Solusi:** Gunakan `SESSION_DRIVER=database` untuk menyimpan session di database, bukan di cookie. Cookie hanya berisi session ID (40 karakter), bukan seluruh data session.

## 📚 Referensi

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel Session Regeneration](https://laravel.com/docs/session#regenerating-the-session-id)
- [Nginx Cookie Size Limit](https://nginx.org/en/docs/http/ngx_http_core_module.html#large_client_header_buffers)

