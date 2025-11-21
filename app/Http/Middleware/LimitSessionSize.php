<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LimitSessionSize
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini mencegah session cookie menjadi terlalu besar
     * dengan membersihkan data yang tidak diperlukan.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya jalankan untuk cookie driver
        if (config('session.driver') === 'cookie') {
            // Hitung ukuran session saat ini
            $sessionData = $request->session()->all();
            $sessionSize = strlen(serialize($sessionData));

            // Jika mendekati batas cookie (~4KB), hapus data yang cenderung besar
            if ($sessionSize > 3000) { // threshold lebih konservatif
                $keysBefore = array_keys($sessionData);

                // Key yang jelas tidak penting / berpotensi besar
                $hardDeleteKeys = [
                    '_old_input',
                    'errors',
                    '_flash',
                    'flash_notification',
                    'report_params',
                ];

                foreach ($hardDeleteKeys as $key) {
                    if ($request->session()->has($key)) {
                        $request->session()->forget($key);
                    }
                }

                // Hapus key debug yang bisa menumpuk
                foreach ($request->session()->all() as $key => $value) {
                    if (Str::startsWith($key, ['debug_', 'debug-test', 'debug_test'])) {
                        $request->session()->forget($key);
                    }
                }


                // Recalculate dan log setelah trimming
                $sessionData = $request->session()->all();
                $sessionSizeAfter = strlen(serialize($sessionData));
                Log::warning('Trimmed session to avoid cookie too large', [
                    'size_bytes_before' => $sessionSize,
                    'size_bytes_after' => $sessionSizeAfter,
                    'session_keys_before' => $keysBefore,
                    'session_keys_after' => array_keys($sessionData),
                    'user_id' => auth()->id(),
                ]);

                // Jika masih terlalu besar, lakukan hard-trim dengan whitelist key esensial saja
                if ($sessionSizeAfter > 3500) {
                    $current = $request->session()->all();

                    $allowed = [];
                    foreach ($current as $key => $value) {
                        // CRITICAL: Preserve authentication keys
                        if ($key === '_token' || $key === 'tenant_id' || $key === 'url' || $key === '_previous') {
                            $allowed[$key] = $value;
                            continue;
                        }
                        // CRITICAL: Preserve Laravel auth keys
                        if (Str::startsWith($key, ['login_web_', 'password_hash_'])) {
                            $allowed[$key] = $value;
                            continue;
                        }
                        // CRITICAL: Preserve SIAR authentication keys
                        if (in_array($key, ['is_superadmin', 'auth_role', 'user_verified', 'current_tenant'])) {
                            $allowed[$key] = $value;
                            continue;
                        }
                    }

                    // Flush lalu restore hanya key yang diizinkan
                    $request->session()->flush();
                    foreach ($allowed as $k => $v) {
                        $request->session()->put($k, $v);
                    }

                    $finalData = $request->session()->all();
                    $finalSize = strlen(serialize($finalData));
                    Log::warning('Applied hard-trim whitelist to session', [
                        'size_bytes_before' => $sessionSizeAfter,
                        'size_bytes_after' => $finalSize,
                        'kept_keys' => array_keys($finalData),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }

        return $response;
    }
}
