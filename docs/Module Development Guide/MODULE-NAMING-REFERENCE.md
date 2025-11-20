# Module Naming Reference

Referensi lengkap untuk naming convention semua modul di SIAR.

---

## 📊 Existing Modules

| Module         | Name                   | Slug                        | Code                  | Folder                  |
| -------------- | ---------------------- | --------------------------- | --------------------- | ----------------------- |
| Correspondence | Correspondence         | `correspondence-management` | `CORRESPONDENCE`      | `Correspondence`        |
| Document       | Document Management    | `document-management`       | `DOCMANAGEMENT`       | `DocumentManagement`    |
| Product        | Manajemen Produk       | `product-management`        | `PRODUCT_MANAGEMENT`  | `ProductManagement`     |
| Risk           | Manajemen Risiko       | `risk-management`           | `risk-management`     | `RiskManagement`        |
| Activity       | Pengelolaan Kegiatan   | `activity-management`       | `activity-management` | `ActivityManagement`    |
| User           | User Management        | `user-management`           | `user_management`     | `UserManagement`        |
| Performance    | Performance Management | `performance-management`    | `PERF`                | `PerformanceManagement` |
| Kendali        | Kendali Mutu Biaya     | `kendali-mutu-biaya`        | `KMKB`                | `KendaliMutuBiaya`      |
| SPO            | Manajemen SPO          | `spo-management`            | `spo-management`      | `SPOManagement`         |
| WorkUnit       | WorkUnit               | `work-unit`                 | `WORK_UNIT`           | `WorkUnit`              |

---

## ✅ Standardized Format (Recommended)

Untuk modul baru, gunakan format ini:

| Field      | Format                          | Example                | Language      |
| ---------- | ------------------------------- | ---------------------- | ------------- |
| **Name**   | Title Case                      | `Manajemen Inventori`  | **Indonesia** |
| **Slug**   | kebab-case + `-management`      | `inventory-management` | English       |
| **Code**   | UPPERCASE_SNAKE + `_MANAGEMENT` | `INVENTORY_MANAGEMENT` | English       |
| **Folder** | PascalCase + `Management`       | `InventoryManagement`  | English       |

> **⚠️ PENTING:** Name WAJIB menggunakan **Bahasa Indonesia** untuk konsistensi UI dan menghindari duplicate menu!

---

## 🎯 Usage Guide

### Where to Use Each Identifier

| Identifier | Used In                                   | Example                  |
| ---------- | ----------------------------------------- | ------------------------ |
| **Name**   | Database `modules.name`, UI               | `"Inventory Management"` |
| **Slug**   | URL, Routes prefix, Views namespace       | `inventory-management`   |
| **Code**   | Middleware parameter, Policy, Permissions | `INVENTORY_MANAGEMENT`   |
| **Folder** | Directory name, Namespace                 | `InventoryManagement`    |

### Code Examples

```php
// ✅ CORRECT USAGE

// 1. Database (Seeder)
Module::create([
    'name' => 'Manajemen Inventori',           // ← Bahasa Indonesia!
    'slug' => 'inventory-management',          // ← kebab-case
    'code' => 'INVENTORY_MANAGEMENT',          // ← UPPERCASE_SNAKE
]);

// 2. Routes
Route::middleware(['module.permission:inventory-management'])  // ← Use slug!
    ->prefix('inventory-management')                           // ← Use slug
    ->name('modules.inventory-management.')                    // ← Use slug
    ->group(function () { });

// 3. Policy
class InventoryPolicy extends BasePolicy
{
    protected string $moduleCode = 'inventory-management';  // ← Use slug!
}

// 4. Controller
namespace Modules\InventoryManagement\Http\Controllers;  // ← Use Folder (PascalCase)

public function index()
{
    Gate::authorize('viewAny', Inventory::class);

    // ✅ BENAR - tanpa tenantScope()
    $items = Inventory::query()->paginate(10);
}

// 5. Views
return view('inventory-management::items.index');  // ← Use slug
```

---

## ❌ Common Mistakes

### Mistake #1: Nama Modul Bahasa Inggris

```php
// ❌ WRONG
'name' => 'Inventory Management',  // Bahasa Inggris

// ✅ CORRECT
'name' => 'Manajemen Inventori',   // Bahasa Indonesia
```

**Dampak:** Menu muncul duplikat di sidebar!

### Mistake #2: Middleware Salah

```php
// ❌ WRONG
Route::middleware(['module:inventory-management'])      // Old middleware
Route::middleware(['module:INVENTORY_MANAGEMENT'])      // Wrong parameter

// ✅ CORRECT
Route::middleware(['module.permission:inventory-management'])  // Use slug!
```

**Dampak:** Error 403 "You do not have access"

### Mistake #3: Policy moduleCode Salah

```php
// ❌ WRONG
protected string $moduleCode = 'INVENTORY_MANAGEMENT';  // CODE format

// ✅ CORRECT
protected string $moduleCode = 'inventory-management';  // slug format
```

**Dampak:** Authorization selalu gagal

### Mistake #4: Menggunakan tenantScope() Manual

```php
// ❌ WRONG
$items = Inventory::tenantScope()->get();

// ✅ CORRECT
$items = Inventory::query()->get();  // Global scope otomatis
```

**Dampak:** Error "Call to undefined method tenantScope()"

### Mistake #5: Policy Method Type Hint Salah

```php
// ❌ WRONG
public function view(User $user, Inventory $item): bool

// ✅ CORRECT
public function view(User $user, Model $item): bool
```

**Dampak:** Error "Method is not compatible with BasePolicy"

### Mistake #6: Inconsistent Naming

```php
// ❌ WRONG
Name: "Inventory Management"      // Bahasa Inggris
Slug: "inventory"                 // Missing -management
Code: "INV_MGMT"                  // Abbreviated
Folder: "Inventory"               // Missing Management

// ✅ CORRECT
Name: "Manajemen Inventori"       // Bahasa Indonesia
Slug: "inventory-management"
Code: "INVENTORY_MANAGEMENT"
Folder: "InventoryManagement"
```

---

## 🔍 Quick Lookup

### By Module Type

#### Management Modules (Standard)

```
Pattern: {Name} Management
Example: Inventory Management

Name:   "{Name} Management"
Slug:   "{name}-management"
Code:   "{NAME}_MANAGEMENT"
Folder: "{Name}Management"
```

#### Specialized Modules

```
Pattern: Custom naming
Example: WorkUnit, Correspondence

Name:   Custom (e.g., "WorkUnit")
Slug:   Custom (e.g., "work-unit")
Code:   Custom (e.g., "WORK_UNIT")
Folder: Custom (e.g., "WorkUnit")
```

---

## 🛠️ Validation Checklist

Before creating a new module, verify:

- [ ] Name is Title Case
- [ ] Slug is kebab-case
- [ ] Slug ends with `-management` (for consistency)
- [ ] Code is UPPERCASE_SNAKE_CASE
- [ ] Code ends with `_MANAGEMENT` (for consistency)
- [ ] Folder is PascalCase
- [ ] All identifiers are consistent
- [ ] No duplicate slug/code in database

---

## 📝 Template for New Module

```
Name:   "Manajemen __________"        (Bahasa Indonesia!)
Slug:   "__________-management"       (kebab-case)
Code:   "__________-MANAGEMENT"       (UPPERCASE_SNAKE)
Folder: "__________Management"        (PascalCase)
```

Fill in the blanks with your module name, then use in:

- Database INSERT / Seeder
- Routes file (middleware: `module.permission:slug`)
- Policy file (moduleCode: `slug`)
- ServiceProvider
- Folder structure

---

## �️ Pre-Launch Checklist

Sebelum deploy modul baru:

- [ ] ✅ Name menggunakan **Bahasa Indonesia**
- [ ] ✅ Middleware: `module.permission:slug`
- [ ] ✅ Policy moduleCode: `slug` (kebab-case)
- [ ] ✅ Policy methods type hint: `Model`
- [ ] ✅ Model pakai `BelongsToTenant` trait
- [ ] ✅ Controller TIDAK pakai `tenantScope()`
- [ ] ✅ Controller pakai `Gate::authorize()`
- [ ] ✅ ServiceProvider registered
- [ ] ✅ Policy registered
- [ ] ✅ Cache cleared
- [ ] ✅ Test CRUD operations
- [ ] ✅ Test tenant isolation
- [ ] ✅ Verify menu tidak duplikat

---

## �🔗 Related Documentation

- `MODULE-DEVELOPMENT-GUIDE.md` - Complete development guide
- `MODULE-CHECKLIST.md` - Detailed checklist untuk modul baru
- `COMMON-MISTAKES.md` - Kesalahan umum dan cara fix
- `MODULE-QUICK-REFERENCE.md` - Quick reference card
- `MODULE-TEMPLATE.md` - Copy-paste templates
- `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md` - Lessons learned

---

**Last Updated:** 20 November 2025  
**Based on:** Product Management module fixes
