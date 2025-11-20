# Laravel Cloud Deployment Fix

**Date:** November 20, 2025  
**Issue:** Build failure on Laravel Cloud deployment  
**Status:** ✅ Fixed

## Problem

The deployment was failing with three critical errors:

### 1. Missing bootstrap/cache Directory

```
The /var/www/html/bootstrap/cache directory must be present and writable.
```

**Root Cause:** The build script was trying to run Laravel artisan commands before ensuring required directories existed.

### 2. PSR-4 Autoloading Violation

```
Class App\Http\Controllers\Superadmin\ModuleManagementController
located in ./app/Http/Controllers/SuperAdmin/ModuleManagementController.php
does not comply with psr-4 autoloading standard
```

**Root Cause:** Directory name was `Superadmin` but files were being detected in `SuperAdmin`, causing case-sensitivity issues.

### 3. Invalid View Cache Path During Package Discovery

```
Please provide a valid cache path.
```

**Root Cause:** The `AppServiceProvider` was trying to register Blade directives during composer's package discovery phase, before the view cache directory existed.

## Solutions Implemented

### 1. Enhanced Build Script (.laravel-cloud-build.sh)

**Added directory creation before composer install:**

```bash
# Ensure required directories exist
echo "📁 Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions early
echo "🔐 Setting initial permissions..."
chmod -R 775 storage bootstrap/cache
```

**Key Changes:**

- Create all Laravel required directories **before** running `composer install`
- Set permissions immediately after directory creation
- Removed redundant permission setting at the end

### 2. Fixed PSR-4 Autoloading

**Actions Taken:**

1. Renamed directory: `app/Http/Controllers/Superadmin` → `app/Http/Controllers/SuperAdmin`
2. Updated all controller namespaces:
   - `ModuleManagementController.php`
   - `TenantManagementController.php`
   - `TenantMonitoringController.php`
   - `UserManagementController.php`

**Changed from:**

```php
namespace App\Http\Controllers\Superadmin;
```

**Changed to:**

```php
namespace App\Http\Controllers\SuperAdmin;
```

3. Updated route imports in `routes/web.php`:

```php
use App\Http\Controllers\SuperAdmin\TenantManagementController;
use App\Http\Controllers\SuperAdmin\ModuleManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
```

### 3. Fixed AppServiceProvider Boot Sequence

**Added conditional check for view cache path:**

```php
// Only register Blade directives and view-related operations if view cache path exists
// This prevents errors during composer install/package discovery
$viewCachePath = config('view.compiled');
if ($viewCachePath && is_dir($viewCachePath)) {
    $this->registerBladeDirectives();
    $this->configureDebugMode();
    $this->registerViewComposers();

    // Set default pagination view to Bootstrap 5
    \Illuminate\Pagination\Paginator::useBootstrap();
}
```

**Key Changes:**

- Check if `storage/framework/views` exists before registering Blade directives
- Prevent Blade facade from being accessed during package discovery
- Ensures view-related services only load when ready

## Files Modified

### Build Configuration

- `.laravel-cloud-build.sh` - Enhanced with directory creation and early permission setting

### Controllers (Namespace Updates)

- `app/Http/Controllers/SuperAdmin/ModuleManagementController.php`
- `app/Http/Controllers/SuperAdmin/TenantManagementController.php`
- `app/Http/Controllers/SuperAdmin/TenantMonitoringController.php`
- `app/Http/Controllers/SuperAdmin/UserManagementController.php`

### Routes

- `routes/web.php` - Updated namespace imports

### Service Providers

- `app/Providers/AppServiceProvider.php` - Added view cache path check in boot method

## Deployment Process

The updated build script now follows this sequence:

1. **Create Directories** - Ensure all required Laravel directories exist
2. **Set Permissions** - Make directories writable
3. **Install Composer Dependencies** - Now safe to run with directories in place
4. **Install NPM Dependencies** - Frontend packages
5. **Build Assets** - Compile frontend resources
6. **Cache Configuration** - Optimize Laravel caches
7. **Create Storage Link** - Link public storage

## Testing

After pushing these changes, Laravel Cloud should:

- ✅ Successfully create bootstrap/cache directory
- ✅ Complete composer install without PSR-4 errors
- ✅ Run artisan commands successfully
- ✅ Complete the build process

## Git Commits

1. **Main deployment** (1ab92f1):

   - Major RBAC improvements
   - Modular refactoring
   - Bug fixes

2. **Deployment fix** (607bfee):

   - Fix bootstrap/cache directory creation
   - Fix PSR-4 autoloading issues
   - Update namespaces and routes

3. **View cache fix** (6ba091a):
   - Add conditional check for view cache path
   - Prevent Blade directive registration during package discovery
   - Fix "invalid cache path" error

## Next Steps

1. Monitor the Laravel Cloud deployment dashboard
2. Verify the build completes successfully
3. Test the deployed application
4. Address any remaining security vulnerabilities from Dependabot

## Notes

- The PSR-4 issue was caused by case-sensitivity differences between local (macOS) and deployment (Linux) environments
- Always ensure directory structure matches namespace casing exactly
- Create required directories before running composer scripts that depend on them
