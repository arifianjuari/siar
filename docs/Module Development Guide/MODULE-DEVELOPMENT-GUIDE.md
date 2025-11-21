# Panduan Pengembangan Modul SIAR

**Version:** 1.0  
**Last Updated:** 20 November 2025

## 📋 Daftar Isi

1. [Module Structure](#module-structure)
2. [Naming Convention](#naming-convention)
3. [Checklist Pembuatan Modul](#checklist-pembuatan-modul)
4. [Step-by-Step Guide](#step-by-step-guide)
5. [Common Pitfalls](#common-pitfalls)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## Module Structure

### 🏗️ Konsensus Struktur Modular

> **⚠️ CRITICAL:** Semua modul WAJIB mengikuti struktur ini. JANGAN membuat file di luar struktur modular!

#### Lokasi Modul

```
✅ BENAR: Semua file modul di dalam folder modules/
modules/
├── ProductManagement/
├── RiskManagement/
├── ActivityManagement/
└── [ModuleName]/

❌ SALAH: File modul tercecer di tempat lain
app/Models/Product.php              ← SALAH! Harus di modules/
app/Http/Controllers/ProductController.php  ← SALAH!
resources/views/products/           ← SALAH!
```

#### Struktur Folder Standar

```
modules/{ModuleName}/
├── Config/
│   └── config.php                  # Module configuration
├── Database/
│   ├── Migrations/                 # Module-specific migrations
│   │   └── 2024_01_01_create_products_table.php
│   └── Seeders/                    # Module-specific seeders
│       └── ProductSeeder.php
├── Http/
│   ├── Controllers/                # Module controllers
│   │   └── ProductController.php
│   ├── Middleware/                 # Module-specific middleware (optional)
│   ├── Requests/                   # Form requests (optional)
│   │   └── StoreProductRequest.php
│   └── routes.php                  # Module routes (WAJIB)
├── Models/                         # Module models
│   └── Product.php
├── Providers/
│   └── {ModuleName}ServiceProvider.php  # Module service provider (WAJIB)
├── Resources/
│   ├── Views/                      # Module views
│   │   └── products/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       ├── edit.blade.php
│   │       └── show.blade.php
│   └── Assets/                     # Module assets (optional)
│       ├── js/
│       └── css/
├── Services/                       # Business logic (optional)
│   └── ProductService.php
├── Tests/                          # Module tests (optional)
│   ├── Unit/
│   └── Feature/
└── module.json                     # Module metadata (optional)
```

#### File yang WAJIB Ada

```
✅ WAJIB:
- Http/routes.php
- Http/Controllers/{Name}Controller.php
- Models/{Name}.php
- Providers/{ModuleName}ServiceProvider.php
- Resources/Views/{name}/index.blade.php

✅ OPTIONAL (tapi recommended):
- Database/Migrations/
- Database/Seeders/
- Http/Requests/
- Services/
- Tests/
```

#### File di Luar Modul

Hanya file berikut yang boleh di luar folder `modules/`:

```
✅ ALLOWED:
app/Policies/{Name}Policy.php           # Policies tetap di app/Policies
app/Services/PermissionService.php      # Shared services
app/Traits/BelongsToTenant.php          # Shared traits
database/seeders/{Name}ModuleSeeder.php # Module registration seeder
```

#### Namespace Convention

```php
// ✅ BENAR: Namespace mengikuti folder structure
namespace Modules\ProductManagement\Http\Controllers;
namespace Modules\ProductManagement\Models;
namespace Modules\ProductManagement\Services;

// ❌ SALAH: Namespace tidak konsisten
namespace App\Http\Controllers\Product;  // SALAH!
namespace ProductManagement\Models;      // SALAH!
```

#### Autoloading

Pastikan `composer.json` sudah configure PSR-4 autoloading:

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

Setelah menambah modul baru, WAJIB run:

```bash
composer dump-autoload
```

---

## Naming Convention

### ⚠️ PENTING: Module Identification

Setiap modul memiliki **3 identifier** yang harus konsisten:

| Identifier | Format          | Contoh               | Digunakan Di          |
| ---------- | --------------- | -------------------- | --------------------- |
| **Name**   | Title Case      | `Product Management` | UI, Database          |
| **Slug**   | kebab-case      | `product-management` | URL, Routes           |
| **Code**   | UPPERCASE_SNAKE | `PRODUCT_MANAGEMENT` | Permissions, Policies |

### ✅ Aturan Naming

#### 1. Module Name (Database)

```
Format: Title Case dengan spasi
Bahasa: WAJIB Bahasa Indonesia (untuk konsistensi UI)

Contoh:
  ✅ "Manajemen Produk"
  ✅ "Manajemen Risiko"
  ✅ "Pengelolaan Kegiatan"
  ❌ "Product Management" (Bahasa Inggris)
  ❌ "product-management" (Format salah)

⚠️ PENTING: Gunakan Bahasa Indonesia untuk menghindari duplicate menu!
```

#### 2. Module Slug (URL)

```
Format: kebab-case (lowercase dengan dash)
Aturan: Akhiri dengan "-management" untuk konsistensi

Contoh:
  ✅ "product-management"
  ✅ "risk-management"
  ❌ "ProductManagement"
  ❌ "product" (tanpa suffix)
```

#### 3. Module Code (Permissions)

```
Format: UPPERCASE_SNAKE_CASE
Aturan: Gunakan underscore (_) sebagai separator

Contoh:
  ✅ "PRODUCT_MANAGEMENT"
  ✅ "RISK_MANAGEMENT"
  ❌ "product-management"
```

---

## Checklist Pembuatan Modul

### ✅ Pre-Development

- [ ] Tentukan nama modul (Title Case)
- [ ] Buat slug (kebab-case dengan suffix `-management`)
- [ ] Buat code (UPPERCASE_SNAKE_CASE)
- [ ] Pastikan ketiga identifier konsisten

### ✅ Database Setup

- [ ] Insert record ke table `modules`
- [ ] Insert ke `tenant_modules` untuk tenant yang perlu
- [ ] Insert permissions ke `role_module_permissions`

### ✅ Code Structure

- [ ] Buat folder modul di `modules/{ModuleName}/`
- [ ] Buat ServiceProvider
- [ ] Buat routes.php dengan middleware yang benar
- [ ] Buat Controller dengan Gate authorization
- [ ] Buat Model (extend BelongsToTenant trait)
- [ ] Buat Policy (extend BasePolicy dengan moduleCode yang benar)

### ✅ Registration

- [ ] Register ServiceProvider di `config/app.php`
- [ ] Register Policy di `AuthServiceProvider.php`
- [ ] Run `composer dump-autoload`

### ✅ Testing

- [ ] Clear all caches
- [ ] Test middleware authorization
- [ ] Test policy authorization
- [ ] Test CRUD operations

---

## Step-by-Step Guide

### Step 1: Tentukan Identifiers

```php
// Contoh: Membuat modul "Inventory Management"
$moduleName = "Inventory Management";      // Title Case
$moduleSlug = "inventory-management";      // kebab-case
$moduleCode = "INVENTORY_MANAGEMENT";      // UPPERCASE_SNAKE
$moduleFolderName = "InventoryManagement"; // PascalCase
```

### Step 2: Buat Module Seeder (WAJIB untuk Registrasi)

> **⚠️ PENTING:** Seeder ini WAJIB dibuat agar modul teridentifikasi di halaman Superadmin dan dapat disinkronisasi dengan tenant!

**File:** `database/seeders/InventoryManagementModuleSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class InventoryManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Register modul ke database (untuk Superadmin)
        $module = Module::updateOrCreate(
            ['code' => 'inventory-management'],  // Unique identifier
            [
                'name' => 'Manajemen Inventori',  // ✅ Bahasa Indonesia!
                'slug' => 'inventory-management',
                'description' => 'Modul untuk mengelola inventori dan stok barang',
                'icon' => 'box',  // Icon untuk UI
                'order' => 5,     // Urutan di menu
                'is_active' => true
            ]
        );

        $this->command->info("✅ Modul '{$module->name}' berhasil diregistrasi");

        // 2. Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Sync modul ke tenant (akan muncul di halaman Superadmin)
            $tenant->modules()->syncWithoutDetaching([
                $module->id => ['is_active' => true]
            ]);

            $this->command->info("✅ Modul diaktifkan untuk tenant: {$tenant->name}");

            // 3. Set permissions untuk setiap role di tenant
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

                $this->command->info("  ✅ Permissions set untuk role: {$role->name}");
            }
        }

        $this->command->info('🎉 InventoryManagementModuleSeeder selesai!');
    }
}
```

**Penjelasan:**

- `Module::updateOrCreate()` - Register modul ke table `modules` (muncul di Superadmin)
- `$tenant->modules()->sync()` - Aktifkan modul untuk tenant (table `tenant_modules`)
- `RoleModulePermission::updateOrCreate()` - Set permissions per role

**Hasil:**

- ✅ Modul muncul di halaman `/superadmin/modules`
- ✅ Superadmin bisa enable/disable modul per tenant
- ✅ Modul tersinkronisasi dengan semua tenant

### Step 3: Buat Routes

**File:** `modules/InventoryManagement/Http/routes.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryManagement\Http\Controllers\InventoryController;

// ⚠️ PENTING: Gunakan MODULE CODE (bukan slug) untuk middleware!

// ✅ BENAR: Gunakan module CODE
Route::middleware(['web', 'auth', 'tenant', 'module:INVENTORY_MANAGEMENT'])
    ->prefix('inventory-management')  // Gunakan slug untuk URL
    ->name('modules.inventory-management.')
    ->group(function () {
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
        Route::resource('items', InventoryController::class);
    });

// ❌ SALAH: Jangan gunakan slug untuk middleware
// Route::middleware(['module:inventory-management']) // SALAH!
```

### Step 4: Buat Model

**File:** `modules/InventoryManagement/Models/Inventory.php`

```php
<?php

namespace Modules\InventoryManagement\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Inventory extends Model
{
    use BelongsToTenant;  // ⚠️ WAJIB untuk tenant isolation

    protected $fillable = ['tenant_id', 'name', 'code', 'quantity'];
}
```

### Step 5: Buat Policy

**File:** `app/Policies/InventoryPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InventoryPolicy extends BasePolicy
{
    // ⚠️ PENTING: Gunakan MODULE CODE (bukan slug)!
    protected string $moduleCode = 'INVENTORY_MANAGEMENT';  // ✅ BENAR

    // ❌ SALAH:
    // protected string $moduleCode = 'inventory-management';  // SALAH!

    public function viewAny(User $user): bool
    {
        return parent::viewAny($user);
    }

    public function create(User $user): bool
    {
        return parent::create($user);
    }
}
```

### Step 6: Buat Controller

**File:** `modules/InventoryManagement/Http/Controllers/InventoryController.php`

```php
<?php

namespace Modules\InventoryManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\InventoryManagement\Models\Inventory;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Inventory::class);

        $items = Inventory::paginate(10);
        return view('inventory-management::inventory.index', compact('items'));
    }

    public function create()
    {
        Gate::authorize('create', Inventory::class);
        return view('inventory-management::inventory.create');
    }
}
```

### Step 7: Register ServiceProvider

**File:** `config/app.php`

```php
'providers' => [
    // ... providers lain

    // ✅ Tambahkan ServiceProvider modul baru
    Modules\InventoryManagement\Providers\InventoryManagementServiceProvider::class,
],
```

### Step 8: Register Policy

**File:** `app/Providers/AuthServiceProvider.php`

```php
use Modules\InventoryManagement\Models\Inventory;
use App\Policies\InventoryPolicy;

protected $policies = [
    // ✅ Tambahkan Policy modul baru
    Inventory::class => InventoryPolicy::class,
];
```

### Step 9: Configure Sidebar URL

**File:** `resources/views/layouts/partials/sidebar.blade.php`

> **⚠️ PENTING:** Setiap modul baru WAJIB ditambahkan kondisi URL di sidebar agar link mengarah ke dashboard modul, bukan halaman detail modul!

**Cari bagian ini:**

```php
// Sekitar baris 170-175
if ($module->slug == 'user-management') {
    $moduleUrl = 'javascript:void(0);';
} elseif ($module->slug == 'product-management') {
    $moduleUrl = url('product-management/products');
} elseif ($module->slug == 'risk-management') {
    $moduleUrl = url('risk-management/dashboard');
}
```

**Tambahkan kondisi untuk modul baru:**

```php
} elseif ($module->slug == 'inventory-management') {
    $moduleUrl = url('inventory-management'); // ✅ Sesuai dengan route prefix
} else {
    $moduleUrl = url('modules/' . $module->slug);
}
```

**Penjelasan:**

- URL harus sesuai dengan **route prefix** yang didefinisikan di `routes.php`
- Jika route prefix adalah `inventory-management`, maka URL adalah `url('inventory-management')`
- Jika ada dashboard khusus, gunakan `url('inventory-management/dashboard')`

**Dampak jika tidak dikonfigurasi:**

- ❌ Link sidebar mengarah ke `/modules/inventory-management` (halaman detail modul)
- ❌ User tidak bisa langsung akses dashboard/fitur modul
- ❌ User harus manual ganti URL

### Step 10: Verify Route Order in web.php

**File:** `routes/web.php`

> **⚠️ CRITICAL:** Route catch-all `/{slug}` HARUS berada di paling bawah dalam group `modules.*`!

**Cek urutan route:**

```php
// routes/web.php
Route::middleware(['auth', 'tenant'])->prefix('modules')->name('modules.')->group(function () {
    // ✅ Route spesifik di ATAS
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::post('/request-activation', [ModuleController::class, 'requestActivation'])->name('request-activation');

    // ✅ Route catch-all di PALING BAWAH
    Route::get('/{slug}', [ModuleController::class, 'show'])->name('show');
});
```

**Penjelasan:**

- Route catch-all `/{slug}` akan menangkap SEMUA request ke `/modules/*`
- Jika diletakkan di atas, route modul spesifik tidak akan pernah tercapai
- **HARUS** diletakkan di paling bawah agar route spesifik diproses terlebih dahulu

**Dampak jika salah urutan:**

- ❌ URL `/modules/inventory-management` menampilkan halaman detail modul
- ❌ Bukan dashboard fungsional modul
- ❌ User tidak bisa akses fitur modul

### Step 11: Run Commands

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Common Pitfalls

> **⚠️ CRITICAL:** Kesalahan berikut sering terjadi dan menyebabkan error 403, cache issues, dan duplicate menu. Baca dengan teliti!

### ❌ Pitfall #1: Menggunakan Slug di Middleware/Policy

**SALAH:**

```php
// routes.php
Route::middleware(['module:inventory-management']) // ❌ SALAH!

// Policy
protected string $moduleCode = 'inventory-management'; // ❌ SALAH!
```

**BENAR:**

```php
// routes.php
Route::middleware(['module:INVENTORY_MANAGEMENT']) // ✅ BENAR!

// Policy
protected string $moduleCode = 'INVENTORY_MANAGEMENT'; // ✅ BENAR!
```

### ❌ Pitfall #2: Lupa Insert Permission Records

**Masalah:** Menu tidak muncul di sidebar

**Solusi:**

```sql
INSERT INTO role_module_permissions (role_id, module_id, can_view)
VALUES (2, @module_id, 1);
```

### ❌ Pitfall #3: Model Tidak Pakai BelongsToTenant

**SALAH:**

```php
class Inventory extends Model { } // ❌ Tidak ada trait
```

**BENAR:**

```php
class Inventory extends Model {
    use BelongsToTenant; // ✅ Pakai trait
}
```

### ❌ Pitfall #4: Lupa Clear Cache

Selalu clear cache setelah perubahan:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### ❌ Pitfall #5: Menggunakan tenantScope() Manual

**SALAH:**

```php
// Controller
$products = Product::tenantScope()->get(); // ❌ SALAH!
$product = Product::tenantScope()->findOrFail($id); // ❌ SALAH!
```

**BENAR:**

```php
// Controller - BelongsToTenant trait sudah apply global scope otomatis
$products = Product::query()->get(); // ✅ BENAR!
$product = Product::findOrFail($id); // ✅ BENAR!
```

**Penjelasan:** Trait `BelongsToTenant` sudah menerapkan global scope secara otomatis, jadi tidak perlu memanggil `tenantScope()` manual.

### ❌ Pitfall #6: Nama Modul Tidak Konsisten (Bahasa)

**SALAH:**

```php
// Seeder
'name' => 'Product Management', // ❌ Bahasa Inggris
```

**BENAR:**

```php
// Seeder
'name' => 'Manajemen Produk', // ✅ Bahasa Indonesia
```

**Dampak:** Jika nama modul tidak konsisten dengan modul lain, akan muncul **duplicate menu** di sidebar.

**Aturan:**

- ✅ Gunakan **Bahasa Indonesia** untuk semua nama modul
- ✅ Contoh: "Manajemen Produk", "Manajemen Risiko", "Pengelolaan Kegiatan"
- ❌ Hindari: "Product Management", "Risk Management"

### ❌ Pitfall #7: Middleware Salah untuk Permission Check

**SALAH:**

```php
// routes.php
Route::middleware(['module:product-management']) // ❌ Middleware lama/salah
```

**BENAR:**

```php
// routes.php
Route::middleware(['module.permission:product-management']) // ✅ Middleware baru
```

**Penjelasan:** Gunakan middleware `module.permission` (bukan `module`) untuk permission checking yang benar dengan `PermissionService`.

### ❌ Pitfall #8: Policy Method Signature Tidak Match BasePolicy

**SALAH:**

```php
class ProductPolicy extends BasePolicy
{
    // ❌ Type hint terlalu spesifik
    public function view(User $user, Product $product): bool
    {
        return parent::view($user, $product);
    }
}
```

**BENAR:**

```php
class ProductPolicy extends BasePolicy
{
    // ✅ Type hint generic Model
    public function view(User $user, Model $product): bool
    {
        return parent::view($user, $product);
    }
}
```

**Penjelasan:** Method signature harus match dengan `BasePolicy` yang menggunakan `Model` type hint, bukan model spesifik.

### ❌ Pitfall #9: File Modul Tercecer di Luar Folder modules/

**SALAH:**

```
❌ File tercecer di berbagai tempat:
app/Models/Product.php
app/Http/Controllers/ProductController.php
resources/views/products/
routes/product.php
```

**BENAR:**

```
✅ Semua file modul dalam satu folder:
modules/ProductManagement/
├── Http/Controllers/ProductController.php
├── Models/Product.php
├── Resources/Views/products/
└── Http/routes.php
```

**Dampak:**

- Sulit maintenance
- Tidak konsisten dengan modul lain
- Namespace tidak terorganisir
- Sulit untuk enable/disable modul

**Fix:**

1. Buat folder structure di `modules/{ModuleName}/`
2. Pindahkan semua file ke folder modul
3. Update namespace ke `Modules\{ModuleName}\...`
4. Run `composer dump-autoload`

### ❌ Pitfall #10: Lupa Konfigurasi Sidebar URL

**Masalah:** Link di sidebar mengarah ke halaman detail modul, bukan dashboard

**Penyebab:** Tidak menambahkan kondisi URL untuk modul baru di sidebar

**Solusi:**

1. Buka `resources/views/layouts/partials/sidebar.blade.php`
2. Cari bagian yang generate `$moduleUrl` (sekitar baris 170-175)
3. Tambahkan kondisi untuk modul baru:

```php
elseif ($module->slug == 'your-module-slug') {
    $moduleUrl = url('your-module-slug'); // Sesuai dengan route prefix
}
```

**Contoh:**

```php
// Untuk modul Inventory Management dengan route prefix 'inventory-management'
elseif ($module->slug == 'inventory-management') {
    $moduleUrl = url('inventory-management');
}

// Untuk modul dengan dashboard khusus
elseif ($module->slug == 'risk-management') {
    $moduleUrl = url('risk-management/dashboard');
}
```

### ❌ Pitfall #11: Route Catch-All di Posisi Salah

**Masalah:** URL `/modules/{module-slug}` menampilkan halaman detail modul, bukan dashboard

**Penyebab:** Route catch-all `/{slug}` berada di atas route modul spesifik

**Solusi:**

Pastikan route catch-all berada di **paling bawah** dalam group:

```php
// routes/web.php
Route::middleware(['auth', 'tenant'])->prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::post('/request-activation', [ModuleController::class, 'requestActivation'])->name('request-activation');

    // ✅ Route catch-all HARUS di paling bawah
    Route::get('/{slug}', [ModuleController::class, 'show'])->name('show');
});
```

**Penjelasan:**

- Route catch-all akan menangkap SEMUA request ke `/modules/*`
- Jika di atas, route modul spesifik tidak akan pernah tercapai
- Harus di paling bawah agar route spesifik diproses terlebih dahulu

### ❌ Pitfall #12: Namespace Tidak Konsisten

**SALAH:**

```php
// File: modules/ProductManagement/Models/Product.php
namespace App\Models;  // ❌ SALAH!

// File: modules/ProductManagement/Http/Controllers/ProductController.php
namespace ProductManagement\Controllers;  // ❌ SALAH!
```

**BENAR:**

```php
// File: modules/ProductManagement/Models/Product.php
namespace Modules\ProductManagement\Models;  // ✅ BENAR!

// File: modules/ProductManagement/Http/Controllers/ProductController.php
namespace Modules\ProductManagement\Http\Controllers;  // ✅ BENAR!
```

**Penjelasan:** Namespace harus mengikuti struktur folder dan dimulai dengan `Modules\`.

---

## Testing

### Database Testing

```sql
-- Cek module exists
SELECT * FROM modules WHERE slug = 'inventory-management';

-- Cek permissions
SELECT * FROM role_module_permissions
WHERE role_id = 2 AND module_id = @module_id;
```

### Route Testing

```bash
php artisan route:list --name=inventory-management
```

### Browser Testing

1. Login sebagai Tenant Admin
2. Check menu muncul di sidebar
3. Click menu → tidak ada error 403
4. Test CRUD operations

---

## Troubleshooting

### Error: "Modul tidak ditemukan"

**Penyebab:** Slug di routes tidak match dengan database  
**Solusi:** Cek slug di database dan update routes

**Verifikasi:**

```bash
# Cek slug di database
php artisan tinker
>>> App\Models\Module::where('code', 'YOUR_CODE')->first(['slug', 'code', 'name']);

# Pastikan slug match dengan:
# 1. Middleware di routes.php: module.permission:{slug}
# 2. Sidebar configuration: $module->slug == '{slug}'
# 3. Policy moduleCode: protected string $moduleCode = '{slug}';
```

### Error: "You do not have access to this module" (403)

**Penyebab 1:** Slug di database tidak match dengan middleware

```php
// Database: slug = 'corres'
// Routes: module.permission:correspondence-management
// ❌ Tidak match!
```

**Solusi:** Update slug di database agar match

```php
php artisan tinker
>>> App\Models\Module::where('code', 'CORRES')->update(['slug' => 'correspondence-management']);
```

**Penyebab 2:** PermissionService tidak support slug (sudah fixed di v2.1)

**Solusi:** Pastikan PermissionService mencari module by code OR slug:

```php
// app/Services/PermissionService.php
$module = Module::where('code', $moduleCode)
    ->orWhere('slug', $moduleCode)  // ✅ Support slug
    ->first();
```

**Penyebab 3:** Module tidak aktif untuk tenant atau role tidak punya permission

**Penyebab:** Module code di middleware salah  
**Solusi:** Gunakan CODE (bukan slug) di middleware

### Error: "This action is unauthorized"

**Penyebab:** Policy moduleCode salah  
**Solusi:** Gunakan CODE (bukan slug) di Policy

### Menu Tidak Muncul

**Penyebab:** Permission record tidak ada  
**Solusi:** Insert ke role_module_permissions

### Error: "This cache store does not support tagging"

**Penyebab:** Menggunakan `Cache::tags()` dengan driver `file`  
**Solusi:**

- Gunakan `Cache::remember()` dengan simple key
- Atau upgrade ke Redis/Memcached driver

**Contoh Fix:**

```php
// SALAH
Cache::tags(['permissions', 'user-' . $user->id])->remember(...);

// BENAR
Cache::remember('permissions:user:' . $user->id, ...);
```

### Error: "Call to undefined method tenantScope()"

**Penyebab:** Memanggil `tenantScope()` manual padahal trait `BelongsToTenant` sudah apply global scope  
**Solusi:** Hapus semua pemanggilan `tenantScope()`

**Contoh Fix:**

```php
// SALAH
$products = Product::tenantScope()->get();

// BENAR
$products = Product::query()->get();
```

### Menu Muncul Duplikat

**Penyebab:** Nama modul tidak konsisten (ada yang Bahasa Inggris, ada yang Indonesia)  
**Solusi:**

1. Update nama modul di seeder ke Bahasa Indonesia
2. Re-run seeder
3. Clear cache

**Contoh Fix:**

```php
// Seeder
'name' => 'Manajemen Produk', // ✅ Konsisten dengan modul lain
```

### Error: "Method 'Policy::view()' is not compatible"

**Penyebab:** Type hint di Policy method terlalu spesifik  
**Solusi:** Gunakan `Model` type hint (bukan model spesifik)

**Contoh Fix:**

```php
// SALAH
public function view(User $user, Product $product): bool

// BENAR
public function view(User $user, Model $product): bool
```

---

## Quick Reference

### Identifiers Template

```
Name:      "Inventory Management"
Slug:      "inventory-management"
Code:      "INVENTORY_MANAGEMENT"
Folder:    "InventoryManagement"
```

### Critical Rules

1. ✅ **Module Name:** WAJIB Bahasa Indonesia
2. ✅ **Routes middleware:** Gunakan `module.permission:slug` (bukan `module:code`)
3. ✅ **Policy moduleCode:** Gunakan slug (kebab-case)
4. ✅ **URL prefix:** Gunakan slug
5. ✅ **Model:** Wajib pakai `BelongsToTenant` trait
6. ✅ **Controller:** Wajib pakai `Gate::authorize()`
7. ✅ **Policy methods:** Type hint `Model` (bukan model spesifik)
8. ✅ **Tenant filtering:** JANGAN pakai `tenantScope()` manual

### Must-Have Files

```
✅ ServiceProvider
✅ routes.php (dengan middleware module.permission)
✅ Controller (dengan Gate::authorize)
✅ Model (dengan BelongsToTenant trait)
✅ Policy (extend BasePolicy, type hint Model)
✅ Seeder (nama Bahasa Indonesia)
✅ Register di config/app.php
✅ Register di AuthServiceProvider.php
```

### Pre-Launch Checklist

Sebelum deploy modul baru, pastikan:

- [ ] Nama modul Bahasa Indonesia
- [ ] Middleware menggunakan `module.permission:slug`
- [ ] Policy extends `BasePolicy` dengan moduleCode = slug
- [ ] Policy methods type hint `Model` (bukan spesifik)
- [ ] Model menggunakan `BelongsToTenant` trait
- [ ] Controller TIDAK menggunakan `tenantScope()` manual
- [ ] Seeder sudah dijalankan
- [ ] Policy sudah registered di `AuthServiceProvider`
- [ ] ServiceProvider sudah registered di `config/app.php`
- [ ] **Sidebar URL sudah dikonfigurasi di `sidebar.blade.php`**
- [ ] **Route catch-all di paling bawah di `web.php`**
- [ ] Cache sudah di-clear
- [ ] Test CRUD operations
- [ ] Test tenant isolation
- [ ] Test link sidebar mengarah ke dashboard (bukan halaman detail modul)

---

**Dokumentasi Terkait:**

- `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md` - Lessons learned dari Product Management
- `/docs/BUGFIX-CACHE-TAGS-NOT-SUPPORTED.md` - Cache issues
- `/docs/RBAC-MULTITENANT-IMPROVEMENTS.md` - RBAC system overview
