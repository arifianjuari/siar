<?php

use App\Models\Tenant;
use App\Models\User;
use App\Helpers\ModulePermissionHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

if (!function_exists('getCurrentTenant')) {
    /**
     * Get current tenant from session
     *
     * @return \App\Models\Tenant|null
     */
    function getCurrentTenant()
    {
        try {
            $tenant_id = session('tenant_id');
            if ($tenant_id) {
                return Tenant::find($tenant_id);
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return null;
    }
}

if (!function_exists('hasModulePermission')) {
    /**
     * Check if a user has permission for a module
     *
     * @param string|int $moduleCode Module code or ID
     * @param \App\Models\User|int|null $user User or user ID
     * @param string $permission Permission type (can_view, can_create, etc)
     * @return bool
     */
    function hasModulePermission($moduleCode, $user = null, $permission = 'can_view')
    {
        try {
            // Log panggilan fungsi untuk debugging
            Log::debug("Panggilan hasModulePermission", [
                'module' => $moduleCode,
                'user' => $user instanceof \App\Models\User ? $user->id : ($user ? $user : 'null'),
                'permission' => $permission,
                'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['file'] . ':' . debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['line']
            ]);

            if (empty($moduleCode)) {
                Log::debug("hasModulePermission: moduleCode kosong");
                return false;
            }

            if (is_null($user)) {
                if (!auth()->check()) {
                    Log::debug("hasModulePermission: user null dan tidak terautentikasi");
                    return false;
                }
                $user = auth()->user();

                Log::debug("hasModulePermission: menggunakan user dari auth", [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ? $user->role->name . ' (ID: ' . $user->role->id . ')' : 'No role'
                ]);
            }

            if (class_exists('\App\Helpers\ModulePermissionHelper')) {
                $result = \App\Helpers\ModulePermissionHelper::hasModulePermission($user, $moduleCode, $permission);

                // Debug detail hasil pengecekan
                $module = \App\Models\Module::where('slug', $moduleCode)->orWhere('code', $moduleCode)->first();
                $role = $user->role;
                $pivotRecord = null;

                if ($module && $role) {
                    $pivotRecord = DB::table('role_module_permissions')
                        ->where('role_id', $role->id)
                        ->where('module_id', $module->id)
                        ->first();
                }

                Log::debug("hasModulePermission result: " . ($result ? 'true' : 'false'), [
                    'user' => $user instanceof \App\Models\User ? $user->id : $user,
                    'module' => $moduleCode,
                    'module_id' => $module ? $module->id : null,
                    'role_id' => $role ? $role->id : null,
                    'permission' => $permission,
                    'permission_value' => $pivotRecord->{$permission} ?? null,
                    'pivot_record' => $pivotRecord ? 'Ada' : 'Tidak ada'
                ]);

                return $result;
            }

            if (!$user->tenant) {
                Log::debug("hasModulePermission: user tidak memiliki tenant");
                return false;
            }

            // Simplified fallback if ModulePermissionHelper not available
            Log::debug("hasModulePermission: menggunakan fallback (TIDAK AMAN)");
            return true;
        } catch (\Exception $e) {
            // Log error if needed
            Log::error('Error pada hasModulePermission: ' . $e->getMessage(), [
                'moduleCode' => $moduleCode,
                'user_id' => $user instanceof \App\Models\User ? $user->id : $user,
                'permission' => $permission,
                'exception' => $e
            ]);
            return false;
        }
    }
}

if (!function_exists('isTenantActive')) {
    /**
     * Check if tenant is active
     *
     * @param int|null $tenantId Tenant ID
     * @return bool
     */
    function isTenantActive($tenantId = null)
    {
        try {
            if (is_null($tenantId)) {
                $tenant = getCurrentTenant();
            } else {
                $tenant = Tenant::find($tenantId);
            }

            if (!$tenant) {
                return false;
            }

            return $tenant->is_active;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('roleView')) {
    /**
     * Render a view based on user role
     *
     * @param string $view View name
     * @param string|null $fallback Fallback view name
     * @param array $data View data
     * @return \Illuminate\View\View
     */
    function roleView($view, $fallback = null, $data = [])
    {
        $role = auth()->user()->role->slug ?? null;
        $customView = "roles.$role.$view";

        return view()->exists($customView)
            ? view($customView, $data)
            : view($fallback ?? $view, $data);
    }
}
