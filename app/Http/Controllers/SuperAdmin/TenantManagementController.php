<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TenantManagementController extends Controller
{
    /**
     * Display a listing of the tenants.
     */
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'roles'])
            ->orderBy('name')
            ->paginate(10);

        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $modules = Module::orderBy('name')->get();

        return view('superadmin.tenants.create', compact('modules'));
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'domain' => 'required|string|max:100|unique:tenants,domain',
            'database' => 'required|string|max:100|unique:tenants,database',
            'is_active' => 'boolean',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
            'admin_name' => 'required|string|max:100',
            'admin_email' => 'required|email|max:100|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'domain' => $request->domain,
                'database' => $request->database,
                'is_active' => $request->has('is_active'),
            ]);

            // Attach modules
            foreach ($request->modules as $moduleId) {
                $tenant->modules()->attach($moduleId, ['is_active' => true]);
            }

            // Create tenant admin role
            $role = Role::create([
                'tenant_id' => $tenant->id,
                'name' => 'Tenant Admin',
                'slug' => 'tenant-admin',
                'description' => 'Administrator untuk tenant ' . $tenant->name,
                'is_active' => true,
            ]);

            // Create admin user
            User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['modules', 'users' => function ($q) {
            $q->withTrashed();
        }, 'roles']);

        $userCount = $tenant->users()->count();
        $activeModules = $tenant->activeModules()->count();
        $adminUsers = $tenant->users()->whereHas('role', function ($q) {
            $q->where('slug', 'tenant-admin');
        })->get();

        return view('superadmin.tenants.show', compact('tenant', 'userCount', 'activeModules', 'adminUsers'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        $modules = Module::orderBy('name')->get();
        $tenant->load('modules');
        $activeModuleIds = $tenant->modules()->wherePivot('is_active', true)->pluck('modules.id')->toArray();

        return view('superadmin.tenants.edit', compact('tenant', 'modules', 'activeModuleIds'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'domain' => ['required', 'string', 'max:100', Rule::unique('tenants')->ignore($tenant->id)],
            'database' => ['required', 'string', 'max:100', Rule::unique('tenants')->ignore($tenant->id)],
            'is_active' => 'boolean',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update tenant
            $tenant->update([
                'name' => $request->name,
                'domain' => $request->domain,
                'database' => $request->database,
                'is_active' => $request->has('is_active'),
            ]);

            // Sync modules
            $syncData = [];
            foreach ($request->modules as $moduleId) {
                $syncData[$moduleId] = ['is_active' => true];
            }

            // Nonaktifkan modul yang tidak dipilih
            $allModuleIds = Module::pluck('id')->toArray();
            $uncheckedModuleIds = array_diff($allModuleIds, $request->modules);

            foreach ($uncheckedModuleIds as $moduleId) {
                $syncData[$moduleId] = ['is_active' => false];
            }

            $tenant->modules()->sync($syncData);

            DB::commit();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant)
    {
        try {
            // Cek apakah tenant ini adalah System tenant (yang berisi superadmin)
            if ($tenant->domain === 'system') {
                return redirect()->back()
                    ->with('error', 'Tenant System tidak dapat dihapus karena berisi user superadmin.');
            }

            // Cek apakah tenant masih memiliki user aktif
            $userCount = $tenant->users()->count();
            if ($userCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tenant tidak dapat dihapus karena masih memiliki ' . $userCount . ' pengguna. Hapus semua pengguna terlebih dahulu.');
            }

            // Delete tenant (akan cascade delete semua data terkait)
            $tenant->delete();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle module activation for a tenant
     */
    public function toggleModule(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $tenant->modules()->updateExistingPivot($request->module_id, [
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status modul berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset password admin tenant
     */
    public function resetAdminPassword(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::where('id', $request->user_id)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();

            // Cek apakah user adalah admin tenant
            if (!$user->hasRole('tenant-admin')) {
                return redirect()->back()
                    ->with('error', 'User bukan merupakan Admin Tenant.');
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->back()
                ->with('success', 'Password admin berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get tenant statistics
     */
    public function statistics()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalModules = Module::count();

        $popularModules = Module::withCount(['tenants' => function ($query) {
            $query->where('tenant_modules.is_active', true);
        }])->orderBy('tenants_count', 'desc')->take(5)->get();

        $recentTenants = Tenant::latest()->take(5)->get();

        return view('superadmin.statistics', compact(
            'totalTenants',
            'activeTenants',
            'totalUsers',
            'totalModules',
            'popularModules',
            'recentTenants'
        ));
    }

    /**
     * Menampilkan form untuk membuat role baru untuk tenant
     */
    public function createRole(Tenant $tenant)
    {
        return view('superadmin.tenants.roles.create', compact('tenant'));
    }

    /**
     * Menyimpan role baru untuk tenant
     */
    public function storeRole(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generate slug dari nama
            $slug = Str::slug($request->name);

            // Pastikan slug unik untuk tenant
            $exists = Role::where('tenant_id', $tenant->id)
                ->where('slug', $slug)
                ->exists();

            if ($exists) {
                $slug = $slug . '-' . time();
            }

            // Buat role baru
            Role::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', 'Role berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan form untuk edit role
     */
    public function editRole(Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        return view('superadmin.tenants.roles.edit', compact('tenant', 'role'));
    }

    /**
     * Update role tenant
     */
    public function updateRole(Request $request, Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update role
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', 'Role berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus role tenant
     */
    public function destroyRole(Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        try {
            // Cek apakah role memiliki user
            $userCount = User::where('role_id', $role->id)->count();
            if ($userCount > 0) {
                return redirect()->route('superadmin.tenants.show', $tenant)
                    ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh ' . $userCount . ' pengguna.');
            }

            // Cek apakah role adalah tenant-admin
            if ($role->slug === 'tenant-admin') {
                return redirect()->route('superadmin.tenants.show', $tenant)
                    ->with('error', 'Role Admin Tenant tidak dapat dihapus.');
            }

            $role->delete();

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit hak akses modul untuk role
     */
    public function editRolePermissions(Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        // Ambil semua modul yang aktif untuk tenant ini
        $modules = $tenant->activeModules()->get();

        // Ambil permission yang sudah ada
        $rolePermissions = \App\Models\RoleModulePermission::where('role_id', $role->id)
            ->get()
            ->keyBy('module_id');

        return view('superadmin.tenants.roles.permissions', compact('tenant', 'role', 'modules', 'rolePermissions'));
    }

    /**
     * Update hak akses modul untuk role
     */
    public function updateRolePermissions(Request $request, Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        // Debug: Log request data
        \Illuminate\Support\Facades\Log::info('Update Role Permissions Request', [
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'permissions' => $request->permissions,
            'all_request' => $request->all()
        ]);

        // Modifikasi validasi untuk mengatasi masalah array format
        if (!$request->has('permissions') || !is_array($request->permissions)) {
            return redirect()->back()
                ->with('error', 'Format data permissions tidak valid. Pastikan Anda mencentang setidaknya satu permission.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Hapus semua permission yang ada untuk role ini
            $deleteResult = \App\Models\RoleModulePermission::where('role_id', $role->id)->delete();
            \Illuminate\Support\Facades\Log::info('Deleted existing permissions', [
                'role_id' => $role->id,
                'delete_result' => $deleteResult
            ]);

            // Debug: Log active modules
            $activeModules = $tenant->activeModules()->get();
            \Illuminate\Support\Facades\Log::info('Active modules for tenant', [
                'tenant_id' => $tenant->id,
                'active_modules' => $activeModules->pluck('id')->toArray(),
                'active_modules_details' => $activeModules->toArray()
            ]);

            // Simpan permission baru
            $permissionsCreated = 0;
            $permissionErrors = [];

            foreach ($request->permissions as $moduleId => $permission) {
                // Validasi modul ID
                if (!is_numeric($moduleId) || !isset($permission['module_id'])) {
                    $permissionErrors[] = "Module ID '$moduleId' tidak valid";
                    \Illuminate\Support\Facades\Log::warning('Invalid module_id in permission data', [
                        'module_id_key' => $moduleId,
                        'permission' => $permission
                    ]);
                    continue;
                }

                // Ensure moduleId is numeric
                $moduleId = (int)$moduleId;

                // Debug: Log current module permission
                \Illuminate\Support\Facades\Log::info('Processing module permission', [
                    'module_id' => $moduleId,
                    'permission' => $permission
                ]);

                // Pastikan modul aktif untuk tenant
                $moduleActive = $tenant->modules()
                    ->where('modules.id', $moduleId)
                    ->wherePivot('is_active', true)
                    ->exists();

                // Debug: Log module active status
                \Illuminate\Support\Facades\Log::info('Module active status', [
                    'module_id' => $moduleId,
                    'is_active' => $moduleActive
                ]);

                if ($moduleActive) {
                    // Siapkan data permission
                    $permissionData = [
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => isset($permission['can_view']) ? true : false,
                        'can_create' => isset($permission['can_create']) ? true : false,
                        'can_edit' => isset($permission['can_edit']) ? true : false,
                        'can_delete' => isset($permission['can_delete']) ? true : false,
                        'can_export' => isset($permission['can_export']) ? true : false,
                        'can_import' => isset($permission['can_import']) ? true : false,
                    ];

                    // Log data yang akan dibuat
                    \Illuminate\Support\Facades\Log::info('Creating permission with data', $permissionData);

                    $rolePermission = \App\Models\RoleModulePermission::create($permissionData);
                    $permissionsCreated++;

                    // Debug: Log created permission
                    \Illuminate\Support\Facades\Log::info('Created role permission', [
                        'id' => $rolePermission->id,
                        'role_id' => $rolePermission->role_id,
                        'module_id' => $rolePermission->module_id
                    ]);
                } else {
                    $permissionErrors[] = "Module ID '$moduleId' tidak aktif untuk tenant ini";
                }
            }

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Role permissions updated successfully', [
                'role_id' => $role->id,
                'permissions_created' => $permissionsCreated
            ]);

            // Jika ada error tapi proses tetap berhasil, tampilkan warning
            if (!empty($permissionErrors)) {
                return redirect()->route('superadmin.tenants.roles.permissions.edit', [$tenant, $role])
                    ->with('warning', 'Hak akses berhasil diperbarui dengan beberapa peringatan: ' . implode(', ', $permissionErrors));
            }

            return redirect()->route('superadmin.tenants.roles.permissions.edit', [$tenant, $role])
                ->with('success', 'Hak akses role berhasil diperbarui. Total ' . $permissionsCreated . ' permission ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Debug: Log error
            \Illuminate\Support\Facades\Log::error('Error updating role permissions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui hak akses: ' . $e->getMessage())
                ->withInput();
        }
    }
}
