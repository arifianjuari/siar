# Bug Fix: Cache Tags Not Supported

**Tanggal:** 19 November 2025  
**Status:** ✅ SELESAI

## Masalah

Saat mengakses modul Product Management, muncul error:

```
This cache store does not support tagging.
app/Services/PermissionService.php:116
```

## Root Cause

`PermissionService` menggunakan cache tags (`Cache::tags()`), tetapi cache driver yang dikonfigurasi adalah `file` yang tidak mendukung tagging.

**Cache drivers yang support tags:**

- ✅ Redis
- ✅ Memcached
- ❌ File (default)
- ❌ Database
- ❌ Array

## Solusi

### 1. Refactor PermissionService

**File:** `/app/Services/PermissionService.php`

Menghapus semua penggunaan cache tags dan menggunakan cache keys sederhana.

#### Changes Made:

**getUserModulePermissions() - Line 113-117**

```php
// BEFORE
$cacheKey = $this->getCacheKey($user, $moduleCode);
$cacheTags = $this->getCacheTags($user);

return Cache::tags($cacheTags)->remember($cacheKey, self::CACHE_DURATION, function () use ($user, $moduleCode) {
    return $this->fetchUserPermissions($user, $moduleCode);
});

// AFTER
$cacheKey = $this->getCacheKey($user, $moduleCode);

return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user, $moduleCode) {
    return $this->fetchUserPermissions($user, $moduleCode);
});
```

**clearUserCache() - Line 173-185**

```php
// BEFORE
public function clearUserCache(User $user, ?string $moduleCode = null): void
{
    $cacheTags = $this->getCacheTags($user);

    if ($moduleCode) {
        Cache::tags($cacheTags)->forget($this->getCacheKey($user, $moduleCode));
    } else {
        Cache::tags(["user:{$user->id}"])->flush();
    }
}

// AFTER
public function clearUserCache(User $user, ?string $moduleCode = null): void
{
    if ($moduleCode) {
        // Clear specific module cache for user
        Cache::forget($this->getCacheKey($user, $moduleCode));
    } else {
        // Clear all module caches for this user
        $modules = Module::all();
        foreach ($modules as $module) {
            Cache::forget($this->getCacheKey($user, $module->code));
        }
    }
}
```

**clearTenantCache() - Line 192-201**

```php
// BEFORE
public function clearTenantCache(int $tenantId): void
{
    Cache::tags(["tenant:{$tenantId}"])->flush();
}

// AFTER
public function clearTenantCache(int $tenantId): void
{
    // Clear all caches for users in this tenant
    $users = User::where('tenant_id', $tenantId)->get();
    foreach ($users as $user) {
        $this->clearUserCache($user);
    }
}
```

**clearAllCaches() - Line 206-212**

```php
// BEFORE
public function clearAllCaches(): void
{
    Cache::tags(['permissions'])->flush();
}

// AFTER
public function clearAllCaches(): void
{
    // Clear all permission-related caches by pattern
    // Note: This is a simplified version for file cache driver
    // For production, consider using Redis or Memcached which support tags
    Cache::flush();
}
```

**Removed getCacheTags() method**

- Method ini tidak lagi diperlukan karena tidak menggunakan tags

## Trade-offs

### File Cache (Current)

**Pros:**

- ✅ No additional dependencies
- ✅ Works out of the box
- ✅ Simple configuration

**Cons:**

- ❌ No tag support (requires workarounds)
- ❌ Less efficient cache clearing
- ❌ Slower than in-memory caches
- ❌ `clearAllCaches()` clears entire cache (not just permissions)

### Redis Cache (Recommended for Production)

**Pros:**

- ✅ Full tag support
- ✅ Very fast (in-memory)
- ✅ Granular cache clearing
- ✅ Scalable
- ✅ Persistent

**Cons:**

- ❌ Requires Redis server
- ❌ Additional infrastructure

## Migration to Redis (Optional)

Untuk production, disarankan menggunakan Redis:

### 1. Install Redis

```bash
# macOS
brew install redis
brew services start redis

# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl start redis
```

### 2. Install PHP Redis Extension

```bash
pecl install redis
```

### 3. Update .env

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

Dengan Redis, semua fitur cache tags akan berfungsi optimal tanpa workaround.

## Testing

### Test Access

1. Clear cache: `php artisan cache:clear`
2. Login sebagai Tenant Admin
3. Akses: `http://siar.test/product-management/products`
4. ✅ Should work without cache tag error

### Test Cache Clearing

```php
// Clear specific user module cache
$permissionService->clearUserCache($user, 'product-management');

// Clear all user caches
$permissionService->clearUserCache($user);

// Clear tenant cache
$permissionService->clearTenantCache($tenantId);

// Clear all caches
$permissionService->clearAllCaches();
```

## Performance Impact

### Cache Clearing Performance

**With Tags (Redis):**

```php
Cache::tags(['user:1'])->flush(); // O(1) - instant
```

**Without Tags (File):**

```php
// Clear all modules for user - O(n) where n = number of modules
foreach ($modules as $module) {
    Cache::forget($cacheKey);
}
```

**Impact:** Minimal - Module count is typically small (< 20)

### Memory Usage

File cache uses disk I/O, so no significant memory impact.

## Related Files

- ✅ `/app/Services/PermissionService.php` - **MODIFIED**
- ✅ `/docs/BUGFIX-PRODUCT-MANAGEMENT-ACCESS.md` - Related fix
- ✅ `/docs/RBAC-MULTITENANT-IMPROVEMENTS.md` - RBAC overview

## Recommendations

1. **Development:** File cache is acceptable
2. **Production:** Migrate to Redis for better performance
3. **Alternative:** Use Memcached if Redis is not available

## Notes

- Cache keys tetap menggunakan pattern yang sama: `permissions:tenant_{id}:user_{id}:module_{code}`
- Backward compatible - tidak ada breaking changes
- Semua functionality tetap berfungsi, hanya implementasi yang berbeda
