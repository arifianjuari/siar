<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user ada dan memiliki role superadmin
        if (!auth()->check()) {
            Log::warning('SuperadminMiddleware: Pengguna tidak terautentikasi', [
                'session_id' => $request->session()->getId(),
                'session_has_auth' => $request->session()->has('login_web_' . sha1('Illuminate\Auth\SessionGuard')),
                'path' => $request->path(),
            ]);
            return redirect()->route('login');
        }

        $user = auth()->user()->load(['role', 'tenant']);
        Log::info('SuperadminMiddleware: Memeriksa akses', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ? $user->role->slug : 'tidak ada role',
            'tenant_id' => $user->tenant_id,
            'tenant_name' => $user->tenant ? $user->tenant->name : null,
        ]);

        // Cek apakah user memiliki role superadmin
        // Cek tenant_id = 1 atau tenant name = 'System'
        $isSystemTenant = false;
        if ($user->tenant) {
            $isSystemTenant = $user->tenant->id === 1 || $user->tenant->name === 'System';
        }
        
        if ($user->role && $user->role->slug === 'superadmin' && $isSystemTenant) {
            Log::info('SuperadminMiddleware: Akses diberikan', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_slug' => $user->role->slug,
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant ? $user->tenant->name : null,
            ]);
            return $next($request);
        }

        Log::warning('SuperadminMiddleware: Akses ditolak - bukan superadmin', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ? $user->role->slug : 'tidak ada role',
            'tenant_id' => $user->tenant_id,
            'tenant_name' => $user->tenant ? $user->tenant->name : null,
            'is_system_tenant' => $isSystemTenant,
        ]);

        // Jika bukan superadmin, redirect ke dashboard regular (NOT superadmin.dashboard)
        // Clear any superadmin-related session data
        session()->forget(['superadmin_access', 'superadmin_verified']);
        
        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki akses ke halaman ini. Hanya Superadmin yang dapat mengakses.');
    }
}
