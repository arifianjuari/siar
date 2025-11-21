<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleCode = null)
    {
        // Proses request
        $response = $next($request);

        // Jika user terautentikasi, log akses modul
        if (Auth::check() && $moduleCode) {
            $user = Auth::user();

            // Ambil nama modul dari bagian URL jika tidak ada parameter
            if (!$moduleCode) {
                $path = $request->path();
                if (Str::startsWith($path, 'modules/')) {
                    $segments = explode('/', $path);
                    if (count($segments) > 1) {
                        $moduleCode = $segments[1];
                    }
                }
            }

            // Log akses modul
            if ($moduleCode) {
                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'tenant_id' => $user->tenant_id,
                        'module' => $moduleCode,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'method' => $request->method(),
                        'url' => $request->fullUrl()
                    ])
                    ->log("akses modul {$moduleCode}");
            }
        }

        return $response;
    }
}
