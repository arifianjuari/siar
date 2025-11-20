# Permission Helpers Deprecation Notice

## ⚠️ DEPRECATED COMPONENTS

The following permission helper classes are **DEPRECATED** and will be removed in a future version. Please migrate to the new centralized `PermissionService`.

### Deprecated Classes:

1. **`App\Helpers\PermissionHelper`**
2. **`App\Helpers\ModulePermissionHelper`**

### Reason for Deprecation:

- **Inconsistent implementation** across the codebase
- **No caching** - performance issues with repeated permission checks
- **No audit logging** - difficult to track permission-related issues
- **No support for role hierarchy** - missing advanced RBAC features
- **No user-level permission overrides** - inflexible permission management

---

## ✅ Migration Guide

### Old Way (DEPRECATED):

```php
use App\Helpers\PermissionHelper;
use App\Helpers\ModulePermissionHelper;

// Old method 1
if (PermissionHelper::hasPermission($user, 'documents', 'can_view')) {
    // Do something
}

// Old method 2
if (ModulePermissionHelper::checkPermission($user, 'documents', 'can_edit')) {
    // Do something
}
```

### New Way (RECOMMENDED):

```php
use App\Services\PermissionService;
// OR use the facade
use App\Facades\Permission;

// Method 1: Inject the service (BEST PRACTICE)
public function __construct(PermissionService $permissionService)
{
    $this->permissionService = $permissionService;
}

public function someMethod()
{
    if ($this->permissionService->userHasPermission($user, 'documents', 'can_view')) {
        // Do something
    }
}

// Method 2: Use the Facade (CONVENIENT)
if (Permission::userHasPermission($user, 'documents', 'can_view')) {
    // Do something
}

// Method 3: Use Laravel's Gate (RECOMMENDED FOR AUTHORIZATION)
if ($user->can('view', $document)) {
    // Do something
}
```

---

## 🔄 Step-by-Step Migration

### 1. Update Controller Authorization

**Before:**

```php
use App\Helpers\PermissionHelper;

class DocumentController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission(auth()->user(), 'documents', 'can_view')) {
            abort(403);
        }

        // Controller logic
    }
}
```

**After:**

```php
class DocumentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Document::class);

        // Controller logic
    }
}
```

### 2. Update Middleware Usage

**Before:**

```php
// Using custom permission checking in middleware
Route::middleware(['auth', function ($request, $next) {
    if (!PermissionHelper::hasPermission(auth()->user(), 'documents', 'can_view')) {
        abort(403);
    }
    return $next($request);
}])->group(function () {
    // Routes
});
```

**After:**

```php
// Use the new CheckModulePermission middleware
Route::middleware(['auth', 'module.permission:documents,can_view'])->group(function () {
    // Routes
});
```

### 3. Update Blade Templates

**Before:**

```blade
@php
    use App\Helpers\PermissionHelper;
    $canEdit = PermissionHelper::hasPermission(auth()->user(), 'documents', 'can_edit');
@endphp

@if($canEdit)
    <button>Edit</button>
@endif
```

**After:**

```blade
@can('update', $document)
    <button>Edit</button>
@endcan

{{-- OR using Permission facade --}}
@if(Permission::userHasPermission(auth()->user(), 'documents', 'can_edit'))
    <button>Edit</button>
@endif
```

### 4. Update Service Classes

**Before:**

```php
use App\Helpers\ModulePermissionHelper;

class SomeService
{
    public function doSomething($user, $document)
    {
        if (!ModulePermissionHelper::checkPermission($user, 'documents', 'can_edit')) {
            throw new UnauthorizedException();
        }

        // Service logic
    }
}
```

**After:**

```php
use App\Services\PermissionService;

class SomeService
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function doSomething($user, $document)
    {
        if (!$this->permissionService->userHasPermission($user, 'documents', 'can_edit')) {
            throw new UnauthorizedException();
        }

        // Service logic
    }
}
```

---

## 🎯 Benefits of New System

### 1. **Performance Improvements**

- ✅ 1-hour caching of permission checks
- ✅ Reduced database queries
- ✅ Faster response times

### 2. **Security Enhancements**

- ✅ Comprehensive audit logging
- ✅ Better tenant isolation
- ✅ Standardized permission checking

### 3. **Advanced Features**

- ✅ Role hierarchy with inheritance
- ✅ User-level permission overrides
- ✅ Temporary permission grants with expiration
- ✅ Permission audit trail

### 4. **Code Quality**

- ✅ Consistent API across the application
- ✅ Better testability
- ✅ Easier maintenance
- ✅ Laravel best practices

---

## 📋 Migration Checklist

- [ ] Search for all usages of `PermissionHelper` in codebase
- [ ] Search for all usages of `ModulePermissionHelper` in codebase
- [ ] Update all controllers to use `$this->authorize()` method
- [ ] Update all Blade templates to use `@can` directive
- [ ] Update all services to inject `PermissionService`
- [ ] Update all route files to use `module.permission` middleware
- [ ] Test all permission-protected features
- [ ] Update documentation
- [ ] Remove deprecated helper files (after migration complete)

---

## 🔍 Finding Deprecated Usages

Use these commands to find deprecated usages in your codebase:

```bash
# Find PermissionHelper usage
grep -r "PermissionHelper" app/ resources/ --include="*.php" --include="*.blade.php"

# Find ModulePermissionHelper usage
grep -r "ModulePermissionHelper" app/ resources/ --include="*.php" --include="*.blade.php"

# Find use statements
grep -r "use App\\\\Helpers\\\\PermissionHelper" app/ --include="*.php"
grep -r "use App\\\\Helpers\\\\ModulePermissionHelper" app/ --include="*.php"
```

---

## ⏰ Timeline

- **Now - 1 month:** Migration period - both old and new systems work
- **1-2 months:** Deprecation warnings added to old helpers
- **3+ months:** Old helpers removed completely

---

## 💡 Need Help?

If you encounter any issues during migration:

1. Check the new PermissionService documentation
2. Review the BasePolicy implementation
3. Look at existing migrated controllers for examples
4. Contact the development team

---

## 📚 Related Documentation

- [RBAC & Multi-Tenant Improvements](./RBAC-MULTITENANT-IMPROVEMENTS.md)
- [BasePolicy Guide](./BASE-POLICY-GUIDE.md) _(to be created)_
- [PermissionService API](./PERMISSION-SERVICE-API.md) _(to be created)_

---

**Last Updated:** November 2024  
**Status:** Active Migration  
**Priority:** HIGH
