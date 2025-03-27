<?php

namespace App\Helpers;

use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Cek apakah user memiliki akses ke suatu modul dan aksi tertentu
     *
     * @param string $moduleCode       Code dari modul yang akan dicek
     * @param string $permission       Permission yang dicek (can_view, can_create, dll)
     * @param bool   $checkModuleActive Apakah perlu cek juga status aktif modul
     * @return bool
     */
    public static function hasPermission($moduleCode, $permission = 'can_view', $checkModuleActive = true)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $tenant = $user->tenant;

        // Cek apakah user memiliki tenant aktif
        if (!$tenant || !$tenant->is_active) {
            return false;
        }

        // Jika perlu cek status modul aktif
        if ($checkModuleActive) {
            // Cek apakah modul aktif untuk tenant ini
            $moduleActive = $tenant->hasModule($moduleCode);
            if (!$moduleActive) {
                return false;
            }
        }

        // Cek apakah user memiliki role
        if (!$user->role) {
            return false;
        }

        // Cek permission di role
        return $user->role->hasPermission($moduleCode, $permission);
    }

    /**
     * Mendapatkan daftar modul yang dapat diakses oleh user saat ini
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAccessibleModules()
    {
        if (!Auth::check()) {
            return collect([]);
        }

        $user = Auth::user();
        $tenant = $user->tenant;

        // Cek apakah user memiliki tenant aktif
        if (!$tenant || !$tenant->is_active) {
            return collect([]);
        }

        // Ambil modul yang aktif untuk tenant ini
        $activeModules = $tenant->activeModules()->get();

        // Filter modul yang dapat diakses (hanya yang user memiliki permission view)
        if ($user->role) {
            return $activeModules->filter(function ($module) use ($user) {
                return $user->role->hasPermission($module->code, 'can_view');
            });
        }

        return collect([]);
    }

    /**
     * Cek apakah user dapat mengakses semua fitur (admin tenant)
     *
     * @return bool
     */
    public static function isAdmin()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Cek apakah user memiliki role dengan slug 'admin'
        return $user->role && $user->role->slug === 'admin';
    }
}
