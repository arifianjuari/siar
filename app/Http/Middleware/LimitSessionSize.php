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
            // Clean up flash messages yang sudah digunakan
            $request->session()->reflash(); // Keep flash for one more request
            
            // Batasi jumlah flash keys (max 5)
            $flashData = $request->session()->get('_flash', []);
            if (isset($flashData['new']) && count($flashData['new']) > 5) {
                // Ambil 5 terakhir saja
                $flashData['new'] = array_slice($flashData['new'], -5);
                $request->session()->put('_flash', $flashData);
            }

            // Log warning jika session terlalu besar (untuk debugging)
            $sessionData = $request->session()->all();
            $sessionSize = strlen(serialize($sessionData));
            
            if ($sessionSize > 3072) { // 3KB threshold (cookie limit is usually 4KB)
                Log::warning('Session size approaching cookie limit', [
                    'size_bytes' => $sessionSize,
                    'session_keys' => array_keys($sessionData),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return $response;
    }
}
