# Module Structure Standards

> **⚠️ KONSENSUS WAJIB:** Semua modul di SIAR WAJIB mengikuti struktur ini. JANGAN membuat file di luar struktur modular!

**Version:** 1.0  
**Last Updated:** 20 November 2025  
**Status:** 🔒 MANDATORY

---

## 📐 Prinsip Dasar

### 1. Self-Contained Modules

Setiap modul harus **self-contained** - semua file terkait modul berada dalam satu folder.

```
✅ BENAR: Semua file dalam modules/{ModuleName}/
❌ SALAH: File tercecer di app/, resources/, routes/
```

### 2. Consistent Structure

Semua modul mengikuti struktur folder yang sama untuk konsistensi.

### 3. Clear Namespace

Namespace mengikuti struktur folder: `Modules\{ModuleName}\{SubFolder}`

---

## 🏗️ Struktur Folder Standar

### Complete Structure

```
modules/{ModuleName}/
├── Config/
│   └── config.php                  # Module configuration
│
├── Database/
│   ├── Migrations/                 # Module-specific migrations
│   │   └── 2024_01_01_create_{table}_table.php
│   ├── Seeders/                    # Module-specific seeders
│   │   └── {Name}Seeder.php
│   └── Factories/                  # Model factories (optional)
│       └── {Name}Factory.php
│
├── Http/
│   ├── Controllers/                # Module controllers
│   │   └── {Name}Controller.php
│   ├── Middleware/                 # Module-specific middleware (optional)
│   │   └── {Name}Middleware.php
│   ├── Requests/                   # Form requests (optional)
│   │   ├── Store{Name}Request.php
│   │   └── Update{Name}Request.php
│   └── routes.php                  # Module routes (WAJIB)
│
├── Models/                         # Module models
│   └── {Name}.php
│
├── Providers/
│   └── {ModuleName}ServiceProvider.php  # Module service provider (WAJIB)
│
├── Resources/
│   ├── Views/                      # Module views
│   │   └── {name}/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       ├── edit.blade.php
│   │       └── show.blade.php
│   ├── Assets/                     # Module assets (optional)
│   │   ├── js/
│   │   │   └── {name}.js
│   │   └── css/
│   │       └── {name}.css
│   └── lang/                       # Translations (optional)
│       └── en/
│           └── messages.php
│
├── Services/                       # Business logic (optional)
│   └── {Name}Service.php
│
├── Repositories/                   # Data access layer (optional)
│   └── {Name}Repository.php
│
├── Events/                         # Module events (optional)
│   └── {Name}Created.php
│
├── Listeners/                      # Event listeners (optional)
│   └── Send{Name}Notification.php
│
├── Jobs/                           # Queue jobs (optional)
│   └── Process{Name}.php
│
├── Notifications/                  # Notifications (optional)
│   └── {Name}Notification.php
│
├── Tests/                          # Module tests (optional)
│   ├── Unit/
│   │   └── {Name}Test.php
│   └── Feature/
│       └── {Name}ControllerTest.php
│
├── module.json                     # Module metadata (optional)
└── README.md                       # Module documentation (recommended)
```

---

## ✅ File yang WAJIB Ada

### Minimum Required Files

```
modules/{ModuleName}/
├── Http/
│   ├── Controllers/{Name}Controller.php  ✅ WAJIB
│   └── routes.php                        ✅ WAJIB
├── Models/{Name}.php                     ✅ WAJIB
├── Providers/{ModuleName}ServiceProvider.php  ✅ WAJIB
└── Resources/Views/{name}/
    └── index.blade.php                   ✅ WAJIB
```

### Recommended Files

```
modules/{ModuleName}/
├── Database/
│   ├── Migrations/                       ⭐ RECOMMENDED
│   └── Seeders/                          ⭐ RECOMMENDED
├── Http/Requests/                        ⭐ RECOMMENDED
├── Services/                             ⭐ RECOMMENDED
├── Tests/                                ⭐ RECOMMENDED
└── README.md                             ⭐ RECOMMENDED
```

---

## 🚫 File yang TIDAK BOLEH di Folder Modul

### Exceptions (File di Luar modules/)

Hanya file berikut yang boleh di luar folder `modules/`:

```
✅ ALLOWED di luar modules/:

1. Policies
   app/Policies/{Name}Policy.php
   Reason: Policies di-centralize untuk consistency

2. Shared Services
   app/Services/PermissionService.php
   app/Services/NotificationService.php
   Reason: Digunakan oleh multiple modules

3. Shared Traits
   app/Traits/BelongsToTenant.php
   app/Traits/HasUuid.php
   Reason: Digunakan oleh multiple modules

4. Module Registration Seeder
   database/seeders/{Name}ModuleSeeder.php
   Reason: Register module ke database

5. Global Middleware
   app/Http/Middleware/CheckModulePermission.php
   Reason: Digunakan oleh semua modules
```

### ❌ TIDAK BOLEH di Luar modules/

```
❌ SALAH - Jangan buat file ini di luar modules/:

app/Models/{Name}.php                    ← SALAH! Harus di modules/
app/Http/Controllers/{Name}Controller.php ← SALAH!
resources/views/{name}/                  ← SALAH!
routes/{name}.php                        ← SALAH!
database/migrations/create_{name}_table.php ← SALAH! (kecuali shared table)
```

---

## 📝 Namespace Convention

### Standard Namespace Pattern

```php
// ✅ BENAR: Namespace mengikuti folder structure

// Models
namespace Modules\ProductManagement\Models;

// Controllers
namespace Modules\ProductManagement\Http\Controllers;

// Requests
namespace Modules\ProductManagement\Http\Requests;

// Services
namespace Modules\ProductManagement\Services;

// Providers
namespace Modules\ProductManagement\Providers;
```

### ❌ SALAH: Namespace Tidak Konsisten

```php
// ❌ SALAH - Namespace tidak dimulai dengan Modules\
namespace App\Models\Product;
namespace ProductManagement\Models;

// ❌ SALAH - Namespace tidak sesuai folder
namespace Modules\Product\Models;  // Folder: ProductManagement
```

---

## 📊 Module Registration (Database)

### ⚠️ CRITICAL: Registrasi Modul ke Database

> **WAJIB:** Setiap modul HARUS diregistrasi ke database agar:
>
> - ✅ Muncul di halaman Superadmin (`/superadmin/modules`)
> - ✅ Dapat di-enable/disable per tenant oleh Superadmin
> - ✅ Tersinkronisasi dengan semua tenant
> - ✅ Permissions dapat diatur per role

### Database Tables

Modul menggunakan 3 tables utama:

```
1. modules                    # Master data modul
2. tenant_modules             # Aktivasi modul per tenant
3. role_module_permissions    # Permissions per role
```

### Module Seeder (WAJIB)

Setiap modul WAJIB punya seeder untuk registrasi:

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
    public function run(): void
    {
        // 1. Register modul ke table modules
        $module = Module::updateOrCreate(
            ['code' => 'product-management'],  // Unique key
            [
                'name' => 'Manajemen Produk',  // ✅ Bahasa Indonesia!
                'slug' => 'product-management',
                'description' => 'Modul untuk mengelola produk dan inventori',
                'icon' => 'shopping-bag',      // Icon name (Feather Icons)
                'order' => 4,                  // Menu order
                'is_active' => true
            ]
        );

        // 2. Aktifkan untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Sync ke tenant_modules table
            $tenant->modules()->syncWithoutDetaching([
                $module->id => ['is_active' => true]
            ]);

            // 3. Set permissions untuk setiap role
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                RoleModulePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'module_id' => $module->id
                    ],
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
}
```

### Module Fields Explanation

```php
[
    'name' => 'Manajemen Produk',        // Display name (Bahasa Indonesia!)
    'slug' => 'product-management',      // URL identifier (kebab-case)
    'code' => 'product-management',      // Unique code (kebab-case)
    'description' => '...',              // Module description
    'icon' => 'shopping-bag',            // Icon name (Feather Icons)
    'order' => 4,                        // Display order in menu
    'is_active' => true                  // Global active status
]
```

### Register Seeder

Tambahkan seeder ke `DatabaseSeeder.php`:

```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    $this->call([
        // ... other seeders
        ProductManagementModuleSeeder::class,  // ✅ Add here
    ]);
}
```

### Run Seeder

```bash
# Run specific seeder
php artisan db:seed --class=ProductManagementModuleSeeder

# Or run all seeders
php artisan db:seed
```

### Verification

Setelah run seeder, verify:

```sql
-- 1. Check module exists
SELECT * FROM modules WHERE code = 'product-management';

-- 2. Check tenant activation
SELECT t.name, tm.is_active
FROM tenant_modules tm
JOIN tenants t ON t.id = tm.tenant_id
WHERE tm.module_id = (SELECT id FROM modules WHERE code = 'product-management');

-- 3. Check permissions
SELECT r.name, rmp.*
FROM role_module_permissions rmp
JOIN roles r ON r.id = rmp.role_id
WHERE rmp.module_id = (SELECT id FROM modules WHERE code = 'product-management');
```

### Superadmin Access

Setelah seeder dijalankan:

1. **View Modules:** `/superadmin/modules`

   - Modul akan muncul di list
   - Dapat melihat status active/inactive

2. **Manage Per Tenant:** `/superadmin/tenants/{tenant}/modules`

   - Enable/disable modul per tenant
   - Set permissions per role

3. **Sync to Tenants:**
   - Superadmin dapat sync modul ke tenant baru
   - Modul otomatis tersedia untuk tenant

---

## 🔧 ServiceProvider Template

Setiap modul WAJIB punya ServiceProvider:

```php
<?php

namespace Modules\ProductManagement\Providers;

use Illuminate\Support\ServiceProvider;

class ProductManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register module services
        $this->app->singleton(ProductService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'product-management');

        // Load migrations (optional)
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Load translations (optional)
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'product-management');

        // Publish assets (optional)
        $this->publishes([
            __DIR__ . '/../Resources/Assets' => public_path('modules/product-management'),
        ], 'product-management-assets');
    }
}
```

---

## 🔗 Routes Configuration

### routes.php Template

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductManagement\Http\Controllers\ProductController;

// ✅ BENAR: Middleware, prefix, name sesuai standard
Route::middleware(['web', 'auth', 'tenant', 'module.permission:product-management'])
    ->prefix('product-management')
    ->name('modules.product-management.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [ProductController::class, 'dashboard'])
            ->name('dashboard');

        // Resource routes
        Route::resource('products', ProductController::class);

        // Custom routes
        Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])
            ->name('products.duplicate');
    });
```

---

## 📦 Composer Autoloading

### composer.json Configuration

Pastikan PSR-4 autoloading sudah configured:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
    }
  }
}
```

### After Creating New Module

```bash
# WAJIB run setelah membuat modul baru
composer dump-autoload

# Verify autoloading
composer dump-autoload -o
```

---

## 🎯 View Namespace

### Accessing Module Views

```php
// ✅ BENAR: Gunakan namespace view
return view('product-management::products.index');
return view('product-management::products.create');

// ❌ SALAH: Path langsung
return view('products.index');  // SALAH!
```

### View Namespace Registration

```php
// Di ServiceProvider boot()
$this->loadViewsFrom(
    __DIR__ . '/../Resources/Views',
    'product-management'  // ← View namespace
);
```

---

## 🗂️ Migration Naming

### Module Migration Naming

```
✅ BENAR:
modules/ProductManagement/Database/Migrations/
└── 2024_01_01_000001_create_products_table.php

Format: {timestamp}_create_{table}_table.php
```

### Migration Class

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

---

## ✅ Validation Checklist

Sebelum commit modul baru, pastikan:

### Structure Validation

- [ ] ✅ Semua file modul dalam folder `modules/{ModuleName}/`
- [ ] ✅ Tidak ada file modul di `app/Models/`, `app/Http/Controllers/`, dll
- [ ] ✅ Struktur folder mengikuti standard
- [ ] ✅ File WAJIB sudah ada (Controller, Model, routes, ServiceProvider, Views)

### Namespace Validation

- [ ] ✅ Semua namespace dimulai dengan `Modules\{ModuleName}\`
- [ ] ✅ Namespace sesuai dengan folder structure
- [ ] ✅ No namespace conflicts

### ServiceProvider Validation

- [ ] ✅ ServiceProvider exists di `Providers/`
- [ ] ✅ ServiceProvider registered di `config/app.php`
- [ ] ✅ Routes loaded dengan `loadRoutesFrom()`
- [ ] ✅ Views loaded dengan `loadViewsFrom()`

### Autoloading Validation

- [ ] ✅ `composer dump-autoload` sudah dijalankan
- [ ] ✅ No autoload errors
- [ ] ✅ Classes dapat di-import dengan benar

---

## 🚀 Quick Setup Commands

### Create Module Structure

```bash
# Create module folder structure
MODULE_NAME="ProductManagement"

mkdir -p modules/$MODULE_NAME/{Config,Database/{Migrations,Seeders},Http/{Controllers,Middleware,Requests},Models,Providers,Resources/{Views,Assets/{js,css}},Services,Tests/{Unit,Feature}}

# Create required files
touch modules/$MODULE_NAME/Http/routes.php
touch modules/$MODULE_NAME/Providers/${MODULE_NAME}ServiceProvider.php
touch modules/$MODULE_NAME/README.md

# Autoload
composer dump-autoload
```

---

## 📚 Examples

### ✅ Good Example: ProductManagement Module

```
modules/ProductManagement/
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php
│   ├── Requests/
│   │   ├── StoreProductRequest.php
│   │   └── UpdateProductRequest.php
│   └── routes.php
├── Models/
│   └── Product.php
├── Providers/
│   └── ProductManagementServiceProvider.php
├── Resources/
│   └── Views/
│       └── products/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
├── Services/
│   └── ProductService.php
└── README.md
```

### ❌ Bad Example: Scattered Files

```
❌ JANGAN SEPERTI INI:
app/Models/Product.php
app/Http/Controllers/ProductController.php
resources/views/products/
routes/product.php
```

---

## 🔗 Related Documentation

- `MODULE-DEVELOPMENT-GUIDE.md` - Complete development guide
- `MODULE-CHECKLIST.md` - Development checklist
- `COMMON-MISTAKES.md` - Common mistakes to avoid
- `MODULE-NAMING-REFERENCE.md` - Naming conventions

---

**Last Updated:** 20 November 2025  
**Maintained by:** Development Team  
**Status:** 🔒 MANDATORY - All modules MUST follow this structure
