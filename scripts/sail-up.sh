#!/bin/bash

# Pastikan skrip berhenti ketika ada error
set -e

# Check jika file .env ada
if [ ! -f .env ]; then
    echo "File .env tidak ditemukan. Menyalin dari .env.example..."
    cp .env.example .env
    
    # Generate application key jika belum ada
    php artisan key:generate
fi

# Jalankan sail di background
./sail up -d

echo "Laravel Sail berhasil dijalankan di background!"
echo "Aplikasi dapat diakses di: http://localhost"
echo ""
echo "Untuk menghentikan container, jalankan: './sail down'" 