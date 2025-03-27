<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Cek apakah tenant user aktif
        $tenant = Tenant::find($user->tenant_id);
        if (!$tenant || !$tenant->is_active) {
            Auth::logout();
            abort(403, 'Tenant Anda tidak aktif. Silahkan hubungi administrator.');
        }

        // Set tenant_id ke session
        session(['tenant_id' => $user->tenant_id]);
        session(['tenant_name' => $tenant->name]);

        return $next($request);
    }
}
