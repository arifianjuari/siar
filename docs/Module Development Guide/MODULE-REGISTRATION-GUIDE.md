# Module Registration Guide

> **⚠️ CRITICAL:** Panduan ini menjelaskan cara registrasi modul ke database agar teridentifikasi di halaman Superadmin dan dapat disinkronisasi dengan tenant.

**Version:** 1.0  
**Last Updated:** 20 November 2025

---

## 📋 Mengapa Registrasi Penting?

Tanpa registrasi yang benar, modul TIDAK AKAN:

- ❌ Muncul di halaman `/superadmin/modules`
- ❌ Dapat di-enable/disable oleh Superadmin
- ❌ Tersinkronisasi dengan tenant
- ❌ Memiliki permissions yang dapat diatur

**Dengan registrasi yang benar:**

- ✅ Modul muncul di Superadmin dashboard
- ✅ Superadmin dapat manage modul per tenant
- ✅ Permissions dapat diatur per role
- ✅ Modul otomatis tersedia untuk tenant baru

---

## 🗄️ Database Architecture

### Tables yang Digunakan

```
1. modules
   - Master data semua modul
   - Dikelola oleh Superadmin

2. tenant_modules
   - Aktivasi modul per tenant
   - Pivot table: tenant_id + module_id

3. role_module_permissions
   - Permissions per role per modul
   - can_view, can_create, can_edit, can_delete, dll
```

### Entity Relationship

```
modules (1) ----< (N) tenant_modules (N) >---- (1) tenants
   |
   |
   v
role_module_permissions (N) >---- (1) roles
```

---

## 📝 Step-by-Step Registration

### Step 1: Buat Module Seeder

**Location:** `database/seeders/{ModuleName}ModuleSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ProductManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting ProductManagementModuleSeeder...');

        // 1. Register modul ke table modules
        $module = $this->registerModule();

        // 2. Aktifkan untuk semua tenant
        $this->activateForTenants($module);

        $this->command->info('✅ ProductManagementModuleSeeder completed!');
    }

    /**
     * Register module to database
     */
    private function registerModule(): Module
    {
        $module = Module::updateOrCreate(
            ['code' => 'product-management'],  // Unique identifier
            [
                'name' => 'Manajemen Produk',  // ✅ WAJIB Bahasa Indonesia!
                'slug' => 'product-management',
                'description' => 'Modul untuk mengelola produk dan inventori',
                'icon' => 'shopping-bag',      // Feather Icons name
                'order' => 4,                  // Menu order (1-100)
                'is_active' => true
            ]
        );

        $this->command->info("✅ Module '{$module->name}' registered (ID: {$module->id})");

        return $module;
    }

    /**
     * Activate module for all tenants
     */
    private function activateForTenants(Module $module): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->warn('⚠️  No tenants found. Module registered but not activated.');
            return;
        }

        foreach ($tenants as $tenant) {
            // Sync to tenant_modules table
            $tenant->modules()->syncWithoutDetaching([
                $module->id => ['is_active' => true]
            ]);

            $this->command->info("  ✅ Activated for tenant: {$tenant->name}");

            // Set permissions for each role in tenant
            $this->setPermissionsForTenant($module, $tenant);
        }
    }

    /**
     * Set permissions for all roles in tenant
     */
    private function setPermissionsForTenant(Module $module, Tenant $tenant): void
    {
        $roles = Role::where('tenant_id', $tenant->id)->get();

        foreach ($roles as $role) {
            RoleModulePermission::updateOrCreate(
                [
                    'role_id' => $role->id,
                    'module_id' => $module->id
                ],
                [
                    'can_view' => true,
                    'can_create' => $this->canCreate($role),
                    'can_edit' => $this->canEdit($role),
                    'can_delete' => $this->canDelete($role),
                    'can_export' => true,
                    'can_import' => $this->canImport($role),
                ]
            );

            $this->command->info("    ✅ Permissions set for role: {$role->name}");
        }
    }

    /**
     * Determine if role can create
     */
    private function canCreate(Role $role): bool
    {
        return in_array($role->slug, [
            'super-admin',
            'tenant-admin',
            'manager'
        ]);
    }

    /**
     * Determine if role can edit
     */
    private function canEdit(Role $role): bool
    {
        return in_array($role->slug, [
            'super-admin',
            'tenant-admin',
            'manager'
        ]);
    }

    /**
     * Determine if role can delete
     */
    private function canDelete(Role $role): bool
    {
        return in_array($role->slug, [
            'super-admin',
            'tenant-admin'
        ]);
    }

    /**
     * Determine if role can import
     */
    private function canImport(Role $role): bool
    {
        return in_array($role->slug, [
            'super-admin',
            'tenant-admin',
            'manager'
        ]);
    }
}
```

### Step 2: Register Seeder

Tambahkan ke `DatabaseSeeder.php`:

```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    $this->call([
        ModuleSeeder::class,
        UserManagementModuleSeeder::class,
        // ... other module seeders
        ProductManagementModuleSeeder::class,  // ✅ Add here
    ]);
}
```

### Step 3: Run Seeder

```bash
# Run specific seeder
php artisan db:seed --class=ProductManagementModuleSeeder

# Or run all seeders
php artisan db:seed

# With output
php artisan db:seed --class=ProductManagementModuleSeeder -v
```

---

## 🔍 Module Fields Reference

### Required Fields

```php
[
    'name' => 'Manajemen Produk',        // Display name
    'slug' => 'product-management',      // URL identifier
    'code' => 'product-management',      // Unique code (same as slug)
    'is_active' => true                  // Global status
]
```

### Optional Fields

```php
[
    'description' => 'Modul untuk...',   // Module description
    'icon' => 'shopping-bag',            // Icon name (Feather Icons)
    'order' => 4,                        // Display order (default: 0)
]
```

### Field Validation

```php
'name'        => 'required|string|max:255',        // Bahasa Indonesia!
'slug'        => 'required|string|max:255|unique', // kebab-case
'code'        => 'required|string|max:255|unique', // kebab-case
'description' => 'nullable|string',
'icon'        => 'nullable|string|max:50',
'order'       => 'nullable|integer',
'is_active'   => 'boolean'
```

---

## 🎨 Icon Reference

Gunakan nama icon dari [Feather Icons](https://feathericons.com/):

```php
// Common icons untuk modul
'icon' => 'shopping-bag',    // Product/Inventory
'icon' => 'alert-triangle',  // Risk Management
'icon' => 'calendar',        // Activity Management
'icon' => 'file-text',       // Document Management
'icon' => 'mail',            // Correspondence
'icon' => 'users',           // User Management
'icon' => 'settings',        // Settings
'icon' => 'bar-chart',       // Reports/Analytics
'icon' => 'dollar-sign',     // Finance
'icon' => 'package',         // Inventory
```

---

## ✅ Verification Steps

### 1. Database Verification

```sql
-- Check module exists
SELECT * FROM modules WHERE code = 'product-management';

-- Expected result:
-- id | name              | slug                | code                | is_active
-- 5  | Manajemen Produk  | product-management  | product-management  | 1

-- Check tenant activation
SELECT
    t.name as tenant_name,
    tm.is_active,
    tm.created_at
FROM tenant_modules tm
JOIN tenants t ON t.id = tm.tenant_id
WHERE tm.module_id = (SELECT id FROM modules WHERE code = 'product-management');

-- Expected result: All tenants with is_active = 1

-- Check permissions
SELECT
    r.name as role_name,
    rmp.can_view,
    rmp.can_create,
    rmp.can_edit,
    rmp.can_delete
FROM role_module_permissions rmp
JOIN roles r ON r.id = rmp.role_id
WHERE rmp.module_id = (SELECT id FROM modules WHERE code = 'product-management');

-- Expected result: All roles with appropriate permissions
```

### 2. Superadmin UI Verification

```
1. Login sebagai Superadmin
2. Navigate to: /superadmin/modules
3. Verify:
   ✅ Module "Manajemen Produk" muncul di list
   ✅ Status is_active = true
   ✅ Icon muncul dengan benar
   ✅ Order sesuai

4. Click "Manage Tenants"
5. Verify:
   ✅ Semua tenant listed
   ✅ Module active untuk semua tenant
   ✅ Dapat toggle active/inactive

6. Click "Permissions" untuk tenant
7. Verify:
   ✅ Semua role listed
   ✅ Permissions sesuai dengan seeder
   ✅ Dapat edit permissions
```

### 3. Tenant User Verification

```
1. Login sebagai Tenant Admin
2. Check sidebar menu
3. Verify:
   ✅ Menu "Manajemen Produk" muncul
   ✅ Icon muncul dengan benar
   ✅ Urutan menu sesuai dengan order

4. Click menu
5. Verify:
   ✅ Dapat akses modul
   ✅ Tidak ada error 403
   ✅ CRUD operations berfungsi
```

---

## 🔄 Update Existing Module

Jika modul sudah ada dan perlu update:

```php
// Seeder akan update, bukan create baru
$module = Module::updateOrCreate(
    ['code' => 'product-management'],  // Find by code
    [
        'name' => 'Manajemen Produk (Updated)',  // Update name
        'description' => 'Updated description',   // Update description
        'icon' => 'package',                      // Update icon
        'order' => 10,                            // Update order
    ]
);

// Re-run seeder
php artisan db:seed --class=ProductManagementModuleSeeder
```

---

## 🆕 Add Module to New Tenant

Ketika tenant baru dibuat, modul otomatis tersinkronisasi:

```php
// Di TenantSeeder atau saat create tenant
$tenant = Tenant::create([...]);

// Sync all active modules
$activeModules = Module::where('is_active', true)->get();
foreach ($activeModules as $module) {
    $tenant->modules()->attach($module->id, ['is_active' => true]);

    // Set default permissions for tenant roles
    $roles = Role::where('tenant_id', $tenant->id)->get();
    foreach ($roles as $role) {
        RoleModulePermission::create([
            'role_id' => $role->id,
            'module_id' => $module->id,
            'can_view' => true,
            // ... other permissions
        ]);
    }
}
```

---

## 🚀 Production Deployment

### ⚠️ PENTING: Deployment ke Server Production

#### Apakah Seeder Aman untuk Production?

**✅ YA, AMAN!** Seeder menggunakan `updateOrCreate()` yang:

- ✅ Tidak menghapus data existing
- ✅ Hanya update jika sudah ada
- ✅ Create jika belum ada
- ✅ Tidak mengganggu tenant_modules atau permissions yang sudah di-customize

```php
// updateOrCreate() aman karena:
Module::updateOrCreate(
    ['code' => 'product-management'],  // Find by code
    [
        'name' => 'Manajemen Produk',  // Update these fields
        // ...
    ]
);
// Jika code sudah ada → UPDATE
// Jika code belum ada → CREATE
```

#### Apakah Otomatis Muncul di Superadmin?

**❌ TIDAK OTOMATIS** - Seeder harus dijalankan minimal sekali.

**Kenapa tidak otomatis?**

- Modul perlu diregistrasi ke database
- Permissions perlu di-setup
- Tenant activation perlu di-configure

#### Strategi Deployment

### Option 1: Manual Run (Simple)

Setelah deploy ke production:

```bash
# SSH ke server
ssh user@production-server

# Navigate to project
cd /path/to/siar

# Run seeder (AMAN!)
php artisan db:seed --class=ProductManagementModuleSeeder

# Verify
php artisan tinker
>>> Module::where('code', 'product-management')->first()
```

**Pros:**

- ✅ Full control
- ✅ Dapat verify sebelum run
- ✅ Mudah troubleshoot

**Cons:**

- ❌ Manual step (bisa lupa)
- ❌ Tidak automated

---

### Option 2: CI/CD Pipeline (Recommended)

Tambahkan ke GitHub Actions / deployment script:

```yaml
# .github/workflows/deploy.yml

name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      # ... build steps

      - name: Deploy to Server
        run: |
          ssh ${{ secrets.SSH_USER }}@${{ secrets.SSH_HOST }} << 'EOF'
            cd /path/to/siar
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            
            # Run module seeders (AMAN!)
            php artisan db:seed --class=ProductManagementModuleSeeder --force
            
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
          EOF
```

**Pros:**

- ✅ Fully automated
- ✅ Consistent deployment
- ✅ No manual steps

**Cons:**

- ❌ Perlu setup CI/CD

---

### Option 3: Migration-Based (Best Practice)

Buat migration untuk registrasi modul:

```php
// database/migrations/2024_11_20_000001_register_product_management_module.php

<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\RoleModulePermission;

return new class extends Migration
{
    public function up(): void
    {
        // Register module
        $module = Module::updateOrCreate(
            ['code' => 'product-management'],
            [
                'name' => 'Manajemen Produk',
                'slug' => 'product-management',
                'description' => 'Modul untuk mengelola produk dan inventori',
                'icon' => 'shopping-bag',
                'order' => 4,
                'is_active' => true
            ]
        );

        // Activate for all tenants
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            $tenant->modules()->syncWithoutDetaching([
                $module->id => ['is_active' => true]
            ]);

            // Set permissions
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    [
                        'can_view' => true,
                        'can_create' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                        'can_edit' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                        'can_delete' => in_array($role->slug, ['super-admin', 'tenant-admin']),
                        'can_export' => true,
                        'can_import' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // Optional: Remove module (biasanya tidak perlu)
        Module::where('code', 'product-management')->delete();
    }
};
```

**Deployment:**

```bash
# Otomatis run saat migrate
php artisan migrate --force
```

**Pros:**

- ✅ Fully automated dengan migrate
- ✅ Version controlled
- ✅ Rollback support
- ✅ Idempotent (aman dijalankan berkali-kali)

**Cons:**

- ❌ Migration file bisa banyak jika banyak modul

---

### Option 4: Laravel Cloud (Recommended for Laravel Cloud Users)

**✅ BEST OPTION untuk Laravel Cloud!**

Laravel Cloud menggunakan file `.laravel-cloud-build.sh` untuk deployment hooks.

#### Setup:

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
php artisan db:seed --class=ProductManagementModuleSeeder --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old caches
php artisan cache:clear

echo "✅ Deployment completed!"
```

#### Permissions:

```bash
# Make script executable (run once)
chmod +x .laravel-cloud-build.sh

# Commit to git
git add .laravel-cloud-build.sh
git commit -m "Add module seeder to deployment"
git push
```

#### Deployment Flow:

```
1. Push to GitHub
   ↓
2. Laravel Cloud detects push
   ↓
3. Runs .laravel-cloud-build.sh
   ↓
4. Seeder runs automatically ✅
   ↓
5. Module muncul di Superadmin ✅
```

#### Pros:

- ✅ Fully automated
- ✅ Runs on every deployment
- ✅ No manual steps
- ✅ Version controlled
- ✅ Idempotent (aman dijalankan berkali-kali)

#### Cons:

- ❌ Tidak ada (ini best practice untuk Laravel Cloud)

---

### Option 5: Post-Deploy Hook (Laravel Forge/Envoyer)

Jika menggunakan Laravel Forge atau Envoyer:

```bash
# Deployment Script
cd /home/forge/siar.com

git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force

# Run module seeders
php artisan db:seed --class=ProductManagementModuleSeeder --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

---

### Recommended Approach

#### **Untuk Laravel Cloud (RECOMMENDED):**

**Setup Once:**

```bash
# 1. Edit .laravel-cloud-build.sh
# Tambahkan seeder ke build script

# 2. Make executable
chmod +x .laravel-cloud-build.sh

# 3. Commit & push
git add .laravel-cloud-build.sh
git commit -m "Add module seeder to deployment"
git push
```

**Setelah itu:**

```
✅ Otomatis run setiap deployment
✅ Tidak perlu manual lagi
✅ Module langsung muncul di Superadmin
```

#### **Untuk Server Lain:**

1. **First Time Deployment:**

   ```bash
   # Run migration-based registration
   php artisan migrate --force
   ```

2. **Subsequent Deployments:**

   ```bash
   # Migration akan skip jika sudah run
   php artisan migrate --force
   ```

3. **Update Existing Module:**
   ```bash
   # Re-run seeder untuk update (AMAN!)
   php artisan db:seed --class=ProductManagementModuleSeeder --force
   ```

---

### Safety Guarantees

#### ✅ Aman untuk Production:

```php
// 1. updateOrCreate tidak menghapus data
Module::updateOrCreate(['code' => 'X'], [...]);  // ✅ AMAN

// 2. syncWithoutDetaching tidak menghapus tenant lain
$tenant->modules()->syncWithoutDetaching([...]);  // ✅ AMAN

// 3. updateOrCreate tidak menghapus permissions lain
RoleModulePermission::updateOrCreate([...], [...]);  // ✅ AMAN
```

#### ❌ TIDAK AMAN (Jangan gunakan):

```php
// 1. Jangan gunakan create() langsung
Module::create([...]);  // ❌ Error jika sudah ada

// 2. Jangan gunakan sync() tanpa WithoutDetaching
$tenant->modules()->sync([...]);  // ❌ Hapus yang lain

// 3. Jangan delete existing data
Module::where('code', 'X')->delete();  // ❌ Hapus data
```

---

### Verification After Deployment

```bash
# 1. Check module registered
php artisan tinker
>>> Module::where('code', 'product-management')->first()

# 2. Check tenant activation
>>> TenantModule::where('module_id', X)->get()

# 3. Check permissions
>>> RoleModulePermission::where('module_id', X)->count()

# 4. Test Superadmin access
# Navigate to: https://your-domain.com/superadmin/modules
```

---

### Rollback Strategy

Jika ada masalah:

```bash
# 1. Disable module via Superadmin UI
# Navigate to /superadmin/modules
# Click "Disable" untuk modul

# 2. Or via tinker
php artisan tinker
>>> $module = Module::where('code', 'product-management')->first()
>>> $module->update(['is_active' => false])

# 3. Or remove from specific tenant
>>> $tenant = Tenant::find(2)
>>> $tenant->modules()->detach($module->id)
```

---

## 🚨 Common Issues

### Issue 1: Module Tidak Muncul di Superadmin

**Cause:** Seeder belum dijalankan

**Fix:**

```bash
php artisan db:seed --class=ProductManagementModuleSeeder
```

### Issue 2: Module Tidak Muncul di Tenant

**Cause:** Module tidak di-sync ke tenant_modules

**Fix:**

```php
// Re-run seeder atau manual sync
$module = Module::where('code', 'product-management')->first();
$tenant = Tenant::find(2);
$tenant->modules()->syncWithoutDetaching([$module->id => ['is_active' => true]]);
```

### Issue 3: Permissions Tidak Ada

**Cause:** RoleModulePermission tidak dibuat

**Fix:**

```php
// Re-run seeder atau manual create
$module = Module::where('code', 'product-management')->first();
$role = Role::find(2);
RoleModulePermission::create([
    'role_id' => $role->id,
    'module_id' => $module->id,
    'can_view' => true,
    // ... other permissions
]);
```

---

## 📚 Related Documentation

- `MODULE-DEVELOPMENT-GUIDE.md` - Complete development guide
- `MODULE-STRUCTURE-STANDARDS.md` - Module structure standards
- `MODULE-CHECKLIST.md` - Development checklist
- `/docs/RBAC-MULTITENANT-IMPROVEMENTS.md` - RBAC system overview

---

**Last Updated:** 20 November 2025  
**Maintained by:** Development Team  
**Status:** 🔒 MANDATORY - All modules MUST be registered
