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
            Log::info('Mencoba login', ['email' => $request->email]);

            // Authenticate user
            $request->authenticate();

            $user = Auth::user();
            
            Log::info('Autentikasi berhasil', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_driver' => config('session.driver'),
            ]);

            $request->session()->forget(['_previous', '_flash']);
            
            // Re-bind auth to the regenerated session and explicitly persist the guard key
            Auth::login($user);
            $authKeyName = Auth::guard()->getName(); // e.g. login_web_...
            $request->session()->put($authKeyName, $user->getAuthIdentifier());
            $request->session()->save();

            // Reload user dengan relationships
            $user = Auth::user()->load(['role', 'tenant']);

            // Set tenant ke session jika user bukan superadmin
            if ($user->role && $user->role->slug !== 'superadmin' && $user->tenant) {
                session(['tenant_id' => $user->tenant_id]);
                view()->share('current_tenant', $user->tenant);
            }
            
            Log::info('Session regenerated', [
                'user_id' => $user->id,
                'session_id' => $request->session()->getId(),
                'is_authenticated' => Auth::check(),
            ]);

            // Determine redirect route based on role
            if ($user->role && $user->role->slug === 'superadmin') {
                Log::info('Redirecting superadmin to dashboard', [
                    'user_id' => $user->id,
                    'role_slug' => $user->role->slug,
                ]);
                // Save session sekali lagi untuk memastikan data auth tersimpan
                $request->session()->save();
                return redirect()->intended(route('superadmin.dashboard'));
            }
            
            Log::info('Redirecting regular user to dashboard');
            // Save session sekali lagi untuk memastikan data auth tersimpan
            $request->session()->save();
            return redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            Log::error('Error saat login', [
                'email' => $request->email,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
