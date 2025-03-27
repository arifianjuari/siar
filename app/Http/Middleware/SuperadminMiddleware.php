<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user ada dan memiliki role superadmin
        if (!Auth::check()) {
            Log::warning('SuperadminMiddleware: Pengguna tidak terautentikasi');
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $user = auth()->user();
        Log::info('SuperadminMiddleware: Memeriksa akses', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ? ($user->role->name . ' (' . $user->role->slug . ')') : 'Tidak ada role'
        ]);

        if (!$user->role || $user->role->slug !== 'superadmin') {
            Log::warning('SuperadminMiddleware: Akses ditolak - bukan superadmin', [
                'user_id' => $user->id,
                'role' => $user->role ? $user->role->slug : 'null'
            ]);
            // Jika bukan superadmin, redirect ke dashboard dengan pesan error
            return redirect()->route('dashboard.debug')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        Log::info('SuperadminMiddleware: Akses diberikan', [
            'user_id' => $user->id
        ]);

        return $next($request);
    }
}
