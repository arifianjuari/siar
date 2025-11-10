# Troubleshooting Laravel Cloud Deployment

## Error 404 Not Found (nginx)

Jika Anda mendapatkan error **404 Not Found** dari nginx saat mengakses domain aplikasi, ikuti langkah-langkah berikut:

### Solusi 1: Clear Route Cache

Jalankan command berikut di Laravel Cloud dashboard (Artisan Commands):

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Kemudian rebuild route cache:

```bash
php artisan route:cache
php artisan config:cache
```

### Solusi 2: Verifikasi Konfigurasi Environment

Pastikan environment variables berikut sudah benar di Laravel Cloud dashboard:

```env
APP_URL=https://siar-main-bot1z9.laravel.cloud
APP_URL_SCHEME=https://
APP_URL_BASE=siar-main-bot1z9.laravel.cloud
APP_DOMAIN=laravel.cloud
SESSION_DOMAIN=.laravel.cloud
```

### Solusi 3: Verifikasi Route Root

Pastikan route root (`/`) sudah terdaftar. Route root seharusnya redirect ke login atau dashboard.

Untuk memverifikasi, jalankan:

```bash
php artisan route:list | grep "^GET.*/$"
```

### Solusi 4: Rebuild Aplikasi

Jika masih error, coba rebuild aplikasi di Laravel Cloud dashboard:

1. Buka aplikasi di Laravel Cloud dashboard
2. Klik **Deploy** atau **Rebuild**
3. Tunggu proses build selesai
4. Coba akses lagi

### Solusi 5: Cek Logs

Cek logs aplikasi di Laravel Cloud dashboard untuk melihat error detail:

1. Buka aplikasi di Laravel Cloud dashboard
2. Buka bagian **Logs**
3. Cari error terkait routing atau nginx

### Solusi 6: Verifikasi File Public

Pastikan file `public/index.php` ada dan bisa diakses. File ini adalah entry point aplikasi Laravel.

### Solusi 7: Test Route Langsung

Coba akses route spesifik untuk memastikan routing berfungsi:

- `https://siar-main-bot1z9.laravel.cloud/login` - Harus menampilkan halaman login
- `https://siar-main-bot1z9.laravel.cloud/register` - Harus menampilkan halaman register

Jika route spesifik berfungsi tapi root (`/`) tidak, kemungkinan masalah di route root.

## Error Lainnya

### Database Connection Error

Jika ada error koneksi database:

1. Pastikan environment variables database sudah benar
2. Pastikan database sudah dibuat di Laravel Cloud
3. Cek credentials database

### Storage Permission Error

Jika ada error permission pada storage:

1. Pastikan build script menjalankan `php artisan storage:link`
2. Pastikan folder `storage` dan `bootstrap/cache` memiliki permission 775

### Session Error

Jika ada error session:

1. Pastikan `SESSION_DRIVER=database`
2. Pastikan tabel `sessions` sudah dibuat (jalankan `php artisan migrate`)
3. Cek konfigurasi `SESSION_DOMAIN`

## Kontak Support

Jika masalah masih berlanjut:

1. Cek dokumentasi Laravel Cloud: https://laravel.com/docs/cloud
2. Hubungi support Laravel Cloud
3. Cek logs aplikasi untuk detail error

