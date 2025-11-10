#!/bin/bash

# Warna terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo -e "${RED}Menghentikan Laravel Sail...${NC}"

# Check apakah menggunakan file sail atau vendor/bin/sail
if [ -f sail ]; then
    ./sail down
else
    ./vendor/bin/sail down
fi

echo -e "${GREEN}Laravel Sail berhasil dihentikan!${NC}"

# Tanyakan apakah ingin membersihkan volume Docker
read -p "Apakah Anda ingin membersihkan volume Docker? (y/n): " answer

if [[ $answer = y || $answer = Y ]]; then
    echo -e "${RED}Membersihkan volume Docker...${NC}"
    # Check apakah menggunakan file sail atau vendor/bin/sail
    if [ -f sail ]; then
        ./sail down -v
    else
        ./vendor/bin/sail down -v
    fi
    echo -e "${GREEN}Volume Docker berhasil dibersihkan!${NC}"
fi

echo -e "${GREEN}Selesai!${NC}" 