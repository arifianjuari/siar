# Laravel Cloud Session Fix - Deployment Instructions

## Problem

1. Session tidak persist setelah login di Laravel Cloud karena konflik antara `SESSION_DRIVER=database` (custom) dan `SESSION_DRIVER=cookie` (injected by Laravel Cloud).
2. **"400 Bad Request - Request Header Or Cookie Too Large"** error karena session data terlalu besar dalam cookie.

## Solution

1. Accept cookie-based sessions dan simplify authentication logic.
2. Optimize middleware to prevent repeated session writes.
3. Add session size monitoring and cleanup.

---

## Step 1: Update Laravel Cloud Environment Variables

Di **Laravel Cloud Dashboard** → **Environment** → **Custom Variables**:

### ❌ HAPUS Variable Ini:

```
SESSION_DRIVER=database
```

### ✅ UPDATE/TAMBAH Variables Berikut:

```env
# Session Configuration
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# Keep existing
APP_TIMEZONE=Asia/Jakarta
APP_URL_SCHEME=https://
APP_KEY=base64:Oto5UTUNnofMt6xxnRG4XS9PgKcJzdkXAgI2VqJKxB0=
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**PENTING:**

- `SESSION_SECURE_COOKIE=true` → WAJIB untuk HTTPS
- `SESSION_DOMAIN=` → HARUS KOSONG (bukan `null` atau `.laravel.cloud`)
- JANGAN set `SESSION_DRIVER`, biarkan Laravel Cloud inject `cookie`

---

## Step 2: Commit & Push Code Changes

```bash
git add app/Http/Controllers/Auth/AuthenticatedSessionController.php
git commit -m "Fix: Simplify authentication for cookie-based sessions"
git push origin main
```

---

## Step 3: Deploy di Laravel Cloud

1. Buka **Laravel Cloud Dashboard**
2. Klik **Deploy** pada branch `main`
3. Tunggu deployment selesai

---

## Step 4: Clear Config Cache (Penting!)

Setelah deployment selesai, jalankan:

```bash
# Di Laravel Cloud terminal
php artisan config:clear
php artisan config:cache
php artisan cache:clear
```

---

## Step 5: Test Login

1. **Clear browser cookies** untuk domain `siar-main-bot1z9.laravel.cloud`
2. Buka https://siar-main-bot1z9.laravel.cloud/
3. Login dengan credentials valid
4. Setelah redirect, buka `/debug-auth`
5. **Expected result:**

   ```json
   {
     "auth_check": true,
     "user": { ... },
     "session_info": {
       "session_cookie_name": "siar_session",
       "has_session_cookie": true,
       "session_has_auth_key": true
     },
     "session_config": {
       "driver": "cookie",
       "secure": true,
       "same_site": "lax"
     }
   }
   ```

6. Test navigation:
   - Klik menu lain (dashboard, dll)
   - Refresh halaman
   - **Should stay logged in** ✅

---

## Troubleshooting

### Jika masih redirect ke login:

1. **Verify environment variables:**

   ```bash
   php artisan tinker
   >>> config('session.driver');      // Should return "cookie"
   >>> config('session.secure');      // Should return true
   >>> config('session.domain');      // Should return null
   >>> config('session.same_site');   // Should return "lax"
   ```

2. **Check browser cookies:**

   - Open DevTools → Application → Cookies
   - Look for `siar_session` cookie
   - Verify:
     - Domain: `siar-main-bot1z9.laravel.cloud`
     - Secure: ✓
     - SameSite: `Lax`
     - HttpOnly: ✓

3. **Check response headers** (Network tab):

   - After login, verify `Set-Cookie` header exists
   - Should contain `siar_session=...`

4. **Clear everything and retry:**

   ```bash
   # In Laravel Cloud
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear

   # In browser
   - Clear all cookies for the domain
   - Hard refresh (Cmd+Shift+R)
   ```

---

## What Changed

### Before (Database Sessions):

- 300+ lines of manual database session handling
- Conflicts with Laravel Cloud's injected `SESSION_DRIVER=cookie`
- Complex cookie management
- Race conditions with session saving
- **Repeated session writes on every request causing cookie bloat**

### After (Cookie Sessions):

- Simple, standard Laravel authentication
- Works with Laravel Cloud's cookie driver
- Proper session regeneration
- No manual cookie handling needed
- **Optimized session writes** - only write when data changes
- **Session size monitoring** via `LimitSessionSize` middleware
- **Automatic flash message cleanup** to prevent accumulation

---

## Key Points

1. **Laravel Cloud injects `SESSION_DRIVER=cookie`** - Don't fight it, use it
2. **Cookie-based sessions are fine** - They work well for most applications
3. **Set `SESSION_SECURE_COOKIE=true`** - Required for HTTPS
4. **Keep `SESSION_DOMAIN` empty** - For same-domain cookies
5. **Always clear config cache** after env changes

---

## Files Modified

1. `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

   - Removed 250+ lines of database session logic
   - Simplified to standard Laravel authentication flow
   - Added session cleanup after login
   - Works seamlessly with cookie-based sessions

2. `app/Http/Middleware/TenantMiddleware.php`

   - Optimized to only write `tenant_id` if not already set
   - Removed redundant `tenant_name` writes (use relationship instead)

3. `app/Http/Middleware/SetTenantId.php`
   - Only write to session when tenant changes
4. `app/Http/Middleware/ResolveTenant.php`

   - Only write to session when tenant changes

5. `app/Http/Middleware/ResolveTenantByDomain.php`

   - Only write to session when tenant changes

6. `app/Http/Middleware/LimitSessionSize.php` ✨ NEW

   - Monitors session size and logs warnings
   - Cleans up flash messages automatically
   - Prevents cookie bloat

7. `app/Http/Kernel.php`
   - Registered `LimitSessionSize` middleware in web group
