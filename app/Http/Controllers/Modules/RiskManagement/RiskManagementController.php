<?php

namespace App\Http\Controllers\Modules\RiskManagement;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TenantModuleConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiskManagementController extends Controller
{
    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('module:risk-management');
    }

    /**
     * Menampilkan halaman konfigurasi akses analisis risiko
     */
    public function showAnalysisConfig()
    {
        // Hanya tenant admin yang bisa mengakses konfigurasi
        $userRole = auth()->user()->role->slug ?? '';
        $isTenantAdmin = $userRole === 'tenant-admin' ||
            strtolower($userRole) === 'tenant-admin';

        if (!auth()->user()->role || !$isTenantAdmin) {
            return redirect()->route('modules.risk-management.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk mengatur konfigurasi analisis risiko');
        }

        $tenantId = session('tenant_id');
        $roles = Role::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Ambil konfigurasi yang sudah ada jika ada
        $currentConfig = TenantModuleConfig::where('tenant_id', $tenantId)
            ->where('module', 'risk_management')
            ->where('feature', 'risk_analysis')
            ->first();

        return view('modules.RiskManagement.analysis-config', compact('roles', 'currentConfig'));
    }

    /**
     * Menyimpan konfigurasi akses analisis risiko
     */
    public function saveAnalysisConfig(Request $request)
    {
        // Hanya tenant admin yang bisa mengakses konfigurasi
        $userRole = auth()->user()->role->slug ?? '';
        $isTenantAdmin = $userRole === 'tenant-admin' ||
            strtolower($userRole) === 'tenant-admin';

        if (!auth()->user()->role || !$isTenantAdmin) {
            return redirect()->route('modules.risk-management.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk mengatur konfigurasi analisis risiko');
        }

        $request->validate([
            'module' => 'required|string',
            'feature' => 'required|string',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'exists:roles,id',
        ]);

        $tenantId = session('tenant_id');

        try {
            // Cari konfigurasi yang sudah ada atau buat baru
            $config = TenantModuleConfig::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'module' => $request->module,
                    'feature' => $request->feature,
                ],
                [
                    'allowed_roles' => $request->allowed_roles ?? [],
                ]
            );

            return redirect()->route('modules.risk-management.analysis-config')
                ->with('success', 'Konfigurasi akses analisis risiko berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error saving analysis config', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
