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
            // Regenerate session sesuai standar Laravel
            $request->session()->regenerate();

            $user = Auth::user();

            // Load relationships secara explicit
            $user->load(['role', 'tenant']);
            
            // Set tenant ke session jika user bukan superadmin
            if ($user->role && $user->role->slug !== 'superadmin' && $user->tenant) {
                session(['tenant_id' => $user->tenant_id]);
                view()->share('current_tenant', $user->tenant);
            }

            // Determine redirect route based on role
            if ($user->role && $user->role->slug === 'superadmin') {
                return redirect()->intended(route('superadmin.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
            
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

        return redirect('/');
    }
}
