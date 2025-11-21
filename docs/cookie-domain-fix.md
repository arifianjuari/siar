# Fix: Browser Tidak Mengirim Session Cookie

## Masalah

Dari output `/debug-auth`, terlihat:
- `has_session_cookie: false` - Browser tidak mengirim session cookie
- `all_cookies` hanya berisi cookie Cloudflare
- Session ID ada di server, tapi cookie tidak ter-kirim oleh browser

## Penyebab

Cookie domain `.laravel.cloud` mungkin tidak cocok dengan host `siar-main-bot1z9.laravel.cloud`, sehingga browser menolak untuk mengirim cookie kembali.

## Solusi

### Opsi 1: Set SESSION_DOMAIN ke null (RECOMMENDED)

**Di Laravel Cloud Custom Variables, tambahkan:**

```env
SESSION_DOMAIN=
```

Atau hapus `SESSION_DOMAIN` dari custom variables jika ada, sehingga akan menggunakan `null` (default).

**Penjelasan:**
- Dengan `SESSION_DOMAIN=null`, cookie akan ter-set untuk domain yang sama (siar-main-bot1z9.laravel.cloud)
- Browser akan mengirim cookie kembali karena domain match
- Tidak perlu prefix dot (`.`) karena hanya untuk satu domain

### Opsi 2: Set SESSION_DOMAIN ke host yang sama

**Di Laravel Cloud Custom Variables, set:**

```env
SESSION_DOMAIN=siar-main-bot1z9.laravel.cloud
```

**Penjelasan:**
- Cookie akan ter-set untuk domain yang sama persis
- Browser akan mengirim cookie kembali

### Opsi 3: Pastikan SESSION_DOMAIN dengan dot prefix benar

Jika tetap ingin menggunakan `.laravel.cloud` untuk subdomain support:

**Pastikan di Custom Variables:**
```env
SESSION_DOMAIN=.laravel.cloud
SESSION_SECURE_COOKIE=null
```

**Lalu clear config cache:**
```bash
php artisan config:clear
php artisan config:cache
```

## Langkah-Langkah

1. **Buka Laravel Cloud Dashboard**
2. **Buka Environment Variables**
3. **Di Custom Variables:**
   - **HAPUS** `SESSION_DOMAIN=.laravel.cloud` jika ada
   - **ATAU** set `SESSION_DOMAIN=` (kosong)
   - **ATAU** set `SESSION_DOMAIN=siar-main-bot1z9.laravel.cloud`
4. **Save** perubahan
5. **Deploy ulang** aplikasi
6. **Clear config cache** (jika perlu):
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```
7. **Test login** dan cek:
   - Akses `/debug-auth` setelah login
   - Cek `has_session_cookie` harus `true`
   - Cek di browser DevTools > Application > Cookies, harus ada `siar_session`

## Verifikasi

Setelah deploy, akses `/debug-config` dan pastikan:
- `session_config.domain` adalah `null` atau sesuai dengan yang Anda set
- `session_config.secure` adalah `true` (karena HTTPS)
- `session_config.same_site` adalah `"lax"`

Lalu coba login dan akses `/debug-auth`, pastikan:
- `has_session_cookie: true`
- `session_cookie_value` tidak `null`
- `session_has_auth_key: true`

## Catatan

- **Opsi 1 (SESSION_DOMAIN=null)** adalah yang paling recommended karena:
  - Paling sederhana
  - Tidak perlu konfigurasi khusus
  - Bekerja untuk single domain
  - Browser akan mengirim cookie dengan benar

- Jika Anda perlu subdomain support di masa depan, gunakan Opsi 3 dengan dot prefix


