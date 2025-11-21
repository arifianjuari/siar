<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthenticationGuard Middleware
 * 
 * This middleware ensures proper authentication and prevents privilege escalation
 * between superadmin and tenant admin roles.
 * 
 * CRITICAL: This middleware must run BEFORE any role-specific middleware
 */
class AuthenticationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $requiredRole
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('AuthenticationGuard: User not authenticated', [
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::user();
        
        // Force reload user with relationships to prevent stale data
        $user = $user->fresh(['role', 'tenant']);
        
        if (!$user) {
            Log::error('AuthenticationGuard: User fresh load failed', [
                'user_id' => Auth::id(),
            ]);
            
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Authentication error. Please login again.');
        }

        // Validate user has role and tenant
        if (!$user->role || !$user->tenant) {
            Log::error('AuthenticationGuard: User missing role or tenant', [
                'user_id' => $user->id,
                'has_role' => (bool)$user->role,
                'has_tenant' => (bool)$user->tenant,
            ]);
            
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'User configuration invalid. Contact administrator.');
        }

        // Store authentication verification in cache (not session to prevent tampering)
        $verificationKey = 'auth_verified_' . $user->id;
        $verificationData = [
            'user_id' => $user->id,
            'role_id' => $user->role->id,
            'role_slug' => $user->role->slug,
            'tenant_id' => $user->tenant_id,
            'is_superadmin' => $user->isSuperadmin(),
            'verified_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        
        // Cache for 5 minutes (short lived for security)
        Cache::put($verificationKey, $verificationData, 300);
        
        // Verify role if specified
        if ($requiredRole) {
            $hasRequiredRole = false;
            
            switch ($requiredRole) {
                case 'superadmin':
                    $hasRequiredRole = $user->isSuperadmin();
                    break;
                    
                case 'tenant-admin':
                    $hasRequiredRole = $user->role->slug === 'tenant-admin' && !$user->isSuperadmin();
                    break;
                    
                case 'regular':
                    $hasRequiredRole = !in_array($user->role->slug, ['superadmin', 'tenant-admin']);
                    break;
                    
                default:
                    $hasRequiredRole = $user->role->slug === $requiredRole;
                    break;
            }
            
            if (!$hasRequiredRole) {
                Log::warning('AuthenticationGuard: Role requirement not met', [
                    'user_id' => $user->id,
                    'user_role' => $user->role->slug,
                    'required_role' => $requiredRole,
                    'is_superadmin' => $user->isSuperadmin(),
                ]);
                
                // Redirect based on actual role
                if ($user->isSuperadmin()) {
                    return redirect()->route('superadmin.dashboard')
                        ->with('error', 'Access denied. You are logged in as Superadmin.');
                } else {
                    return redirect()->route('dashboard')
                        ->with('error', 'Access denied. Insufficient permissions.');
                }
            }
        }
        
        // Set session data based on role
        if ($user->isSuperadmin()) {
            // Superadmin: Clear tenant session, set superadmin flag
            session()->forget('tenant_id');
            session()->put('is_superadmin', true);
            session()->put('auth_role', 'superadmin');
        } else {
            // Regular/Tenant Admin: Set tenant session, clear superadmin flag
            session()->put('tenant_id', $user->tenant_id);
            session()->forget('is_superadmin');
            session()->put('auth_role', $user->role->slug);
            
            // Share tenant with views
            view()->share('current_tenant', $user->tenant);
        }
        
        // Log successful authentication
        Log::info('AuthenticationGuard: Access granted', [
            'user_id' => $user->id,
            'role' => $user->role->slug,
            'tenant_id' => $user->tenant_id,
            'is_superadmin' => $user->isSuperadmin(),
            'required_role' => $requiredRole,
            'path' => $request->path(),
        ]);
        
        return $next($request);
    }
}
