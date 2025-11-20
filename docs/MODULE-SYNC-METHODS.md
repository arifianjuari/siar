# Module Sync - Multiple Methods

**Date:** November 20, 2025  
**Issue Fixed:** 419 CSRF error saat sync via web UI  
**Solution:** Multiple sync methods untuk flexibility

## Problem: 419 Page Expired

Error **419 Page Expired** terjadi karena:

- POST route membutuhkan CSRF token
- Token expired atau tidak valid
- Session timeout

## Solutions Implemented

Sekarang ada **3 cara** untuk sync modules:

### **Method 1: Artisan Command** ✅ (Recommended)

Command line interface untuk sync modules.

#### **Basic Usage:**

```bash
php artisan modules:sync
```

#### **Options:**

```bash
# Dry run - preview tanpa membuat perubahan
php artisan modules:sync --dry-run

# Force delete modules even if used by tenants
php artisan modules:sync --force

# Non-interactive (untuk automated scripts)
php artisan modules:sync --no-interaction

# Kombinasi
php artisan modules:sync --dry-run --force
```

#### **Output Example:**

```
Starting module synchronization...

Found 11 modules in filesystem:
+----------------------------+---------------------------+--------------+-------+
| Name                       | Slug                      | Has module.j | In DB |
+----------------------------+---------------------------+--------------+-------+
| Activity Management        | activity-management       | ✓            | ✓     |
| Correspondence             | correspondence            | ✓            | ✓     |
| Document Management        | document-management       | ✓            | ✓     |
| ...                        | ...                       | ...          | ...   |
+----------------------------+---------------------------+--------------+-------+

Do you want to proceed with synchronization? (yes/no) [yes]: yes

✓ Created: Activity Management
✓ Updated: Correspondence
- Unchanged: Document Management
...

Synchronization completed successfully!
Created: 3
Updated: 2
Deleted: 0
```

#### **Via Laravel Cloud Console:**

Di Laravel Cloud Dashboard → Console:

```bash
php artisan modules:sync --no-interaction
```

---

### **Method 2: Database Seeder** ✅

Seeder approach untuk initial setup atau re-sync.

#### **Usage:**

```bash
# Run specific seeder
php artisan db:seed --class=ModuleSyncSeeder

# Run with main seeder
php artisan db:seed
```

#### **Pros:**

- ✅ Bagus untuk initial setup
- ✅ Bisa digabung dengan seeder lain
- ✅ Idempotent (aman dijalankan berulang kali)

#### **Cons:**

- ❌ Tidak delete orphaned modules
- ❌ Less verbose output

---

### **Method 3: Web UI (POST Route)** ✅

Original method via web interface.

#### **Usage:**

1. Login sebagai SuperAdmin
2. Buka https://siar-beta-ctegvo.laravel.cloud/superadmin/modules
3. Klik tombol **"Sync dari Filesystem"**
4. Pastikan session tidak expired

#### **Pros:**

- ✅ User-friendly
- ✅ Visual feedback

#### **Cons:**

- ❌ Prone to CSRF/session issues
- ❌ Membutuhkan manual click

#### **Fix for 419 Error:**

Jika tetap error 419:

- Refresh halaman sebelum klik sync
- Clear browser cache/cookies
- Atau gunakan Method 1/2 sebagai gantinya

---

## Automated Sync During Deployment

Build script sudah di-update untuk **otomatis sync modules** setiap deploy:

```bash
# In .laravel-cloud-build.sh
echo "🔄 Syncing modules from filesystem..."
php artisan modules:sync --no-interaction --force || true
```

**What this does:**

- ✅ Runs automatically on every deployment
- ✅ No manual intervention needed
- ✅ Uses --force to ensure orphaned modules are deleted
- ✅ Uses --no-interaction for automated execution
- ✅ || true prevents build failure if sync fails

**Result:**
Setiap kali deploy, modules akan otomatis ter-sync dari filesystem ke database.

---

## Comparison Table

| Feature            | Artisan Command | Seeder      | Web UI        |
| ------------------ | --------------- | ----------- | ------------- |
| **Interactive**    | Yes (optional)  | No          | Yes           |
| **Dry Run**        | ✅ Yes          | ❌ No       | ❌ No         |
| **Force Delete**   | ✅ Yes          | ❌ No       | ✅ Yes        |
| **Verbose Output** | ✅✅✅ Detailed | ✅ Basic    | ✅✅ Medium   |
| **Table Display**  | ✅ Yes          | ❌ No       | ✅ Yes        |
| **Delete Orphans** | ✅ Yes          | ❌ No       | ✅ Yes        |
| **CSRF Issues**    | ✅ No           | ✅ No       | ❌ Yes        |
| **Automation**     | ✅✅✅ Perfect  | ✅✅ Good   | ❌ Manual     |
| **Console Access** | ✅ Required     | ✅ Required | ❌ Not needed |

---

## When to Use Which Method?

### **Use Artisan Command when:**

- ✅ You have console/SSH access
- ✅ You want detailed output
- ✅ You want to preview changes (dry-run)
- ✅ You want automation/scripting
- ✅ **Recommended for production deployments**

### **Use Seeder when:**

- ✅ Initial database setup
- ✅ Part of migration process
- ✅ You're running multiple seeders
- ✅ Simple update without deletions

### **Use Web UI when:**

- ✅ Quick manual sync
- ✅ No console access
- ✅ Visual confirmation needed
- ✅ One-time sync
- ⚠️ Watch out for CSRF/session issues

---

## Troubleshooting

### 1. "No modules found in filesystem"

**Causes:**

- modules/ directory doesn't exist
- modules/ directory is empty
- No module.json or Config/config.php files

**Fix:**

```bash
# Check if modules exist
ls -la modules/

# Check for module.json files
find modules/ -name "module.json"

# Ensure modules are in git
git ls-files modules/
```

### 2. Command not found

**Cause:** Command not registered.

**Fix:**

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Re-run composer autoload
composer dump-autoload
```

### 3. Permission denied

**Cause:** modules/ not readable.

**Fix:**

```bash
chmod -R 755 modules/
```

### 4. Seeder class not found

**Cause:** Autoload issue.

**Fix:**

```bash
composer dump-autoload
php artisan db:seed --class=ModuleSyncSeeder
```

---

## Module Structure Required

Untuk agar module terdeteksi, harus punya salah satu dari:

### **Option 1: module.json** (Recommended)

```json
{
  "name": "Activity Management",
  "alias": "activity-management",
  "description": "Manage activities and tasks",
  "version": "1.0.0",
  "icon": "fa-tasks"
}
```

### **Option 2: Config/config.php**

```php
<?php
return [
    'name' => 'Activity Management',
    'alias' => 'activity-management',
    'description' => 'Manage activities and tasks',
    'version' => '1.0.0',
    'icon' => 'fa-tasks',
];
```

### **Directory Structure:**

```
modules/
├── ActivityManagement/
│   ├── module.json          ← Required!
│   ├── Config/
│   │   └── config.php       ← Alternative
│   ├── Http/
│   ├── Models/
│   └── Resources/
└── ...
```

---

## Best Practices

### 1. **Always Use Dry Run First**

```bash
php artisan modules:sync --dry-run
```

Preview changes sebelum apply.

### 2. **Run After Adding New Modules**

```bash
# After creating new module
php artisan modules:sync
```

### 3. **Include in Deployment**

Sudah otomatis di build script! ✅

### 4. **Monitor Logs**

```bash
# Check if sync ran successfully
tail -f storage/logs/laravel.log
```

### 5. **Test Locally First**

```bash
# Local development
php artisan modules:sync --dry-run
php artisan modules:sync
```

---

## Summary

**Problem:** 419 CSRF error saat sync via web  
**Root Cause:** POST route + expired session  
**Solutions:**

1. ✅ **Artisan Command** - Best untuk automation
2. ✅ **Seeder** - Good untuk initial setup
3. ✅ **Web UI** - Quick manual sync (watch for CSRF)

**Auto-sync on Deploy:** ✅ Enabled

**Recommended Workflow:**

```bash
# Development
php artisan modules:sync --dry-run
php artisan modules:sync

# Production (automated)
# Build script handles it automatically!
```

**Files Modified:**

- `app/Console/Commands/SyncModulesFromFilesystem.php` - New command
- `database/seeders/ModuleSyncSeeder.php` - New seeder
- `.laravel-cloud-build.sh` - Auto-sync on deploy

**Commit:** 3c0bfc9 - "Add module sync command and seeder, fix 419 CSRF error"
