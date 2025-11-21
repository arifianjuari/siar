<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Module;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleSlug
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $moduleSlug = null): Response
    {
        // Debug untuk melihat moduleSlug
        Log::info('Mencoba akses modul', [
            'module_slug' => $moduleSlug,
            'user_id' => auth()->id(),
            'tenant_id' => session('tenant_id'),
            'path' => $request->path()
        ]);

        // Jika moduleSlug kosong/tidak valid, tolak akses
        if (empty($moduleSlug)) {
            Log::warning('Akses ditolak: moduleSlug tidak valid');
            if ($request->ajax()) {
                return response()->json(['error' => 'Parameter modul tidak valid'], 400);
            }
            return redirect()->route('dashboard')->with('error', 'Parameter modul tidak valid');
        }

        // Jika user adalah superadmin, bypass checking (with proper tenant validation)
        if (auth()->check() && auth()->user()->isSuperadmin()) {
            Log::info('User adalah superadmin, bypass checking');
            return $next($request);
        }

        // Jika tidak ada tenant_id di session, kembalikan error
        if (!session()->has('tenant_id')) {
            Log::warning('Tenant ID tidak ditemukan di session');
            if ($request->ajax()) {
                return response()->json(['error' => 'Tenant tidak ditemukan'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan. Silakan hubungi administrator.');
        }

        // Cek modul dan izin
        $user = auth()->user();
        if (!$user) {
            Log::warning('User tidak terautentikasi');
            return redirect()->route('login');
        }

        // Cek akses modul - pastikan modul valid
        $module = Module::where('slug', $moduleSlug)->orWhere('code', $moduleSlug)->first();
        if (!$module) {
            Log::warning('Modul tidak ditemukan: ' . $moduleSlug);
            if ($request->ajax()) {
                return response()->json(['error' => 'Modul tidak ditemukan'], 404);
            }
            return redirect()->route('dashboard')->with('error', 'Modul tidak ditemukan');
        }

        // Cek apakah modul aktif untuk tenant
        $tenantModule = $user->tenant->modules()
            ->where('modules.id', $module->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$tenantModule) {
            Log::warning('Modul tidak aktif untuk tenant: ' . $moduleSlug);
            if ($request->ajax()) {
                return response()->json(['error' => 'Modul tidak aktif untuk tenant Anda'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Modul tidak aktif untuk tenant Anda');
        }

        // Cek izin view modul untuk role user
        // 1. Cek melalui fungsi helper
        if (function_exists('hasModulePermission')) {
            $hasAccess = hasModulePermission($moduleSlug, $user, 'can_view');

            Log::info('Memeriksa izin modul', [
                'module_slug' => $moduleSlug,
                'module_id' => $module->id,
                'user_id' => $user->id,
                'role' => $user->role ? $user->role->name . ' (ID: ' . $user->role->id . ')' : 'no-role',
                'has_access' => $hasAccess
            ]);

            if (!$hasAccess) {
                Log::warning('Akses modul ditolak - tidak memiliki izin view', [
                    'module_slug' => $moduleSlug,
                    'user_id' => $user->id,
                    'role' => $user->role ? $user->role->name : 'no-role'
                ]);

                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses modul ini'], 403);
                }

                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses modul ini');
            }
        }
        // 2. Cek langsung melalui database (double check)
        else {
            $hasDirectAccess = $user->role && $user->role->modulePermissions()
                ->where('modules.id', $module->id)
                ->wherePivot('can_view', true)
                ->exists();

            if (!$hasDirectAccess) {
                Log::warning('Akses modul ditolak - cek langsung database', [
                    'module_slug' => $moduleSlug,
                    'user_id' => $user->id,
                    'role' => $user->role ? $user->role->name : 'no-role'
                ]);

                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses modul ini'], 403);
                }

                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses modul ini');
            }
        }

        Log::info('Akses modul diberikan', [
            'module_slug' => $moduleSlug,
            'user_id' => $user->id,
            'path' => $request->path()
        ]);

        // Simpan info modul ke request
        $request->route()->setParameter('module', $module);

        // Log akses ke modul
        $this->logModuleAccess($user, $moduleSlug);

        return $next($request);
    }

    /**
     * Log akses modul
     */
    protected function logModuleAccess($user, $moduleCode)
    {
        try {
            activity()
                ->causedBy($user)
                ->withProperties([
                    'tenant_id' => $user->tenant_id,
                    'module' => $moduleCode,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'method' => request()->method(),
                    'url' => request()->fullUrl()
                ])
                ->log("akses modul {$moduleCode}");
        } catch (\Exception $e) {
            Log::error('Error logging module access: ' . $e->getMessage());
        }
    }
}
