# Module Development Checklist

> **Copy checklist ini untuk setiap modul baru yang akan dibuat**

## Module Information

- **Module Name (ID):** **\*\*\*\***\_\_\_**\*\*\*\*** (contoh: "Manajemen Inventori")
- **Module Slug:** **\*\*\*\***\_\_\_**\*\*\*\*** (contoh: "inventory-management")
- **Module Code:** **\*\*\*\***\_\_\_**\*\*\*\*** (contoh: "INVENTORY_MANAGEMENT")
- **Folder Name:** **\*\*\*\***\_\_\_**\*\*\*\*** (contoh: "InventoryManagement")

---

## Phase 1: Planning & Setup

### ✅ Naming Convention

- [ ] Module name menggunakan **Bahasa Indonesia**
- [ ] Slug menggunakan format **kebab-case**
- [ ] Code menggunakan format **UPPERCASE_SNAKE_CASE**
- [ ] Folder menggunakan format **PascalCase**
- [ ] Semua identifier sudah konsisten

### ✅ Database Planning

- [ ] Tentukan tabel yang diperlukan
- [ ] Pastikan semua tabel punya kolom `tenant_id`
- [ ] Buat migration files
- [ ] Buat seeder untuk module registration

---

## Phase 2: Code Structure

### ✅ Module Folder Structure

- [ ] Buat folder `modules/{ModuleName}/`
- [ ] Buat subfolder: `Config/`, `Database/`, `Http/`, `Models/`, `Providers/`, `Resources/`

### ✅ Models

- [ ] Buat model di `modules/{ModuleName}/Models/`
- [ ] **WAJIB:** Model extends `Model`
- [ ] **WAJIB:** Model menggunakan trait `BelongsToTenant`
- [ ] Set `$fillable` dengan kolom yang diperlukan (termasuk `tenant_id`)
- [ ] Set `$casts` jika diperlukan

**Contoh:**

```php
use App\Traits\BelongsToTenant;

class Product extends Model
{
    use BelongsToTenant; // ✅ WAJIB!

    protected $fillable = ['tenant_id', 'name', 'code'];
}
```

### ✅ Controllers

- [ ] Buat controller di `modules/{ModuleName}/Http/Controllers/`
- [ ] **WAJIB:** Setiap method menggunakan `Gate::authorize()`
- [ ] **JANGAN:** Gunakan `tenantScope()` manual
- [ ] Gunakan `Product::query()` atau `Product::findOrFail()` langsung

**Contoh:**

```php
public function index()
{
    Gate::authorize('viewAny', Product::class);

    // ✅ BENAR - tanpa tenantScope()
    $products = Product::query()->paginate(10);

    // ❌ SALAH - jangan pakai tenantScope()
    // $products = Product::tenantScope()->paginate(10);
}
```

### ✅ Policies

- [ ] Buat policy di `app/Policies/{ModuleName}Policy.php`
- [ ] **WAJIB:** Policy extends `BasePolicy`
- [ ] Set `protected string $moduleCode = 'module-slug';` (kebab-case)
- [ ] **WAJIB:** Method type hint menggunakan `Model` (bukan model spesifik)

**Contoh:**

```php
class ProductPolicy extends BasePolicy
{
    protected string $moduleCode = 'product-management'; // ✅ slug

    // ✅ BENAR - type hint Model
    public function view(User $user, Model $product): bool
    {
        return parent::view($user, $product);
    }

    // ❌ SALAH - type hint terlalu spesifik
    // public function view(User $user, Product $product): bool
}
```

### ✅ Routes

- [ ] Buat `modules/{ModuleName}/Http/routes.php`
- [ ] **WAJIB:** Middleware menggunakan `module.permission:slug` (bukan `module:code`)
- [ ] Prefix menggunakan slug
- [ ] Name prefix menggunakan `modules.slug.`

**Contoh:**

```php
// ✅ BENAR
Route::middleware(['web', 'auth', 'tenant', 'module.permission:product-management'])
    ->prefix('product-management')
    ->name('modules.product-management.')
    ->group(function () {
        Route::resource('products', ProductController::class);
    });

// ❌ SALAH
// Route::middleware(['module:PRODUCT_MANAGEMENT']) // SALAH!
```

### ✅ Service Provider

- [ ] Buat `modules/{ModuleName}/Providers/{ModuleName}ServiceProvider.php`
- [ ] Register routes di `boot()` method
- [ ] Load views jika ada
- [ ] Load migrations jika ada

### ✅ Sidebar Configuration

- [ ] Buka `resources/views/layouts/partials/sidebar.blade.php`
- [ ] Cari bagian yang generate `$moduleUrl` (sekitar baris 170-175)
- [ ] Tambahkan kondisi URL untuk modul baru:

```php
elseif ($module->slug == 'your-module-slug') {
    $moduleUrl = url('your-module-slug'); // Sesuai dengan route prefix
}
```

- [ ] Pastikan URL sesuai dengan route prefix di `routes.php`
- [ ] Test link sidebar mengarah ke dashboard (bukan halaman detail modul)

### ✅ Route Order Verification

- [ ] Buka `routes/web.php`
- [ ] Cari group `Route::prefix('modules')->group(function () {`
- [ ] Pastikan route catch-all `/{slug}` berada di **paling bawah**
- [ ] Pastikan route spesifik berada di atas route catch-all

**Urutan yang benar:**

```php
Route::prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'index']);
    Route::post('/request-activation', [ModuleController::class, 'requestActivation']);

    // Route catch-all HARUS di paling bawah
    Route::get('/{slug}', [ModuleController::class, 'show']);
});
```

---

## Phase 3: Database Setup & Registration

### ✅ Module Seeder (WAJIB untuk Superadmin)

- [ ] Buat `database/seeders/{ModuleName}ModuleSeeder.php`
- [ ] **WAJIB:** Nama modul menggunakan **Bahasa Indonesia**
- [ ] Register modul ke table `modules` dengan `Module::updateOrCreate()`
- [ ] Set `code` sebagai unique identifier
- [ ] Set `icon` untuk UI (Feather Icons)
- [ ] Set `order` untuk urutan menu
- [ ] Aktifkan untuk semua tenant via `$tenant->modules()->syncWithoutDetaching()`
- [ ] Set permissions untuk setiap role via `RoleModulePermission::updateOrCreate()`

**Purpose:**

- ✅ Modul muncul di `/superadmin/modules`
- ✅ Superadmin dapat enable/disable per tenant
- ✅ Modul tersinkronisasi dengan tenant

**Contoh:**

```php
$module = Module::updateOrCreate(
    ['code' => 'product-management'],
    [
        'name' => 'Manajemen Produk', // ✅ Bahasa Indonesia!
        'description' => 'Modul untuk mengelola produk',
        'icon' => 'shopping-bag',
        'is_active' => true
    ]
);
```

### ✅ Run Migrations & Seeders

- [ ] Run `php artisan migrate`
- [ ] Run `php artisan db:seed --class={ModuleName}ModuleSeeder`
- [ ] Verify module exists: `SELECT * FROM modules WHERE code = 'module-slug'`
- [ ] Verify tenant activation: `SELECT * FROM tenant_modules WHERE module_id = ?`
- [ ] Verify permissions: `SELECT * FROM role_module_permissions WHERE module_id = ?`

### ✅ Verify Superadmin Access

- [ ] Login sebagai Superadmin
- [ ] Access `/superadmin/modules`
- [ ] Verify modul muncul di list
- [ ] Verify dapat enable/disable per tenant
- [ ] Verify permissions dapat diatur

---

## Phase 4: Registration

### ✅ Register ServiceProvider

- [ ] Tambahkan ke `config/app.php` di array `providers`
- [ ] Format: `Modules\{ModuleName}\Providers\{ModuleName}ServiceProvider::class`

### ✅ Register Policy

- [ ] Tambahkan ke `app/Providers/AuthServiceProvider.php` di array `$policies`
- [ ] Format: `\Modules\{ModuleName}\Models\{Model}::class => \App\Policies\{Model}Policy::class`

### ✅ Autoload

- [ ] Run `composer dump-autoload`

---

## Phase 5: Testing

### ✅ Clear Caches

- [ ] `php artisan cache:clear`
- [ ] `php artisan config:clear`
- [ ] `php artisan route:clear`
- [ ] `php artisan view:clear`

### ✅ Route Testing

- [ ] Run `php artisan route:list --name={module-slug}`
- [ ] Verify routes terdaftar dengan benar

### ✅ Database Testing

```sql
-- Cek module exists
SELECT * FROM modules WHERE code = 'module-slug';

-- Cek tenant activation
SELECT * FROM tenant_modules WHERE module_id = ?;

-- Cek permissions
SELECT * FROM role_module_permissions WHERE module_id = ?;
```

### ✅ Browser Testing

- [ ] Login sebagai Tenant Admin
- [ ] Verify menu muncul di sidebar (hanya 1x, tidak duplikat)
- [ ] **Click menu → verify mengarah ke dashboard modul (bukan halaman detail)**
- [ ] Verify URL adalah `/{module-slug}` (bukan `/modules/{module-slug}`)
- [ ] Click menu → tidak ada error 403
- [ ] Test Create operation
- [ ] Test Read/List operation
- [ ] Test Update operation
- [ ] Test Delete operation

### ✅ Tenant Isolation Testing

- [ ] Login sebagai user dari tenant A
- [ ] Create data
- [ ] Login sebagai user dari tenant B
- [ ] Verify tidak bisa lihat data tenant A
- [ ] Verify tidak bisa edit/delete data tenant A

---

## Phase 6: Documentation

### ✅ Update Documentation

- [ ] Update `README.md` jika perlu
- [ ] Tambahkan module ke daftar modul aktif
- [ ] Dokumentasikan API endpoints jika ada
- [ ] Dokumentasikan permission requirements

---

## Common Issues Checklist

Pastikan TIDAK melakukan kesalahan berikut:

- [ ] ❌ Nama modul Bahasa Inggris → **HARUS Bahasa Indonesia**
- [ ] ❌ Middleware `module:CODE` → **HARUS `module.permission:slug`**
- [ ] ❌ Policy moduleCode menggunakan CODE → **HARUS slug**
- [ ] ❌ Policy method type hint spesifik → **HARUS `Model`**
- [ ] ❌ Model tanpa `BelongsToTenant` → **HARUS pakai trait**
- [ ] ❌ Controller pakai `tenantScope()` → **JANGAN pakai manual**
- [ ] ❌ Controller tanpa `Gate::authorize()` → **HARUS ada authorization**
- [ ] ❌ Lupa register ServiceProvider → **HARUS register**
- [ ] ❌ Lupa register Policy → **HARUS register**
- [ ] ❌ **Lupa konfigurasi sidebar URL** → **HARUS tambahkan kondisi di sidebar.blade.php**
- [ ] ❌ **Route catch-all tidak di paling bawah** → **HARUS di paling bawah di web.php**
- [ ] ❌ Lupa clear cache → **HARUS clear**

---

## Sign-off

**Developer:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Date:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Reviewed by:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Status:** [ ] Ready for Production

---

**Reference:**

- `/docs/Module Development Guide/MODULE-DEVELOPMENT-GUIDE.md`
- `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md`
