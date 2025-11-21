#!/bin/bash

echo "Menjalankan Laravel Scheduler..."
while true; do
    php artisan schedule:run --verbose
    sleep 60
done 