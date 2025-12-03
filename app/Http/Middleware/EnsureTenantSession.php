<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureTenantSession
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memastikan:
     * 1. User terautentikasi
     * 2. Superadmin tidak bisa akses tenant routes
     * 3. User tenant memiliki tenant_id yang valid
     * 4. Session tenant_id sesuai dengan user tenant
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Log untuk debugging masalah session
            Log::debug('EnsureTenantSession: User not authenticated', [
                'session_id' => session()->getId(),
                'session_driver' => config('session.driver'),
                'has_session_cookie' => $request->hasCookie(config('session.cookie')),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
            
            // Jika AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Session expired. Please login again.',
                    'redirect' => route('login'),
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $user = auth()->user();
        
        // Load relationships to ensure fresh data (dengan error handling)
        try {
            $user->load(['role', 'tenant']);
        } catch (\Exception $e) {
            Log::error('EnsureTenantSession: Failed to load user relationships', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat memuat data user.');
        }
        
        // CRITICAL: Block superadmin from tenant routes
        if ($user->isSuperadmin()) {
            Log::info('EnsureTenantSession: Superadmin redirected from tenant route', [
                'user_id' => $user->id,
                'email' => $user->email,
                'path' => $request->path(),
            ]);
            
            // Clear any tenant session data
            session()->forget('tenant_id');
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Access denied',
                    'message' => 'Superadmin tidak dapat mengakses halaman tenant.',
                    'redirect' => route('superadmin.dashboard'),
                ], 403);
            }
            
            return redirect()->route('superadmin.dashboard')
                ->with('info', 'Superadmin tidak dapat mengakses halaman tenant. Gunakan dashboard superadmin.');
        }

        // Ensure regular user has tenant
        if (!$user->tenant_id || !$user->tenant) {
            Log::error('EnsureTenantSession: User without valid tenant', [
                'user_id' => $user->id,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'has_tenant' => (bool) $user->tenant,
            ]);
            
            auth()->logout();
            session()->invalidate();
            
            return redirect()->route('login')
                ->with('error', 'Konfigurasi user tidak valid. Hubungi administrator.');
        }

        // Ensure tenant is active
        if (!$user->tenant->is_active) {
            Log::warning('EnsureTenantSession: User tenant is inactive', [
                'user_id' => $user->id,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant->name,
            ]);
            
            auth()->logout();
            session()->invalidate();
            
            return redirect()->route('login')
                ->with('error', 'Tenant Anda tidak aktif. Hubungi administrator.');
        }

        // Set or validate tenant session
        $sessionTenantId = session('tenant_id');
        
        if (!$sessionTenantId || $sessionTenantId !== $user->tenant_id) {
            // Set correct tenant in session
            session(['tenant_id' => $user->tenant_id]);
            
            // Log hanya jika ada perubahan signifikan
            if ($sessionTenantId && $sessionTenantId !== $user->tenant_id) {
                Log::warning('EnsureTenantSession: Tenant mismatch corrected', [
                    'user_id' => $user->id,
                    'user_tenant_id' => $user->tenant_id,
                    'session_tenant_id' => $sessionTenantId,
                ]);
            }
        }
        
        // Share tenant info to views
        view()->share('current_tenant', $user->tenant);

        return $next($request);
    }
}
