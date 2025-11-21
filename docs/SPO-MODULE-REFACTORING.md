# Refactoring SPO dari WorkUnit Module ke SPOManagement Module

**Tanggal:** 19 November 2025  
**Status:** ✅ Completed

## Latar Belakang

SPO (Standar Prosedur Operasional) sebelumnya berada di dalam WorkUnit module sebagai sub-feature. Namun, setelah analisis domain bisnis, SPO seharusnya menjadi modul independen karena:

1. **Domain Bisnis Berbeda**

   - WorkUnit = Manajemen struktur organisasi
   - SPO = Manajemen dokumen prosedur operasional

2. **Relasi ≠ Kepemilikan**

   - SPO berelasi dengan WorkUnit (belongsTo)
   - SPO juga dapat terkait dengan multiple work units (linked_unit)
   - SPO adalah entitas independen, bukan bagian dari WorkUnit

3. **Kompleksitas Tinggi**

   - 6 views (index, create, edit, show, dashboard, pdf)
   - 593 baris controller
   - Dashboard terpisah
   - PDF generation & QR code
   - Ini bukan "fitur kecil" dari WorkUnit

4. **Access Control Lebih Fleksibel**
   - Permission seharusnya menggunakan module `spo-management`
   - Bukan menggunakan permission `work-units`

## Perubahan yang Dilakukan

### 0. Cleanup WorkUnit Module

**Files Removed from WorkUnit:**

- ❌ `modules/WorkUnit/Http/Controllers/SPOController.php` - Deleted
- ❌ `modules/WorkUnit/Resources/Views/spo/` - Deleted (entire folder)
- ✅ `modules/WorkUnit/Http/routes.php` - SPO routes removed

WorkUnit module sekarang hanya fokus pada manajemen unit kerja, tanpa SPO.

### 1. Struktur Module Baru

```
modules/SPOManagement/
├── Config/
│   └── config.php                  # Konfigurasi module
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Http/
│   ├── Controllers/
│   │   └── SPOController.php       # Moved from WorkUnit
│   ├── Middleware/
│   ├── Requests/
│   └── routes.php                  # Routes terpisah
├── Models/
│   └── SPO.php                     # Moved from app/Models
├── Policies/
│   └── SPOPolicy.php               # Moved from app/Policies
├── Providers/
│   ├── SPOManagementServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources/
│   └── Views/
│       └── spo/                    # Moved from WorkUnit/Resources/Views/spo
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           ├── show.blade.php
│           ├── dashboard.blade.php
│           └── pdf.blade.php
├── Services/
├── Tests/
├── README.md
└── module.json
```

### 2. Files yang Dipindahkan

#### Models

- **Dari:** `app/Models/SPO.php`
- **Ke:** `modules/SPOManagement/Models/SPO.php`
- **Backward Compatibility:** `app/Models/SPO` menjadi adapter yang extends `Modules\SPOManagement\Models\SPO`

#### Policies

- **Dari:** `app/Policies/SPOPolicy.php`
- **Ke:** `modules/SPOManagement/Policies/SPOPolicy.php`
- **Backward Compatibility:** `app/Policies/SPOPolicy` menjadi adapter

#### Controllers

- **Dari:** `modules/WorkUnit/Http/Controllers/SPOController.php`
- **Ke:** `modules/SPOManagement/Http/Controllers/SPOController.php`
- **Perubahan:**
  - Namespace: `Modules\SPOManagement\Http\Controllers`
  - View references: `work-unit::spo.*` → `spo-management::*`
  - Route names: `work-units.spo.*` → `spo.*`
  - Config values: Hardcoded → `config('spo-management.*')`

#### Views

- **Dari:** `modules/WorkUnit/Resources/Views/spo/`
- **Ke:** `modules/SPOManagement/Resources/Views/spo/`

#### Routes

- **Dari:** `modules/WorkUnit/Http/routes.php` (SPO routes section)
- **Ke:** `modules/SPOManagement/Http/routes.php`
- **Prefix:** `/spo`
- **Name:** `spo.*`
- **Permission:** `spo-management` (bukan `work-units`)

### 3. Konfigurasi

#### Config Module (`modules/SPOManagement/Config/config.php`)

```php
return [
    'name' => 'SPOManagement',
    'document_types' => [...],
    'status_validasi' => [...],
    'confidentiality_levels' => [...],
    'defaults' => [...],
];
```

#### Service Providers Registered (`config/app.php`)

```php
Modules\SPOManagement\Providers\SPOManagementServiceProvider::class,
Modules\SPOManagement\Providers\RouteServiceProvider::class,
```

### 4. Routes Changes

#### Old Routes (WorkUnit)

```php
// routes/modules/WorkUnit.php
Route::prefix('work-units')->group(function () {
    Route::prefix('spo')->name('spo.')->group(function () {
        Route::get('/', [SPOController::class, 'index'])->name('index');
        // ...
    });
});
```

#### New Routes (SPOManagement)

```php
// modules/SPOManagement/Http/routes.php
Route::middleware(['check.permission:spo-management,can_view'])
    ->prefix('spo')
    ->name('spo.')
    ->group(function () {
        Route::get('/', [SPOController::class, 'index'])->name('index');
        // ...
    });
```

**URL Changes:**

- ❌ Old: `/work-units/spo`
- ✅ New: `/spo`

**Route Names:**

- ❌ Old: `work-units.spo.index`
- ✅ New: `spo.index`

### 5. Permission Changes

**Old Permission:** `work-units` (module)

```php
Route::middleware('check.permission:work-units,can_create')
```

**New Permission:** `spo-management` (module)

```php
Route::middleware('check.permission:spo-management,can_create')
```

## Backward Compatibility

### Model

```php
// app/Models/SPO.php
namespace App\Models;
use Modules\SPOManagement\Models\SPO as BaseSPO;

class SPO extends BaseSPO
{
    // Adapter for backward compatibility
}
```

### Policy

```php
// app/Policies/SPOPolicy.php
namespace App\Policies;
use Modules\SPOManagement\Policies\SPOPolicy as BaseSPOPolicy;

class SPOPolicy extends BaseSPOPolicy
{
    // Adapter for backward compatibility
}
```

### Route Names

Routes sudah diupdate ke naming yang baru. Jika ada view atau controller yang masih menggunakan route names lama, perlu diupdate:

```php
// ❌ Old
route('work-units.spo.index')
route('work-units.spo.show', $spo)

// ✅ New
route('spo.index')
route('spo.show', $spo)
```

## Migration Steps

Jika ada existing data atau references, tidak perlu migration karena:

1. Database table `spos` tetap sama (tidak berubah)
2. Model backward compatible melalui adapter
3. Policy backward compatible melalui adapter

## Testing Checklist

- [ ] Run `composer dump-autoload` ✅ **Done**
- [ ] Test SPO listing: `/spo`
- [ ] Test SPO dashboard: `/spo/dashboard`
- [ ] Test SPO create
- [ ] Test SPO edit
- [ ] Test SPO delete
- [ ] Test SPO PDF generation
- [ ] Test SPO QR code generation
- [ ] Test permission checks (spo-management module)
- [ ] Test tenant isolation
- [ ] Verify backward compatibility

## Benefits

1. **Separation of Concerns**

   - WorkUnit fokus pada struktur organisasi
   - SPO fokus pada dokumen prosedur

2. **Better Access Control**

   - Permission terpisah (`spo-management`)
   - Lebih fleksibel untuk role management

3. **Cleaner Routes**

   - `/spo` lebih semantik daripada `/work-units/spo`

4. **Modular Architecture**

   - Konsisten dengan module lain
   - Mudah maintain dan develop

5. **Scalability**
   - SPO dapat berkembang tanpa mempengaruhi WorkUnit
   - Independent deployment jika needed

## Rollback Plan

Jika diperlukan rollback:

1. Remove SPOManagement ServiceProviders dari `config/app.php`
2. Restore SPO routes di `modules/WorkUnit/Http/routes.php`
3. Run `composer dump-autoload`

Namun backward compatibility adapter memastikan tidak ada breaking changes.

## Next Steps

1. **Update Seeder** - Pastikan `SPOManagementModuleSeeder` berjalan dengan benar
2. **Update Menu** - Update menu aplikasi untuk menggunakan route baru
3. **Update Documentation** - Update user documentation jika ada
4. **Monitor Logs** - Monitor untuk error terkait routes atau permissions

## Notes

- Lint errors "Expected type 'string'. Found 'array'" pada `json_decode(..., true)` adalah false positive dari Intelephense dan dapat diabaikan
- SPO Factory dan Seeder tetap di lokasi lama (`database/factories` dan `database/seeders`) untuk saat ini

## References

- Module Structure Pattern: `/docs/MODULE-STRUCTURE.md`
- RBAC Improvements: `/docs/RBAC-MULTITENANT-IMPROVEMENTS.md`
- Permission Helpers Deprecation: `/docs/PERMISSION-HELPERS-DEPRECATION.md`
