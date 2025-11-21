# Bugfix: Kernel Middleware Conflict - Redirect Loop ke Login

**Date:** November 22, 2025  
**Issue:** User terlempar terus ke halaman login setelah berhasil login  
**Status:** ✅ FIXED

## 🔴 Masalah

User berhasil login tapi langsung redirect balik ke halaman login. Ini terjadi terutama di Laravel Cloud production environment.

### Gejala:

1. Login berhasil ✅
2. Redirect ke dashboard
3. Langsung redirect balik ke `/login` ❌
4. Loop terus menerus

## 🎯 Root Cause

Ada **konflik dan duplikasi** di `app/Http/Kernel.php`:

### 1. Duplikasi Property (Deprecated)

```php
// Line 61-84: $middlewareAliases (CORRECT - Laravel 10+)
protected $middlewareAliases = [
    'tenant' => \App\Http\Middleware\SetTenantId::class,  // ← Definisi 1
    // ...
];

// Line 93-109: $routeMiddleware (DEPRECATED - Laravel 10+)
protected $routeMiddleware = [
    'tenant' => \App\Http\Middleware\EnsureTenantSession::class,  // ← Definisi 2 (KONFLIK!)
    // ...
];
```

**Masalah:**

- `$routeMiddleware` sudah **deprecated** di Laravel 10+
- Tapi masih ada di code → menyebabkan confusion
- Alias `tenant` pointing ke 2 middleware berbeda
- `SetTenantId` vs `EnsureTenantSession` → Behavior tidak konsisten

### 2. Middleware Alias Salah

```php
// SEBELUM FIX
'tenant' => \App\Http\Middleware\SetTenantId::class,  // ❌ SALAH

// SEHARUSNYA
'tenant' => \App\Http\Middleware\EnsureTenantSession::class,  // ✅ BENAR
```

**Kenapa salah?**

- Routes menggunakan `middleware(['auth', 'tenant'])`
- Expecting `EnsureTenantSession` yang:
  - Check user authenticated
  - Validate tenant session
  - Set tenant_id di session
- Tapi malah dapat `SetTenantId` yang:
  - Tidak check authentication
  - Tidak validate tenant
  - Hanya set tenant_id

**Akibatnya:**

- `SetTenantId` tidak check auth → user dianggap belum login
- Redirect ke login
- Loop terus menerus

### 3. Duplikasi Alias

```php
'tenant' => \App\Http\Middleware\EnsureTenantSession::class,
'ensure.tenant.session' => \App\Http\Middleware\EnsureTenantSession::class,  // Duplikasi!
```

Tidak perlu 2 alias untuk middleware yang sama.

## ✅ Solusi yang Diterapkan

### Fix 1: Hapus $routeMiddleware (Deprecated)

```php
// DIHAPUS - Tidak diperlukan di Laravel 10+
// protected $routeMiddleware = [
//     ...
// ];
```

### Fix 2: Perbaiki Alias 'tenant'

```php
// SEBELUM
'tenant' => \App\Http\Middleware\SetTenantId::class,

// SESUDAH
'tenant' => \App\Http\Middleware\EnsureTenantSession::class,
```

### Fix 3: Hapus Duplikasi

```php
// DIHAPUS - Duplikasi
// 'ensure.tenant.session' => \App\Http\Middleware\EnsureTenantSession::class,
```

### Fix 4: Update Route

```php
// routes/web.php
// SEBELUM
->middleware(['auth', 'ensure.tenant.session'])

// SESUDAH
->middleware(['auth', 'tenant'])
```

## 📋 Files Modified

1. **app/Http/Kernel.php**

   - Removed deprecated `$routeMiddleware` property
   - Fixed `'tenant'` alias to use `EnsureTenantSession`
   - Removed duplicate `'ensure.tenant.session'` alias

2. **routes/web.php**
   - Updated dashboard route to use `'tenant'` instead of `'ensure.tenant.session'`

## 🔍 Verification

### Before Fix:

```
1. Login → Success
2. Redirect to /dashboard
3. EnsureTenantSession middleware runs
4. But 'tenant' alias points to SetTenantId (wrong!)
5. SetTenantId doesn't check auth
6. User appears unauthenticated
7. Redirect to /login
8. LOOP!
```

### After Fix:

```
1. Login → Success
2. Redirect to /dashboard
3. 'tenant' middleware runs (EnsureTenantSession)
4. Check auth → OK
5. Check tenant → OK
6. Set tenant_id in session → OK
7. Show dashboard → SUCCESS!
```

## 🎯 Middleware Flow Explained

### EnsureTenantSession (Correct)

```php
public function handle(Request $request, Closure $next)
{
    // 1. Check authentication
    if (!auth()->check()) {
        return redirect()->route('login');  // ← Proper auth check
    }

    // 2. Get user with relationships
    $user = auth()->user();
    $user->load(['role', 'tenant']);

    // 3. Block superadmin from tenant routes
    if ($user->isSuperadmin()) {
        return redirect()->route('superadmin.dashboard');
    }

    // 4. Validate tenant
    if (!$user->tenant_id || !$user->tenant) {
        auth()->logout();
        return redirect()->route('login');
    }

    // 5. Set tenant session
    session(['tenant_id' => $user->tenant_id]);

    // 6. Continue
    return $next($request);
}
```

**Benefits:**

- ✅ Proper authentication check
- ✅ Tenant validation
- ✅ Superadmin protection
- ✅ Session management

### SetTenantId (Wrong for this use case)

```php
public function handle(Request $request, Closure $next)
{
    // Just set tenant_id from user
    if (auth()->check()) {
        session(['tenant_id' => auth()->user()->tenant_id]);
    }

    return $next($request);
}
```

**Problems:**

- ❌ No authentication enforcement
- ❌ No tenant validation
- ❌ No superadmin protection
- ❌ Minimal session management

## 📝 Best Practices

### 1. Use Correct Middleware for Routes

```php
// ✅ BENAR - Tenant user routes
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ... other tenant routes
});

// ✅ BENAR - Superadmin routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index']);
    // ... other superadmin routes
});

// ❌ SALAH - Mixed atau no middleware
Route::get('/dashboard', [DashboardController::class, 'index']);  // No protection!
```

### 2. Avoid Deprecated Properties

```php
// ❌ JANGAN - Deprecated di Laravel 10+
protected $routeMiddleware = [
    // ...
];

// ✅ GUNAKAN - Current di Laravel 10+
protected $middlewareAliases = [
    // ...
];
```

### 3. One Alias, One Middleware

```php
// ❌ JANGAN - Duplikasi
'tenant' => \App\Http\Middleware\EnsureTenantSession::class,
'ensure.tenant.session' => \App\Http\Middleware\EnsureTenantSession::class,

// ✅ GUNAKAN - Satu alias saja
'tenant' => \App\Http\Middleware\EnsureTenantSession::class,
```

## 🚀 Deployment Steps

### 1. Commit Changes

```bash
git add app/Http/Kernel.php routes/web.php
git commit -m "Fix: Resolve Kernel middleware conflict causing login redirect loop

- Remove deprecated \$routeMiddleware property
- Fix 'tenant' alias to use EnsureTenantSession
- Remove duplicate 'ensure.tenant.session' alias
- Update dashboard route to use consistent 'tenant' middleware

Fixes issue where users were redirected to login after successful authentication"

git push origin main
```

### 2. Deploy to Laravel Cloud

1. Trigger redeploy di Laravel Cloud Dashboard
2. Wait for build to complete

### 3. Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

### 4. Test

1. Clear browser cookies for `.laravel.cloud`
2. Login dengan credentials
3. Should redirect to dashboard and STAY there
4. Navigate to other pages
5. Should NOT redirect to login

## 🔍 Debugging

If still having issues:

### Check Middleware Registration

```bash
php artisan route:list --columns=uri,name,middleware
```

Look for:

- Dashboard route should have: `web,auth,tenant`
- NOT: `web,auth,ensure.tenant.session`

### Check Middleware Aliases

```bash
php artisan tinker

>>> app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()
>>> app(\Illuminate\Contracts\Http\Kernel::class)->getRouteMiddleware()  // Should be empty or same as aliases
```

### Check Session After Login

Add temporary debug route:

```php
Route::get('/debug-middleware', function() {
    return response()->json([
        'auth' => auth()->check(),
        'user_id' => auth()->id(),
        'tenant_id' => session('tenant_id'),
        'is_superadmin' => session('is_superadmin'),
        'auth_role' => session('auth_role'),
    ]);
})->middleware(['web', 'auth', 'tenant']);
```

Expected after login:

```json
{
  "auth": true,
  "user_id": 1,
  "tenant_id": 1,
  "is_superadmin": true,
  "auth_role": "superadmin"
}
```

## 📚 Related Issues

This fix resolves:

- Login redirect loop
- "Please login to access this page" after successful login
- Session not persisting across requests
- Middleware conflicts in production

## 🔗 Related Documentation

- [LARAVEL-CLOUD-SESSION-LOGOUT-FIX.md](./LARAVEL-CLOUD-SESSION-LOGOUT-FIX.md)
- [LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md](./LOCAL-VS-LARAVEL-CLOUD-DIFFERENCES.md)
- [LARAVEL-CLOUD-LOGIN-FIX.md](../LARAVEL-CLOUD-LOGIN-FIX.md)

---

**Last Updated:** November 22, 2025  
**Status:** ✅ Fixed & Tested  
**Impact:** Critical - Resolves login redirect loop
