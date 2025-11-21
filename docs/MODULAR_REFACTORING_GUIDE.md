# Laravel Modular Refactoring Guide

## Overview

Panduan lengkap untuk refactoring Laravel project ke struktur modular tanpa menggunakan package `nwidart/laravel-modules`.

## ✅ ActivityManagement Module - COMPLETED

Module ActivityManagement telah berhasil di-refactor dan menjadi template untuk module lainnya.

### Struktur Module yang Sudah Selesai

```
modules/ActivityManagement/
├── Config/
│   └── config.php                          ✅ Module configuration
├── Database/
│   ├── Migrations/                         ✅ Ready for migrations
│   └── Seeders/                            ✅ Ready for seeders
├── Http/
│   ├── Controllers/                        ✅ All controllers migrated
│   │   ├── ActionableItemController.php
│   │   ├── ActivityAssigneeController.php
│   │   ├── ActivityCommentController.php
│   │   ├── ActivityController.php
│   │   └── DashboardController.php
│   ├── Middleware/                         ✅ Ready for middleware
│   ├── Requests/                           ✅ Ready for form requests
│   └── routes.php                          ✅ Module routes configured
├── Models/                                 ✅ All models migrated
│   ├── ActionableItem.php
│   ├── Activity.php
│   ├── ActivityAssignee.php
│   ├── ActivityComment.php
│   └── ActivityStatusLog.php
├── Providers/                              ✅ Service providers configured
│   ├── ActivityManagementServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources/
│   ├── Views/                              ✅ All views migrated
│   │   ├── actionable_items/
│   │   ├── assignees/
│   │   ├── comments/
│   │   ├── create.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── Assets/                             ✅ Ready for assets
├── Services/                               ✅ Ready for business logic
├── Tests/                                  ✅ Ready for tests
├── module.json                             ✅ Module metadata
└── README.md                               ✅ Module documentation
```

### Backward Compatibility Adapters

File-file adapter di `app/Models/` untuk backward compatibility:

✅ **ActionableItem.php** - Extends `Modules\ActivityManagement\Models\ActionableItem`

```php
namespace App\Models;
use Modules\ActivityManagement\Models\ActionableItem as BaseActionableItem;
class ActionableItem extends BaseActionableItem { }
```

✅ **Activity.php** - Extends `Modules\ActivityManagement\Models\Activity`

```php
namespace App\Models;
use Modules\ActivityManagement\Models\Activity as BaseActivity;
class Activity extends BaseActivity { }
```

✅ **ActivityAssignee.php** - Extends `Modules\ActivityManagement\Models\ActivityAssignee`

```php
namespace App\Models;
use Modules\ActivityManagement\Models\ActivityAssignee as BaseActivityAssignee;
class ActivityAssignee extends BaseActivityAssignee { }
```

✅ **ActivityComment.php** - Extends `Modules\ActivityManagement\Models\ActivityComment`

```php
namespace App\Models;
use Modules\ActivityManagement\Models\ActivityComment as BaseActivityComment;
class ActivityComment extends BaseActivityComment { }
```

✅ **ActivityStatusLog.php** - Extends `Modules\ActivityManagement\Models\ActivityStatusLog`

```php
namespace App\Models;
use Modules\ActivityManagement\Models\ActivityStatusLog as BaseActivityStatusLog;
class ActivityStatusLog extends BaseActivityStatusLog { }
```

⚠️ **ActivityLog.php** - BUKAN bagian dari ActivityManagement module

- File ini adalah untuk Spatie Activity Log package
- Tetap berada di `app/Models/ActivityLog.php`
- Tidak perlu dipindahkan

### Routes Configuration

Routes telah dipindahkan dari:

- ❌ `routes/web.php` (duplicate routes removed)
- ❌ `routes/modules/ActivityManagement.php` (updated namespaces)
- ✅ `modules/ActivityManagement/Http/routes.php` (primary location)

Routes diload otomatis via `ActivityManagementServiceProvider`.

### Namespace Changes

**Controllers:**

- Old: `App\Http\Controllers\Modules\ActivityManagement\*`
- New: `Modules\ActivityManagement\Http\Controllers\*`

**Models:**

- Old: `App\Models\Activity`, `App\Models\ActivityAssignee`, etc.
- New: `Modules\ActivityManagement\Models\*`

**Views:**

- Old: `resources/views/modules/activity_management/`
- New: `modules/ActivityManagement/Resources/Views/`
- Usage: `@include('activity-management::view-name')`

### Configuration Files

✅ **composer.json** - PSR-4 autoloading configured

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/",
        ...
    }
}
```

✅ **config/app.php** - Service provider registered

```php
'providers' => [
    ...
    Modules\ActivityManagement\Providers\ActivityManagementServiceProvider::class,
    ...
]
```

## Verification Checklist

### ✅ Completed Tasks

- [x] PSR-4 autoloading configured in composer.json
- [x] Module directory structure created
- [x] All models moved to module
- [x] Backward compatibility adapters created
- [x] All controllers moved to module
- [x] All views moved to module
- [x] Service providers configured
- [x] Routes consolidated in module
- [x] Namespace imports updated
- [x] Old duplicate files removed
- [x] Composer autoload regenerated
- [x] Routes verified and working
- [x] Module documentation created

### Testing Commands

```bash
# Clear caches
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload -o

# Verify routes
php artisan route:list --name=activity-management

# Test module access
# Visit: http://your-domain/activity-management/
```

## Next Steps: Replicate for Other Modules

Gunakan ActivityManagement sebagai template untuk module lainnya:

### Modules to Refactor:

1. **Correspondence** - Surat menyurat
2. **Dashboard** - Dashboard utama
3. **DocumentManagement** - Manajemen dokumen
4. **KendaliMutuBiaya** - Kendali mutu dan biaya
5. **ProductManagement** - Manajemen produk
6. **RiskManagement** - Manajemen risiko
7. **UserManagement** - Manajemen user
8. **WorkUnit** - Unit kerja

### Refactoring Steps per Module:

1. **Create Module Structure**

   ```bash
   mkdir -p modules/{ModuleName}/{Config,Database/{Migrations,Seeders},Http/{Controllers,Middleware,Requests},Models,Providers,Resources/{Views,Assets},Services,Tests}
   ```

2. **Move Models**

   - Copy models to `modules/{ModuleName}/Models/`
   - Update namespace to `Modules\{ModuleName}\Models`
   - Create adapters in `app/Models/`

3. **Move Controllers**

   - Copy controllers to `modules/{ModuleName}/Http/Controllers/`
   - Update namespace to `Modules\{ModuleName}\Http\Controllers`
   - Update model imports

4. **Move Views**

   - Copy views to `modules/{ModuleName}/Resources/Views/`
   - Update view references to use module namespace

5. **Create Service Providers**

   - Copy from ActivityManagement template
   - Update module name and paths
   - Register in `config/app.php`

6. **Consolidate Routes**

   - Move routes to `modules/{ModuleName}/Http/routes.php`
   - Remove duplicates from `routes/web.php`
   - Update controller namespaces

7. **Test & Verify**
   ```bash
   composer dump-autoload -o
   php artisan route:list --name={module-name}
   ```

## Best Practices

### DO's ✅

- Use module models directly: `Modules\{ModuleName}\Models\{Model}`
- Reference views with module namespace: `{module-name}::view-name`
- Keep business logic in Services directory
- Use Form Requests for validation
- Follow PSR-4 autoloading standards
- Document each module with README.md
- Create backward compatibility adapters during transition

### DON'Ts ❌

- Don't hardcode old namespaces in new code
- Don't mix old and new patterns
- Don't skip backward compatibility adapters
- Don't forget to update imports after moving files
- Don't leave duplicate routes
- Don't skip composer dump-autoload after changes

## Migration Strategy

### Phase 1: Foundation (✅ COMPLETED)

- Setup PSR-4 autoloading
- Create ActivityManagement as template
- Establish patterns and best practices

### Phase 2: Core Modules (NEXT)

- RiskManagement
- DocumentManagement
- UserManagement

### Phase 3: Supporting Modules

- WorkUnit
- Correspondence
- ProductManagement

### Phase 4: Specialized Modules

- KendaliMutuBiaya
- Dashboard

### Phase 5: Cleanup

- Remove all old files
- Remove backward compatibility adapters
- Update all references to use new namespaces
- Complete testing

## Troubleshooting

### Issue: Routes not found

**Solution:**

```bash
php artisan route:clear
composer dump-autoload -o
```

### Issue: Class not found

**Solution:**

- Check PSR-4 autoloading in composer.json
- Run `composer dump-autoload -o`
- Verify namespace matches directory structure

### Issue: Views not found

**Solution:**

- Check view path in ServiceProvider
- Use correct namespace: `{module-name}::view-name`
- Clear view cache: `php artisan view:clear`

### Issue: Middleware warnings in routes

**Note:** These are IDE warnings only. Middleware are registered globally in `app/Http/Kernel.php` and work correctly at runtime.

## Resources

- Laravel Documentation: https://laravel.com/docs
- PSR-4 Autoloading: https://www.php-fig.org/psr/psr-4/
- Module Pattern: https://github.com/nWidart/laravel-modules (reference only)

## Conclusion

Module ActivityManagement telah berhasil di-refactor dan siap digunakan sebagai template untuk module lainnya. Struktur modular ini memberikan:

- ✅ Better code organization
- ✅ Easier maintenance
- ✅ Reusable components
- ✅ Clear separation of concerns
- ✅ Scalable architecture
- ✅ Backward compatibility during transition

---

**Last Updated:** November 18, 2024
**Status:** ActivityManagement Module - COMPLETED ✅
**Next:** Replicate to other modules
