<?php

namespace App\Policies;

use App\Models\RiskAnalysis;
use App\Models\RiskReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\TenantModuleConfig;
use Illuminate\Support\Facades\Log;

class RiskAnalysisPolicy
{
    use HandlesAuthorization;

    /**
     * Memberikan izin langsung untuk superadmin atau tenant-admin
     */
    public function before(User $user, $ability)
    {
        // Log untuk debug
        Log::info('RiskAnalysisPolicy before check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_slug' => $user->role ? $user->role->slug : 'no_role',
            'ability' => $ability
        ]);

        // Jika superadmin, izinkan semua
        if ($user->role && $user->role->slug === 'superadmin') {
            return true;
        }

        // Jika tenant admin, izinkan juga semua
        if ($user->role && strtolower($user->role->slug) === 'tenant-admin') {
            return true;
        }

        // Tidak bypass, lanjutkan ke kebijakan normal
        return null;
    }

    /**
     * Menentukan apakah user dapat melihat daftar analisis risiko.
     */
    public function viewAny(User $user)
    {
        return $this->hasAnalysisPermission($user, 'can_view');
    }

    /**
     * Menentukan apakah user dapat melihat detail analisis risiko.
     */
    public function view(User $user, RiskAnalysis $analysis)
    {
        return $this->hasAnalysisPermission($user, 'can_view');
    }

    /**
     * Menentukan apakah user dapat membuat analisis risiko baru.
     */
    public function create(User $user, RiskReport $report = null)
    {
        // Log untuk debugging
        Log::info('RiskAnalysisPolicy create check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_slug' => $user->role ? $user->role->slug : 'no_role',
            'report_id' => $report ? $report->id : null
        ]);

        // Periksa akses dasar ke modul analisis
        if (!$this->hasAnalysisPermission($user, 'can_create')) {
            Log::warning('RiskAnalysisPolicy create: denied, no permission', [
                'user_id' => $user->id
            ]);
            return false;
        }

        // Jika laporan sudah memiliki analisis, tidak boleh membuat lagi
        if ($report && $report->analysis) {
            Log::warning('RiskAnalysisPolicy create: denied, report already has analysis', [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'analysis_id' => $report->analysis->id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Menentukan apakah user dapat mengedit analisis risiko.
     */
    public function update(User $user, RiskAnalysis $analysis)
    {
        // Cek permission dasar
        if (!$this->hasAnalysisPermission($user, 'can_edit')) {
            return false;
        }

        // Hanya analis yang membuat analisis atau admin yang dapat mengedit
        $isAnalyst = $analysis->analyzed_by === $user->id;
        $isAdmin = $user->hasRole('tenant-admin') ||
            strtolower($user->role->slug ?? '') === 'tenant-admin';

        return $isAnalyst || $isAdmin;
    }

    /**
     * Menentukan apakah user dapat menghapus analisis risiko.
     */
    public function delete(User $user, RiskAnalysis $analysis)
    {
        // Hanya tenant admin yang dapat menghapus analisis
        $isAdmin = $user->hasRole('tenant-admin') ||
            strtolower($user->role->slug ?? '') === 'tenant-admin';

        return $isAdmin && $this->hasAnalysisPermission($user, 'can_delete');
    }

    /**
     * Fungsi helper untuk memeriksa hak akses fitur analisis
     */
    private function hasAnalysisPermission(User $user, string $permission)
    {
        // Log untuk debugging
        Log::info('RiskAnalysisPolicy hasAnalysisPermission check', [
            'user_id' => $user->id,
            'permission' => $permission,
            'has_tenant' => $user->tenant ? true : false,
            'tenant_active' => $user->tenant ? $user->tenant->is_active : false,
            'has_role' => $user->role ? true : false,
            'role_slug' => $user->role ? $user->role->slug : 'no_role'
        ]);

        // Periksa apakah user memiliki tenant aktif
        if (!$user->tenant || !$user->tenant->is_active) {
            Log::warning('RiskAnalysisPolicy: User tidak memiliki tenant aktif', [
                'user_id' => $user->id
            ]);
            return false;
        }

        // Cek apakah user memiliki role
        if (!$user->role) {
            Log::warning('RiskAnalysisPolicy: User tidak memiliki role', [
                'user_id' => $user->id
            ]);
            return false;
        }

        // Cek permission pada modul Risk Management
        $hasBasePermission = $user->hasPermission('risk-management', $permission);

        Log::info('RiskAnalysisPolicy hasBasePermission check', [
            'user_id' => $user->id,
            'permission' => $permission,
            'has_permission' => $hasBasePermission
        ]);

        if (!$hasBasePermission) {
            return false;
        }

        // Untuk tenant admin, izinkan selalu
        if ($user->role && strtolower($user->role->slug) === 'tenant-admin') {
            return true;
        }

        // Cek konfigurasi khusus untuk fitur analisis (jika ada)
        $config = TenantModuleConfig::where('tenant_id', $user->tenant_id)
            ->where('module', 'risk_management')
            ->where('feature', 'risk_analysis')
            ->first();

        if (!$config) {
            // Jika tidak ada konfigurasi khusus, tentukan peran default yang diizinkan
            $defaultAllowedRoles = ['tenant-admin', 'risk_manager', 'quality_manager'];
            $userRoleSlug = strtolower($user->role->slug ?? '');
            return in_array($userRoleSlug, array_map('strtolower', $defaultAllowedRoles));
        }

        // Log untuk debugging
        Log::info('RiskAnalysisPolicy config check', [
            'user_id' => $user->id,
            'config_id' => $config->id,
            'allowed_roles' => $config->allowed_roles,
            'user_role_id' => $user->role_id
        ]);

        // Periksa apakah role user ada dalam daftar role yang diizinkan
        $allowedRoles = is_array($config->allowed_roles) ? $config->allowed_roles : json_decode($config->allowed_roles);

        return in_array($user->role_id, (array)$allowedRoles);
    }
}
