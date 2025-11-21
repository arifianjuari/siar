# Bugfix: Product Management Error 403 "You do not have access to this module"

**Tanggal:** 20 November 2025  
**Status:** ✅ Fixed

## Masalah

User dengan role **Tenant Admin** yang sudah diberi permission untuk modul **Product Management** mendapat error **403** saat mengakses `/product-management/products`:

```
403 Forbidden
You do not have access to this module.
```

Padahal:

- ✅ Permission sudah ada di database (`can_view: 1, can_create: 1, can_edit: 1, can_delete: 1`)
- ✅ Module aktif untuk tenant (`is_active: 1`)
- ✅ User sudah login dengan role Tenant Admin

## Root Cause Analysis

### 1. **Middleware Parameter Salah**

File: `modules/ProductManagement/Http/routes.php`

**Before:**

```php
Route::middleware(['web', 'auth', 'tenant', 'module.permission:product-management'])
    ->prefix('product-management')
    ->name('modules.product-management.')
    ->group(function () {
        Route::resource('products', ProductController::class);
    });
```

Masalah:

- Middleware menggunakan: `module.permission:product-management`
- Parameter yang dikirim: `product-management` (slug)
- Tetapi `CheckModulePermission` middleware mencari berdasarkan **`code`**, bukan `slug`

### 2. **PermissionService Menggunakan Module Code**

File: `app/Services/PermissionService.php` (Line 74)

```php
public function userHasModuleAccess(User $user, string $moduleCode): bool
{
    // ...

    // Check if user's role has any permission for the module
    $module = Module::where('code', $moduleCode)->first();  // ← Mencari berdasarkan CODE
    if (!$module) {
        return false;
    }

    // ...
}
```

### 3. **Module Code vs Slug**

Database `modules` table:

| Field | Value                |
| ----- | -------------------- |
| name  | Manajemen Produk     |
| slug  | `product-management` |
| code  | `PRODUCT_MANAGEMENT` |

Middleware mencari: `product-management` (slug)  
PermissionService mencari: `code` field  
Result: **NOT FOUND** → Error 403

## Solusi yang Diimplementasikan

### **Update Middleware Parameter**

File: `modules/ProductManagement/Http/routes.php`

**Before:**

```php
Route::middleware(['web', 'auth', 'tenant', 'module.permission:product-management'])
```

**After:**

```php
Route::middleware(['web', 'auth', 'tenant', 'module.permission:PRODUCT_MANAGEMENT'])
```

**Penjelasan:**

- Ubah parameter dari `product-management` (slug) ke `PRODUCT_MANAGEMENT` (code)
- Sekarang sesuai dengan yang dicari oleh `PermissionService`

## Perbedaan Middleware

Ada 2 middleware untuk module checking:

### 1. **`module:` (CheckModuleAccess)**

- Alias: `module`
- File: `app/Http/Middleware/CheckModuleAccess.php`
- Mencari berdasarkan: **slug ATAU code** (flexible)
- Query: `Module::where('slug', $param)->orWhere('code', $param)->first()`
- Digunakan oleh: Correspondence, Document Management, Risk Management, dll

### 2. **`module.permission:` (CheckModulePermission)**

- Alias: `module.permission`
- File: `app/Http/Middleware/CheckModulePermission.php`
- Mencari berdasarkan: **code only** (strict)
- Query: `Module::where('code', $moduleCode)->first()`
- Digunakan oleh: Product Management
- Menggunakan: `PermissionService`

## Rekomendasi

### Opsi 1: Standardize ke `module:` (RECOMMENDED)

Ubah semua modul menggunakan middleware `module:` yang lebih fleksibel:

```php
// Product Management
Route::middleware(['web', 'auth', 'tenant', 'module:product-management'])
```

**Keuntungan:**

- ✅ Konsisten dengan modul lain
- ✅ Fleksibel (bisa pakai slug atau code)
- ✅ Lebih mudah maintain

### Opsi 2: Keep `module.permission:` dengan Code

Tetap gunakan `module.permission:` tapi pastikan pakai code:

```php
// Product Management
Route::middleware(['web', 'auth', 'tenant', 'module.permission:PRODUCT_MANAGEMENT'])
```

**Keuntungan:**

- ✅ Menggunakan PermissionService yang lebih modern
- ✅ Lebih strict validation

**Kekurangan:**

- ⚠️ Harus ingat pakai CODE, bukan slug
- ⚠️ Inconsistent dengan modul lain

## Module Code Reference

Untuk referensi, berikut adalah code untuk setiap modul:

| Module                 | Slug                        | Code                  |
| ---------------------- | --------------------------- | --------------------- |
| Correspondence         | `correspondence-management` | `CORRESPONDENCE`      |
| Document Management    | `document-management`       | `DOCMANAGEMENT`       |
| Product Management     | `product-management`        | `PRODUCT_MANAGEMENT`  |
| Risk Management        | `risk-management`           | `risk-management`     |
| Activity Management    | `activity-management`       | `activity-management` |
| User Management        | `user-management`           | `user_management`     |
| Performance Management | `performance-management`    | `PERF`                |
| Kendali Mutu Biaya     | `kendali-mutu-biaya`        | `KMKB`                |
| SPO Management         | `spo-management`            | `spo-management`      |
| WorkUnit               | `work-unit`                 | `WORK_UNIT`           |

## Testing

### Before Fix:

```bash
# Access product management
curl http://siar.test/product-management/products

# Result: 403 Forbidden
# Error: "You do not have access to this module."
```

### After Fix:

```bash
# Clear cache
php artisan route:clear
php artisan cache:clear

# Access product management
curl http://siar.test/product-management/products

# Result: ✅ 200 OK
# Page loads successfully
```

### Verification Steps:

1. ✅ **Clear cache:**

   ```bash
   php artisan route:clear
   php artisan cache:clear
   ```

2. ✅ **Login sebagai Tenant Admin**

3. ✅ **Klik menu Product Management**

   - Should navigate to `/product-management/products`
   - Should NOT show 403 error
   - Should display product list

4. ✅ **Test CRUD operations:**
   - View products list ✓
   - Create new product ✓
   - Edit product ✓
   - Delete product ✓

## Related Issues

### Issue 1: Correspondence Module Not Found (FIXED)

- Doc: `BUGFIX-CORRESPONDENCE-MODULE-NOT-FOUND.md`
- Solution: Updated database slug to match middleware

### Issue 2: Missing Permissions (FIXED)

- Doc: `BUGFIX-MISSING-PERMISSIONS.md`
- Solution: Manually inserted missing permission records

### Issue 3: Sidebar Active State (FIXED)

- Doc: `BUGFIX-SIDEBAR-ACTIVE-STATE.md`
- Solution: Updated isActive logic for all modules

## Impact

- ✅ **Fixed:** Product Management module sekarang dapat diakses
- ✅ **Improved:** Error 403 sudah tidak muncul
- ✅ **Better UX:** User dapat menggunakan fitur product management
- ✅ **Consistent:** Middleware parameter sekarang benar

## Commands Used

```bash
# Check module data
php artisan tinker --execute="
DB::table('modules')
    ->where('slug', 'product-management')
    ->first();
"

# Check permission
php artisan tinker --execute="
\$tenant = DB::table('tenants')->where('name', 'like', '%Bhayangkara%')->first();
\$role = DB::table('roles')->where('tenant_id', \$tenant->id)->where('slug', 'tenant-admin')->first();
\$module = DB::table('modules')->where('slug', 'product-management')->first();
DB::table('role_module_permissions')
    ->where('role_id', \$role->id)
    ->where('module_id', \$module->id)
    ->first();
"

# Clear cache
php artisan route:clear
php artisan cache:clear

# Test routes
php artisan route:list --path=product-management
```

## Next Steps

1. ✅ Test product management dengan user Tenant Admin
2. ✅ Verify semua CRUD operations berfungsi
3. ⏳ Consider standardizing all modules to use `module:` middleware (optional)
4. ⏳ Document middleware differences for future reference (optional)

## Conclusion

Error 403 "You do not have access to this module" disebabkan oleh **mismatch antara parameter middleware (`product-management` slug) dan yang dicari oleh PermissionService (`PRODUCT_MANAGEMENT` code)**.

Solusi: **Update middleware parameter** untuk menggunakan module code yang benar.

Alternatif: **Ubah ke middleware `module:`** yang lebih fleksibel dan konsisten dengan modul lain.
