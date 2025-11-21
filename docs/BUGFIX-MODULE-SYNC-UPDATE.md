# Bugfix: Module Sync Not Updating Name & Icon

**Tanggal**: 21 November 2025  
**Issue**: Sync modul tidak meng-update field `name` dan `icon` dari module.json

## Problem

Ketika menjalankan "Sync dari Filesystem", hasilnya:

```
Created: 0
Updated: 0
Deleted: 0
```

Padahal file `module.json` sudah diubah dengan nama Bahasa Indonesia dan icon baru.

## Root Cause

### 1. Icon tidak dibaca dari module.json

**File**: `ModuleManagementController.php` line 184

**Sebelum:**

```php
$moduleData = [
    'name' => $fsModule['name'],
    'code' => $code,
    'slug' => $slug,
    'description' => $fsModule['description'] ?? 'Module ' . $fsModule['name'],
    'icon' => 'fa-cube', // ❌ Hardcoded, tidak baca dari module.json
];
```

**Sesudah:**

```php
// Get icon from module.json metadata or use default
$icon = 'fa-cube'; // Default icon
if (isset($fsModule['metadata']['icon'])) {
    $icon = $fsModule['metadata']['icon'];
}

$moduleData = [
    'name' => $fsModule['name'],
    'code' => $code,
    'slug' => $slug,
    'description' => $fsModule['description'] ?? 'Module ' . $fsModule['name'],
    'icon' => $icon, // ✅ Dibaca dari module.json
];
```

### 2. Hanya update description, tidak update name & icon

**File**: `ModuleManagementController.php` line 193-197

**Sebelum:**

```php
} else {
    // Update description if changed
    if ($existingModule->description !== $moduleData['description']) {
        $existingModule->update(['description' => $moduleData['description']]);
        $updated++;
    }
}
```

**Sesudah:**

```php
} else {
    // Update name, description, and icon if changed
    $updateData = [];

    if ($existingModule->name !== $moduleData['name']) {
        $updateData['name'] = $moduleData['name'];
    }

    if ($existingModule->description !== $moduleData['description']) {
        $updateData['description'] = $moduleData['description'];
    }

    if ($existingModule->icon !== $moduleData['icon']) {
        $updateData['icon'] = $moduleData['icon'];
    }

    if (!empty($updateData)) {
        $existingModule->update($updateData);
        $updated++;
    }
}
```

## Files Modified

1. ✅ `/app/Http/Controllers/SuperAdmin/ModuleManagementController.php`

   - Baca icon dari `$fsModule['metadata']['icon']`
   - Update logic untuk name, description, dan icon

2. ✅ `/app/Console/Commands/SyncModulesFromFilesystem.php`
   - Tambah update logic untuk field `name`

## Testing

### Test via Web Interface:

1. Buka: `http://siar.test/superadmin/modules`
2. Klik tombol **"Sync dari Filesystem"**
3. Hasil seharusnya:
   ```
   Created: 0
   Updated: 10  ← Semua modul ter-update
   Deleted: 0
   ```

### Test via Console:

```bash
php artisan modules:sync --no-interaction
```

Output seharusnya:

```
✓ Updated: Manajemen Pengguna (name, icon)
✓ Updated: Manajemen Aktivitas (name, icon)
✓ Updated: Manajemen Risiko (name, icon)
...
```

## Expected Result

Setelah sync, tabel `modules` akan ter-update:

| ID  | Nama (Updated)           | Slug                   | Icon (Updated)              |
| --- | ------------------------ | ---------------------- | --------------------------- |
| 3   | **Manajemen Dokumen**    | document-management    | **fa-folder-open**          |
| 4   | **Kendali Mutu & Biaya** | kendali-mutu-biaya     | **fa-chart-line**           |
| 5   | **Manajemen Produk**     | product-management     | **fa-boxes**                |
| 6   | **Manajemen Risiko**     | risk-management        | **fa-exclamation-triangle** |
| 7   | **Manajemen SPO**        | spo-management         | **fa-file-medical-alt**     |
| 8   | **Manajemen Aktivitas**  | activity-management    | **fa-tasks**                |
| 9   | **Manajemen Kinerja**    | performance-management | **fa-chart-bar**            |
| 10  | **Manajemen Pengguna**   | user-management        | **fa-users-cog**            |
| 11  | **Unit Kerja**           | work-unit              | **fa-sitemap**              |
| 13  | **Surat Menyurat**       | correspondence         | **fa-envelope-open-text**   |

## Verification

Setelah sync, verifikasi dengan query:

```sql
SELECT id, name, slug, icon FROM modules ORDER BY id;
```

Atau via web:

```
http://siar.test/superadmin/modules
```

Pastikan:

- ✅ Nama dalam Bahasa Indonesia
- ✅ Icon sesuai dengan module.json
- ✅ Semua modul ter-update

## Related Files

- `/modules/*/module.json` - Source of truth untuk metadata
- `/app/Http/Controllers/SuperAdmin/ModuleManagementController.php` - Web sync
- `/app/Console/Commands/SyncModulesFromFilesystem.php` - Console sync
- `/docs/MODULE-METADATA-UPDATE.md` - Dokumentasi metadata update
