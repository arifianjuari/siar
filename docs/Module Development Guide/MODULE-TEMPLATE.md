# Template: Pembuatan Modul Baru

Copy template ini dan replace `{MODULE_NAME}`, `{MODULE_SLUG}`, `{MODULE_CODE}` dengan nilai yang sesuai.

---

## 1. Define Identifiers

```php
// Replace dengan nilai modul Anda
$MODULE_NAME = "Inventory Management";     // Title Case
$MODULE_SLUG = "inventory-management";     // kebab-case
$MODULE_CODE = "INVENTORY_MANAGEMENT";     // UPPERCASE_SNAKE
$MODULE_FOLDER = "InventoryManagement";    // PascalCase
```

---

## 2. Database Setup

```sql
-- Step 1: Insert module
INSERT INTO modules (name, slug, code, description, icon, is_active, created_at, updated_at)
VALUES (
    '{MODULE_NAME}',           -- e.g., 'Inventory Management'
    '{MODULE_SLUG}',           -- e.g., 'inventory-management'
    '{MODULE_CODE}',           -- e.g., 'INVENTORY_MANAGEMENT'
    'Deskripsi modul',
    'package',                 -- Icon name
    1,
    NOW(),
    NOW()
);

-- Step 2: Get module ID
SET @module_id = LAST_INSERT_ID();

-- Step 3: Activate for tenant (adjust tenant_id as needed)
INSERT INTO tenant_modules (tenant_id, module_id, is_active, created_at, updated_at)
VALUES (2, @module_id, 1, NOW(), NOW());

-- Step 4: Add permissions for Tenant Admin (adjust role_id as needed)
INSERT INTO role_module_permissions (
    role_id, module_id,
    can_view, can_create, can_edit, can_delete, can_export, can_import,
    created_at, updated_at
)
VALUES (
    2, @module_id,
    1, 1, 1, 1, 1, 1,
    NOW(), NOW()
);

-- Verify
SELECT * FROM modules WHERE slug = '{MODULE_SLUG}';
SELECT * FROM tenant_modules WHERE module_id = @module_id;
SELECT * FROM role_module_permissions WHERE module_id = @module_id;
```

---

## 3. Create Folder Structure

```bash
# Create folders
mkdir -p modules/{MODULE_FOLDER}/{Config,Database/{Migrations,Seeders},Http/Controllers,Models,Providers,Resources/Views/{module-view}}

# Create files
touch modules/{MODULE_FOLDER}/Config/config.php
touch modules/{MODULE_FOLDER}/Http/routes.php
touch modules/{MODULE_FOLDER}/Http/Controllers/{MODULE_NAME}Controller.php
touch modules/{MODULE_FOLDER}/Models/{MODEL_NAME}.php
touch modules/{MODULE_FOLDER}/Providers/{MODULE_FOLDER}ServiceProvider.php
```

---

## 4. ServiceProvider Template

**File:** `modules/{MODULE_FOLDER}/Providers/{MODULE_FOLDER}ServiceProvider.php`

```php
<?php

namespace Modules\{MODULE_FOLDER}\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class {MODULE_FOLDER}ServiceProvider extends ServiceProvider
{
    protected $moduleName = '{MODULE_FOLDER}';
    protected $moduleNameLower = '{MODULE_SLUG}';

    public function register(): void
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
    }

    protected function registerRoutes(): void
    {
        if (!$this->app->routesAreCached()) {
            Route::middleware('web')
                ->group(module_path($this->moduleName, 'Http/routes.php'));
        }
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/Views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach ($this->app['config']->get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
```

---

## 5. Routes Template

**File:** `modules/{MODULE_FOLDER}/Http/routes.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\{MODULE_FOLDER}\Http\Controllers\{MODULE_NAME}Controller;

/*
|--------------------------------------------------------------------------
| {MODULE_NAME} Module Routes
|--------------------------------------------------------------------------
|
| ⚠️ IMPORTANT: Use MODULE CODE (not slug) for middleware!
|
*/

Route::middleware(['web', 'auth', 'tenant', 'module:{MODULE_CODE}'])
    ->prefix('{MODULE_SLUG}')
    ->name('modules.{MODULE_SLUG}.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [{MODULE_NAME}Controller::class, 'dashboard'])
            ->name('dashboard');

        // Resource routes
        Route::resource('items', {MODULE_NAME}Controller::class);

        // Additional routes
        Route::get('/export', [{MODULE_NAME}Controller::class, 'export'])
            ->name('export');
    });
```

---

## 6. Model Template

**File:** `modules/{MODULE_FOLDER}/Models/{MODEL_NAME}.php`

```php
<?php

namespace Modules\{MODULE_FOLDER}\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class {MODEL_NAME} extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = '{table_name}';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
```

---

## 7. Policy Template

**File:** `app/Policies/{MODEL_NAME}Policy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class {MODEL_NAME}Policy extends BasePolicy
{
    /**
     * ⚠️ IMPORTANT: Use MODULE CODE (not slug)!
     */
    protected string $moduleCode = '{MODULE_CODE}';

    public function viewAny(User $user): bool
    {
        return parent::viewAny($user);
    }

    public function view(User $user, Model $model): bool
    {
        return parent::view($user, $model);
    }

    public function create(User $user): bool
    {
        return parent::create($user);
    }

    public function update(User $user, Model $model): bool
    {
        return parent::update($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return parent::delete($user, $model);
    }

    public function export(User $user): bool
    {
        return parent::export($user);
    }

    public function import(User $user): bool
    {
        return parent::import($user);
    }
}
```

---

## 8. Controller Template

**File:** `modules/{MODULE_FOLDER}/Http/Controllers/{MODULE_NAME}Controller.php`

```php
<?php

namespace Modules\{MODULE_FOLDER}\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\{MODULE_FOLDER}\Models\{MODEL_NAME};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class {MODULE_NAME}Controller extends Controller
{
    public function dashboard()
    {
        Gate::authorize('viewAny', {MODEL_NAME}::class);

        $stats = [
            'total' => {MODEL_NAME}::count(),
            'active' => {MODEL_NAME}::where('is_active', true)->count(),
        ];

        return view('{MODULE_SLUG}::dashboard', compact('stats'));
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', {MODEL_NAME}::class);

        $items = {MODEL_NAME}::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('{MODULE_SLUG}::items.index', compact('items'));
    }

    public function create()
    {
        Gate::authorize('create', {MODEL_NAME}::class);

        return view('{MODULE_SLUG}::items.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', {MODEL_NAME}::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:{table_name},code',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        {MODEL_NAME}::create($validated);

        return redirect()
            ->route('modules.{MODULE_SLUG}.items.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show({MODEL_NAME} $item)
    {
        Gate::authorize('view', $item);

        return view('{MODULE_SLUG}::items.show', compact('item'));
    }

    public function edit({MODEL_NAME} $item)
    {
        Gate::authorize('update', $item);

        return view('{MODULE_SLUG}::items.edit', compact('item'));
    }

    public function update(Request $request, {MODEL_NAME} $item)
    {
        Gate::authorize('update', $item);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:{table_name},code,' . $item->id,
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()
            ->route('modules.{MODULE_SLUG}.items.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy({MODEL_NAME} $item)
    {
        Gate::authorize('delete', $item);

        $item->delete();

        return redirect()
            ->route('modules.{MODULE_SLUG}.items.index')
            ->with('success', 'Data berhasil dihapus');
    }

    public function export()
    {
        Gate::authorize('export', {MODEL_NAME}::class);

        // Export logic here
    }
}
```

---

## 9. Register ServiceProvider

**File:** `config/app.php`

```php
'providers' => [
    // ... existing providers

    // Add new module ServiceProvider
    Modules\{MODULE_FOLDER}\Providers\{MODULE_FOLDER}ServiceProvider::class,
],
```

---

## 10. Register Policy

**File:** `app/Providers/AuthServiceProvider.php`

```php
use Modules\{MODULE_FOLDER}\Models\{MODEL_NAME};
use App\Policies\{MODEL_NAME}Policy;

protected $policies = [
    // ... existing policies

    // Add new module Policy
    {MODEL_NAME}::class => {MODEL_NAME}Policy::class,
];
```

---

## 11. Run Commands

```bash
# Dump autoload
composer dump-autoload

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verify routes registered
php artisan route:list --name={MODULE_SLUG}
```

---

## 12. Testing Checklist

### Database Check

```sql
SELECT * FROM modules WHERE slug = '{MODULE_SLUG}';
SELECT * FROM tenant_modules WHERE module_id = (SELECT id FROM modules WHERE slug = '{MODULE_SLUG}');
SELECT * FROM role_module_permissions WHERE module_id = (SELECT id FROM modules WHERE slug = '{MODULE_SLUG}');
```

### Browser Check

- [ ] Login as Tenant Admin
- [ ] Menu "{MODULE_NAME}" appears in sidebar
- [ ] Click menu → navigates to `/{MODULE_SLUG}/dashboard`
- [ ] No 403 error
- [ ] No "Modul tidak ditemukan" error
- [ ] CRUD operations work (view, create, edit, delete)
- [ ] Menu highlights when active (blue border + blue text)

---

## ⚠️ Critical Reminders

1. ✅ **Middleware:** Gunakan `{MODULE_CODE}` (bukan slug)
2. ✅ **Policy:** Gunakan `{MODULE_CODE}` (bukan slug)
3. ✅ **Model:** Wajib pakai `BelongsToTenant` trait
4. ✅ **Controller:** Wajib pakai `Gate::authorize()`
5. ✅ **Database:** Insert ke 3 tables (modules, tenant_modules, role_module_permissions)
6. ✅ **Register:** ServiceProvider di config/app.php
7. ✅ **Register:** Policy di AuthServiceProvider.php
8. ✅ **Cache:** Clear semua cache setelah perubahan

---

## 📚 Related Documentation

- `MODULE-DEVELOPMENT-GUIDE.md` - Full guide
- `MODULE-QUICK-REFERENCE.md` - Quick reference
- `SUMMARY-BUGFIXES-20NOV2025.md` - Common issues & solutions
