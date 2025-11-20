#!/bin/bash

# Laravel Cloud Build Script
# Script ini akan dijalankan otomatis oleh Laravel Cloud saat build

set -e

echo "🚀 Starting Laravel Cloud build process..."

# Ensure required directories exist
echo "📁 Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs

# Create .gitkeep files to ensure directories are preserved
touch storage/framework/cache/data/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/framework/testing/.gitkeep

# Set permissions early
echo "🔐 Setting initial permissions..."
chmod -R 775 storage bootstrap/cache
chmod -R 755 modules 2>/dev/null || true

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies and build assets
echo "📦 Installing NPM dependencies..."
npm ci

echo "🔨 Building assets..."
npm run build

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction || true

# Seed essential data (only if needed)
echo "🌱 Seeding essential data..."
php artisan db:seed --class=ModuleSeeder --force --no-interaction || true

# Sync modules from filesystem to database
echo "🔄 Syncing modules from filesystem..."
php artisan modules:sync --no-interaction --force || true

# Clear and cache configuration
echo "⚙️ Optimizing configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Set proper permissions for production
echo "🔐 Setting production permissions..."
chmod -R 755 storage bootstrap/cache

echo "✅ Build process completed successfully!"

