<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolveTenant
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
        // Ambil subdomain dari host
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = count($parts) > 2 ? $parts[0] : null;

        // Jika tidak ada subdomain, redirect ke domain utama
        if (!$subdomain) {
            return redirect(config('app.url'));
        }

        // Cari tenant berdasarkan domain
        $tenant = Tenant::where('domain', $subdomain)->first();

        // Jika tenant tidak ditemukan, tampilkan 404
        if (!$tenant) {
            abort(404, 'Tenant tidak ditemukan.');
        }

        // Jika tenant tidak aktif, tampilkan informasi
        if (!$tenant->is_active) {
            abort(403, 'Tenant ini tidak aktif. Silahkan hubungi administrator.');
        }

        // Set tenant ID ke session
        session(['tenant_id' => $tenant->id]);
        session(['tenant_name' => $tenant->name]);

        // Jika user login, pastikan dia memiliki akses ke tenant ini
        if (Auth::check() && Auth::user()->tenant_id !== $tenant->id) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke tenant ini.');
        }

        return $next($request);
    }
}
