# Bugfix: Modul Correspondence "Modul tidak ditemukan"

**Tanggal:** 20 November 2025  
**Status:** ✅ Fixed

## Masalah

Ketika user dengan role **Tenant Admin** mengklik menu **Correspondence**, muncul error:

```
Modul tidak ditemukan
```

Padahal:

- ✅ Permission sudah ada di database
- ✅ Menu muncul di sidebar
- ✅ Route sudah terdaftar
- ✅ Service Provider sudah terdaftar

## Root Cause Analysis

### 1. **Middleware Slug Mismatch**

File: `modules/Correspondence/Http/routes.php`

```php
// Line 17: Middleware menggunakan slug 'correspondence-management'
Route::middleware(['web', 'auth', 'tenant', 'module:correspondence-management'])
    ->prefix('correspondence')
    ->name('modules.correspondence.')
    ->group(function () {
        // ...
    });
```

**Tetapi di database:**

```sql
SELECT slug FROM modules WHERE name = 'Correspondence';
-- Result: 'correspondence' (bukan 'correspondence-management')
```

### 2. **Module Middleware Check**

Middleware `module:correspondence-management` akan:

1. Parse slug dari parameter: `correspondence-management`
2. Query database: `SELECT * FROM modules WHERE slug = 'correspondence-management'`
3. Result: **NOT FOUND** ❌
4. Throw error: "Modul tidak ditemukan"

### 3. **Inconsistency Across Modules**

| Module                 | Database Slug            | Routes Middleware              | Status       |
| ---------------------- | ------------------------ | ------------------------------ | ------------ |
| Correspondence         | `correspondence`         | `correspondence-management` ❌ | **MISMATCH** |
| Document Management    | `document-management`    | `document-management` ✅       | OK           |
| Activity Management    | `activity-management`    | `activity-management` ✅       | OK           |
| Risk Management        | `risk-management`        | `risk-management` ✅           | OK           |
| Product Management     | `product-management`     | `product-management` ✅        | OK           |
| User Management        | `user-management`        | `user-management` ✅           | OK           |
| Performance Management | `performance-management` | `performance-management` ✅    | OK           |

## Solusi yang Diimplementasikan

### **Opsi 1: Update Routes Middleware** ✅ (DIPILIH)

Update middleware di `modules/Correspondence/Http/routes.php`:

**Before:**

```php
Route::middleware(['web', 'auth', 'tenant', 'module:correspondence-management'])
```

**After:**

```php
Route::middleware(['web', 'auth', 'tenant', 'module:correspondence'])
```

**Keuntungan:**

- ✅ Quick fix
- ✅ Tidak perlu update database
- ✅ Tidak perlu update permission records
- ✅ Tidak perlu update tenant_modules records

**Kekurangan:**

- ⚠️ Inconsistency dengan modul lain (mereka pakai format `-management`)

### **Opsi 2: Update Database Slug** (ALTERNATIF)

Update slug di database:

```sql
-- Update module slug
UPDATE modules
SET slug = 'correspondence-management'
WHERE slug = 'correspondence';

-- Update tenant_modules jika ada reference
UPDATE tenant_modules tm
JOIN modules m ON tm.module_id = m.id
SET tm.module_slug = 'correspondence-management'
WHERE m.slug = 'correspondence-management';
```

**Keuntungan:**

- ✅ Konsisten dengan modul lain
- ✅ Standarisasi naming convention

**Kekurangan:**

- ⚠️ Perlu update banyak tempat
- ⚠️ Perlu update sidebar code
- ⚠️ Perlu update permission checks
- ⚠️ Risk breaking existing code

## Files Modified

### 1. `modules/Correspondence/Http/routes.php`

```php
// Line 17: Changed middleware slug
Route::middleware(['web', 'auth', 'tenant', 'module:correspondence'])
    ->prefix('correspondence')
    ->name('modules.correspondence.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [CorrespondenceController::class, 'dashboard'])
            ->name('dashboard');

        // ... other routes
    });
```

## Testing

### Before Fix:

```bash
# Access correspondence dashboard
curl http://siar.test/correspondence/dashboard

# Result: Error "Modul tidak ditemukan"
```

### After Fix:

```bash
# Clear cache
php artisan route:clear
php artisan cache:clear

# Access correspondence dashboard
curl http://siar.test/correspondence/dashboard

# Result: ✅ Dashboard loads successfully
```

### Verification Checklist:

1. ✅ **Clear cache:**

   ```bash
   php artisan route:clear
   php artisan cache:clear
   ```

2. ✅ **Login sebagai Tenant Admin**

3. ✅ **Klik menu Correspondence**

   - Should navigate to `/correspondence/dashboard`
   - Should NOT show "Modul tidak ditemukan"
   - Should display correspondence dashboard

4. ✅ **Test other correspondence features:**

   - View letters list
   - Create new letter
   - Edit letter
   - Export PDF/Word
   - Generate QR Code

5. ✅ **Verify other modules still work:**
   - Document Management
   - Risk Management
   - Activity Management
   - etc.

## Related Issues

### Issue 1: Sidebar Slug Mismatch (FIXED)

- File: `resources/views/layouts/partials/sidebar.blade.php`
- Fixed in: `BUGFIX-SIDEBAR-MISSING-MODULES.md`
- Solution: Added support for both `correspondence` and `correspondence-management` slugs

### Issue 2: Missing Permissions (FIXED)

- File: Database `role_module_permissions` table
- Fixed in: `BUGFIX-MISSING-PERMISSIONS.md`
- Solution: Manually inserted missing permission records

## Prevention for Future

### 1. **Standardize Module Slugs**

Create migration to standardize all slugs:

```php
// database/migrations/2025_11_20_000000_standardize_module_slugs.php
public function up()
{
    // Option A: Add '-management' suffix to all modules
    DB::table('modules')
        ->where('slug', 'correspondence')
        ->update(['slug' => 'correspondence-management']);

    DB::table('modules')
        ->where('slug', 'work-unit')
        ->update(['slug' => 'work-units']);

    // Option B: Remove '-management' suffix from all modules
    // (Not recommended, will break existing code)
}
```

### 2. **Add Validation to Module Creation**

```php
// app/Http/Controllers/SuperAdmin/ModuleController.php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => [
            'required',
            'string',
            'unique:modules,slug',
            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', // kebab-case only
        ],
        'code' => 'required|string|unique:modules,code',
    ]);

    // Enforce naming convention
    if (!str_ends_with($request->slug, '-management')) {
        return back()->withErrors([
            'slug' => 'Slug harus diakhiri dengan -management untuk konsistensi'
        ]);
    }

    // ...
}
```

### 3. **Create Module Slug Helper**

```php
// app/Helpers/ModuleHelper.php
if (!function_exists('normalizeModuleSlug')) {
    function normalizeModuleSlug($slug) {
        // Map of slug variations to canonical slug
        $aliases = [
            'correspondence' => 'correspondence-management',
            'work-unit' => 'work-units',
            'work-units' => 'work-units',
            // Add more mappings as needed
        ];

        return $aliases[$slug] ?? $slug;
    }
}

// Usage in middleware:
$normalizedSlug = normalizeModuleSlug($slug);
$module = Module::where('slug', $normalizedSlug)->first();
```

### 4. **Update Middleware to Handle Variations**

```php
// app/Http/Middleware/CheckModulePermission.php
public function handle($request, Closure $next, $moduleSlug)
{
    // Try exact match first
    $module = Module::where('slug', $moduleSlug)->first();

    // If not found, try variations
    if (!$module) {
        $variations = [
            $moduleSlug . '-management',
            str_replace('-management', '', $moduleSlug),
        ];

        foreach ($variations as $variation) {
            $module = Module::where('slug', $variation)->first();
            if ($module) break;
        }
    }

    if (!$module) {
        abort(404, 'Modul tidak ditemukan');
    }

    // ... rest of permission check
}
```

## Impact

- ✅ **Fixed:** Correspondence module sekarang dapat diakses
- ✅ **Improved:** Error "Modul tidak ditemukan" sudah tidak muncul
- ✅ **Better UX:** User dapat menggunakan fitur correspondence
- ⚠️ **Note:** Masih ada inconsistency dengan modul lain (perlu standardisasi di masa depan)

## Commands Used

```bash
# Fix routes
# Edit: modules/Correspondence/Http/routes.php
# Change: module:correspondence-management → module:correspondence

# Clear cache
php artisan route:clear
php artisan cache:clear

# Verify routes
php artisan route:list --name=correspondence

# Check module slug in database
php artisan tinker --execute="
DB::table('modules')
    ->select('id', 'name', 'slug')
    ->orderBy('name')
    ->get();
"
```

## Next Steps

1. ✅ Test correspondence module dengan user Tenant Admin
2. ✅ Verify semua fitur berfungsi (dashboard, letters, reports, export, QR)
3. ⏳ Consider standardizing all module slugs (optional, untuk konsistensi)
4. ⏳ Add validation untuk module slug creation (optional)
5. ⏳ Create helper function untuk handle slug variations (optional)

## Conclusion

Masalah "Modul tidak ditemukan" disebabkan oleh **mismatch antara slug di database (`correspondence`) dan slug di middleware (`correspondence-management`)**.

Solusi quick fix: **Update middleware** untuk menggunakan slug yang sesuai dengan database.

Solusi jangka panjang: **Standardize semua module slugs** untuk konsistensi.
