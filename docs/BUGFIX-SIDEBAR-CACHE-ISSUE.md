# Bugfix: Sidebar Menu Tidak Update Setelah Module Sync

**Tanggal**: 21 November 2025  
**Issue**: Nama modul di sidebar menu tidak berubah meskipun sudah sync dari filesystem

## Problem

Setelah menjalankan "Sync dari Filesystem" dan nama modul di database sudah berubah ke Bahasa Indonesia, sidebar menu masih menampilkan nama lama dalam bahasa Inggris:

❌ **Sebelum:**

- ActivityManagement
- DocumentManagement
- RiskManagement
- dll.

✅ **Seharusnya:**

- Manajemen Aktivitas
- Manajemen Dokumen
- Manajemen Risiko
- dll.

## Root Cause

Data modul untuk sidebar di-**cache selama 1 jam** (3600 detik) di `SidebarComposer.php`:

```php
// Line 49
$activeModules = Cache::remember($cacheKey, 3600, function () use ($tenant_id) {
    $tenant = Tenant::find($tenant_id);
    if (!$tenant) {
        return collect([]);
    }

    return $tenant->modules()
        ->where('tenant_modules.is_active', true)
        ->orderBy('name')
        ->get();
});
```

**Cache Key**: `sidebar_modules_tenant_{tenant_id}`

Ketika modul di-sync dan nama berubah di database, cache tidak ter-invalidate, sehingga sidebar masih menampilkan data lama dari cache.

## Solution

Tambahkan **automatic cache invalidation** setelah module sync.

### 1. ModuleManagementController.php

**Method baru: `clearSidebarCache()`**

```php
private function clearSidebarCache()
{
    try {
        // Get all tenant IDs
        $tenantIds = Tenant::pluck('id');

        // Clear cache for each tenant
        foreach ($tenantIds as $tenantId) {
            $cacheKey = 'sidebar_modules_tenant_' . $tenantId;
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        \Illuminate\Support\Facades\Log::info('Sidebar cache cleared for all tenants');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::warning('Failed to clear sidebar cache: ' . $e->getMessage());
    }
}
```

**Panggil setelah sync:**

```php
DB::commit();

// Clear sidebar cache for all tenants after module sync
$this->clearSidebarCache();

$message = "Sinkronisasi selesai...";
```

### 2. SyncModulesFromFilesystem.php (Console Command)

**Method baru: `clearSidebarCache()`**

```php
private function clearSidebarCache()
{
    try {
        $tenantIds = \App\Models\Tenant::pluck('id');

        foreach ($tenantIds as $tenantId) {
            $cacheKey = 'sidebar_modules_tenant_' . $tenantId;
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        $this->info('✓ Sidebar cache cleared for all tenants');
    } catch (\Exception $e) {
        $this->warn('⚠ Failed to clear sidebar cache: ' . $e->getMessage());
    }
}
```

**Panggil setelah sync:**

```php
DB::commit();

// Clear sidebar cache for all tenants
$this->clearSidebarCache();

$this->newLine();
$this->info('Synchronization completed successfully!');
```

## Files Modified

1. ✅ `/app/Http/Controllers/SuperAdmin/ModuleManagementController.php`

   - Added `clearSidebarCache()` method
   - Call after `DB::commit()` in `syncFromFilesystem()`

2. ✅ `/app/Console/Commands/SyncModulesFromFilesystem.php`
   - Added `clearSidebarCache()` method
   - Call after `DB::commit()` in `handle()`

## How It Works

### Flow Diagram:

```
1. User clicks "Sync dari Filesystem"
   ↓
2. Module data updated in database
   ↓
3. DB::commit()
   ↓
4. clearSidebarCache() called
   ↓
5. Cache cleared for ALL tenants
   ↓
6. User refreshes page
   ↓
7. SidebarComposer loads fresh data from DB
   ↓
8. Sidebar shows updated module names ✅
```

## Testing

### Test via Web:

1. Buka: `http://siar.test/superadmin/modules`
2. Klik **"Sync dari Filesystem"**
3. Tunggu pesan sukses
4. **Refresh halaman** (F5 atau Cmd+R)
5. Sidebar seharusnya menampilkan nama Bahasa Indonesia

### Test via Console:

```bash
php artisan modules:sync --no-interaction
```

Output:

```
✓ Updated: Manajemen Pengguna (name, icon)
✓ Updated: Manajemen Aktivitas (name, icon)
...
✓ Sidebar cache cleared for all tenants
```

## Manual Cache Clear (Alternative)

Jika perlu clear cache manual:

### Via Artisan:

```bash
php artisan cache:forget sidebar_modules_tenant_1
php artisan cache:forget sidebar_modules_tenant_2
# ... untuk setiap tenant
```

### Via Tinker:

```bash
php artisan tinker
```

```php
// Clear untuk semua tenant
$tenantIds = \App\Models\Tenant::pluck('id');
foreach ($tenantIds as $id) {
    Cache::forget('sidebar_modules_tenant_' . $id);
}
```

### Clear All Cache:

```bash
php artisan cache:clear
```

## Cache Strategy

### Current Strategy:

- **Cache Duration**: 1 hour (3600 seconds)
- **Cache Key Pattern**: `sidebar_modules_tenant_{tenant_id}`
- **Invalidation**: Automatic after module sync

### Why Cache?

- ✅ **Performance**: Mengurangi query database untuk setiap page load
- ✅ **Scalability**: Penting untuk sistem dengan banyak tenant
- ✅ **User Experience**: Sidebar load lebih cepat

### When Cache is Cleared?

1. ✅ After module sync (automatic)
2. ✅ After module create/update/delete (should be added)
3. ✅ Manual clear via artisan command

## Future Improvements

### 1. Add Cache Invalidation to Other Module Operations

**ModuleManagementController.php:**

```php
public function store(Request $request)
{
    // ... create module
    $this->clearSidebarCache();
    return redirect()->route('superadmin.modules.index')
        ->with('success', 'Modul berhasil dibuat.');
}

public function update(Request $request, Module $module)
{
    // ... update module
    $this->clearSidebarCache();
    return redirect()->route('superadmin.modules.index')
        ->with('success', 'Modul berhasil diperbarui.');
}

public function destroy(Module $module)
{
    // ... delete module
    $this->clearSidebarCache();
    return redirect()->route('superadmin.modules.index')
        ->with('success', 'Modul berhasil dihapus.');
}
```

### 2. Use Cache Tags (Laravel 10+)

```php
// Store with tags
Cache::tags(['sidebar', 'tenant_' . $tenant_id])
    ->remember($cacheKey, 3600, function () {
        // ...
    });

// Clear by tag
Cache::tags(['sidebar'])->flush();
```

### 3. Event-Based Cache Invalidation

```php
// Create event
event(new ModuleUpdated($module));

// Listen and clear cache
class ClearSidebarCache
{
    public function handle(ModuleUpdated $event)
    {
        // Clear cache for all tenants
    }
}
```

## Related Files

- `/app/Http/ViewComposers/SidebarComposer.php` - Sidebar data provider with caching
- `/resources/views/layouts/partials/sidebar.blade.php` - Sidebar view
- `/app/Http/Controllers/SuperAdmin/ModuleManagementController.php` - Module CRUD
- `/app/Console/Commands/SyncModulesFromFilesystem.php` - Console sync command

## Verification

Setelah fix, verifikasi dengan:

1. **Check Database:**

```sql
SELECT id, name, slug, icon FROM modules ORDER BY name;
```

2. **Check Cache:**

```bash
php artisan tinker
```

```php
Cache::get('sidebar_modules_tenant_2'); // Should be null after clear
```

3. **Check Sidebar:**

- Login sebagai user tenant
- Lihat sidebar menu
- Nama modul harus dalam Bahasa Indonesia dengan icon yang sesuai

## Summary

✅ **Problem**: Sidebar menu tidak update karena cache  
✅ **Solution**: Auto-clear cache setelah module sync  
✅ **Impact**: Sidebar langsung menampilkan perubahan setelah refresh  
✅ **Performance**: Tetap optimal dengan caching strategy

---

**Status**: ✅ FIXED  
**Priority**: HIGH  
**Tested**: YES
