<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SetTenantId
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
        // Pastikan user login
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Pastikan user memiliki tenant
        if (!$user->tenant_id) {
            // Jika tidak memiliki tenant, redirect ke halaman pemilihan tenant
            // atau tampilkan error
            abort(403, 'Anda tidak terhubung dengan tenant manapun.');
        }

        // Cek apakah tenant user aktif (dengan caching untuk performa)
        $cacheKey = 'tenant_status_' . $user->tenant_id;
        $tenant = Cache::remember($cacheKey, 300, function () use ($user) {
            return Tenant::select('id', 'is_active', 'name')->find($user->tenant_id);
        });
        
        if (!$tenant || !$tenant->is_active) {
            // Clear cache jika tenant tidak aktif
            Cache::forget($cacheKey);
            Auth::logout();
            abort(403, 'Tenant Anda tidak aktif. Silahkan hubungi administrator.');
        }

        // Set tenant_id ke session HANYA jika belum ada
        if (!session()->has('tenant_id') || session('tenant_id') !== $user->tenant_id) {
            session(['tenant_id' => $user->tenant_id]);
        }

        return $next($request);
    }
}
