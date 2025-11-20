# Fix: 419 Page Expired Error After Logout

## Problem

Users experienced a "419 Page Expired" error when accessing the logout URL on Laravel Cloud production environment (`https://siar-beta-ctegvo.laravel.cloud/logout`).

## Root Cause

1. **Session/Cookie Domain Mismatch**: The session configuration wasn't optimized for Laravel Cloud's HTTPS environment
2. **Redirect to Root Path**: After logout, redirecting to `/` caused CSRF token issues because the session was invalidated but the browser still had old cookies
3. **Insecure Cookie Settings**: Session cookies weren't properly configured for HTTPS in production

## Solution Implemented

### 1. Updated Logout Controller

**File**: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Changed redirect from `/` to named route `login`:

```php
public function destroy(Request $request)
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Use named route with absolute URL to avoid 419 errors
    return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
}
```

**Benefits**:

- Named routes generate absolute URLs correctly on Laravel Cloud
- Success message confirms logout was successful
- Avoids CSRF token issues by going directly to login page

### 2. Updated Session Configuration

**File**: `config/session.php`

#### Secure Cookies (Line 171)

```php
'secure' => env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production'),
```

- Automatically enables secure cookies in production (HTTPS only)
- Prevents cookie theft over insecure connections

#### Same-Site Policy (Line 199)

```php
'same_site' => env('SESSION_SAME_SITE', env('APP_ENV') === 'production' ? 'strict' : 'lax'),
```

- Uses `strict` in production for better CSRF protection
- Uses `lax` in development for easier testing

### 3. Updated Logout Routes

**File**: `routes/auth.php`

Simplified and standardized both POST and GET logout routes:

```php
Route::middleware(['web'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('logout', function () {
        Log::info('Logout via GET method - redirecting to POST');
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    })->name('logout.get');
});
```

### 4. Added Success Message Display

**File**: `resources/views/auth/login.blade.php`

Added alert to show logout success message:

```blade
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
```

## Environment Variables for Laravel Cloud

Ensure these are set in your Laravel Cloud environment:

```env
APP_ENV=production
APP_URL=https://siar-beta-ctegvo.laravel.cloud
SESSION_DRIVER=database  # or redis for better performance
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_DOMAIN=  # Leave empty, Laravel will auto-detect
```

## Testing Checklist

- [x] Logout via POST method (navbar button)
- [x] Logout via GET method (direct URL access)
- [x] Verify redirect to login page
- [x] Verify success message displays
- [x] Verify no 419 errors
- [x] Verify session is properly cleared
- [x] Verify CSRF token is regenerated

## Security Improvements

1. **HTTPS-Only Cookies**: Session cookies only transmitted over HTTPS in production
2. **Strict Same-Site Policy**: Prevents CSRF attacks by blocking cross-site cookie transmission
3. **Proper Session Invalidation**: Complete session cleanup on logout
4. **Token Regeneration**: New CSRF token generated after logout

## Related Files Modified

- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `config/session.php`
- `routes/auth.php`
- `resources/views/auth/login.blade.php`

## References

- [Laravel Session Configuration](https://laravel.com/docs/10.x/session)
- [Laravel CSRF Protection](https://laravel.com/docs/10.x/csrf)
- [Laravel Cloud Deployment](https://cloud.laravel.com/docs)
