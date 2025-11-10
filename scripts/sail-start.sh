#!/bin/bash

# Warna terminal
CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${CYAN}Memulai setup Laravel Sail...${NC}"

# Mendapatkan user dan group id
USER_ID=$(id -u)
GROUP_ID=$(id -g)

# Check apakah Docker sudah terinstall
if ! command -v docker &> /dev/null; then
    echo -e "${YELLOW}Docker tidak ditemukan. Silakan install Docker terlebih dahulu.${NC}"
    echo "https://docs.docker.com/engine/install/"
    exit 1
fi

# Check apakah Docker berjalan
if ! docker info &> /dev/null; then
    echo -e "${YELLOW}Docker daemon tidak berjalan. Silakan jalankan Docker terlebih dahulu.${NC}"
    exit 1
fi

# Check apakah Laravel Sail sudah terinstall
if [ ! -f vendor/bin/sail ]; then
    echo -e "${YELLOW}Laravel Sail belum terinstall. Menginstall Laravel Sail...${NC}"
    composer require laravel/sail --dev
fi

# Check apakah file docker-compose.yml sudah ada
if [ ! -f docker-compose.yml ]; then
    echo -e "${YELLOW}File docker-compose.yml belum ada. Membuat file konfigurasi...${NC}"
    php artisan sail:install
fi

# Membuat .env jika belum ada
if [ ! -f .env ]; then
    echo -e "${YELLOW}File .env belum ada. Menyalin dari .env.example...${NC}"
    cp .env.example .env
    php artisan key:generate
fi

# Pastikan variabel Sail ada di .env
if ! grep -q "WWWUSER" .env; then
    echo -e "${YELLOW}Menambahkan konfigurasi Sail ke .env...${NC}"
    echo "" >> .env
    echo "# Laravel Sail" >> .env
    echo "WWWUSER=$USER_ID" >> .env
    echo "WWWGROUP=$GROUP_ID" >> .env
    echo "APP_PORT=80" >> .env
    echo "FORWARD_DB_PORT=3306" >> .env
    echo "FORWARD_REDIS_PORT=6379" >> .env
    echo "SAIL_XDEBUG_MODE=off" >> .env
    echo 'SAIL_XDEBUG_CONFIG="client_host=host.docker.internal"' >> .env
fi

# Ekspor variabel untuk script sail
export WWWUSER=$USER_ID
export WWWGROUP=$GROUP_ID

# Menjalankan Sail
echo -e "${GREEN}Menjalankan Laravel Sail...${NC}"

# Check apakah menggunakan file sail atau vendor/bin/sail
if [ -f sail ]; then
    ./sail up -d
else
    ./vendor/bin/sail up -d
fi

# Menunggu beberapa saat sampai container siap
echo -e "${YELLOW}Menunggu container siap...${NC}"
sleep 5

# Menjalankan migrasi (opsional, hapus tanda # jika ingin menjalankan migrasi)
# ./sail artisan migrate

echo -e "${GREEN}Laravel Sail berhasil dijalankan!${NC}"
echo -e "${GREEN}Aplikasi dapat diakses di: http://localhost${NC}"
echo -e "${YELLOW}Untuk menghentikan container, jalankan: './sail down'${NC}" 