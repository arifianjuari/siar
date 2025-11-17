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
                // Simpan DB asal, lalu switch ke DB tenant untuk proses request
                $originalDatabase = config('database.connections.mysql.database');
                config(['database.connections.mysql.database' => $tenant->database]);

                // Simpan tenant_id ke session
                session(['tenant_id' => $tenant->id]);
                session(['tenant_name' => $tenant->name]);

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

                // Proses request kemudian kembalikan DB ke asal agar penulisan session
                // tetap ke database pusat (bukan tenant)
                $response = $next($request);
                config(['database.connections.mysql.database' => $originalDatabase]);
                return $response;
            }
        }

        // Ambil domain dari request
        $domain = $request->getHost();

        // Untuk mendukung valet (.test), localhost:8000, dan online
        $parts = explode('.', $domain);
        $subdomain = null;

        // Jika ada subdomain dan bukan www
        if (count($parts) > 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];

            // Cari tenant berdasarkan domain subdomain
            $tenant = \App\Models\Tenant::where('domain', $subdomain)
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                // Simpan DB asal, lalu switch ke DB tenant saat memproses request
                $originalDatabase = config('database.connections.mysql.database');
                config(['database.connections.mysql.database' => $tenant->database]);

                // Set tenant_id ke session
                session(['tenant_id' => $tenant->id]);
                session(['tenant_name' => $tenant->name]);

                // Share tenant data ke semua view
                view()->share('current_tenant', $tenant);

                // Simpan tenant ke request untuk digunakan di controller
                $request->merge(['tenant' => $tenant]);

                $response = $next($request);
                config(['database.connections.mysql.database' => $originalDatabase]);
                return $response;
            }
        }

        // Khusus untuk localhost tanpa subdomain
        if ($domain === '127.0.0.1' || $domain === 'localhost' || str_contains($domain, '127.0.0.1:')) {
            // Ambil tenant terakhir dari session jika ada
            $tenant_id = session('tenant_id');
            if ($tenant_id) {
                $tenant = \App\Models\Tenant::find($tenant_id);
                if ($tenant && $tenant->is_active) {
                    // Share tenant data ke semua view
                    view()->share('current_tenant', $tenant);

                    // Simpan tenant ke request untuk digunakan di controller
                    $request->merge(['tenant' => $tenant]);

                    return $next($request);
                }
            }
        }

        // Jika tidak ada tenant yang ditemukan
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses tenant');
    }
}
