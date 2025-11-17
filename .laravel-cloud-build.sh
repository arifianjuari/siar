#!/bin/bash

# Laravel Cloud Build Script
# Script ini akan dijalankan otomatis oleh Laravel Cloud saat build

set -e

echo "🚀 Starting Laravel Cloud build process..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies and build assets
echo "📦 Installing NPM dependencies..."
npm ci

echo "🔨 Building assets..."
npm run build

# Clear and cache configuration
echo "⚙️ Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache || true

echo "✅ Build process completed successfully!"

