# Panduan Setup SIAR (macOS)

## Persiapan

1. Pastikan Anda memiliki PHP 8.1+ dan MySQL 5.7+ atau 8.x terpasang di macOS Anda
2. Pastikan Composer terpasang untuk mengelola dependencies Laravel

## Setup Database

### Menggunakan MySQL

1. Buat database baru dengan menjalankan script:
   ```bash
   ./create_db.sh
   ```
   atau buat manual:
   ```bash
   mysql -u root -e "CREATE DATABASE siar_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. Pastikan konfigurasi database di `.env` sudah benar:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=siar_dev
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Jalankan migrasi dan seeder untuk membuat struktur database awal:
   ```bash
   php artisan migrate --seed
   ```

### Menggunakan SQLite (Opsional untuk Testing)

1. Buat file SQLite:
   ```bash
   mkdir -p database/sqlite && touch database/sqlite/database.sqlite
   ```

2. Salin .env.sqlite ke .env:
   ```bash
   cp .env.sqlite .env
   ```

3. Jalankan migrasi untuk SQLite:
   ```bash
   php artisan migrate --seed
   ```

## Menjalankan Aplikasi

### Menggunakan Laravel Valet (Direkomendasikan untuk Development)

1. Pastikan Laravel Valet terinstal:
   ```bash
   composer global require laravel/valet
   valet install
   ```

2. Link direktori proyek dengan Valet:
   ```bash
   cd /path/to/project
   valet link siar
   ```

3. Konfigurasi .env untuk Valet:
   ```
   APP_URL=http://siar.test
   APP_URL_SCHEME=http://
   APP_URL_BASE=siar.test
   APP_DOMAIN=siar.test
   SESSION_DOMAIN=.siar.test
   ```

4. Akses aplikasi di browser melalui `http://siar.test`

### Menggunakan Localhost (Port 8000)

1. Konfigurasi .env untuk localhost:
   ```
   APP_URL=http://127.0.0.1:8000
   APP_URL_SCHEME=http://
   APP_URL_BASE=127.0.0.1:8000
   APP_DOMAIN=127.0.0.1
   SESSION_DOMAIN=127.0.0.1
   ```

2. Jalankan server development:
   ```bash
   ./serve.sh
   ```
   atau
   ```bash
   php artisan serve
   ```

3. Akses aplikasi melalui browser dengan URL http://127.0.0.1:8000

### Menggunakan Deploy Online (Production)

1. Konfigurasi .env untuk server online:
   ```
   APP_URL=https://siar.example.com
   APP_URL_SCHEME=https://
   APP_URL_BASE=siar.example.com
   APP_DOMAIN=example.com
   SESSION_DOMAIN=.example.com
   ```

2. Pastikan konfigurasi web server (Apache/Nginx) untuk subdomain wildcard

## Membuat Tenant Pertama

Jika database dan migrasi sudah siap, buat tenant pertama dengan command:

```bash
php artisan tenant:provision --name="RS Contoh" --domain="rs-contoh.localhost" --admin-name="Admin" --admin-email="admin@example.com" --admin-password="password"
```

## Testing Domain Tenant

Untuk pengujian domain tenant, Anda perlu mengkonfigurasi host file lokal di macOS:

1. Edit file `/etc/hosts`:
   ```bash
   sudo nano /etc/hosts
   ```

2. Tambahkan baris berikut:
   ```
   127.0.0.1 rs-contoh.localhost
   ```

3. Simpan dengan menekan `Ctrl+O`, kemudian `Enter`, dan keluar dengan `Ctrl+X`

4. Kemudian akses tenant melalui browser: http://rs-contoh.localhost:8080

## Menjalankan Backup Otomatis

Backup database akan berjalan otomatis setiap hari pukul 02:00 WIB. Untuk menjalankan scheduler:

```bash
./run_scheduler.sh
```

Backup akan disimpan di `storage/app/backups/` dengan format nama `Y-m-d_His.sql` 