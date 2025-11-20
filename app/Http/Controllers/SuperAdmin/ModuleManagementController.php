<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Detect modules from filesystem
        $filesystemModules = $this->discoverModulesFromFilesystem();
        $discoveredCount = count($filesystemModules);

        return view('roles.superadmin.modules.index', compact('modules', 'filesystemModules', 'discoveredCount'));
    }

    /**
     * Discover modules from filesystem by scanning modules/ directory
     */
    private function discoverModulesFromFilesystem()
    {
        $discovered = [];
        $modulesPath = base_path('modules');

        // Log for debugging
        \Illuminate\Support\Facades\Log::info('Discovering modules from filesystem', [
            'path' => $modulesPath,
            'exists' => is_dir($modulesPath),
            'readable' => is_readable($modulesPath),
        ]);

        if (!is_dir($modulesPath)) {
            \Illuminate\Support\Facades\Log::warning('Modules directory does not exist', ['path' => $modulesPath]);
            return $discovered;
        }

        if (!is_readable($modulesPath)) {
            \Illuminate\Support\Facades\Log::error('Modules directory is not readable', ['path' => $modulesPath]);
            return $discovered;
        }

        // Try glob first, fallback to scandir if glob is disabled
        try {
            $directories = @glob($modulesPath . '/*', GLOB_ONLYDIR);
            if ($directories === false) {
                // glob failed, try scandir
                $directories = [];
                $items = scandir($modulesPath);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') continue;
                    $fullPath = $modulesPath . '/' . $item;
                    if (is_dir($fullPath)) {
                        $directories[] = $fullPath;
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to scan modules directory', [
                'error' => $e->getMessage()
            ]);
            return $discovered;
        }

        \Illuminate\Support\Facades\Log::info('Found directories', [
            'count' => count($directories),
            'directories' => array_map('basename', $directories)
        ]);

        foreach ($directories as $dir) {
            $moduleName = basename($dir);
            $moduleJsonPath = $dir . '/module.json';
            $configPath = $dir . '/Config/config.php';

            $moduleData = [
                'name' => $moduleName,
                'path' => $dir,
                'exists_in_db' => false,
                'metadata' => null,
            ];

            // Check if module.json exists
            if (file_exists($moduleJsonPath)) {
                $json = json_decode(file_get_contents($moduleJsonPath), true);
                if ($json) {
                    $moduleData['metadata'] = $json;
                    $moduleData['name'] = $json['name'] ?? $moduleName;
                    $moduleData['description'] = $json['description'] ?? '';
                    $moduleData['version'] = $json['version'] ?? '1.0.0';
                    $moduleData['alias'] = $json['alias'] ?? Str::slug($moduleName);
                }
            } elseif (file_exists($configPath)) {
                // Fallback to config.php
                $config = include $configPath;
                if (is_array($config)) {
                    $moduleData['metadata'] = $config;
                    $moduleData['name'] = $config['name'] ?? $moduleName;
                    $moduleData['description'] = $config['description'] ?? '';
                    $moduleData['version'] = $config['version'] ?? '1.0.0';
                    $moduleData['alias'] = $config['alias'] ?? Str::slug($moduleName);
                }
            }

            // Check if exists in database
            $existingModule = Module::where('slug', $moduleData['alias'] ?? Str::slug($moduleName))->first();
            $moduleData['exists_in_db'] = !is_null($existingModule);
            $moduleData['db_module'] = $existingModule;

            $discovered[] = $moduleData;
        }

        return $discovered;
    }

    /**
     * Sync modules from filesystem to database
     */
    public function syncFromFilesystem()
    {
        try {
            DB::beginTransaction();
            
            \Illuminate\Support\Facades\Log::info('Starting module sync from filesystem');
            
            $filesystemModules = $this->discoverModulesFromFilesystem();
            
            if (empty($filesystemModules)) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::warning('No modules found in filesystem');
                return redirect()->route('superadmin.modules.index')
                    ->with('warning', 'Tidak ada modul yang ditemukan di direktori modules/. Pastikan direktori modules/ exists dan readable.');
            }
            
            $created = 0;
            $updated = 0;
            $deleted = 0;
            
            // Collect slugs from filesystem
            $filesystemSlugs = [];

            foreach ($filesystemModules as $fsModule) {
                $slug = $fsModule['alias'] ?? Str::slug($fsModule['name']);
                $filesystemSlugs[] = $slug;
                
                // Generate code from slug (uppercase with underscores)
                $code = strtoupper(str_replace('-', '_', $slug));
                
                $moduleData = [
                    'name' => $fsModule['name'],
                    'code' => $code,
                    'slug' => $slug,
                    'description' => $fsModule['description'] ?? 'Module ' . $fsModule['name'],
                    'icon' => 'fa-cube', // Default icon, can be customized later
                ];

                $existingModule = Module::where('slug', $slug)->first();

                if (!$existingModule) {
                    Module::create($moduleData);
                    $created++;
                } else {
                    // Update description if changed
                    if ($existingModule->description !== $moduleData['description']) {
                        $existingModule->update(['description' => $moduleData['description']]);
                        $updated++;
                    }
                }
            }

            // Delete modules that no longer exist in filesystem
            $orphanedModules = Module::whereNotIn('slug', $filesystemSlugs)->get();
            
            foreach ($orphanedModules as $orphanedModule) {
                // Check if module is used by any tenant
                $usedByTenants = $orphanedModule->tenants()->wherePivot('is_active', true)->count();
                
                if ($usedByTenants > 0) {
                    // Don't delete, just mark as inactive or skip
                    \Illuminate\Support\Facades\Log::warning('Module not deleted because it is used by tenants', [
                        'module_id' => $orphanedModule->id,
                        'module_slug' => $orphanedModule->slug,
                        'used_by_tenants' => $usedByTenants
                    ]);
                    continue;
                }
                
                // Safe to delete - not used by any tenant
                $orphanedModule->delete();
                $deleted++;
            }

            DB::commit();

            $message = "Sinkronisasi selesai. Dibuat: {$created}, Diperbarui: {$updated}, Dihapus: {$deleted}";
            
            if ($orphanedModules->count() > $deleted) {
                $skipped = $orphanedModules->count() - $deleted;
                $message .= ". {$skipped} modul tidak dihapus karena masih digunakan oleh tenant.";
            }
            
            return redirect()->route('superadmin.modules.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Module sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Sinkronisasi gagal: ' . $e->getMessage());
        }
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
            // Generate code from slug (uppercase with underscores)
            $code = strtoupper(str_replace('-', '_', $slug));

            Module::create([
                'name' => $request->name,
                'code' => $code,
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
