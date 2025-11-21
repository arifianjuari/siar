# TEST LOGIN - Debugging Steps

## Current Status

- Login code: ✅ FIXED (commit 3bf4919)
- Logs show: ✅ "Login: Superadmin authenticated"
- But user reports: ❌ "tidak bisa masuk atau login"

## Possible Issues

### 1. Browser Cache/Session Issue

Browser mungkin masih pakai session lama yang invalid.

### 2. Redirect Loop

Middleware mungkin redirect balik ke login.

### 3. JavaScript Error

Frontend error yang prevent redirect.

---

## STEP-BY-STEP DEBUGGING

### Step 1: Clear Everything

```bash
# Terminal 1: Clear Laravel cache
cd /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My\ Drive/01\ PAPA/05\ DEVELOPMENT/siar

# Clear all caches
/opt/homebrew/bin/php artisan cache:clear
/opt/homebrew/bin/php artisan config:clear
/opt/homebrew/bin/php artisan route:clear
/opt/homebrew/bin/php artisan view:clear

# Clear sessions (IMPORTANT!)
rm -rf storage/framework/sessions/*
echo ".gitignore" > storage/framework/sessions/.gitignore
```

### Step 2: Clear Browser

**Chrome/Safari:**

1. Open DevTools (Cmd + Option + I)
2. Right-click Refresh button
3. Select "Empty Cache and Hard Reload"
4. Or: Cmd + Shift + Delete → Clear cookies for `siar.test`

### Step 3: Test Login with DevTools Open

1. Open: http://siar.test/login
2. Open DevTools → Network tab
3. Login with:
   - Email: `superadmin@siar.com`
   - Password: `asdfasdf`
4. Click "Masuk"

### Step 4: Check Network Tab

Look for:

- POST to `/login` → Status should be **302** (redirect)
- Redirect to `/superadmin/dashboard` → Status should be **200**

If you see:

- **302 → /login** (redirect back to login) = Middleware blocking
- **500 error** = Server error
- **419 error** = CSRF token issue
- **No redirect** = JavaScript error

### Step 5: Check Console Tab

Look for JavaScript errors:

- Red errors in console
- Failed network requests

### Step 6: Check Application Tab

1. Go to Application → Cookies → http://siar.test
2. Look for: `siar_session` cookie
3. Should exist after login

---

## EXPECTED BEHAVIOR

### Successful Login Flow:

```
1. POST /login (credentials)
   ↓
2. 302 Redirect to /superadmin/dashboard
   ↓
3. GET /superadmin/dashboard
   ↓
4. 200 OK (show dashboard)
```

### Failed Login Scenarios:

**Scenario A: Redirect Loop**

```
1. POST /login
   ↓
2. 302 → /superadmin/dashboard
   ↓
3. GET /superadmin/dashboard
   ↓
4. 302 → /login (WRONG!)
```

**Cause:** Middleware thinks user not authenticated

**Scenario B: Session Not Saved**

```
1. POST /login (success)
2. Session created but not saved
3. Next request has no auth
```

**Cause:** Session driver issue

**Scenario C: Middleware Blocking**

```
1. POST /login (success)
2. Redirect to /superadmin/dashboard
3. SuperadminMiddleware checks auth
4. Fails check, redirect to /login
```

**Cause:** `isSuperadmin()` returning false

---

## QUICK TEST COMMANDS

### Test 1: Check if user exists

```bash
/opt/homebrew/bin/php artisan tinker
>>> \App\Models\User::where('email', 'superadmin@siar.com')->first()
>>> $user = \App\Models\User::find(1)
>>> $user->load(['role', 'tenant'])
>>> $user->isSuperadmin()  // Should return TRUE
>>> exit
```

### Test 2: Check session driver

```bash
grep SESSION_DRIVER .env
# Should show: SESSION_DRIVER=file
```

### Test 3: Check session directory

```bash
ls -la storage/framework/sessions/
# Should be writable
```

### Test 4: Test login via curl

```bash
# Get CSRF token
curl -c cookies.txt http://siar.test/login

# Extract CSRF token from HTML
TOKEN=$(curl -s http://siar.test/login | grep '_token' | grep -oP 'value="\K[^"]+')

# Login
curl -b cookies.txt -c cookies.txt \
  -X POST http://siar.test/login \
  -d "_token=$TOKEN" \
  -d "email=superadmin@siar.com" \
  -d "password=asdfasdf" \
  -L -v

# Should see redirect to /superadmin/dashboard
```

---

## WHAT TO REPORT

Please provide:

1. **Network Tab Screenshot**

   - Show POST /login request
   - Show redirect chain

2. **Console Errors**

   - Any red errors

3. **Session Cookie**

   - Does `siar_session` cookie exist?

4. **Laravel Log**

   - Last 20 lines after login attempt:

   ```bash
   tail -n 20 storage/logs/laravel.log
   ```

5. **Behavior Description**
   - What happens after clicking "Masuk"?
   - Does page reload?
   - Does it stay on /login?
   - Any error message shown?

---

## TEMPORARY DEBUG ROUTE

Add this to test authentication:

```php
// In routes/web.php, add temporarily:
Route::get('/debug-auth', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->id(),
            'email' => auth()->user()->email,
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id,
            'is_superadmin' => auth()->user()->isSuperadmin(),
        ] : null,
        'session' => session()->all(),
    ]);
})->middleware('web');
```

Then visit: http://siar.test/debug-auth

- Before login: should show `authenticated: false`
- After login: should show user data

---

## NEXT STEPS

Based on the issue found, we can:

1. Fix middleware if blocking incorrectly
2. Fix session if not persisting
3. Fix redirect if looping
4. Fix frontend if JavaScript error
