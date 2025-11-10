# Debugging Error 419 Page Expired

Dokumen ini menjelaskan langkah-langkah debugging untuk error 419 Page Expired yang terjadi setelah login.

## Gejala

- Login berhasil (tidak ada error di form login)
- Setelah submit login, redirect ke halaman dengan error 419 Page Expired
- Error terjadi di halaman setelah login (biasanya dashboard)

## Penyebab Umum

1. **Session Cookie Tidak Ter-Set**
   - Session cookie tidak ter-set di browser
   - Session cookie domain tidak sesuai
   - Session cookie secure flag tidak benar

2. **CSRF Token Mismatch**
   - CSRF token tidak ter-regenerate setelah session regenerate
   - CSRF token di meta tag tidak ter-update
   - CSRF token di form tidak sesuai dengan session

3. **TrustProxies Tidak Dikonfigurasi**
   - Laravel tidak mendeteksi HTTPS dengan benar
   - Secure cookie tidak ter-set karena Laravel mengira request HTTP

4. **Session Driver Tidak Berfungsi**
   - Tabel sessions belum dibuat
   - Database connection error
   - Session tidak tersimpan di database

## Langkah Debugging

### 1. Cek Session Cookie di Browser

1. Buka Developer Tools (F12)
2. Buka tab **Application** > **Cookies**
3. Cek apakah ada session cookie dengan nama sesuai `SESSION_COOKIE` (default: `siar_session`)
4. Verifikasi:
   - **Domain**: Harus sesuai dengan `SESSION_DOMAIN` (misal: `.laravel.cloud`)
   - **Secure**: Harus checked jika menggunakan HTTPS
   - **SameSite**: Harus `Lax` atau `None; Secure`
   - **Value**: Harus ada (tidak kosong)

### 2. Cek Environment Variables

Pastikan di Laravel Cloud dashboard, environment variables berikut sudah benar:

```env
APP_URL=https://siar-main-bot1z9.laravel.cloud
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=.laravel.cloud
SESSION_SECURE_COOKIE=null
CACHE_DRIVER=database
```

**PENTING:**
- `SESSION_SECURE_COOKIE=null` akan membuat Laravel otomatis mendeteksi HTTPS
- `SESSION_DOMAIN` harus dengan titik di depan (`.laravel.cloud`) untuk subdomain
- `APP_URL` harus menggunakan HTTPS

### 3. Cek TrustProxies Middleware

File `app/Http/Middleware/TrustProxies.php` harus:

```php
protected $proxies = '*';
```

Ini penting untuk Laravel Cloud karena aplikasi berjalan di balik load balancer.

### 4. Cek Tabel Sessions

Pastikan tabel `sessions` sudah dibuat:

```bash
php artisan migrate --force
```

Verifikasi dengan:

```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

Jika error atau return 0, berarti tabel belum dibuat atau ada masalah.

### 5. Test Session Manual

Test apakah session berfungsi:

```bash
php artisan tinker
>>> session()->put('test', 'value');
>>> session()->get('test');
```

Jika tidak berfungsi, kemungkinan masalah di konfigurasi session.

### 6. Cek Logs

Buka logs aplikasi di Laravel Cloud dashboard dan cari:

- `CSRF token mismatch`
- `Session not found`
- `Cookie not set`
- `Session driver error`

### 7. Cek Network Request

1. Buka Developer Tools > Network
2. Submit form login
3. Cek request headers:
   - **Cookie**: Harus ada session cookie
   - **X-CSRF-TOKEN**: Harus ada di request header
4. Cek response headers:
   - **Set-Cookie**: Harus ada session cookie di response

### 8. Test dengan Browser Lain

Coba dengan:
- Browser lain (Chrome, Firefox, Safari)
- Mode incognito/private
- Clear cookies dan cache

## Solusi yang Sudah Diterapkan

### 1. TrustProxies Configuration

```php
// app/Http/Middleware/TrustProxies.php
protected $proxies = '*';
```

### 2. CSRF Token Regeneration

```php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php
$request->session()->regenerate();
$request->session()->regenerateToken();
```

### 3. Session Configuration

```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', null),
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

## Checklist Troubleshooting

- [ ] Session cookie ter-set di browser (cek di Developer Tools)
- [ ] Session cookie memiliki flag Secure (jika HTTPS)
- [ ] Session cookie domain sesuai dengan SESSION_DOMAIN
- [ ] Environment variables sudah benar
- [ ] TrustProxies sudah dikonfigurasi (`$proxies = '*'`)
- [ ] Tabel sessions sudah dibuat
- [ ] Session driver berfungsi (test dengan tinker)
- [ ] CSRF token ter-regenerate setelah login
- [ ] Logs tidak menunjukkan error terkait session

## Jika Masih Error

1. **Clear Semua Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Rebuild Cache:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

3. **Deploy Ulang:**
   - Deploy ulang aplikasi di Laravel Cloud
   - Pastikan semua perubahan sudah ter-push ke repository

4. **Cek Logs Detail:**
   - Buka logs aplikasi
   - Cari error terkait session atau CSRF
   - Share error message untuk debugging lebih lanjut

## Referensi

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel CSRF Protection](https://laravel.com/docs/csrf)
- [Laravel TrustProxies](https://laravel.com/docs/requests#configuring-trusted-proxies)

