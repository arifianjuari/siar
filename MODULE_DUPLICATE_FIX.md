# Module Duplicate Fix Documentation

## Issue: Duplicate "User Management" Menu in Sidebar

### Problem Description:

Two identical "User Management" menu items appeared in the sidebar due to duplicate module records in the database.

### Root Cause:

The `modules` table contained duplicate entries:

- **ID 3**: User Management (slug: user-management)
- **ID 5**: User Management (slug: user-management) ← **DUPLICATE**

This happened likely due to:

1. Manual seeding multiple times
2. Running module sync multiple times
3. Database migration issues

### Solution Applied:

Deleted the duplicate module record (ID 5):

```bash
php artisan tinker --execute="\App\Models\Module::where('id', 5)->delete();"
```

### Verification:

After deletion, only one "User Management" module remains (ID 3).

---

## How to Prevent Duplicate Modules

### 1. Use Unique Constraint in Database

Add unique constraint to `modules` table:

```php
// In migration file
$table->string('slug')->unique();
```

### 2. Check Before Creating Module

When using auto-discovery sync:

```php
// In ModuleManagementController::syncFromFilesystem()
$existingModule = Module::where('slug', $slug)->first();

if (!$existingModule) {
    Module::create($moduleData);
    $created++;
} else {
    // Update instead of create
    $existingModule->update(['description' => $moduleData['description']]);
    $updated++;
}
```

### 3. Use updateOrCreate Method

Better approach:

```php
Module::updateOrCreate(
    ['slug' => $slug], // Search criteria
    $moduleData        // Data to update/create
);
```

---

## How to Check for Duplicates

### Method 1: Using Tinker

```bash
php artisan tinker --execute="echo json_encode(\App\Models\Module::select('id', 'name', 'slug')->get()->toArray(), JSON_PRETTY_PRINT);"
```

### Method 2: Using SQL

```sql
SELECT slug, COUNT(*) as count
FROM modules
GROUP BY slug
HAVING count > 1;
```

### Method 3: Using Artisan Command (Create Custom)

```php
// app/Console/Commands/CheckDuplicateModules.php
public function handle()
{
    $duplicates = Module::select('slug', DB::raw('COUNT(*) as count'))
        ->groupBy('slug')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->count() > 0) {
        $this->error('Found duplicate modules:');
        foreach ($duplicates as $dup) {
            $this->line("- {$dup->slug} ({$dup->count} entries)");
        }
    } else {
        $this->info('No duplicate modules found.');
    }
}
```

---

## How to Clean Up Duplicates Safely

### Step 1: Identify Duplicates

```php
$duplicateSlugs = Module::select('slug')
    ->groupBy('slug')
    ->havingRaw('COUNT(*) > 1')
    ->pluck('slug');
```

### Step 2: Keep Oldest, Delete Others

```php
foreach ($duplicateSlugs as $slug) {
    $modules = Module::where('slug', $slug)->orderBy('id')->get();

    // Keep first (oldest), delete rest
    $modules->skip(1)->each(function ($module) {
        $module->delete();
    });
}
```

### Step 3: Verify

```php
$remaining = Module::select('slug', DB::raw('COUNT(*) as count'))
    ->groupBy('slug')
    ->having('count', '>', 1)
    ->count();

if ($remaining === 0) {
    echo "All duplicates cleaned!";
}
```

---

## Prevention Checklist

- [ ] Add unique constraint to `slug` column in `modules` table
- [ ] Use `updateOrCreate()` instead of `create()` in sync function
- [ ] Add validation in ModuleSeeder to prevent duplicates
- [ ] Create custom artisan command to check duplicates
- [ ] Add database constraint in migration
- [ ] Document module creation process

---

## Migration to Add Unique Constraint

Create new migration:

```bash
php artisan make:migration add_unique_constraint_to_modules_slug
```

Migration content:

```php
public function up()
{
    // First, remove duplicates
    $duplicateSlugs = DB::table('modules')
        ->select('slug')
        ->groupBy('slug')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('slug');

    foreach ($duplicateSlugs as $slug) {
        $modules = DB::table('modules')
            ->where('slug', $slug)
            ->orderBy('id')
            ->get();

        // Keep first, delete rest
        foreach ($modules->skip(1) as $module) {
            DB::table('modules')->where('id', $module->id)->delete();
        }
    }

    // Then add unique constraint
    Schema::table('modules', function (Blueprint $table) {
        $table->unique('slug');
    });
}

public function down()
{
    Schema::table('modules', function (Blueprint $table) {
        $table->dropUnique(['slug']);
    });
}
```

---

## Current Module List (After Fix)

| ID  | Name                      | Slug                      |
| --- | ------------------------- | ------------------------- |
| 1   | Manajemen SPO             | spo-management            |
| 2   | Pengelolaan Kegiatan      | activity-management       |
| 3   | User Management           | user-management           |
| 4   | Dashboard                 | dashboard                 |
| 6   | Manajemen Risiko          | risk-management           |
| 7   | Unit Kerja                | work-units                |
| 8   | Correspondence Management | correspondence-management |
| 9   | Document Management       | document-management       |
| 10  | Performance Management    | performance-management    |
| 11  | Kendali Mutu Biaya        | kendali-mutu-biaya        |

**Total: 10 unique modules**

---

## Status: ✅ RESOLVED

- Duplicate "User Management" module (ID 5) has been deleted
- Sidebar now shows only one "User Management" menu
- System is functioning correctly

**Date Fixed**: 2025-11-19
**Fixed By**: Cascade AI Assistant
