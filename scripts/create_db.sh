#!/bin/bash

# Ganti nilai ini sesuai dengan konfigurasi MySQL Anda
DB_USER="root"
DB_PASSWORD=""
DB_NAME="siar_dev"

echo "Membuat database $DB_NAME..."

# Membuat database
mysql -u$DB_USER -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Cek jika database berhasil dibuat
if [ $? -eq 0 ]; then
    echo "Database $DB_NAME berhasil dibuat."
else
    echo "Gagal membuat database. Periksa kredensial MySQL Anda."
    exit 1
fi 