<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

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
            if ($sessionSize > 3500) { // ~3.5KB threshold
                foreach (['_old_input', 'errors', 'report_params'] as $key) {
                    if ($request->session()->has($key)) {
                        $request->session()->forget($key);
                    }
                }

                // Recalculate dan log setelah trimming
                $sessionData = $request->session()->all();
                $sessionSize = strlen(serialize($sessionData));
                Log::warning('Trimmed session to avoid cookie too large', [
                    'size_bytes_after' => $sessionSize,
                    'session_keys' => array_keys($sessionData),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return $response;
    }
}
