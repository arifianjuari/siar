<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleCode
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduleCode, string $permission = 'can_view'): Response
    {
        // Jika user belum login, redirect ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Jika superadmin, bypass pemeriksaan
        if ($user->role && $user->role->slug === 'superadmin') {
            return $next($request);
        }

        // Periksa tenant_id dari session
        $tenantId = session('tenant_id');
        if (!$tenantId || $user->tenant_id != $tenantId) {
            Log::warning('Akses ditolak: tenant tidak valid', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'session_tenant_id' => $tenantId,
                'route' => $request->route()->getName()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki akses ke tenant ini'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke tenant ini');
        }

        // Cek dengan helper function
        if (!function_exists('hasModulePermission') || !hasModulePermission($moduleCode, $user, $permission)) {
            Log::warning("Akses ditolak: {$permission} untuk modul {$moduleCode}", [
                'user_id' => $user->id,
                'module' => $moduleCode,
                'permission' => $permission,
                'route' => $request->route()->getName(),
                'ip' => $request->ip()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak memiliki izin untuk operasi ini'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk operasi ini');
        }

        // Tambahan: Periksa langsung di database (double check)
        try {
            $module = Module::where('slug', $moduleCode)->orWhere('code', $moduleCode)->first();

            if ($module && $user->role) {
                $hasDirectPermission = $user->role->modulePermissions()
                    ->where('modules.id', $module->id)
                    ->wherePivot($permission, true)
                    ->exists();

                if (!$hasDirectPermission) {
                    Log::warning("Akses ditolak (double check): {$permission} untuk modul {$moduleCode}", [
                        'user_id' => $user->id,
                        'role_id' => $user->role->id,
                        'module_id' => $module->id,
                        'route' => $request->route()->getName()
                    ]);

                    if ($request->ajax()) {
                        return response()->json(['error' => 'Anda tidak memiliki izin untuk operasi ini'], 403);
                    }

                    return redirect()->route('dashboard')
                        ->with('error', 'Anda tidak memiliki izin untuk operasi ini');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error saat memeriksa izin: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'module' => $moduleCode,
                'permission' => $permission
            ]);
        }

        return $next($request);
    }
}
