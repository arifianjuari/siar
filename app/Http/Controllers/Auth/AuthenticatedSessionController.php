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

            Log::info('Autentikasi berhasil', ['user_id' => Auth::id(), 'email' => Auth::user()->email]);

            $request->session()->regenerate();

            // Cek apakah user memiliki role superadmin
            $user = Auth::user();

            // Set tenant ke session jika user bukan superadmin
            if ($user->role && $user->role->slug !== 'superadmin' && $user->tenant) {
                session(['tenant_id' => $user->tenant->id]);
                view()->share('current_tenant', $user->tenant);
            }

            if ($user->role && $user->role->slug === 'superadmin') {
                Log::info('User superadmin, mengarahkan ke dashboard superadmin');
                return redirect()->intended(route('superadmin.dashboard'));
            }

            Log::info('User reguler, mengarahkan ke dashboard biasa');
            return redirect()->intended(route('dashboard.debug'));
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
