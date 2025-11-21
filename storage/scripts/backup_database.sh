#!/bin/bash

# Script untuk backup database MySQL/MariaDB
# Untuk dijalankan sebagai cron job harian
# Contoh: 0 2 * * * /path/to/backup_database.sh

# ======== KONFIGURASI ========
# Direktori tempat backup akan disimpan
BACKUP_DIR="/path/to/backups"
BACKUP_RETENTION_DAYS=7 # Berapa hari backup disimpan

# Informasi database 
# Gunakan kredensial dari .env Laravel jika mungkin
if [ -f ".env" ]; then
  DB_CONNECTION=$(grep DB_CONNECTION .env | cut -d '=' -f2)
  DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
  DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
  DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
  DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
  DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
else
  # Fallback ke nilai default jika .env tidak ditemukan
  DB_CONNECTION="mysql"
  DB_HOST="localhost"
  DB_PORT="3306"
  DB_DATABASE="siar"
  DB_USERNAME="root"
  DB_PASSWORD=""
fi

# ======== FUNGSI ========
log_message() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# ======== PERSIAPAN ========
# Buat direktori backup jika belum ada
if [ ! -d "$BACKUP_DIR" ]; then
  mkdir -p "$BACKUP_DIR"
  log_message "Membuat direktori backup: $BACKUP_DIR"
fi

# Timestamp untuk nama file
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILENAME="${DB_DATABASE}_${TIMESTAMP}.sql.gz"
BACKUP_PATH="$BACKUP_DIR/$BACKUP_FILENAME"

# ======== PROSES BACKUP ========
log_message "Memulai backup database $DB_DATABASE ke $BACKUP_PATH"

# Jalankan mysqldump dan compress dengan gzip
if [ "$DB_PASSWORD" != "" ]; then
  # Gunakan password jika ada
  mysqldump --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" --password="$DB_PASSWORD" \
    --default-character-set=utf8mb4 --single-transaction --routines --triggers --events \
    "$DB_DATABASE" | gzip > "$BACKUP_PATH"
else
  # Tanpa password
  mysqldump --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" \
    --default-character-set=utf8mb4 --single-transaction --routines --triggers --events \
    "$DB_DATABASE" | gzip > "$BACKUP_PATH"
fi

# Periksa apakah backup berhasil
if [ $? -eq 0 ]; then
  log_message "Backup berhasil diselesaikan. File: $BACKUP_FILENAME ($(du -h "$BACKUP_PATH" | cut -f1))"
  
  # Buat symlink ke backup terbaru untuk memudahkan akses
  LATEST_LINK="$BACKUP_DIR/${DB_DATABASE}_latest.sql.gz"
  ln -sf "$BACKUP_PATH" "$LATEST_LINK"
  log_message "Symlink ke backup terbaru dibuat: $LATEST_LINK"
else
  log_message "ERROR: Backup gagal!"
  exit 1
fi

# ======== PEMBERSIHAN ========
# Hapus backup yang lebih lama dari BACKUP_RETENTION_DAYS
log_message "Menghapus backup yang lebih lama dari $BACKUP_RETENTION_DAYS hari..."
find "$BACKUP_DIR" -name "${DB_DATABASE}_*.sql.gz" -type f -mtime +$BACKUP_RETENTION_DAYS -delete

log_message "Selesai."
exit 0 