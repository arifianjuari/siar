<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah superadmin, bypass checking tenant session
        if (auth()->check() && auth()->user()->role && auth()->user()->role->slug === 'superadmin') {
            return $next($request);
        }

        // Jika tidak ada tenant_id di session, coba ambil dari user
        if (!session()->has('tenant_id') && auth()->check() && auth()->user()->tenant_id) {
            session(['tenant_id' => auth()->user()->tenant_id]);
        }

        // Jika masih tidak ada tenant_id, redirect ke halaman error
        if (!session()->has('tenant_id')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Tenant tidak ditemukan'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
