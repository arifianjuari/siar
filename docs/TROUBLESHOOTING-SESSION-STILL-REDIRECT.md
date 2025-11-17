# Troubleshooting: Masih Terlempar ke Login Setelah Fix

## 🔴 Masalah

Setelah menerapkan fix dengan `session()->save()`, masih terlempar ke login.

## 🔍 Langkah Debugging Step-by-Step

### Step 1: Verifikasi Environment Variables

Akses endpoint debug:
```
https://siar-main-bot1z9.laravel.cloud/debug-config
```

**Pastikan:**
- ✅ `session_config.driver`: `"database"` (bukan `"cookie"`)
- ✅ `session_config.secure`: `true` (bukan `null` atau `false`)
- ✅ `session_config.domain`: `null` atau `""` (bukan `.laravel.cloud`)
- ✅ `env_vars.SESSION_DRIVER`: `"database"`

**Jika tidak sesuai:**
1. Update environment variables di Laravel Cloud
2. Clear config cache: `php artisan config:clear && php artisan config:cache`
3. Deploy ulang

### Step 2: Verifikasi Tabel Sessions

Akses endpoint:
```
https://siar-main-bot1z9.laravel.cloud/debug-database
```

**Pastikan:**
- ✅ `sessions_table.exists`: `true`
- ✅ `sessions_table.count`: Angka (bukan error)

**Jika error:**
```bash
php artisan migrate --force
```

### Step 3: Test Session Persistence

Akses endpoint:
```
https://siar-main-bot1z9.laravel.cloud/debug-session-test
```

**Refresh halaman 3-5 kali**, perhatikan:
- ✅ `session_id`: Harus **SAMA** setiap refresh
- ✅ `test.match`: Harus `true`

**Jika `session_id` berubah setiap refresh:**
- Session tidak persist → masalah cookie atau session driver

### Step 4: Login dan Cek Session

1. **Clear cookies** di browser (F12 > Application > Cookies > Clear All)
2. **Login** dengan credentials yang valid
3. **Setelah login**, langsung akses:
   ```
   https://siar-main-bot1z9.laravel.cloud/debug-after-login
   ```

**Perhatikan output:**
- ✅ `session_in_db.exists`: `true`
- ✅ `has_auth_key`: `true`
- ✅ `auth_user_id`: Ada (bukan `null`)
- ✅ `cookie_info.has_cookie`: `true`
- ✅ `cookie_info.cookie_value`: Ada (bukan `null`)

**Jika `session_in_db.exists: false`:**
- Session tidak tersimpan di database → masalah dengan session save

**Jika `has_auth_key: false`:**
- Auth data tidak tersimpan di session → masalah dengan login process

**Jika `cookie_info.has_cookie: false`:**
- Cookie tidak ter-set → masalah dengan cookie configuration

### Step 5: Cek Log Laravel Cloud

Setelah login, cek log untuk melihat:
- `session_in_database`: Harus `true`
- `session_config.driver`: Harus `"database"`
- `session_config.secure`: Harus `true`

Jika ada error terkait database atau session, catat error message.

### Step 6: Cek Cookie di Browser

Setelah login, buka Developer Tools (F12) > Application > Cookies:
- ✅ Cookie `siar_session` ada
- ✅ **Domain**: `siar-main-bot1z9.laravel.cloud` (tanpa titik di depan)
- ✅ **Secure**: Checked (untuk HTTPS)
- ✅ **SameSite**: `Lax`
- ✅ **Value**: Ada (40 karakter session ID)

**Jika cookie tidak ada atau domain salah:**
- Update `SESSION_DOMAIN=` (kosong) di environment variables
- Clear config cache dan deploy ulang

## 🎯 Solusi Berdasarkan Hasil Debug

### Masalah 1: `session_config.driver` = `"cookie"`

**Penyebab:** Environment variable `SESSION_DRIVER=database` tidak override injected variable.

**Solusi:**
1. Pastikan `SESSION_DRIVER=database` ada di **Custom Variables** (bukan Injected)
2. Clear config cache: `php artisan config:clear && php artisan config:cache`
3. Deploy ulang aplikasi

### Masalah 2: `session_in_db.exists: false`

**Penyebab:** Session tidak tersimpan di database setelah login.

**Solusi:**
1. Cek apakah tabel `sessions` ada: `php artisan migrate --force`
2. Cek database connection: `php artisan tinker` → `DB::connection()->getPdo()`
3. Cek log untuk error database
4. Pastikan code sudah di-deploy dengan `session()->save()`

### Masalah 3: `has_auth_key: false`

**Penyebab:** Auth data tidak tersimpan di session.

**Solusi:**
1. Pastikan `Auth::login()` dipanggil (bukan hanya `Auth::attempt()`)
2. Cek apakah ada error di log saat login
3. Pastikan session driver menggunakan `database`

### Masalah 4: Cookie Tidak Ter-Set

**Penyebab:** Cookie configuration salah atau cookie tidak ter-kirim.

**Solusi:**
1. Pastikan `SESSION_SECURE_COOKIE=true` (bukan `null` atau kosong)
2. Pastikan `SESSION_DOMAIN=` (kosong, bukan `.laravel.cloud`)
3. Cek response headers setelah login (Network tab) → harus ada `Set-Cookie: siar_session=...`
4. Clear browser cookies dan coba lagi

### Masalah 5: Session ID Berubah Setiap Request

**Penyebab:** Cookie tidak ter-kirim kembali ke server atau session tidak persist.

**Solusi:**
1. Cek cookie di browser → pastikan ada dan domain benar
2. Cek `SESSION_DOMAIN` → harus kosong
3. Cek `SESSION_SECURE_COOKIE` → harus `true` untuk HTTPS
4. Cek apakah ada multiple cookies dengan nama sama (clear semua cookies)

## 📋 Checklist Lengkap

- [ ] `SESSION_DRIVER=database` ada di Custom Variables
- [ ] `SESSION_SECURE_COOKIE=true` (bukan `null`)
- [ ] `SESSION_DOMAIN=` (kosong)
- [ ] Config cache sudah di-clear dan di-rebuild
- [ ] Tabel `sessions` sudah dibuat
- [ ] Code sudah di-deploy dengan fix `session()->save()`
- [ ] Browser cookies sudah di-clear
- [ ] Test login dan verifikasi session persist
- [ ] Cek log untuk error atau warning
- [ ] Cek cookie di browser (domain, secure, value)

## 🔧 Command untuk Verifikasi

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache

# Verifikasi tabel sessions
php artisan tinker
>>> DB::table('sessions')->count();

# Verifikasi config
>>> config('session.driver');  // Harus "database"
>>> config('session.secure');  // Harus true
>>> config('session.domain');  // Harus null
```

## 📞 Jika Masih Error

Jika setelah semua langkah di atas masih error, kirimkan:
1. Output dari `/debug-config`
2. Output dari `/debug-after-login` (setelah login)
3. Output dari `/debug-session-test` (refresh 3x)
4. Log dari Laravel Cloud (setelah login)
5. Screenshot cookie di browser (Developer Tools > Application > Cookies)

