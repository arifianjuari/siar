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

1. Jalankan server pada port 8080:
   ```bash
   ./serve.sh
   ```
   atau manual:
   ```bash
   php -S localhost:8080 -t public/
   ```

2. Akses aplikasi melalui browser dengan URL http://localhost:8080

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