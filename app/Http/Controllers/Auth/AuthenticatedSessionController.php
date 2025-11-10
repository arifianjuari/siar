<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            $request->authenticate();

            $user = Auth::user();
            Log::info('Autentikasi berhasil', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_id_before' => $request->session()->getId()
            ]);

            // Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            // Regenerate CSRF token setelah session regenerate
            $request->session()->regenerateToken();

            // Pastikan user masih terautentikasi setelah regenerate
            Auth::login($user);
            
            // Simpan session secara eksplisit
            $request->session()->save();

            Log::info('Session setelah regenerate', [
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
                'session_id_after' => $request->session()->getId(),
            ]);

            // Reload user dengan relationships
            $user = Auth::user()->load(['role', 'tenant']);

            // Set tenant ke session jika user bukan superadmin
            if ($user->role && $user->role->slug !== 'superadmin' && $user->tenant) {
                session(['tenant_id' => $user->tenant_id]);
                view()->share('current_tenant', $user->tenant);
            }

            if ($user->role && $user->role->slug === 'superadmin') {
                Log::info('User superadmin, mengarahkan ke dashboard superadmin', [
                    'user_id' => $user->id,
                    'role_slug' => $user->role->slug,
                    'tenant_id' => $user->tenant_id,
                    'tenant_name' => $user->tenant ? $user->tenant->name : null,
                ]);
                return redirect()->intended(route('superadmin.dashboard'));
            }

            Log::info('User reguler, mengarahkan ke dashboard biasa');
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
