# Common Mistakes - Module Development

> **⚠️ CRITICAL:** Kesalahan-kesalahan ini sering terjadi dan menyebabkan error. Baca sebelum membuat modul baru!

---

## 🔴 Mistake #1: Nama Modul Bahasa Inggris

### ❌ SALAH

```php
// Seeder
'name' => 'Product Management',
```

### ✅ BENAR

```php
// Seeder
'name' => 'Manajemen Produk',
```

### 💥 Dampak

- Menu muncul **duplikat** di sidebar
- Tidak konsisten dengan modul lain

### 🔧 Fix

1. Update seeder dengan nama Bahasa Indonesia
2. Re-run seeder
3. Clear cache

---

## 🔴 Mistake #2: Middleware Salah

### ❌ SALAH

```php
// routes.php
Route::middleware(['module:product-management'])
Route::middleware(['module:PRODUCT_MANAGEMENT'])
```

### ✅ BENAR

```php
// routes.php
Route::middleware(['module.permission:product-management'])
```

### 💥 Dampak

- Error 403 "You do not have access"
- Permission check tidak jalan

### 🔧 Fix

Gunakan `module.permission:slug` (bukan `module:code`)

---

## 🔴 Mistake #3: Policy moduleCode Salah

### ❌ SALAH

```php
class ProductPolicy extends BasePolicy
{
    protected string $moduleCode = 'PRODUCT_MANAGEMENT'; // CODE
}
```

### ✅ BENAR

```php
class ProductPolicy extends BasePolicy
{
    protected string $moduleCode = 'product-management'; // slug
}
```

### 💥 Dampak

- Authorization selalu gagal
- Error "This action is unauthorized"

### 🔧 Fix

Gunakan slug (kebab-case), bukan CODE

---

## 🔴 Mistake #4: Policy Method Type Hint Salah

### ❌ SALAH

```php
class ProductPolicy extends BasePolicy
{
    public function view(User $user, Product $product): bool
    {
        return parent::view($user, $product);
    }
}
```

### ✅ BENAR

```php
class ProductPolicy extends BasePolicy
{
    public function view(User $user, Model $product): bool
    {
        return parent::view($user, $product);
    }
}
```

### 💥 Dampak

- Error "Method is not compatible with BasePolicy"
- Policy tidak bisa digunakan

### 🔧 Fix

Type hint harus `Model`, bukan model spesifik

---

## 🔴 Mistake #5: Model Tanpa BelongsToTenant

### ❌ SALAH

```php
class Product extends Model
{
    protected $fillable = ['name', 'code'];
}
```

### ✅ BENAR

```php
class Product extends Model
{
    use BelongsToTenant; // ✅ WAJIB!

    protected $fillable = ['tenant_id', 'name', 'code'];
}
```

### 💥 Dampak

- Data tidak ter-isolasi per tenant
- User bisa lihat data tenant lain
- **SECURITY RISK!**

### 🔧 Fix

Selalu gunakan trait `BelongsToTenant`

---

## 🔴 Mistake #6: Menggunakan tenantScope() Manual

### ❌ SALAH

```php
// Controller
$products = Product::tenantScope()->get();
$product = Product::tenantScope()->findOrFail($id);
```

### ✅ BENAR

```php
// Controller
$products = Product::query()->get();
$product = Product::findOrFail($id);
```

### 💥 Dampak

- Error "Call to undefined method tenantScope()"
- Code tidak perlu karena trait sudah apply global scope

### 🔧 Fix

Hapus semua `tenantScope()`, trait sudah handle otomatis

---

## 🔴 Mistake #7: Controller Tanpa Gate::authorize()

### ❌ SALAH

```php
public function index()
{
    $products = Product::all();
    return view('products.index', compact('products'));
}
```

### ✅ BENAR

```php
public function index()
{
    Gate::authorize('viewAny', Product::class);

    $products = Product::all();
    return view('products.index', compact('products'));
}
```

### 💥 Dampak

- Tidak ada authorization check
- User tanpa permission bisa akses
- **SECURITY RISK!**

### 🔧 Fix

Tambahkan `Gate::authorize()` di setiap method

---

## 🔴 Mistake #8: Lupa Register ServiceProvider

### ❌ SALAH

```php
// config/app.php
'providers' => [
    // ... ServiceProvider modul tidak ada
],
```

### ✅ BENAR

```php
// config/app.php
'providers' => [
    // ...
    Modules\ProductManagement\Providers\ProductManagementServiceProvider::class,
],
```

### 💥 Dampak

- Routes tidak terdaftar
- Error 404 Not Found

### 🔧 Fix

Register ServiceProvider di `config/app.php`

---

## 🔴 Mistake #9: Lupa Register Policy

### ❌ SALAH

```php
// AuthServiceProvider.php
protected $policies = [
    // ... Policy modul tidak ada
];
```

### ✅ BENAR

```php
// AuthServiceProvider.php
protected $policies = [
    \Modules\ProductManagement\Models\Product::class => \App\Policies\ProductPolicy::class,
];
```

### 💥 Dampak

- Policy tidak digunakan
- Authorization selalu gagal

### 🔧 Fix

Register Policy di `AuthServiceProvider.php`

---

## 🔴 Mistake #10: Lupa Clear Cache

### ❌ SALAH

```bash
# Langsung test tanpa clear cache
```

### ✅ BENAR

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 💥 Dampak

- Perubahan tidak terdeteksi
- Error aneh yang sulit di-debug

### 🔧 Fix

Selalu clear cache setelah perubahan

---

## 🔴 Mistake #11: File Modul Tercecer di Luar modules/

### ❌ SALAH

```
app/Models/Product.php
app/Http/Controllers/ProductController.php
resources/views/products/
routes/product.php
```

### ✅ BENAR

```
modules/ProductManagement/
├── Http/Controllers/ProductController.php
├── Models/Product.php
├── Resources/Views/products/
└── Http/routes.php
```

### 💥 Dampak

- Sulit maintenance dan tracking
- Tidak konsisten dengan modul lain
- Namespace tidak terorganisir
- **Sulit enable/disable modul**

### 🔧 Fix

1. Buat folder structure: `modules/{ModuleName}/`
2. Pindahkan semua file ke folder modul
3. Update namespace: `Modules\{ModuleName}\...`
4. Run `composer dump-autoload`

---

## 🔴 Mistake #12: Namespace Tidak Konsisten

### ❌ SALAH

```php
// File: modules/ProductManagement/Models/Product.php
namespace App\Models;  // ❌ SALAH!

// File: modules/ProductManagement/Http/Controllers/ProductController.php
namespace ProductManagement\Controllers;  // ❌ SALAH!
```

### ✅ BENAR

```php
// File: modules/ProductManagement/Models/Product.php
namespace Modules\ProductManagement\Models;  // ✅ BENAR!

// File: modules/ProductManagement/Http/Controllers/ProductController.php
namespace Modules\ProductManagement\Http\Controllers;  // ✅ BENAR!
```

### 💥 Dampak

- Autoloading error
- Class not found
- Namespace conflicts

### 🔧 Fix

Namespace harus: `Modules\{ModuleName}\{SubFolder}`

---

## 🔴 Mistake #13: Cache Tags dengan File Driver

### ❌ SALAH

```php
Cache::tags(['permissions', 'user-' . $user->id])->remember(...);
```

### ✅ BENAR

```php
Cache::remember('permissions:user:' . $user->id, ...);
```

### 💥 Dampak

- Error "This cache store does not support tagging"

### 🔧 Fix

Gunakan simple cache key, atau upgrade ke Redis/Memcached

---

## 🔴 Mistake #14: Route Catch-All Menimpa Route Modul

### ❌ SALAH

```php
// routes/web.php
Route::middleware(['auth', 'tenant'])->prefix('modules')->group(function () {
    // Route catch-all di ATAS - akan menangkap semua request
    Route::get('/{slug}', [ModuleController::class, 'show'])->name('show');

    // Route modul spesifik tidak akan pernah tercapai
    // karena sudah ditangkap oleh route di atas
});
```

### ✅ BENAR

```php
// routes/web.php
Route::middleware(['auth', 'tenant'])->prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::post('/request-activation', [ModuleController::class, 'requestActivation'])->name('request-activation');

    // Route catch-all HARUS di paling bawah
    // Agar route modul spesifik diproses terlebih dahulu
    Route::get('/{slug}', [ModuleController::class, 'show'])->name('show');
});
```

### 💥 Dampak

- URL `/modules/{module-slug}` menampilkan halaman detail modul (info page)
- Bukan halaman dashboard/fungsional modul
- User tidak bisa akses fitur modul

### 🔧 Fix

1. Pindahkan route catch-all `/{slug}` ke **paling bawah** dalam group
2. Pastikan route modul spesifik didefinisikan terlebih dahulu

---

## 🔴 Mistake #15: Slug di Database Tidak Sesuai dengan Configuration

### ❌ SALAH

```php
// database/seeders/CorrespondenceModuleSeeder.php
Module::create([
    'name' => 'Korespondensi',
    'slug' => 'corres',  // ❌ Slug pendek, tidak standar
    'code' => 'CORRES',
]);

// modules/Correspondence/Http/routes.php
Route::middleware(['module.permission:correspondence-management'])  // ❌ Tidak match!
```

### ✅ BENAR

```php
// database/seeders/CorrespondenceModuleSeeder.php
Module::create([
    'name' => 'Korespondensi',
    'slug' => 'correspondence-management',  // ✅ Slug standar kebab-case
    'code' => 'CORRES',
]);

// modules/Correspondence/Http/routes.php
Route::middleware(['module.permission:correspondence-management'])  // ✅ Match!
```

### 💥 Dampak

- Error 403: "You do not have access to this module"
- PermissionService tidak bisa menemukan module
- Sidebar configuration tidak match
- Middleware checking gagal

### 🔧 Fix

**Option 1: Update Database (Recommended)**

```php
php artisan tinker
>>> App\Models\Module::where('code', 'CORRES')->update(['slug' => 'correspondence-management']);
```

**Option 2: Update Configuration (Not Recommended)**

Update semua reference ke slug pendek (routes, sidebar, dll) - tidak disarankan karena tidak konsisten dengan modul lain.

### 📝 Prevention

**Saat membuat seeder, WAJIB gunakan slug standar:**

```php
// ✅ BENAR - Slug standar kebab-case
'slug' => 'module-name-management',

// ❌ SALAH - Slug pendek
'slug' => 'mod',
'slug' => 'corres',
'slug' => 'perf',
```

**Aturan slug:**

- Format: `kebab-case`
- Suffix: `-management` (untuk konsistensi)
- Contoh: `inventory-management`, `correspondence-management`, `performance-management`

---

## 🔴 Mistake #16: Sidebar URL Tidak Sesuai dengan Route Modul

### ❌ SALAH

```php
// resources/views/layouts/partials/sidebar.blade.php
// Default URL untuk modul yang tidak punya kondisi khusus
$moduleUrl = url('modules/' . $module->slug); // ❌ SALAH!
```

### ✅ BENAR

```php
// resources/views/layouts/partials/sidebar.blade.php
// Tambahkan kondisi khusus untuk setiap modul
if ($module->slug == 'performance-management') {
    $moduleUrl = url('performance-management'); // ✅ Sesuai dengan route prefix
} elseif ($module->slug == 'risk-management') {
    $moduleUrl = url('risk-management/dashboard');
} elseif ($module->slug == 'document-management') {
    $moduleUrl = url('document-management/dashboard');
} else {
    $moduleUrl = url('modules/' . $module->slug);
}
```

### 💥 Dampak

- Link di sidebar mengarah ke halaman detail modul (info page)
- Bukan ke dashboard fungsional modul
- User harus manually ganti URL untuk akses fitur

### 🔧 Fix

1. Buka `resources/views/layouts/partials/sidebar.blade.php`
2. Cari bagian yang generate `$moduleUrl`
3. Tambahkan kondisi untuk modul baru:

```php
elseif ($module->slug == 'your-module-slug') {
    $moduleUrl = url('your-module-slug'); // Atau url('your-module-slug/dashboard')
}
```

### 📝 Best Practice

Saat membuat modul baru, **WAJIB** tambahkan kondisi URL di sidebar:

```php
// Contoh untuk modul Inventory Management
elseif ($module->slug == 'inventory-management') {
    $moduleUrl = url('inventory-management'); // Sesuai dengan route prefix
}
```

---

## Quick Checklist

Sebelum deploy modul baru, pastikan:

- [ ] ✅ Nama modul **Bahasa Indonesia**
- [ ] ✅ **Slug di database match dengan routes/sidebar** (kebab-case standar)
- [ ] ✅ Middleware `module.permission:slug`
- [ ] ✅ Policy moduleCode = `slug` (kebab-case)
- [ ] ✅ Policy methods type hint `Model`
- [ ] ✅ Model pakai `BelongsToTenant` trait
- [ ] ✅ Controller **TIDAK** pakai `tenantScope()`
- [ ] ✅ Controller pakai `Gate::authorize()`
- [ ] ✅ ServiceProvider registered
- [ ] ✅ Policy registered
- [ ] ✅ **Sidebar URL sudah ditambahkan** di `sidebar.blade.php`
- [ ] ✅ **Route catch-all di paling bawah** di `web.php`
- [ ] ✅ Cache cleared
- [ ] ✅ Test CRUD operations
- [ ] ✅ Test tenant isolation

---

## Real-World Example: Product Management

Lihat implementasi lengkap di:

- `/modules/ProductManagement/` - Module structure
- `/app/Policies/ProductPolicy.php` - Policy implementation
- `/database/seeders/ProductManagementModuleSeeder.php` - Seeder
- `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md` - Lessons learned

---

**Last Updated:** 20 November 2025  
**Based on:** Product Management module fixes
