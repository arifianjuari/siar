<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;

class ModuleManagementController extends Controller
{
    /**
     * Display a listing of the modules.
     */
    public function index()
    {
        $modules = Module::withCount(['tenants' => function ($query) {
            $query->where('tenant_modules.is_active', true);
        }])->paginate(10);

        return view('roles.superadmin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        return view('roles.superadmin.modules.create');
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:modules,name',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generate slug dari nama
            $slug = Str::slug($request->name);

            Module::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $request->icon,
            ]);

            return redirect()->route('superadmin.modules.index')
                ->with('success', 'Modul berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module)
    {
        $module->load(['tenants' => function ($query) {
            $query->where('tenant_modules.is_active', true);
        }]);

        $totalTenants = Tenant::count();
        $activeInTenantCount = $module->tenants->count();
        $percentageActive = $totalTenants > 0 ? round(($activeInTenantCount / $totalTenants) * 100) : 0;

        return view('roles.superadmin.modules.show', compact('module', 'totalTenants', 'activeInTenantCount', 'percentageActive'));
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        return view('roles.superadmin.modules.edit', compact('module'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, Module $module)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('modules')->ignore($module->id)],
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update module - slug tidak diubah untuk menjaga konsistensi routing
            $module->update([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
            ]);

            return redirect()->route('superadmin.modules.index')
                ->with('success', 'Modul berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        try {
            // Cek apakah modul digunakan oleh tenant
            $usedCount = $module->tenants()->wherePivot('is_active', true)->count();

            if ($usedCount > 0) {
                return redirect()->back()
                    ->with('error', 'Modul tidak dapat dihapus karena masih digunakan oleh ' . $usedCount . ' tenant.');
            }

            // Delete module
            $module->delete();

            return redirect()->route('superadmin.modules.index')
                ->with('success', 'Modul berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Activate module for all tenants
     */
    public function activateForAll(Module $module)
    {
        try {
            $tenants = Tenant::all();

            foreach ($tenants as $tenant) {
                // Tambahkan modul ke tenant jika belum ada, atau update status jika sudah ada
                if ($tenant->modules()->where('module_id', $module->id)->exists()) {
                    $tenant->modules()->updateExistingPivot($module->id, ['is_active' => true]);
                } else {
                    $tenant->modules()->attach($module->id, ['is_active' => true]);
                }
            }

            return redirect()->back()
                ->with('success', 'Modul berhasil diaktifkan untuk semua tenant.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate module for all tenants
     */
    public function deactivateForAll(Module $module)
    {
        try {
            $tenants = Tenant::all();

            foreach ($tenants as $tenant) {
                if ($tenant->modules()->where('module_id', $module->id)->exists()) {
                    $tenant->modules()->updateExistingPivot($module->id, ['is_active' => false]);
                }
            }

            return redirect()->back()
                ->with('success', 'Modul berhasil dinonaktifkan untuk semua tenant.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyetujui permintaan modul dari Admin RS
     */
    public function approveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'module_id' => 'required|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Data permintaan tidak valid.')
                ->withErrors($validator);
        }

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            $module = Module::findOrFail($request->module_id);

            // Update status modul menjadi aktif
            try {
                // Coba gunakan kolom approved_at dan approved_by
                $tenant->modules()->updateExistingPivot($module->id, [
                    'is_active' => true,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                // Jika kolom approved_at tidak tersedia, cukup update is_active
                $tenant->modules()->updateExistingPivot($module->id, [
                    'is_active' => true,
                ]);
            }

            // Tambahkan log aktivitas
            ActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'action' => 'approve_module',
                'model_type' => 'Module',
                'model_id' => $module->id,
                'description' => 'Menyetujui permintaan aktivasi modul ' . $module->name . ' untuk tenant ' . $tenant->name,
            ]);

            // Notifikasi ke Admin RS (bisa diimplementasikan di sini)

            return redirect()->back()
                ->with('success', 'Permintaan modul ' . $module->name . ' untuk tenant ' . $tenant->name . ' berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menolak permintaan modul dari Admin RS
     */
    public function rejectRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'module_id' => 'required|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Data permintaan tidak valid.')
                ->withErrors($validator);
        }

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            $module = Module::findOrFail($request->module_id);

            // Hapus relasi modul dengan tenant atau set is_active = false
            try {
                $tenant->modules()->detach($module->id);
            } catch (\Exception $e) {
                // Jika gagal menghapus, ubah status menjadi tidak aktif
                $tenant->modules()->updateExistingPivot($module->id, [
                    'is_active' => false,
                ]);
            }

            // Tambahkan log aktivitas
            ActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'action' => 'reject_module',
                'model_type' => 'Module',
                'model_id' => $module->id,
                'description' => 'Menolak permintaan aktivasi modul ' . $module->name . ' untuk tenant ' . $tenant->name,
            ]);

            // Notifikasi ke Admin RS (bisa diimplementasikan di sini)

            return redirect()->back()
                ->with('success', 'Permintaan modul ' . $module->name . ' untuk tenant ' . $tenant->name . ' berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
