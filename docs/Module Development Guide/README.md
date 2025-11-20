# Module Development Guide

Panduan lengkap untuk membuat modul baru di SIAR dengan benar dan menghindari kesalahan umum.

---

## 📚 Dokumentasi

### 🚀 Quick Start

1. **[MODULE-STRUCTURE-STANDARDS.md](MODULE-STRUCTURE-STANDARDS.md)** - 🔒 WAJIB: Struktur modular yang HARUS diikuti
2. **[MODULE-REGISTRATION-GUIDE.md](MODULE-REGISTRATION-GUIDE.md)** - 🔒 WAJIB: Registrasi modul ke Superadmin
3. **[MODULE-CHECKLIST.md](MODULE-CHECKLIST.md)** - Copy checklist ini untuk setiap modul baru
4. **[COMMON-MISTAKES.md](COMMON-MISTAKES.md)** - Baca ini DULU sebelum mulai coding!
5. **[MODULE-QUICK-REFERENCE.md](MODULE-QUICK-REFERENCE.md)** - Quick reference saat coding

### 📖 Detailed Guides

6. **[MODULE-DEVELOPMENT-GUIDE.md](MODULE-DEVELOPMENT-GUIDE.md)** - Panduan lengkap step-by-step
7. **[MODULE-NAMING-REFERENCE.md](MODULE-NAMING-REFERENCE.md)** - Naming convention & examples
8. **[MODULE-TEMPLATE.md](MODULE-TEMPLATE.md)** - Copy-paste templates
9. **[LARAVEL-CLOUD-DEPLOYMENT.md](LARAVEL-CLOUD-DEPLOYMENT.md)** - 🔥 Khusus untuk Laravel Cloud users

### 📚 Lessons Learned

10. **[LESSON-LEARNED-PERFORMANCE-MANAGEMENT.md](LESSON-LEARNED-PERFORMANCE-MANAGEMENT.md)** - Route catch-all & sidebar URL issues
11. **[LESSON-LEARNED-CORRESPONDENCE.md](LESSON-LEARNED-CORRESPONDENCE.md)** - Slug inconsistency & PermissionService fix

---

## ⚡ Quick Start Guide

### Step 0: Pahami Struktur Modular (WAJIB!)

```bash
# Baca struktur modular yang WAJIB diikuti
cat MODULE-STRUCTURE-STANDARDS.md
```

**Critical Structure Rules:**

- ✅ Semua file modul dalam `modules/{ModuleName}/`
- ✅ Namespace: `Modules\{ModuleName}\...`
- ❌ JANGAN buat file di `app/Models/`, `app/Http/Controllers/`, dll

### Step 1: Baca Kesalahan Umum

```bash
# Baca file ini DULU!
cat COMMON-MISTAKES.md
```

**Critical Points:**

- ✅ Nama modul WAJIB Bahasa Indonesia
- ✅ Middleware: `module.permission:slug`
- ✅ Policy moduleCode: `slug` (bukan CODE)
- ✅ Policy methods type hint: `Model`
- ✅ Model pakai `BelongsToTenant` trait
- ✅ **Sidebar URL WAJIB dikonfigurasi**
- ✅ **Route catch-all di paling bawah**
- ❌ JANGAN pakai `tenantScope()` manual
- ❌ JANGAN buat file di luar `modules/`

### Step 2: Copy Checklist

```bash
# Copy checklist untuk modul baru
cp MODULE-CHECKLIST.md ../my-new-module-checklist.md
```

### Step 3: Follow Guide

```bash
# Ikuti panduan lengkap
cat MODULE-DEVELOPMENT-GUIDE.md
```

---

## 🎯 Naming Convention

### Format Standar

| Field      | Format          | Example                | Language      |
| ---------- | --------------- | ---------------------- | ------------- |
| **Name**   | Title Case      | `Manajemen Inventori`  | **Indonesia** |
| **Slug**   | kebab-case      | `inventory-management` | English       |
| **Code**   | UPPERCASE_SNAKE | `INVENTORY_MANAGEMENT` | English       |
| **Folder** | PascalCase      | `InventoryManagement`  | English       |

### Usage

```php
// Seeder
'name' => 'Manajemen Inventori',  // ✅ Bahasa Indonesia

// Routes
Route::middleware(['module.permission:inventory-management'])  // ✅ slug

// Policy
protected string $moduleCode = 'inventory-management';  // ✅ slug

// Controller
$items = Inventory::query()->get();  // ✅ Tanpa tenantScope()
```

---

## ❌ Top 7 Kesalahan

### 1. Nama Modul Bahasa Inggris

```php
❌ 'name' => 'Inventory Management'
✅ 'name' => 'Manajemen Inventori'
```

**Dampak:** Menu duplikat di sidebar

### 2. Middleware Salah

```php
❌ Route::middleware(['module:INVENTORY_MANAGEMENT'])
✅ Route::middleware(['module.permission:inventory-management'])
```

**Dampak:** Error 403

### 3. Policy moduleCode Salah

```php
❌ protected string $moduleCode = 'INVENTORY_MANAGEMENT';
✅ protected string $moduleCode = 'inventory-management';
```

**Dampak:** Authorization gagal

### 4. Pakai tenantScope() Manual

```php
❌ $items = Inventory::tenantScope()->get();
✅ $items = Inventory::query()->get();
```

**Dampak:** Error "undefined method"

### 5. Policy Type Hint Salah

```php
❌ public function view(User $user, Inventory $item): bool
✅ public function view(User $user, Model $item): bool
```

**Dampak:** Error "not compatible"

### 6. Lupa Konfigurasi Sidebar URL

```php
// sidebar.blade.php
❌ $moduleUrl = url('modules/' . $module->slug);  // Mengarah ke halaman detail
✅ elseif ($module->slug == 'inventory-management') {
       $moduleUrl = url('inventory-management');  // Mengarah ke dashboard
   }
```

**Dampak:** Link sidebar mengarah ke halaman detail modul, bukan dashboard

### 7. Route Catch-All di Posisi Salah

```php
// routes/web.php
❌ Route::get('/{slug}', ...)->name('show');  // Di atas - menangkap semua
   Route::get('/', ...)->name('index');       // Tidak akan tercapai

✅ Route::get('/', ...)->name('index');       // Di atas - route spesifik
   Route::get('/{slug}', ...)->name('show');  // Di bawah - catch-all
```

**Dampak:** URL modul menampilkan halaman detail, bukan dashboard fungsional

---

## ✅ Validation Checklist

Sebelum commit modul baru, pastikan:

### 🏗️ Structure Validation

- [ ] ✅ Semua file modul dalam folder `modules/{ModuleName}/`
- [ ] ✅ Tidak ada file modul di `app/Models/`, `app/Http/Controllers/`, dll
- [ ] ✅ Struktur folder mengikuti `MODULE-STRUCTURE-STANDARDS.md`
- [ ] ✅ File WAJIB sudah ada (Controller, Model, routes, ServiceProvider, Views)

### 📝 Namespace Validation

- [ ] ✅ Semua namespace dimulai dengan `Modules\{ModuleName}\`
- [ ] ✅ Namespace sesuai dengan folder structure
- [ ] ✅ No namespace conflicts

### 🔧 ServiceProvider Validation

- [ ] ✅ ServiceProvider exists di `Providers/`
- [ ] ✅ ServiceProvider registered di `config/app.php`
- [ ] ✅ Routes loaded dengan `loadRoutesFrom()`
- [ ] ✅ Views loaded dengan `loadViewsFrom()`

### 📦 Autoloading Validation

- [ ] ✅ `composer dump-autoload` sudah dijalankan
- [ ] ✅ No autoload errors
- [ ] ✅ Classes dapat di-import dengan benar
- [ ] ✅ Verify menu tidak duplikat

---

## 🔧 Commands Reference

### Development

```bash
# Create module structure
mkdir -p modules/InventoryManagement/{Config,Database,Http,Models,Providers,Resources}

# Autoload
composer dump-autoload

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Testing

```bash
# Check routes
php artisan route:list --name=inventory-management

# Run seeder
php artisan db:seed --class=InventoryManagementModuleSeeder

# Check database
mysql> SELECT * FROM modules WHERE slug = 'inventory-management';
```

---

## 📚 Real-World Examples

### ✅ Good Example: Product Management

Lihat implementasi yang benar:

```bash
# Module structure
modules/ProductManagement/
├── Http/
│   ├── Controllers/ProductController.php
│   └── routes.php
├── Models/Product.php
└── Providers/ProductManagementServiceProvider.php

# Policy
app/Policies/ProductPolicy.php

# Seeder
database/seeders/ProductManagementModuleSeeder.php
```

**Key Points:**

- ✅ Nama: "Manajemen Produk" (Bahasa Indonesia)
- ✅ Middleware: `module.permission:product-management`
- ✅ Policy moduleCode: `product-management`
- ✅ Model pakai `BelongsToTenant`
- ✅ Controller tanpa `tenantScope()`

### 📖 Lessons Learned

Baca dokumentasi lengkap tentang masalah yang terjadi dan cara fix:

- `LESSON-LEARNED-CORRESPONDENCE.md` - Slug inconsistency & PermissionService (Nov 2025)
- `LESSON-LEARNED-PERFORMANCE-MANAGEMENT.md` - Route & sidebar issues (Nov 2025)
- `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md` - Permission & policy issues
- `/docs/BUGFIX-CACHE-TAGS-NOT-SUPPORTED.md` - Cache tagging issues

---

## 🆘 Troubleshooting

### Error: "You do not have access to this module" (403)

**Cause 1:** Slug di database tidak match dengan middleware  
**Fix:** Update slug di database atau update middleware

**Cause 2:** PermissionService tidak support slug (fixed in v2.1)  
**Fix:** Update PermissionService untuk support code OR slug

**Cause 3:** Middleware format salah  
**Fix:** Gunakan `module.permission:slug` (bukan `module:code`)

### Error: "This action is unauthorized"

**Cause:** Policy moduleCode salah  
**Fix:** Gunakan `slug` (bukan CODE)

### Error: "Call to undefined method tenantScope()"

**Cause:** Memanggil `tenantScope()` manual  
**Fix:** Hapus, trait sudah apply global scope

### Menu Muncul Duplikat

**Cause:** Nama modul tidak konsisten (Bahasa Inggris)  
**Fix:** Update seeder ke Bahasa Indonesia, re-run, clear cache

### Error: "Method is not compatible"

**Cause:** Policy method type hint terlalu spesifik  
**Fix:** Gunakan `Model` type hint

### Link Sidebar Mengarah ke Halaman Detail Modul

**Cause:** Lupa konfigurasi URL di sidebar.blade.php  
**Fix:** Tambahkan kondisi URL untuk modul baru di sidebar

### URL Modul Menampilkan Halaman Detail, Bukan Dashboard

**Cause:** Route catch-all di posisi salah (di atas route spesifik)  
**Fix:** Pindahkan route catch-all ke paling bawah di web.php

---

## 📞 Support

Jika menemui masalah:

1. Baca `COMMON-MISTAKES.md`
2. Check `MODULE-DEVELOPMENT-GUIDE.md` troubleshooting section
3. Review Product Management module sebagai reference
4. Check dokumentasi RBAC: `/docs/RBAC-MULTITENANT-IMPROVEMENTS.md`

---

## 📝 Contributing

Saat menemukan kesalahan baru atau best practice:

1. Update `COMMON-MISTAKES.md`
2. Update `MODULE-DEVELOPMENT-GUIDE.md`
3. Update checklist di `MODULE-CHECKLIST.md`
4. Tambahkan contoh di `MODULE-TEMPLATE.md`

---

**Last Updated:** 20 November 2025  
**Version:** 2.1  
**Based on:** Product Management & Performance Management lessons learned

**Maintainer:** Development Team  
**Review Status:** ✅ Reviewed & Tested
