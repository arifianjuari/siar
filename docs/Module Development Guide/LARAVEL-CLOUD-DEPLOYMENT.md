# Laravel Cloud Deployment Guide

> **✅ Panduan khusus untuk deployment modul di Laravel Cloud**

**Version:** 1.0  
**Last Updated:** 20 November 2025  
**Platform:** Laravel Cloud

---

## 🚀 Quick Start untuk Laravel Cloud

### Step 1: Edit Build Script

**File:** `.laravel-cloud-build.sh` (di root project)

```bash
#!/bin/bash

# Laravel Cloud Build Script
set -e

echo "🚀 Starting Laravel Cloud deployment..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations
php artisan migrate --force

# ✅ TAMBAHKAN INI: Run module seeders
echo "📦 Registering modules..."

# Run seeder untuk setiap modul baru
php artisan db:seed --class=ProductManagementModuleSeeder --force
# php artisan db:seed --class=InventoryManagementModuleSeeder --force
# Tambahkan seeder modul lain di sini

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old caches
php artisan cache:clear

echo "✅ Deployment completed!"
```

### Step 2: Make Executable

```bash
# Make script executable (run once)
chmod +x .laravel-cloud-build.sh
```

### Step 3: Commit & Push

```bash
# Add to git
git add .laravel-cloud-build.sh

# Commit
git commit -m "Add module seeder to Laravel Cloud deployment"

# Push to trigger deployment
git push origin main
```

### Step 4: Verify

```
1. Laravel Cloud akan detect push
2. Run .laravel-cloud-build.sh otomatis
3. Seeder akan run
4. Module muncul di Superadmin ✅
```

---

## 📋 Complete Build Script Template

```bash
#!/bin/bash

# Laravel Cloud Build Script for SIAR
# This script runs on every deployment

set -e  # Exit on error

echo "=========================================="
echo "🚀 SIAR Deployment Starting..."
echo "=========================================="

# 1. Install Dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Run Migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# 3. Register Modules
echo "📦 Registering modules..."

# Register all module seeders here
# Add new modules as they are created
php artisan db:seed --class=UserManagementModuleSeeder --force
php artisan db:seed --class=RiskManagementModuleSeeder --force
php artisan db:seed --class=DocumentManagementModuleSeeder --force
php artisan db:seed --class=ActivityManagementModuleSeeder --force
php artisan db:seed --class=ProductManagementModuleSeeder --force
# php artisan db:seed --class=YourNewModuleSeeder --force

echo "✅ All modules registered"

# 4. Optimize Application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Clear Old Caches
echo "🧹 Clearing old caches..."
php artisan cache:clear

# 6. Storage Link (if needed)
# php artisan storage:link

echo "=========================================="
echo "✅ SIAR Deployment Completed Successfully!"
echo "=========================================="
```

---

## 🔄 Deployment Flow

```
Developer                Laravel Cloud              Database
    |                          |                         |
    | 1. git push              |                         |
    |------------------------->|                         |
    |                          |                         |
    |                          | 2. Detect push          |
    |                          | 3. Clone repo           |
    |                          | 4. Run build script     |
    |                          |                         |
    |                          | 5. composer install     |
    |                          |------------------------>|
    |                          |                         |
    |                          | 6. php artisan migrate  |
    |                          |------------------------>|
    |                          |                         |
    |                          | 7. Run seeders          |
    |                          |------------------------>|
    |                          |    (Register modules)   |
    |                          |                         |
    |                          | 8. Optimize caches      |
    |                          |                         |
    |                          | 9. Deploy complete ✅   |
    |<-------------------------|                         |
    |                          |                         |
```

---

## ✅ Benefits Laravel Cloud Approach

### 1. Fully Automated

```
✅ No manual SSH required
✅ No manual seeder run
✅ Consistent deployment
```

### 2. Version Controlled

```
✅ Build script in git
✅ Audit trail
✅ Easy rollback
```

### 3. Idempotent

```
✅ Safe to run multiple times
✅ updateOrCreate() prevents duplicates
✅ No data loss
```

### 4. Zero Downtime

```
✅ Laravel Cloud handles deployment
✅ Automatic rollback on failure
✅ Health checks
```

---

## 🔍 Verification After Deployment

### 1. Check Laravel Cloud Dashboard

```
1. Login to Laravel Cloud
2. Go to your project
3. Check "Deployments" tab
4. Verify latest deployment status: ✅ Success
5. Check deployment logs for seeder output
```

### 2. Check Superadmin UI

```
1. Login as Superadmin
2. Navigate to: https://your-domain.com/superadmin/modules
3. Verify:
   ✅ Module "Manajemen Produk" muncul
   ✅ Status is_active = true
   ✅ Icon muncul
   ✅ Dapat manage per tenant
```

### 3. Check Database (Optional)

```bash
# Via Laravel Cloud terminal
php artisan tinker

# Check module
>>> Module::where('code', 'product-management')->first()

# Check tenant activation
>>> TenantModule::where('module_id', X)->count()

# Check permissions
>>> RoleModulePermission::where('module_id', X)->count()
```

---

## 🆕 Adding New Module

### Step 1: Create Module Seeder

```php
// database/seeders/InventoryManagementModuleSeeder.php
class InventoryManagementModuleSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::updateOrCreate(
            ['code' => 'inventory-management'],
            [
                'name' => 'Manajemen Inventori',
                'slug' => 'inventory-management',
                'description' => '...',
                'icon' => 'package',
                'order' => 5,
                'is_active' => true
            ]
        );

        // ... activate for tenants
    }
}
```

### Step 2: Add to Build Script

```bash
# Edit .laravel-cloud-build.sh
# Add new line:
php artisan db:seed --class=InventoryManagementModuleSeeder --force
```

### Step 3: Commit & Deploy

```bash
git add .
git commit -m "Add Inventory Management module"
git push origin main

# Laravel Cloud will automatically:
# 1. Detect push
# 2. Run build script
# 3. Run new seeder
# 4. Module muncul di Superadmin ✅
```

---

## 🚨 Troubleshooting

### Issue 1: Build Script Tidak Executable

**Error:**

```
Permission denied: .laravel-cloud-build.sh
```

**Fix:**

```bash
chmod +x .laravel-cloud-build.sh
git add .laravel-cloud-build.sh
git commit -m "Fix build script permissions"
git push
```

---

### Issue 2: Seeder Error During Deployment

**Error:**

```
Class 'ProductManagementModuleSeeder' not found
```

**Fix:**

```bash
# Make sure seeder exists
ls database/seeders/ProductManagementModuleSeeder.php

# Make sure autoload is updated
composer dump-autoload

# Commit & push
git add composer.lock
git commit -m "Update autoload"
git push
```

---

### Issue 3: Module Tidak Muncul Setelah Deploy

**Check:**

```bash
# 1. Check deployment logs di Laravel Cloud
# Look for: "📦 Registering modules..."

# 2. Check if seeder ran successfully
# Look for: "✅ All modules registered"

# 3. Manual verification via tinker
php artisan tinker
>>> Module::where('code', 'product-management')->exists()
```

**Fix:**

```bash
# If seeder didn't run, manually run once:
php artisan db:seed --class=ProductManagementModuleSeeder --force
```

---

### Issue 4: Deployment Failed

**Check Laravel Cloud Logs:**

```
1. Go to Laravel Cloud Dashboard
2. Click "Deployments"
3. Click failed deployment
4. Check error logs
```

**Common Causes:**

- ❌ Syntax error in build script
- ❌ Missing seeder file
- ❌ Database connection issue
- ❌ Migration error

**Fix:**

```bash
# Fix the issue
# Commit & push to trigger new deployment
git add .
git commit -m "Fix deployment issue"
git push
```

---

## 📊 Monitoring

### Deployment Logs

Laravel Cloud provides detailed logs:

```
🚀 Starting Laravel Cloud deployment...
📦 Installing dependencies...
   - Installing vendor packages... ✅
🗄️  Running migrations...
   - Running: 2024_11_20_create_products_table... ✅
📦 Registering modules...
   - ProductManagementModuleSeeder... ✅
   - Module 'Manajemen Produk' registered (ID: 5)
   - Activated for tenant: RS Bhayangkara Batu
   - Permissions set for role: Tenant Admin
⚡ Optimizing application...
   - Config cached ✅
   - Routes cached ✅
   - Views cached ✅
🧹 Clearing old caches...
   - Cache cleared ✅
✅ Deployment completed!
```

---

## 🎯 Best Practices

### 1. Keep Build Script Clean

```bash
# ✅ GOOD: Clear sections
echo "📦 Registering modules..."
php artisan db:seed --class=ProductManagementModuleSeeder --force

# ❌ BAD: No organization
php artisan db:seed --class=ProductManagementModuleSeeder --force
php artisan config:cache
php artisan db:seed --class=InventoryManagementModuleSeeder --force
```

### 2. Use --force Flag

```bash
# ✅ GOOD: Use --force for production
php artisan db:seed --class=ProductManagementModuleSeeder --force

# ❌ BAD: No --force (will prompt for confirmation)
php artisan db:seed --class=ProductManagementModuleSeeder
```

### 3. Add Comments

```bash
# ✅ GOOD: Documented
# Register Product Management module (added 2024-11-20)
php artisan db:seed --class=ProductManagementModuleSeeder --force

# ❌ BAD: No context
php artisan db:seed --class=ProductManagementModuleSeeder --force
```

### 4. Group Related Seeders

```bash
# ✅ GOOD: Grouped by category
echo "📦 Registering core modules..."
php artisan db:seed --class=UserManagementModuleSeeder --force
php artisan db:seed --class=RoleManagementModuleSeeder --force

echo "📦 Registering business modules..."
php artisan db:seed --class=ProductManagementModuleSeeder --force
php artisan db:seed --class=InventoryManagementModuleSeeder --force
```

---

## 📚 Related Documentation

- `MODULE-REGISTRATION-GUIDE.md` - Complete registration guide
- `MODULE-DEVELOPMENT-GUIDE.md` - Module development guide
- `MODULE-CHECKLIST.md` - Development checklist

---

## ✅ Summary

### For Laravel Cloud Users:

1. **Setup Once:**

   - Edit `.laravel-cloud-build.sh`
   - Add seeder commands
   - Commit & push

2. **After That:**

   - ✅ Fully automated
   - ✅ No manual steps
   - ✅ Module muncul otomatis di Superadmin

3. **Adding New Modules:**
   - Create seeder
   - Add to build script
   - Push → Deploy → Done ✅

---

**Last Updated:** 20 November 2025  
**Platform:** Laravel Cloud  
**Status:** ✅ Production Ready
