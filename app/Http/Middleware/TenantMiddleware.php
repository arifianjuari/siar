<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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
        // Skip tenant checking for auth routes
        if ($request->routeIs('login', 'logout', 'register')) {
            return $next($request);
        }

        if (Auth::check()) {
            $tenant = Auth::user()->tenant;
            if ($tenant) {
                // IMPORTANT: No database switching! Using shared database with tenant_id filtering
                
                // Set tenant_id to session for fallback (when user is not authenticated)
                if (!session()->has('tenant_id') || session('tenant_id') !== $tenant->id) {
                    session(['tenant_id' => $tenant->id]);
                }

                // Set tenant_id to request for middleware chain
                $request->merge([
                    '__tenant_id' => $tenant->id,
                    'tenant' => $tenant
                ]);

                // Share tenant data to all views
                view()->share('current_tenant', $tenant);

                // Get active modules for tenant
                $activeModules = $tenant->modules()
                    ->where('tenant_modules.is_active', true)
                    ->orderBy('name')
                    ->get();

                // Share active modules to all views
                view()->share('activeModules', $activeModules);

                // Log tenant access for audit
                Log::debug('Tenant access', [
                    'user_id' => Auth::id(),
                    'tenant_id' => $tenant->id,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);

                return $next($request);
            }
        }

        // Handle subdomain-based tenant resolution
        $domain = $request->getHost();
        $parts = explode('.', $domain);
        $subdomain = null;

        // Check for subdomain (not www)
        if (count($parts) > 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];

            // Find tenant by domain
            $tenant = \App\Models\Tenant::where('domain', $subdomain)
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                // IMPORTANT: No database switching! Using shared database with tenant_id filtering
                
                // Set tenant_id to session
                if (!session()->has('tenant_id') || session('tenant_id') !== $tenant->id) {
                    session(['tenant_id' => $tenant->id]);
                }

                // Set tenant_id to request for middleware chain
                $request->merge([
                    '__tenant_id' => $tenant->id,
                    'tenant' => $tenant
                ]);

                // Share tenant data to all views
                view()->share('current_tenant', $tenant);

                // Log subdomain tenant access
                Log::debug('Subdomain tenant access', [
                    'subdomain' => $subdomain,
                    'tenant_id' => $tenant->id,
                    'ip' => $request->ip()
                ]);

                return $next($request);
            }
        }

        // Handle localhost without subdomain
        if ($domain === '127.0.0.1' || $domain === 'localhost' || str_contains($domain, '127.0.0.1:')) {
            // Get tenant from session if available
            $tenant_id = session('tenant_id');
            if ($tenant_id) {
                $tenant = \App\Models\Tenant::find($tenant_id);
                if ($tenant && $tenant->is_active) {
                    // Set tenant_id to request for middleware chain
                    $request->merge([
                        '__tenant_id' => $tenant->id,
                        'tenant' => $tenant
                    ]);

                    // Share tenant data to all views
                    view()->share('current_tenant', $tenant);

                    return $next($request);
                }
            }
        }

        // If no tenant found, redirect to login
        Log::warning('No tenant context found', [
            'domain' => $domain,
            'session_tenant_id' => session('tenant_id'),
            'user_id' => Auth::id(),
            'ip' => $request->ip()
        ]);
        
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses tenant');
    }
}
