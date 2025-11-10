#!/bin/bash

# Informasi
echo "=== SCRIPT PEMBERSIHAN DAN REBUILD APLIKASI ==="
echo "Script ini akan membersihkan cache dan rebuild asset aplikasi"
echo ""

# Hapus direktori build
echo "Menghapus direktori public/build..."
rm -rf public/build

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Rebuild asset dengan npm
echo "Membangun ulang asset dengan npm..."
npm run build

# Pastikan folder storage bisa diakses
echo "Memastikan folder storage bisa diakses..."
php artisan storage:link

# Selesai
echo ""
echo "=== REBUILD SELESAI ==="
echo "Pastikan untuk menjalankan 'composer dump-autoload' jika diperlukan"
echo "Silakan restart server aplikasi jika diperlukan" 