<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        try {
            // Authenticate user
            $request->authenticate();
            
            // CRITICAL: Clear all session data and regenerate to prevent session fixation
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->regenerate();

            $user = Auth::user();

            // Force reload user with relationships to ensure fresh data
            $user = $user->fresh(['role', 'tenant']);
            
            if (!$user || !$user->role || !$user->tenant) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'User configuration invalid. Contact administrator.');
            }
            
            // Clear any stale session data
            session()->forget(['tenant_id', 'is_superadmin', 'auth_role']);
            
            // Set session based on role with proper validation
            if ($user->isSuperadmin()) {
                // SUPERADMIN: Clear tenant, set superadmin flags
                session([
                    'is_superadmin' => true,
                    'auth_role' => 'superadmin',
                    'user_verified' => true,
                ]);
                
                Log::info('Login: Superadmin authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'tenant' => $user->tenant->name,
                ]);
                
                return redirect()->intended(route('superadmin.dashboard'));
            } else {
                // TENANT USER: Set tenant session
                session([
                    'tenant_id' => $user->tenant_id,
                    'auth_role' => $user->role->slug,
                    'user_verified' => true,
                ]);
                
                view()->share('current_tenant', $user->tenant);
                
                Log::info('Login: Tenant user authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role->slug,
                    'tenant_id' => $user->tenant_id,
                ]);
                
                return redirect()->intended(route('dashboard'));
            }
            
        } catch (\Exception $e) {
            // Only log critical errors
            Log::error('Login error', [
                'email' => $request->email,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Use named route with absolute URL to avoid 419 errors
        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }
}
