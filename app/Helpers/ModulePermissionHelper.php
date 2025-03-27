<?php

namespace App\Helpers;

use App\Models\Module;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ModulePermissionHelper
{
    /**
     * Periksa apakah user memiliki izin terhadap modul tertentu
     *
     * @param  \App\Models\User|int  $user User atau user ID
     * @param  string|int  $moduleCode Kode modul (slug) atau ID modul
     * @param  string  $permission Jenis izin yang diperiksa (can_view, can_create, dll)
     * @return bool
     */
    public static function hasModulePermission($user, $moduleCode, $permission = 'can_view'): bool
    {
        try {
            // Log awal eksekusi fungsi
            $userId = $user instanceof User ? $user->id : ($user ? $user : 'null');
            $userName = $user instanceof User ? $user->name : 'Unknown';

            Log::debug("ModulePermissionHelper::hasModulePermission start", [
                'user' => $userId,
                'user_name' => $userName,
                'module' => $moduleCode,
                'permission' => $permission,
                'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['file'] . ':' . debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['line']
            ]);

            // Jika moduleCode kosong, jangan lanjutkan
            if (empty($moduleCode)) {
                Log::debug("ModulePermissionHelper: moduleCode kosong");
                return false;
            }

            // Jika user adalah ID, dapatkan instance model
            if (!$user instanceof User) {
                $user = User::find($user);

                if (!$user) {
                    Log::debug("ModulePermissionHelper: user tidak ditemukan dengan ID: $userId");
                    return false;
                }
            }

            // Jika moduleCode adalah slug, dapatkan moduleId
            $module = null;

            if (is_string($moduleCode) && !is_numeric($moduleCode)) {
                // Ini adalah slug modul
                $module = Module::where('slug', $moduleCode)->orWhere('code', $moduleCode)->first();

                if (!$module) {
                    Log::debug("ModulePermissionHelper: modul tidak ditemukan dengan slug/code: $moduleCode");
                    return false;
                }

                Log::debug("ModulePermissionHelper: modul ditemukan", [
                    'module_id' => $module->id,
                    'module_name' => $module->name,
                    'module_slug' => $module->slug,
                    'module_code' => $module->code
                ]);
            } else {
                // Ini adalah ID modul
                $module = is_numeric($moduleCode) ? Module::find($moduleCode) : $moduleCode;

                if (!$module || !($module instanceof Module)) {
                    Log::debug("ModulePermissionHelper: modul tidak ditemukan dengan ID: $moduleCode");
                    return false;
                }

                Log::debug("ModulePermissionHelper: modul ditemukan", [
                    'module_id' => $module->id,
                    'module_name' => $module->name,
                    'module_slug' => $module->slug,
                    'module_code' => $module->code
                ]);
            }

            // Pastikan modul memiliki slug
            if (empty($module->slug) && !empty($module->code)) {
                $module->slug = Str::slug($module->code);
                $module->save();
                Log::debug("ModulePermissionHelper: slug modul diupdate", [
                    'module_id' => $module->id,
                    'module_slug' => $module->slug
                ]);
            }

            // Jika user->tenant null, return false
            if (!$user->tenant) {
                Log::debug("ModulePermissionHelper: user tidak memiliki tenant");
                return false;
            }

            // Cek apakah modul aktif untuk tenant saat ini
            $tenantModule = $user->tenant->modules()
                ->where('modules.id', $module->id)
                ->wherePivot('is_active', true)
                ->first();

            if (!$tenantModule) {
                Log::debug("ModulePermissionHelper: modul tidak aktif untuk tenant user", [
                    'tenant_id' => $user->tenant_id,
                    'tenant_name' => $user->tenant->name,
                    'module_id' => $module->id
                ]);
                return false;
            }

            Log::debug("ModulePermissionHelper: modul aktif untuk tenant", [
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant->name,
                'module_id' => $module->id
            ]);

            // Cek secara langsung di database (cara yang lebih handal)
            $pivotRecord = DB::table('role_module_permissions')
                ->where('role_id', $user->role_id)
                ->where('module_id', $module->id)
                ->first();

            if (!$pivotRecord) {
                Log::debug("ModulePermissionHelper: tidak ada record role_module_permissions", [
                    'role_id' => $user->role_id,
                    'module_id' => $module->id
                ]);
                return false;
            }

            $hasPermission = $pivotRecord && $pivotRecord->{$permission} == 1;

            Log::debug("ModulePermissionHelper: pengecekan izin langsung dari database", [
                'role_id' => $user->role_id,
                'module_id' => $module->id,
                'permission' => $permission,
                'value' => $pivotRecord->{$permission} ?? null,
                'has_permission' => $hasPermission ? 'Ya' : 'Tidak'
            ]);

            Log::debug("ModulePermissionHelper::hasModulePermission result: " . ($hasPermission ? 'true' : 'false'), [
                'user' => $userId,
                'module' => $module->id,
                'permission' => $permission
            ]);

            return $hasPermission;
        } catch (\Exception $e) {
            // Log error
            Log::error("ModulePermissionHelper error: " . $e->getMessage(), [
                'exception' => $e,
                'user' => $user instanceof User ? $user->id : $user,
                'module' => $moduleCode,
                'permission' => $permission
            ]);
            return false;
        }
    }
}
