# Module Management System Guide

## 📚 Overview

This system has **TWO SEPARATE** module management interfaces:

1. **Tenant Module Management** - For Tenant Admins to request module activation
2. **Superadmin Module Management** - For Superadmins to manage all modules (with auto-discovery)

---

## 🔑 **1. TENANT MODULE MANAGEMENT**

### **Purpose:**

Allow Tenant Admins to view available modules and request activation.

### **Location:**

- **Routes**: `/modules` (tenant access)
- **Views**: `resources/views/modules/`
  - `index.blade.php` - List available modules & request activation
  - `show.blade.php` - View module details
- **Controller**: `App\Http\Controllers\ModuleController`

### **Access:**

- **Role Required**: Tenant Admin
- **Middleware**: `['auth', 'tenant']`
- **URL**: `http://siar.test/modules`

### **Features:**

- ✅ View active modules
- ✅ View available modules
- ✅ Request module activation (requires Superadmin approval)
- ✅ View module details
- ❌ Cannot add/edit/delete modules (Superadmin only)

### **Workflow:**

1. Tenant Admin browses available modules at `/modules`
2. Clicks "Ajukan Modul" button for desired module
3. Request is sent to Superadmin for approval
4. Status shows as "Menunggu Persetujuan"
5. Once approved by Superadmin, module becomes active
6. Tenant Admin can access the module

---

## 🔐 **2. SUPERADMIN MODULE MANAGEMENT**

### **Purpose:**

Allow Superadmins to manage all modules in the system with **auto-discovery** from filesystem.

### **Location:**

- **Routes**: `/superadmin/modules` (superadmin access)
- **Views**: `resources/views/roles/superadmin/modules/`
  - `index.blade.php` - List all modules + Auto-discovery
  - `create.blade.php` - Manually create module
  - `edit.blade.php` - Edit module
  - `show.blade.php` - View module details
- **Controller**: `App\Http\Controllers\Superadmin\ModuleManagementController`

### **Access:**

- **Role Required**: Superadmin
- **Middleware**: `['auth', 'superadmin']`
- **URL**: `http://siar.test/superadmin/modules`

### **Features:**

- ✅ **Auto-discover modules from filesystem** (`modules/` folder)
- ✅ **Sync modules** from filesystem to database
- ✅ Create module manually
- ✅ Edit module (name, description, icon)
- ✅ Delete module (if not used by any tenant)
- ✅ View module usage statistics
- ✅ Activate module for all tenants
- ✅ Deactivate module for all tenants
- ✅ Approve/reject tenant module requests

---

## 🚀 **AUTO-DISCOVERY FEATURE**

### **How It Works:**

The system automatically scans the `modules/` directory and detects all installed modules:

1. **Scan Filesystem**: Reads all folders in `modules/`
2. **Read Metadata**: Parses `module.json` or `Config/config.php`
3. **Compare Database**: Checks if module exists in database
4. **Show Status**: Displays discovered modules with status:
   - ✅ **Sudah di Database** (Already in DB)
   - ⚠️ **Belum di Database** (Not in DB yet)

### **Sync Process:**

When you click **"Sync dari Filesystem"** button:

```php
// Auto-discovers modules from filesystem
$filesystemModules = discoverModulesFromFilesystem();

// For each discovered module:
foreach ($filesystemModules as $module) {
    // If module doesn't exist in database
    if (!existsInDB($module)) {
        // Create new module record
        Module::create([
            'name' => $module['name'],
            'slug' => $module['alias'],
            'description' => $module['description'],
            'icon' => 'fa-cube',
        ]);
    } else {
        // Update description if changed
        $existingModule->update(['description' => $module['description']]);
    }
}
```

### **Module Detection:**

The system reads module information from (in order of priority):

1. **`module.json`** (preferred):

   ```json
   {
     "name": "ProductManagement",
     "alias": "product-management",
     "description": "Module for managing products",
     "version": "1.0.0"
   }
   ```

2. **`Config/config.php`** (fallback):
   ```php
   return [
       'name' => 'ProductManagement',
       'alias' => 'product-management',
       'description' => 'Module for managing products',
       'version' => '1.0.0',
   ];
   ```

---

## 📂 **FILE STRUCTURE**

```
app/Http/Controllers/
├── ModuleController.php                      # Tenant module management
└── Superadmin/
    └── ModuleManagementController.php        # Superadmin module management + Auto-discovery

resources/views/
├── modules/                                  # Tenant views
│   ├── index.blade.php                      # List modules for tenant
│   └── show.blade.php                       # Module details for tenant
└── roles/superadmin/modules/                # Superadmin views
    ├── index.blade.php                      # List + Auto-discovery
    ├── create.blade.php                     # Create module
    ├── edit.blade.php                       # Edit module
    └── show.blade.php                       # Module details

modules/                                      # Application modules (auto-discovered)
├── ActivityManagement/
│   ├── module.json                          # Module metadata (auto-discovered)
│   └── Config/config.php                    # Module config (auto-discovered)
├── RiskManagement/
├── UserManagement/
├── DocumentManagement/
├── ProductManagement/
├── WorkUnit/
├── Correspondence/
└── KendaliMutuBiaya/

routes/web.php                               # Contains both routes
```

---

## 🛣️ **ROUTES**

### **Tenant Routes** (`/modules`):

```php
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/modules', [ModuleController::class, 'index'])
        ->name('modules.index');

    Route::post('/modules/request-activation', [ModuleController::class, 'requestActivation'])
        ->name('modules.request-activation');

    Route::get('/modules/{slug}', [ModuleController::class, 'show'])
        ->name('modules.show');
});
```

### **Superadmin Routes** (`/superadmin/modules`):

```php
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // CRUD Routes
    Route::resource('modules', ModuleManagementController::class);

    // Auto-discovery & Sync
    Route::post('modules/sync-filesystem', [ModuleManagementController::class, 'syncFromFilesystem'])
        ->name('modules.sync');

    // Bulk Actions
    Route::post('modules/{module}/activate-for-all', [ModuleManagementController::class, 'activateForAll'])
        ->name('modules.activate-for-all');
    Route::post('modules/{module}/deactivate-for-all', [ModuleManagementController::class, 'deactivateForAll'])
        ->name('modules.deactivate-for-all');

    // Request Management
    Route::post('modules/approve-request', [ModuleManagementController::class, 'approveRequest'])
        ->name('modules.approve-request');
    Route::post('modules/reject-request', [ModuleManagementController::class, 'rejectRequest'])
        ->name('modules.reject-request');
});
```

---

## 💡 **USAGE EXAMPLES**

### **Scenario 1: New Module Created**

When you create a new module (e.g., `Pharmacy`):

1. Create folder: `modules/Pharmacy/`
2. Create `module.json`:
   ```json
   {
     "name": "Pharmacy",
     "alias": "pharmacy",
     "description": "Pharmacy management module",
     "version": "1.0.0"
   }
   ```
3. Go to: `http://siar.test/superadmin/modules`
4. You'll see alert: **"Modul Terdeteksi dari Filesystem: Pharmacy (Belum di Database)"**
5. Click **"Sync dari Filesystem"** button
6. Module is now in database and available for tenant activation!

### **Scenario 2: Tenant Requests Module**

1. **Tenant Admin** logs in
2. Goes to `/modules`
3. Sees "Pharmacy" in "Modul Tersedia"
4. Clicks **"Ajukan Modul"**
5. Status changes to **"Menunggu Persetujuan"**
6. **Superadmin** goes to `/superadmin/modules`
7. Sees pending request notification
8. Clicks **"Approve"**
9. **Tenant Admin** can now access Pharmacy module

### **Scenario 3: Activate Module for All Tenants**

1. **Superadmin** goes to `/superadmin/modules`
2. Finds "Pharmacy" module
3. Clicks **"Aktifkan untuk Semua Tenant"**
4. All tenants now have access to Pharmacy module

---

## 🔄 **MIGRATION FROM SEEDER TO AUTO-DISCOVERY**

### **Before (Manual Seeder):**

```php
// ModuleSeeder.php
Module::create(['name' => 'ActivityManagement', ...]);
Module::create(['name' => 'RiskManagement', ...]);
// ... etc, need to manually add each module
```

### **After (Auto-Discovery):**

```php
// Just click "Sync dari Filesystem" button!
// All modules in modules/ folder are automatically detected
```

### **Benefits:**

- ✅ No need to run seeder when adding new modules
- ✅ No need to manually update database
- ✅ Automatically detects all installed modules
- ✅ Shows which modules are in filesystem but not in database
- ✅ One-click sync to database

---

## 🎯 **BEST PRACTICES**

### **For Developers:**

1. Always create `module.json` for your modules
2. Include proper metadata (name, alias, description, version)
3. Use kebab-case for alias (e.g., `product-management`)
4. Don't manually insert modules to database - use sync feature

### **For Superadmins:**

1. Regularly check `/superadmin/modules` for discovered modules
2. Click "Sync" after deploying new modules
3. Review tenant requests regularly
4. Use bulk activation for system-wide modules

### **For Tenant Admins:**

1. Request only needed modules
2. Wait for Superadmin approval
3. Don't request duplicate modules

---

## 🔧 **TROUBLESHOOTING**

### **Q: Module not showing in auto-discovery?**

**A:** Check:

- Module folder exists in `modules/`
- `module.json` or `Config/config.php` exists
- JSON is valid (use JSON validator)
- Folder name matches module name

### **Q: Sync button shows 0 modules?**

**A:** This means all filesystem modules are already in database. Good!

### **Q: Module exists in DB but not in filesystem?**

**A:** This is OK. Module was manually created or old module was deleted from filesystem.

### **Q: Can't delete module?**

**A:** Module is being used by tenants. Deactivate from all tenants first.

---

## 📊 **SUMMARY**

| Aspect              | Tenant Management | Superadmin Management |
| ------------------- | ----------------- | --------------------- |
| **URL**             | `/modules`        | `/superadmin/modules` |
| **Access**          | Tenant Admin      | Superadmin            |
| **Can View**        | Own modules       | All modules           |
| **Can Request**     | ✅ Yes            | N/A                   |
| **Can Approve**     | ❌ No             | ✅ Yes                |
| **Can CRUD**        | ❌ No             | ✅ Yes                |
| **Auto-Discovery**  | ❌ No             | ✅ Yes                |
| **Sync Filesystem** | ❌ No             | ✅ Yes                |

---

## 🎉 **CONCLUSION**

- ✅ **Two separate systems**, each with its own purpose
- ✅ **Tenant files stay at** `resources/views/modules/`
- ✅ **Superadmin files at** `resources/views/roles/superadmin/modules/`
- ✅ **Auto-discovery eliminates manual seeding**
- ✅ **One-click sync** for new modules
- ✅ **Clean separation** of concerns

**Result**: No more manual seeder! Just create module, click sync, done! 🚀
