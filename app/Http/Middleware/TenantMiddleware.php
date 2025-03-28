<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika sedang mengakses halaman login atau auth lainnya, lewati pengecekan tenant
        if ($request->routeIs('login') || $request->routeIs('logout') || $request->is('login', 'logout', 'register')) {
            return $next($request);
        }

        if (Auth::check()) {
            $tenant = Auth::user()->tenant;
            if ($tenant) {
                config(['database.connections.mysql.database' => $tenant->database]);

                // Simpan tenant_id ke session
                session(['tenant_id' => $tenant->id]);

                // Share tenant data ke semua view
                view()->share('current_tenant', $tenant);

                // Ambil modul aktif untuk tenant
                $activeModules = $tenant->modules()
                    ->where('tenant_modules.is_active', true)
                    ->orderBy('name')
                    ->get();

                // Share modul aktif ke semua view
                view()->share('activeModules', $activeModules);

                // Simpan tenant ke request untuk digunakan di controller
                $request->merge(['tenant' => $tenant]);

                return $next($request);
            }
        }

        // Ambil domain dari request
        $domain = $request->getHost();

        // Cari tenant berdasarkan domain
        $tenant = Tenant::where('domain', $domain)
            ->where('is_active', true)
            ->first();

        // Jika tenant tidak ditemukan, redirect ke halaman error atau tenant default
        if (!$tenant) {
            if (config('app.env') === 'local' && config('app.debug') === true) {
                // Dalam mode development, gunakan tenant pertama jika ada
                $tenant = Tenant::where('is_active', true)->first();

                if (!$tenant) {
                    abort(404, 'Tenant tidak ditemukan');
                }
            } else {
                abort(404, 'Tenant tidak ditemukan');
            }
        }

        // Simpan tenant_id ke session
        session(['tenant_id' => $tenant->id]);

        // Share tenant data ke semua view
        view()->share('current_tenant', $tenant);

        // Ambil modul aktif untuk tenant
        $activeModules = $tenant->modules()
            ->where('tenant_modules.is_active', true)
            ->orderBy('name')
            ->get();

        // Share modul aktif ke semua view
        view()->share('activeModules', $activeModules);

        // Simpan tenant ke request untuk digunakan di controller
        $request->merge(['tenant' => $tenant]);

        return $next($request);
    }
}
