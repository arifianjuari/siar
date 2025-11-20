# Lesson Learned: Correspondence Module - Slug Inconsistency

**Date:** 20 November 2025  
**Issue:** Error 403 "You do not have access to this module" setelah rename menu dari "Correspondence" ke "Korespondensi"  
**Status:** ✅ Resolved

---

## 📋 Problem Summary

Setelah mengubah nama menu sidebar dari "Correspondence" menjadi "Korespondensi", modul tidak bisa diakses dan menampilkan error 403: "You do not have access to this module".

### Error yang Muncul

- **URL:** `http://siar.test/correspondence`
- **Error:** 403 Forbidden
- **Message:** "You do not have access to this module"

---

## 🔍 Root Cause Analysis

### 1. Slug Inconsistency

**Database:**

```json
{
  "id": 1,
  "name": "Korespondensi",
  "slug": "corres", // ❌ Slug pendek, tidak standar
  "code": "CORRES"
}
```

**Configuration:**

```php
// modules/Correspondence/Http/routes.php
Route::middleware(['module.permission:correspondence-management'])  // ❌ Tidak match!

// resources/views/layouts/partials/sidebar.blade.php
elseif ($module->slug == 'correspondence-management') {  // ❌ Tidak match!
    $moduleUrl = url('correspondence');
}
```

**Masalah:** Slug di database (`corres`) tidak match dengan slug di configuration (`correspondence-management`).

### 2. Middleware Format Salah

```php
// ❌ SALAH - Format lama
Route::middleware(['module:correspondence-management'])

// ✅ BENAR - Format baru
Route::middleware(['module.permission:correspondence-management'])
```

### 3. PermissionService Tidak Support Slug

**Original Code:**

```php
// app/Services/PermissionService.php
public function userHasModuleAccess(User $user, string $moduleCode): bool
{
    // ...
    $module = Module::where('code', $moduleCode)->first();  // ❌ Hanya by code
    // ...
}
```

**Masalah:**

- Middleware mengirim **slug** (`correspondence-management`)
- PermissionService mencari by **code** (`CORRES`)
- Tidak match → Error 403

---

## ✅ Solution Implemented

### 1. Update Database Slug ✅

```php
php artisan tinker
>>> App\Models\Module::where('code', 'CORRES')->update(['slug' => 'correspondence-management']);
```

**Result:**

```json
{
  "id": 1,
  "name": "Korespondensi",
  "slug": "correspondence-management", // ✅ Standar kebab-case
  "code": "CORRES"
}
```

### 2. Fix Middleware Format ✅

```php
// modules/Correspondence/Http/routes.php
// Sebelum: 'module:correspondence-management'
// Sesudah: 'module.permission:correspondence-management'
Route::middleware(['web', 'auth', 'tenant', 'module.permission:correspondence-management'])
```

### 3. Add Root Route ✅

```php
// Tambahkan route untuk URL /correspondence (root)
Route::get('/', [CorrespondenceController::class, 'dashboard'])->name('index');
Route::get('/dashboard', [CorrespondenceController::class, 'dashboard'])->name('dashboard');
```

### 4. Update Sidebar URL ✅

```php
// resources/views/layouts/partials/sidebar.blade.php
elseif ($module->slug == 'correspondence-management') {
    $moduleUrl = url('correspondence'); // ✅ Sesuai dengan route prefix
}
```

### 5. **Fix PermissionService to Support Slug** ✅

**Critical Fix:**

```php
// app/Services/PermissionService.php

public function userHasModuleAccess(User $user, string $moduleCode): bool
{
    // Superadmin always has access
    if ($user->isSuperadmin()) {
        return true;
    }

    // ✅ Get module by code OR slug
    $module = Module::where('code', $moduleCode)
        ->orWhere('slug', $moduleCode)  // ✅ Support slug!
        ->first();

    if (!$module) {
        return false;
    }

    // Check if tenant has module activated (using module code)
    if (!$user->tenant || !$user->tenant->hasModule($module->code)) {
        return false;
    }

    // ... rest of the code
}

private function fetchUserPermissions(User $user, string $moduleCode): array
{
    // ...

    // ✅ Get module by code OR slug
    $module = Module::where('code', $moduleCode)
        ->orWhere('slug', $moduleCode)  // ✅ Support slug!
        ->first();

    // ... rest of the code
}
```

**Impact:** Ini adalah fix yang paling penting! Sekarang PermissionService bisa handle middleware yang mengirim baik `code` maupun `slug`.

---

## 📊 Impact & Results

### Before Fix

- ❌ Database slug: `corres` (tidak standar)
- ❌ Middleware: `module:correspondence-management` (format lama)
- ❌ PermissionService: Hanya support `code`
- ❌ Error: 403 "You do not have access to this module"
- ❌ User tidak bisa akses modul

### After Fix

- ✅ Database slug: `correspondence-management` (standar)
- ✅ Middleware: `module.permission:correspondence-management` (format baru)
- ✅ PermissionService: Support `code` OR `slug`
- ✅ No error: Permission check passed
- ✅ User bisa akses modul dengan normal

---

## 🎓 Key Lessons

### 1. Slug Harus Konsisten dan Standar

> **CRITICAL:** Slug di database HARUS match dengan slug di routes, sidebar, dan policy!

**Aturan Slug:**

- Format: `kebab-case`
- Suffix: `-management` (untuk konsistensi)
- Contoh: `correspondence-management`, `performance-management`, `inventory-management`

**❌ JANGAN gunakan slug pendek:**

```php
'slug' => 'corres',  // ❌ SALAH
'slug' => 'perf',    // ❌ SALAH
'slug' => 'inv',     // ❌ SALAH
```

**✅ GUNAKAN slug standar:**

```php
'slug' => 'correspondence-management',  // ✅ BENAR
'slug' => 'performance-management',     // ✅ BENAR
'slug' => 'inventory-management',       // ✅ BENAR
```

### 2. PermissionService Harus Flexible

> **IMPORTANT:** PermissionService harus bisa handle baik `code` maupun `slug`

**Alasan:**

- Middleware bisa mengirim `code` atau `slug`
- Policy bisa menggunakan `code` atau `slug`
- Seeder bisa menggunakan format yang berbeda

**Solution:**

```php
$module = Module::where('code', $moduleCode)
    ->orWhere('slug', $moduleCode)
    ->first();
```

### 3. Middleware Format Baru

> **WAJIB:** Gunakan `module.permission:slug` (bukan `module:code`)

```php
// ❌ Format lama (deprecated)
Route::middleware(['module:CORRESPONDENCE'])

// ✅ Format baru (recommended)
Route::middleware(['module.permission:correspondence-management'])
```

### 4. Verifikasi Slug Consistency

**Checklist sebelum deploy:**

```bash
# 1. Cek slug di database
php artisan tinker
>>> App\Models\Module::where('code', 'YOUR_CODE')->first(['slug', 'code', 'name']);

# 2. Cek middleware di routes.php
grep -r "module.permission:" modules/YourModule/Http/routes.php

# 3. Cek sidebar configuration
grep -r "module->slug ==" resources/views/layouts/partials/sidebar.blade.php

# 4. Cek policy moduleCode
grep -r "moduleCode =" app/Policies/YourPolicy.php
```

**Semua harus match!**

---

## 📝 Updated Documentation

Dokumentasi berikut telah diupdate untuk mencegah masalah serupa:

### 1. COMMON-MISTAKES.md

**Ditambahkan:**

- ❌ Mistake #15: Slug di Database Tidak Sesuai dengan Configuration
  - Contoh kasus Correspondence
  - Prevention guidelines
  - Fix options

### 2. MODULE-DEVELOPMENT-GUIDE.md

**Ditambahkan:**

- Troubleshooting: Error 403 dengan 3 penyebab
- Verifikasi slug consistency
- PermissionService slug support requirement

### 3. LESSON-LEARNED-CORRESPONDENCE.md (NEW)

**File baru yang berisi:**

- Problem summary
- Root cause analysis lengkap
- Solution implementation
- Key lessons learned
- Prevention checklist

---

## 🔄 Prevention Checklist

Untuk mencegah masalah serupa di modul baru:

### Saat Membuat Seeder

- [ ] ✅ Gunakan slug standar kebab-case
- [ ] ✅ Tambahkan suffix `-management`
- [ ] ✅ Jangan gunakan slug pendek (corres, perf, inv, dll)
- [ ] ✅ Verifikasi slug match dengan folder name

```php
// ✅ BENAR
Module::create([
    'name' => 'Manajemen Inventori',
    'slug' => 'inventory-management',  // ✅ Standar
    'code' => 'INVENTORY_MANAGEMENT',
]);
```

### Saat Membuat Routes

- [ ] ✅ Gunakan middleware `module.permission:slug`
- [ ] ✅ Slug match dengan database
- [ ] ✅ Tambahkan root route (`/`)

```php
// ✅ BENAR
Route::middleware(['web', 'auth', 'tenant', 'module.permission:inventory-management'])
    ->prefix('inventory-management')
    ->group(function () {
        Route::get('/', [Controller::class, 'index'])->name('index');
    });
```

### Saat Konfigurasi Sidebar

- [ ] ✅ Tambahkan kondisi untuk module slug
- [ ] ✅ URL match dengan route prefix

```php
// ✅ BENAR
elseif ($module->slug == 'inventory-management') {
    $moduleUrl = url('inventory-management');
}
```

### Saat Testing

- [ ] ✅ Verifikasi slug di database
- [ ] ✅ Test permission check
- [ ] ✅ Test sidebar link
- [ ] ✅ Test CRUD operations

---

## 🔗 Related Files

### Files Modified

1. `modules/Correspondence/Http/routes.php` - Fix middleware format, add root route
2. `resources/views/layouts/partials/sidebar.blade.php` - Update URL configuration
3. `app/Services/PermissionService.php` - **Add slug support (CRITICAL)**
4. Database: `modules` table - Update slug from `corres` to `correspondence-management`

### Files to Check for New Modules

1. `database/seeders/{ModuleName}ModuleSeeder.php` - Slug definition
2. `modules/{ModuleName}/Http/routes.php` - Middleware and prefix
3. `resources/views/layouts/partials/sidebar.blade.php` - URL configuration
4. `app/Policies/{ModuleName}Policy.php` - moduleCode definition

---

## 📚 References

- [COMMON-MISTAKES.md](./COMMON-MISTAKES.md) - Mistake #15
- [MODULE-DEVELOPMENT-GUIDE.md](./MODULE-DEVELOPMENT-GUIDE.md) - Troubleshooting section
- [LESSON-LEARNED-PERFORMANCE-MANAGEMENT.md](./LESSON-LEARNED-PERFORMANCE-MANAGEMENT.md) - Similar issues

---

## ✅ Verification

### Permission Check Test

```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'like', '%admin%')->first();
>>> $service = app(App\Services\PermissionService::class);
>>> $service->userHasModuleAccess($user, 'correspondence-management');
# Result: true ✅

>>> $service->userHasModuleAccess($user, 'CORRES');
# Result: true ✅
```

### Route Access Test

```bash
curl -I http://siar.test/correspondence
# Result: 302 Redirect to login (normal, not 403) ✅
```

### Database Verification

```bash
php artisan tinker
>>> App\Models\Module::where('code', 'CORRES')->first(['slug', 'code', 'name']);
# Result: {"slug":"correspondence-management","code":"CORRES","name":"Korespondensi"} ✅
```

---

**Status:** ✅ **RESOLVED** - Module Correspondence sekarang berfungsi dengan benar!  
**Next Action:** Apply lessons learned to all existing and future modules  
**Documentation:** Updated and comprehensive
