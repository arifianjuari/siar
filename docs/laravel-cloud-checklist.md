# Checklist Deploy SIAR ke Laravel Cloud

Gunakan checklist ini untuk memastikan semua langkah deployment sudah dilakukan dengan benar.

## ✅ Pre-Deployment

- [ ] Repository GitHub sudah ditautkan ke Laravel Cloud
- [ ] Aplikasi sudah dibuat di Laravel Cloud dashboard
- [ ] Branch yang digunakan sudah benar (biasanya `main`)
- [ ] Semua perubahan sudah di-commit dan di-push ke GitHub

## ✅ Environment Variables

### Konfigurasi Dasar
- [ ] `APP_NAME` sudah di-set
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` sudah di-generate (otomatis oleh Laravel Cloud)
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` sesuai dengan domain Laravel Cloud
- [ ] `APP_TIMEZONE=Asia/Jakarta`

### Konfigurasi Database
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST` menggunakan variable dari Laravel Cloud
- [ ] `DB_PORT` menggunakan variable dari Laravel Cloud
- [ ] `DB_DATABASE` menggunakan variable dari Laravel Cloud
- [ ] `DB_USERNAME` menggunakan variable dari Laravel Cloud
- [ ] `DB_PASSWORD` menggunakan variable dari Laravel Cloud

### Konfigurasi Multi-Tenant
- [ ] `APP_URL_SCHEME=https://`
- [ ] `APP_URL_BASE` sesuai dengan domain
- [ ] `APP_DOMAIN` sesuai dengan domain
- [ ] `SESSION_DOMAIN` sesuai dengan domain (dengan titik di depan)

### Konfigurasi Session & Cache
- [ ] `SESSION_DRIVER=database`
- [ ] `SESSION_LIFETIME=120`
- [ ] `CACHE_DRIVER=database`
- [ ] `QUEUE_CONNECTION=database`

### Konfigurasi Logging
- [ ] `LOG_CHANNEL=stack`
- [ ] `LOG_LEVEL=error`

## ✅ Build Configuration

- [ ] Build script sudah dikonfigurasi (atau menggunakan default)
- [ ] Node.js version sudah sesuai (jika diperlukan)
- [ ] PHP version sudah sesuai (minimal PHP 8.1)

## ✅ Database Setup

- [ ] Database sudah dibuat di Laravel Cloud
- [ ] Migrasi sudah dijalankan (`php artisan migrate --force`)
- [ ] Seeder sudah dijalankan (`php artisan db:seed`)
- [ ] Tabel `sessions` sudah dibuat (untuk session driver database)

## ✅ Storage Configuration

- [ ] Storage link sudah dibuat (`php artisan storage:link`)
- [ ] Folder `storage` memiliki permission yang benar (775)
- [ ] Folder `bootstrap/cache` memiliki permission yang benar (775)

## ✅ Domain Configuration

- [ ] Domain utama sudah dikonfigurasi
- [ ] Subdomain wildcard sudah dikonfigurasi (untuk multi-tenant)
- [ ] DNS sudah dikonfigurasi dengan benar (jika menggunakan custom domain)

## ✅ Scheduler Configuration

- [ ] Scheduler sudah diaktifkan di Laravel Cloud
- [ ] Cron job sudah dikonfigurasi (jika diperlukan)

## ✅ Security

- [ ] File `.env` tidak di-commit ke repository
- [ ] File `.env.example` sudah ada dan lengkap
- [ ] `.gitignore` sudah dikonfigurasi dengan benar
- [ ] Tidak ada credential yang hard-coded di kode

## ✅ Testing

- [ ] Aplikasi bisa diakses via URL utama
- [ ] Login berfungsi dengan benar
- [ ] Database connection berfungsi
- [ ] Multi-tenant berfungsi (jika menggunakan subdomain)
- [ ] File upload berfungsi
- [ ] Session berfungsi dengan benar

## ✅ Post-Deployment

- [ ] Logs sudah dicek untuk error
- [ ] Backup database sudah dilakukan
- [ ] Monitoring sudah diaktifkan
- [ ] Error tracking sudah dikonfigurasi (jika menggunakan)

## 📝 Catatan Tambahan

Tambahkan catatan khusus untuk deployment Anda di sini:

- 
- 
- 

## 🔗 Link Penting

- Laravel Cloud Dashboard: [https://cloud.laravel.com](https://cloud.laravel.com)
- Repository: [https://github.com/arifianjuari/siar](https://github.com/arifianjuari/siar)
- Application URL: 

---

**Terakhir di-update:** $(date)

