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
            
            // IMPORTANT: Regenerate session ID to prevent session fixation
            // But do NOT invalidate - that would clear the auth
            $request->session()->regenerate();
            
            // Get authenticated user with relationships
            $user = Auth::user();
            $user->load(['role', 'tenant']);
            
            if (!$user || !$user->role || !$user->tenant) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'User configuration invalid. Contact administrator.');
            }
            
            // Set session based on role with proper validation
            if ($user->isSuperadmin()) {
                // SUPERADMIN: Clear tenant, set superadmin flags
                session([
                    'is_superadmin' => true,
                    'auth_role' => 'superadmin',
                    'user_verified' => true,
                ]);
                
                $redirectUrl = route('superadmin.dashboard');
                
                Log::info('Login: Superadmin authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'tenant' => $user->tenant->name,
                    'redirect_to' => $redirectUrl,
                    'session_id' => session()->getId(),
                ]);
                
                return redirect()->intended($redirectUrl);
            } else {
                // TENANT USER: Set tenant session
                session([
                    'tenant_id' => $user->tenant_id,
                    'auth_role' => $user->role->slug,
                    'user_verified' => true,
                ]);
                
                view()->share('current_tenant', $user->tenant);
                
                $redirectUrl = route('dashboard');
                
                Log::info('Login: Tenant user authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role->slug,
                    'tenant_id' => $user->tenant_id,
                    'redirect_to' => $redirectUrl,
                    'session_id' => session()->getId(),
                ]);
                
                return redirect()->intended($redirectUrl);
            }
            
        } catch (\Exception $e) {
            // Only log critical errors
            Log::error('Login error', [
                'email' => $request->email ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
