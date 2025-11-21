# Quick Reference: Pembuatan Modul Baru

## 🎯 Critical Rules

### 1. Module Identifiers (WAJIB KONSISTEN!)

```
Name:  "Inventory Management"     ← Title Case (untuk UI)
Slug:  "inventory-management"     ← kebab-case (untuk URL)
Code:  "INVENTORY_MANAGEMENT"     ← UPPERCASE_SNAKE (untuk Permissions)
```

### 2. Middleware & Policy (WAJIB PAKAI CODE!)

```php
// ✅ BENAR
Route::middleware(['module:INVENTORY_MANAGEMENT'])  // Pakai CODE
protected string $moduleCode = 'INVENTORY_MANAGEMENT';  // Pakai CODE

// ❌ SALAH
Route::middleware(['module:inventory-management'])  // Jangan pakai slug!
protected string $moduleCode = 'inventory-management';  // Jangan pakai slug!
```

---

## 📝 Checklist Cepat

### Database

```sql
-- 1. Insert module
INSERT INTO modules (name, slug, code, is_active)
VALUES ('Inventory Management', 'inventory-management', 'INVENTORY_MANAGEMENT', 1);

-- 2. Activate untuk tenant
INSERT INTO tenant_modules (tenant_id, module_id, is_active)
VALUES (2, LAST_INSERT_ID(), 1);

-- 3. Add permissions
INSERT INTO role_module_permissions (role_id, module_id, can_view, can_create, can_edit, can_delete)
VALUES (2, LAST_INSERT_ID(), 1, 1, 1, 1);
```

### Code Files

#### 1. Routes (`modules/InventoryManagement/Http/routes.php`)

```php
Route::middleware(['web', 'auth', 'tenant', 'module:INVENTORY_MANAGEMENT'])
    ->prefix('inventory-management')
    ->name('modules.inventory-management.')
    ->group(function () {
        Route::resource('items', InventoryController::class);
    });
```

#### 2. Model (`modules/InventoryManagement/Models/Inventory.php`)

```php
class Inventory extends Model
{
    use BelongsToTenant;  // ⚠️ WAJIB!
    protected $fillable = ['tenant_id', 'name'];
}
```

#### 3. Policy (`app/Policies/InventoryPolicy.php`)

```php
class InventoryPolicy extends BasePolicy
{
    protected string $moduleCode = 'INVENTORY_MANAGEMENT';  // ⚠️ Pakai CODE!
}
```

#### 4. Controller

```php
public function index()
{
    Gate::authorize('viewAny', Inventory::class);  // ⚠️ WAJIB!
    // ...
}
```

#### 5. Register (`config/app.php`)

```php
Modules\InventoryManagement\Providers\InventoryManagementServiceProvider::class,
```

#### 6. Register Policy (`AuthServiceProvider.php`)

```php
Inventory::class => InventoryPolicy::class,
```

---

## 🚀 Commands

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## ⚠️ Common Mistakes

| Mistake                  | Impact                             | Fix               |
| ------------------------ | ---------------------------------- | ----------------- |
| Pakai slug di middleware | 403: "You do not have access"      | Ganti ke CODE     |
| Pakai slug di Policy     | 403: "This action is unauthorized" | Ganti ke CODE     |
| Lupa BelongsToTenant     | Data tidak ter-isolasi             | Tambah trait      |
| Lupa insert permissions  | Menu tidak muncul                  | Insert ke DB      |
| Lupa clear cache         | Perubahan tidak terlihat           | Clear semua cache |

---

## 🧪 Testing

```bash
# 1. Cek routes
php artisan route:list --name=inventory-management

# 2. Cek permissions
php artisan tinker --execute="
DB::table('role_module_permissions')
    ->where('role_id', 2)
    ->where('module_id', YOUR_MODULE_ID)
    ->first();
"

# 3. Browser test
# - Login as Tenant Admin
# - Menu muncul?
# - Click menu → tidak 403?
# - CRUD works?
```

---

## 📚 Full Documentation

Lihat: `MODULE-DEVELOPMENT-GUIDE.md`
