<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class ResolveTenantByDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil subdomain dari request
        $subdomain = $request->route('tenant');

        if (!$subdomain) {
            $host = $request->getHost();
            $urlParts = explode('.', $host);

            // Mendukung berbagai format domain
            if (count($urlParts) > 1) {
                $subdomain = $urlParts[0];
            } else {
                return redirect()->route('dashboard')->with('error', 'Tenant tidak valid.');
            }
        }

        // Cari tenant berdasarkan domain
        $tenant = Tenant::where('domain', $subdomain)->first();

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan.');
        }

        // Set tenant id ke session
        session(['tenant_id' => $tenant->id]);

        return $next($request);
    }
}
