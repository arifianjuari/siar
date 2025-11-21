<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\Activitylog\Models\Activity;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Simpan status autentikasi awal
        $wasLoggedIn = Auth::check();
        $user = $wasLoggedIn ? Auth::user() : null;

        // Proses request
        $response = $next($request);

        // Cek apakah status login berubah
        $isLoggedIn = Auth::check();
        $newUser = $isLoggedIn ? Auth::user() : null;

        // Login terdeteksi
        if (!$wasLoggedIn && $isLoggedIn) {
            $this->logLoginActivity($newUser);
        }

        // Logout terdeteksi
        if ($wasLoggedIn && !$isLoggedIn) {
            $this->logLogoutActivity($user);
        }

        return $response;
    }

    /**
     * Log aktivitas login
     */
    protected function logLoginActivity($user)
    {
        if (!$user) return;

        activity()
            ->causedBy($user)
            ->withProperties([
                'tenant_id' => $user->tenant_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'url' => request()->fullUrl()
            ])
            ->log('login');
    }

    /**
     * Log aktivitas logout
     */
    protected function logLogoutActivity($user)
    {
        if (!$user) return;

        // Set causer manually karena user sudah logout
        CauserResolver::setCauser($user);

        activity()
            ->withProperties([
                'tenant_id' => $user->tenant_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'url' => request()->fullUrl()
            ])
            ->log('logout');

        // Reset causer
        CauserResolver::setCauser(null);
    }
}
