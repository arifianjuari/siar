# Module Sync Filesystem Issue - Troubleshooting

**Date:** November 20, 2025  
**Issue:** Module sync dari filesystem tidak berfungsi di Laravel Cloud  
**URL:** https://siar-beta-ctegvo.laravel.cloud/superadmin/modules  
**Status:** 🔧 Enhanced with logging & fallbacks

## Problem

Fungsi "Sync dari Filesystem" tidak berfungsi di server Laravel Cloud, padahal di local bisa.

**Expected Behavior:**

- Scan direktori `modules/`
- Detect module.json files
- Create/update modules di database
- Show success message dengan jumlah created/updated/deleted

**Actual Behavior:**

- Nothing happens atau
- No modules detected atau
- Silent failure

## Possible Causes

### 1. **Directory Permissions**

Direktori `modules/` mungkin tidak readable di server Laravel Cloud.

### 2. **glob() Function Disabled**

Beberapa hosting environment disable `glob()` function untuk security.

### 3. **Path Issues**

`base_path('modules')` might resolve differently in production.

### 4. **Modules Directory Missing**

Directory mungkin tidak ter-deploy ke production.

## Solution Implemented

### 1. Enhanced Logging

Tambahan comprehensive logging di `discoverModulesFromFilesystem()`:

```php
\Illuminate\Support\Facades\Log::info('Discovering modules from filesystem', [
    'path' => $modulesPath,
    'exists' => is_dir($modulesPath),
    'readable' => is_readable($modulesPath),
]);
```

**Logs yang ditambahkan:**

- Module path location
- Directory existence check
- Readable permission check
- Directory scan results
- Found modules count

### 2. Fallback to scandir()

Jika `glob()` fails, gunakan `scandir()` sebagai fallback:

```php
$directories = @glob($modulesPath . '/*', GLOB_ONLYDIR);
if ($directories === false) {
    // glob failed, try scandir
    $directories = [];
    $items = scandir($modulesPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $modulesPath . '/' . $item;
        if (is_dir($fullPath)) {
            $directories[] = $fullPath;
        }
    }
}
```

### 3. Better Error Messages

User sekarang mendapat feedback yang lebih jelas:

```php
if (empty($filesystemModules)) {
    return redirect()->route('superadmin.modules.index')
        ->with('warning', 'Tidak ada modul yang ditemukan di direktori modules/.
                           Pastikan direktori modules/ exists dan readable.');
}
```

### 4. Permission Checks

Explicit check untuk readable permission:

```php
if (!is_readable($modulesPath)) {
    \Illuminate\Support\Facades\Log::error('Modules directory is not readable',
        ['path' => $modulesPath]);
    return $discovered;
}
```

## Debugging Steps

Setelah redeploy, lakukan langkah-langkah berikut:

### 1. Check Laravel Logs

Di Laravel Cloud Dashboard → Logs → Application Logs:

```bash
# Look for these log entries:
"Discovering modules from filesystem"
"Found directories"
"Starting module sync from filesystem"
```

**What to check:**

- Is path correct? (should be `/var/www/html/modules`)
- Does directory exist? (`exists: true`)
- Is directory readable? (`readable: true`)
- How many directories found? (`count: X`)

### 2. Check Directory via SSH/Console

Jika punya akses SSH atau console:

```bash
# Check if modules directory exists
ls -la /var/www/html/modules

# Check permissions
ls -ld /var/www/html/modules

# Check contents
find /var/www/html/modules -maxdepth 2 -name "module.json"

# Check if glob is available
php -r "var_dump(glob('/var/www/html/modules/*'));"
```

### 3. Try Sync Function

1. Buka https://siar-beta-ctegvo.laravel.cloud/superadmin/modules
2. Klik tombol **"Sync dari Filesystem"**
3. Perhatikan pesan yang muncul:
   - **Success**: "Sinkronisasi selesai. Dibuat: X, Diperbarui: Y"
   - **Warning**: "Tidak ada modul yang ditemukan..."
   - **Error**: "Sinkronisasi gagal: [error message]"

### 4. Check Logs After Sync Attempt

Segera setelah klik sync, check application logs untuk:

- Path information
- Permission status
- Modules found
- Any errors

## Common Issues & Fixes

### Issue 1: "No modules found"

**Possible causes:**

- Directory doesn't exist
- Directory not readable
- glob() disabled

**Fix:**

- Check logs untuk path dan permissions
- Verify modules directory deployed
- Fallback to scandir should work automatically

### Issue 2: "Permission denied"

**Symptoms:**

```
Modules directory is not readable
```

**Fix:**
Ensure proper permissions in build script:

```bash
chmod -R 755 modules
```

### Issue 3: glob() returns false

**Symptoms:**
Log shows `glob()` failed but fallback worked.

**Action:**
No action needed - scandir fallback handles this automatically.

### Issue 4: Empty modules directory

**Symptoms:**

```
Found directories: count: 0
```

**Fix:**
Modules directory exists but is empty. Ensure modules/ directory and subdirectories are in git:

```bash
# Check if modules are ignored
git check-ignore modules/*

# If ignored, update .gitignore
```

## Verification

### Successful Sync Indicators:

**In UI:**

- ✅ Green success message
- ✅ Shows created/updated counts
- ✅ Modules appear in table

**In Logs:**

```
[INFO] Discovering modules from filesystem
[INFO] Found directories: count: 11
[INFO] Starting module sync from filesystem
```

**In Database:**

```sql
SELECT COUNT(*) FROM modules;
-- Should show expected number of modules
```

## Prevention

### 1. Ensure Modules Are Deployed

Add to `.gitignore` check:

```bash
# Make sure modules/* is NOT in .gitignore
!modules/
```

### 2. Set Proper Permissions in Build Script

In `.laravel-cloud-build.sh`:

```bash
# Set permissions for modules directory
chmod -R 755 modules
```

### 3. Validate Module Structure

Ensure each module has:

- ✅ `module.json` OR `Config/config.php`
- ✅ Proper JSON/PHP syntax
- ✅ Required fields (name, alias, description)

### 4. Test Locally First

Before deploying, test sync locally:

```bash
php artisan tinker
>>> app(App\Http\Controllers\SuperAdmin\ModuleManagementController::class)->syncFromFilesystem();
```

## Module Structure Reference

Expected module structure:

```
modules/
├── ActivityManagement/
│   ├── module.json          ← Required!
│   ├── Config/
│   ├── Http/
│   ├── Models/
│   └── Resources/
├── Correspondence/
│   ├── module.json          ← Required!
│   └── ...
└── ...
```

### module.json Format:

```json
{
  "name": "Activity Management",
  "alias": "activity-management",
  "description": "Manage activities and tasks",
  "version": "1.0.0"
}
```

## Next Steps

1. **Redeploy application** (code sudah di-push)
2. **Try sync** di https://siar-beta-ctegvo.laravel.cloud/superadmin/modules
3. **Check logs** untuk debugging information
4. **Share logs** jika masih tidak bekerja:
   - Application logs entries yang relevant
   - Error messages yang muncul
   - Directory scan results

## Files Modified

- `app/Http/Controllers/SuperAdmin/ModuleManagementController.php`
  - Enhanced `discoverModulesFromFilesystem()` with logging
  - Added scandir() fallback
  - Added permission checks
  - Better error messages

**Commit:** 89ea749 - "Add comprehensive logging and fallback for module sync"

---

**Status**: Code deployed, waiting untuk testing di production untuk identify exact issue.
