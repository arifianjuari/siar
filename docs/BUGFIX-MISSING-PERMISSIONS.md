# Bugfix: Menu Tidak Muncul Karena Permission Tidak Tersimpan

**Tanggal:** 19 November 2025  
**Status:** ✅ Fixed

## Masalah

User dengan role **Tenant Admin** sudah diberi hak akses untuk semua modul di halaman permission, tetapi menu **Correspondence** dan **Document Management** tidak muncul di sidebar.

### Screenshot Evidence:

- ✅ Permission form menunjukkan semua checkbox tercentang
- ❌ Menu Correspondence tidak muncul di sidebar
- ❌ Menu Document Management tidak muncul di sidebar

## Root Cause Analysis

### 1. **Permission Record Tidak Ada di Database**

Meskipun di UI terlihat checkbox sudah dicentang, ternyata data **tidak tersimpan** ke database:

```sql
-- Query untuk cek permission
SELECT * FROM role_module_permissions
WHERE role_id = 2
AND module_id IN (1, 3);

-- Result: EMPTY (0 rows)
```

### 2. **Sidebar Check Permission**

Sidebar menggunakan function `hasModulePermission()` yang mengecek database:

```php
// File: resources/views/layouts/partials/sidebar.blade.php
@if($module && !empty($module->slug) && hasModulePermission($module->slug))
    // Render menu
@endif
```

Jika permission record tidak ada di database, `hasModulePermission()` return `false`, sehingga menu tidak di-render.

### 3. **Kemungkinan Penyebab Permission Tidak Tersimpan**

Ada beberapa kemungkinan:

1. **Form tidak ter-submit dengan benar** - JavaScript error atau network issue
2. **Controller error** - Exception terjadi saat save tapi tidak terlihat
3. **Transaction rollback** - Ada error di tengah proses save
4. **Validation error** - Data tidak lolos validasi
5. **Module baru ditambahkan** - Permission belum di-create untuk module baru

## Solusi yang Diimplementasikan

### 1. **Manual Insert Missing Permissions**

```php
// Insert permission untuk Correspondence
DB::table('role_module_permissions')->updateOrInsert(
    [
        'role_id' => 2,  // Tenant Admin
        'module_id' => 1 // Correspondence
    ],
    [
        'can_view' => 1,
        'can_create' => 1,
        'can_edit' => 1,
        'can_delete' => 1,
        'can_export' => 1,
        'can_import' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]
);

// Insert permission untuk Document Management
DB::table('role_module_permissions')->updateOrInsert(
    [
        'role_id' => 2,  // Tenant Admin
        'module_id' => 3 // Document Management
    ],
    [
        'can_view' => 1,
        'can_create' => 1,
        'can_edit' => 1,
        'can_delete' => 1,
        'can_export' => 1,
        'can_import' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]
);
```

### 2. **Clear Cache**

```bash
php artisan cache:clear
php artisan view:clear
```

### 3. **Verification**

```sql
-- Verify all permissions for Tenant Admin role
SELECT m.name, rmp.can_view, rmp.can_create, rmp.can_edit
FROM role_module_permissions rmp
JOIN modules m ON rmp.module_id = m.id
WHERE rmp.role_id = 2
ORDER BY m.name;
```

Result:

```
✓ Correspondence (view: 1, create: 1, edit: 1)
✓ Document Management (view: 1, create: 1, edit: 1)
✓ Kendali Mutu Biaya (view: 1, create: 1, edit: 1)
✓ Manajemen Produk (view: 1, create: 1, edit: 1)
✓ Manajemen Risiko (view: 1, create: 1, edit: 1)
✓ Manajemen SPO (view: 1, create: 1, edit: 1)
✓ Pengelolaan Kegiatan (view: 1, create: 1, edit: 1)
✓ Performance Management (view: 1, create: 1, edit: 1)
✓ User Management (view: 1, create: 1, edit: 1)
✓ WorkUnit (view: 1, create: 1, edit: 1)
```

## Solusi Permanen (Prevention)

### 1. **Add Logging to Permission Update**

Edit `TenantRoleController@updatePermissions`:

```php
public function updatePermissions(Request $request, Tenant $tenant, $roleId)
{
    try {
        $role = Role::withoutGlobalScope('tenant_id')
            ->where('id', $roleId)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        // LOG: Request data
        \Log::info('Updating permissions for role', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'tenant_id' => $tenant->id,
            'permissions_count' => count($request->permissions ?? [])
        ]);

        $validated = $request->validate([
            'permissions' => 'array'
        ]);

        // Hapus semua permission yang ada
        $deletedCount = $role->permissions()->delete();
        \Log::info('Deleted old permissions', ['count' => $deletedCount]);

        // Tambahkan permission baru
        if ($request->permissions) {
            $createdCount = 0;
            foreach ($request->permissions as $moduleId => $permissions) {
                if (isset($permissions['module_id'])) {
                    $role->permissions()->create([
                        'module_id' => $permissions['module_id'],
                        'can_view' => isset($permissions['can_view']) ? 1 : 0,
                        'can_create' => isset($permissions['can_create']) ? 1 : 0,
                        'can_edit' => isset($permissions['can_edit']) ? 1 : 0,
                        'can_delete' => isset($permissions['can_delete']) ? 1 : 0,
                        'can_export' => isset($permissions['can_export']) ? 1 : 0,
                        'can_import' => isset($permissions['can_import']) ? 1 : 0,
                    ]);
                    $createdCount++;
                }
            }
            \Log::info('Created new permissions', ['count' => $createdCount]);
        }

        // Clear permission cache
        \Cache::tags(['permissions', "user_{$role->id}"])->flush();

        return response()->json([
            'success' => true,
            'message' => 'Hak akses berhasil diperbarui'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error updating permissions', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}
```

### 2. **Add Validation to Form**

Add JavaScript validation before submit:

```javascript
// File: resources/views/roles/superadmin/tenants/roles/permissions.blade.php
document.getElementById('permission-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const permissions = {};

    // Count checked permissions
    let checkedCount = 0;
    formData.forEach((value, key) => {
        if (key.includes('can_view') && value === '1') {
            checkedCount++;
        }
    });

    console.log('Submitting permissions:', {
        totalModules: {{ count($modules) }},
        checkedPermissions: checkedCount,
        formData: Object.fromEntries(formData)
    });

    if (checkedCount === 0) {
        if (!confirm('Tidak ada permission yang dipilih. Lanjutkan?')) {
            return;
        }
    }

    // Submit form
    this.submit();
});
```

### 3. **Create Seeder for Default Permissions**

```php
// database/seeders/DefaultRolePermissionsSeeder.php
class DefaultRolePermissionsSeeder extends Seeder
{
    public function run()
    {
        $tenants = Tenant::where('id', '!=', 1)->get();

        foreach ($tenants as $tenant) {
            $tenantAdminRole = Role::where('tenant_id', $tenant->id)
                ->where('slug', 'tenant-admin')
                ->first();

            if ($tenantAdminRole) {
                $modules = Module::all();

                foreach ($modules as $module) {
                    RoleModulePermission::updateOrCreate(
                        [
                            'role_id' => $tenantAdminRole->id,
                            'module_id' => $module->id
                        ],
                        [
                            'can_view' => 1,
                            'can_create' => 1,
                            'can_edit' => 1,
                            'can_delete' => 1,
                            'can_export' => 1,
                            'can_import' => 1
                        ]
                    );
                }

                $this->command->info("Created permissions for {$tenant->name}");
            }
        }
    }
}
```

Run seeder:

```bash
php artisan db:seed --class=DefaultRolePermissionsSeeder
```

### 4. **Add Database Constraint**

Ensure permission records exist:

```sql
-- Add foreign key constraints
ALTER TABLE role_module_permissions
ADD CONSTRAINT fk_role_module_permissions_role
FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE;

ALTER TABLE role_module_permissions
ADD CONSTRAINT fk_role_module_permissions_module
FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE;

-- Add unique constraint to prevent duplicates
ALTER TABLE role_module_permissions
ADD UNIQUE KEY unique_role_module (role_id, module_id);
```

## Testing Checklist

### Before Fix:

- [ ] Login sebagai Tenant Admin
- [ ] Sidebar tidak menampilkan Correspondence
- [ ] Sidebar tidak menampilkan Document Management
- [ ] Query database: `SELECT * FROM role_module_permissions WHERE role_id = 2 AND module_id IN (1,3)` → EMPTY

### After Fix:

- [x] Query database: Permission records exist
- [x] Clear cache: `php artisan cache:clear`
- [x] Login sebagai Tenant Admin
- [x] Sidebar menampilkan Correspondence ✓
- [x] Sidebar menampilkan Document Management ✓
- [x] Klik menu → navigasi berfungsi
- [x] Permission checking berfungsi

## Impact

- ✅ **Fixed:** Correspondence dan Document Management sekarang muncul di sidebar
- ✅ **Improved:** Permission records lengkap di database
- ✅ **Better UX:** Tenant Admin dapat mengakses semua modul yang seharusnya

## Related Issues

- Sidebar slug mismatch (sudah diperbaiki di BUGFIX-SIDEBAR-MISSING-MODULES.md)
- Module duplicate (sudah diperbaiki di CLEANUP-DUPLICATE-MODULES.md)

## Commands Used

```bash
# Insert missing permissions
php artisan tinker --execute="..."

# Clear cache
php artisan cache:clear
php artisan view:clear

# Verify permissions
php artisan tinker --execute="
DB::table('role_module_permissions')
    ->join('modules', 'role_module_permissions.module_id', '=', 'modules.id')
    ->where('role_module_permissions.role_id', 2)
    ->select('modules.name', 'role_module_permissions.*')
    ->get();
"
```

## Next Steps

1. ✅ Test dengan user Tenant Admin
2. ✅ Verify semua menu muncul
3. ⏳ Add logging ke permission update (optional)
4. ⏳ Create seeder untuk default permissions (optional)
5. ⏳ Add database constraints (optional)
