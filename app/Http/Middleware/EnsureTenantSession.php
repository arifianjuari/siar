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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();
        
        // Load relationships to ensure fresh data
        $user->load(['role', 'tenant']);
        
        // CRITICAL: Block superadmin from tenant routes
        if ($user->isSuperadmin()) {
            Log::warning('EnsureTenantSession: Superadmin attempting to access tenant route', [
                'user_id' => $user->id,
                'email' => $user->email,
                'path' => $request->path(),
            ]);
            
            // Clear any tenant session data
            session()->forget('tenant_id');
            
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Superadmin cannot access tenant pages. Use superadmin dashboard.');
        }

        // Ensure regular user has tenant
        if (!$user->tenant_id || !$user->tenant) {
            Log::error('EnsureTenantSession: User without tenant', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'User configuration error. Contact administrator.');
        }

        // Set or validate tenant session
        $sessionTenantId = session('tenant_id');
        
        if (!$sessionTenantId || $sessionTenantId !== $user->tenant_id) {
            // Set correct tenant in session
            session(['tenant_id' => $user->tenant_id]);
            
            Log::info('EnsureTenantSession: Setting tenant session', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'previous_session' => $sessionTenantId,
            ]);
        }
        
        // Share tenant info to views
        view()->share('current_tenant', $user->tenant);

        return $next($request);
    }
}
