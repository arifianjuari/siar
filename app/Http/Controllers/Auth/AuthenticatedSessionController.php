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

            $request->authenticate();

            $user = Auth::user();
            $sessionIdBefore = $request->session()->getId();
            
            Log::info('Autentikasi berhasil', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_id_before' => $sessionIdBefore,
                'session_driver' => config('session.driver'),
            ]);

            // Regenerate session setelah autentikasi (sesuai rekomendasi Laravel)
            // untuk mencegah session fixation attack
            $request->session()->regenerate();

            $sessionIdAfter = $request->session()->getId();

            // Regenerate CSRF token setelah session regenerate
            $request->session()->regenerateToken();

            // PENTING: Explicitly save session untuk database driver
            // Tanpa ini, session mungkin tidak tersimpan di database sebelum redirect
            $request->session()->save();

            Log::info('Session setelah regenerate', [
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check(),
                'session_id_before' => $sessionIdBefore,
                'session_id_after' => $sessionIdAfter,
                'session_changed' => $sessionIdBefore !== $sessionIdAfter,
                'session_all_keys' => array_keys($request->session()->all()),
                'session_driver' => config('session.driver'),
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

            // PENTING: Save session sekali lagi sebelum redirect untuk memastikan
            // session tersimpan di database (terutama untuk database driver)
            // Juga pastikan auth data tersimpan di session
            $request->session()->put('_token', csrf_token());
            
            // Force save session ke database
            $request->session()->save();
            
            if (config('session.driver') === 'database') {
                // Pastikan session tersimpan di database
                $sessionId = $request->session()->getId();
                $sessionData = $request->session()->all();
                
                // Verifikasi session tersimpan
                $sessionExists = DB::table('sessions')->where('id', $sessionId)->exists();
                
                // Jika session tidak ada di database, insert/update manual
                if (!$sessionExists) {
                    try {
                        // Coba update dulu (jika ada)
                        $updated = DB::table('sessions')
                            ->where('id', $sessionId)
                            ->update([
                                'user_id' => Auth::id(),
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'payload' => base64_encode(serialize($sessionData)),
                                'last_activity' => time(),
                            ]);
                        
                        // Jika tidak ada yang di-update, insert baru
                        if ($updated === 0) {
                            DB::table('sessions')->insert([
                                'id' => $sessionId,
                                'user_id' => Auth::id(),
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'payload' => base64_encode(serialize($sessionData)),
                                'last_activity' => time(),
                            ]);
                            Log::info('Session inserted manually ke database', [
                                'session_id' => $sessionId,
                            ]);
                        } else {
                            Log::info('Session updated manually di database', [
                                'session_id' => $sessionId,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error saving session manually', [
                            'error' => $e->getMessage(),
                            'session_id' => $sessionId,
                        ]);
                    }
                } else {
                    // Update last_activity dan user_id jika sudah ada
                    DB::table('sessions')
                        ->where('id', $sessionId)
                        ->update([
                            'user_id' => Auth::id(),
                            'last_activity' => time(),
                        ]);
                }
            }

            // PENTING: Verifikasi session tersimpan di database SEBELUM set cookie
            $sessionId = $request->session()->getId();
            $sessionExistsInDb = false;
            
            if (config('session.driver') === 'database') {
                $sessionExistsInDb = DB::table('sessions')->where('id', $sessionId)->exists();
                
                if (!$sessionExistsInDb) {
                    Log::error('Session tidak tersimpan di database sebelum set cookie!', [
                        'session_id' => $sessionId,
                    ]);
                    // Coba save sekali lagi
                    $request->session()->save();
                    // Tunggu sebentar
                    usleep(50000); // 50ms
                    $sessionExistsInDb = DB::table('sessions')->where('id', $sessionId)->exists();
                }
            }
            
            // Set cookie menggunakan withCookie() untuk memastikan cookie ter-set dengan benar
            $cookieName = config('session.cookie');
            $cookieDomain = config('session.domain');
            $cookieSecure = config('session.secure') !== null ? (bool) config('session.secure') : request()->isSecure();
            $cookieSameSite = config('session.same_site') ?? 'lax';
            $cookieMinutes = (int) config('session.lifetime');
            
            // Pastikan cookie domain null jika kosong (bukan empty string)
            $cookieDomain = !empty($cookieDomain) ? $cookieDomain : null;
            
            // PENTING: Hapus cookie lama terlebih dahulu untuk memastikan cookie baru ter-set
            // Ini penting karena browser mungkin tidak update cookie jika ada cookie lama dengan attributes berbeda
            $oldCookieValue = $request->cookie($cookieName);
            if ($oldCookieValue && $oldCookieValue !== $sessionId) {
                // Hapus cookie lama dengan expire di masa lalu
                $redirectResponse = $redirectResponse->withCookie(
                    cookie()->forget($cookieName)
                );
                Log::info('Removing old session cookie', [
                    'old_session_id' => $oldCookieValue,
                    'new_session_id' => $sessionId,
                ]);
            }
            
            // Set cookie baru dengan session ID yang benar
            $redirectResponse = $redirectResponse->withCookie(
                cookie(
                    $cookieName, 
                    $sessionId, 
                    $cookieMinutes, 
                    '/', 
                    $cookieDomain, 
                    $cookieSecure, 
                    true, // httpOnly
                    false, // raw
                    $cookieSameSite
                )
            );
            
            Log::info('Setting session cookie', [
                'cookie_name' => $cookieName,
                'session_id' => $sessionId,
                'session_exists_in_db' => $sessionExistsInDb,
                'cookie_domain' => $cookieDomain,
                'cookie_secure' => $cookieSecure,
                'cookie_same_site' => $cookieSameSite,
                'cookie_minutes' => $cookieMinutes,
            ]);
            
            // Log cookie yang akan dikirim (setelah explicit session cookie)
            $cookies = $redirectResponse->headers->getCookies();
            $cookieInfo = [];
            foreach ($cookies as $cookie) {
                $cookieInfo[] = [
                    'name' => $cookie->getName(),
                    'domain' => $cookie->getDomain(),
                    'path' => $cookie->getPath(),
                    'secure' => $cookie->isSecure(),
                    'httpOnly' => $cookie->isHttpOnly(),
                    'sameSite' => $cookie->getSameSite(),
                ];
            }
            
            // Verifikasi session tersimpan di database (untuk debugging)
            $sessionInDb = false;
            if (config('session.driver') === 'database') {
                try {
                    $sessionInDb = DB::table('sessions')
                        ->where('id', $request->session()->getId())
                        ->exists();
                } catch (\Exception $e) {
                    Log::warning('Tidak bisa verifikasi session di database', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            Log::info('Mengirim redirect response', [
                'target_url' => $redirectResponse->getTargetUrl(),
                'session_id' => $request->session()->getId(),
                'cookies_in_response' => count($cookies),
                'cookie_details' => $cookieInfo,
                'session_in_database' => $sessionInDb,
                'session_config' => [
                    'cookie_name' => config('session.cookie'),
                    'domain' => config('session.domain'),
                    'secure' => config('session.secure'),
                    'same_site' => config('session.same_site'),
                    'driver' => config('session.driver'),
                ],
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
