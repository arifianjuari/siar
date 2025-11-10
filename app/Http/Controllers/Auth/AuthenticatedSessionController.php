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
            $sessionIdBefore = $request->session()->getId();
            
            Log::info('Autentikasi berhasil', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_id_before' => $sessionIdBefore,
                'session_driver' => config('session.driver'),
            ]);

            // Login user terlebih dahulu (ini akan menyimpan user ke session)
            Auth::login($user, $request->filled('remember'));
            
            // Regenerate session untuk keamanan
            // Catatan: regenerate() akan membuat session ID baru tapi mempertahankan data
            $request->session()->regenerate();
            
            // Setelah regenerate, pastikan user masih terautentikasi
            // Karena regenerate membuat session baru, kita perlu login lagi
            Auth::login($user, $request->filled('remember'));
            
            // Regenerate CSRF token setelah session regenerate
            $request->session()->regenerateToken();
            
            $sessionIdAfter = $request->session()->getId();
            
            Log::info('Session setelah regenerate', [
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
                'session_id_before' => $sessionIdBefore,
                'session_id_after' => $sessionIdAfter,
                'session_changed' => $sessionIdBefore !== $sessionIdAfter,
            ]);

            // Reload user dengan relationships
            $user = Auth::user()->load(['role', 'tenant']);

            // Set tenant ke session jika user bukan superadmin
            if ($user->role && $user->role->slug !== 'superadmin' && $user->tenant) {
                session(['tenant_id' => $user->tenant_id]);
                view()->share('current_tenant', $user->tenant);
            }

            // Buat response redirect
            $redirectResponse = null;
            
            if ($user->role && $user->role->slug === 'superadmin') {
                Log::info('User superadmin, mengarahkan ke dashboard superadmin', [
                    'user_id' => $user->id,
                    'role_slug' => $user->role->slug,
                    'tenant_id' => $user->tenant_id,
                    'tenant_name' => $user->tenant ? $user->tenant->name : null,
                ]);
                $redirectResponse = redirect()->intended(route('superadmin.dashboard'));
            } else {
                Log::info('User reguler, mengarahkan ke dashboard biasa');
                $redirectResponse = redirect()->intended(route('dashboard'));
            }
            
            // Pastikan session cookie ter-set dengan benar di response
            // Laravel akan otomatis menambahkan session cookie ke response
            // Tapi kita pastikan dengan memanggil save() sebelum redirect
            $request->session()->save();
            
            Log::info('Mengirim redirect response', [
                'target_url' => $redirectResponse->getTargetUrl(),
                'session_id' => $request->session()->getId(),
                'cookies_in_response' => count($redirectResponse->headers->getCookies()),
            ]);
            
            return $redirectResponse;
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
