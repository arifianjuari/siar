<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModulePermissionMiddleware
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
        // Cek jika user telah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek tenant id dari session
        $tenantId = session('tenant_id');
        if (!$tenantId || $user->tenant_id != $tenantId) {
            abort(403, 'Anda tidak memiliki akses ke tenant ini');
        }

        // Cari modul berdasarkan code
        $module = Module::where('code', $moduleCode)->first();

        if (!$module) {
            abort(404, 'Modul tidak ditemukan');
        }

        // Cek apakah modul aktif untuk tenant saat ini
        $tenantModule = $user->tenant->modules()
            ->where('modules.id', $module->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$tenantModule) {
            abort(403, 'Modul tidak aktif untuk tenant Anda');
        }

        // Cek apakah user memiliki izin untuk modul ini
        if (!$user->hasPermission($module->id, $permission)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses modul ini');
        }

        // Simpan module ke request untuk digunakan di controller
        $request->merge(['module' => $module]);

        return $next($request);
    }
}
