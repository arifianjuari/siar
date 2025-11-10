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

        return view('roles.superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $modules = Module::orderBy('name')->get();

        return view('roles.superadmin.tenants.create', compact('modules'));
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
        $users = $tenant->users()->paginate(10);

        return view('roles.superadmin.tenants.show', compact('tenant', 'userCount', 'activeModules', 'adminUsers', 'users'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        $modules = Module::orderBy('name')->get();
        $tenant->load('modules');
        $activeModuleIds = $tenant->modules()->wherePivot('is_active', true)->pluck('modules.id')->toArray();

        return view('roles.superadmin.tenants.edit', compact('tenant', 'modules', 'activeModuleIds'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'domain' => 'required|string|unique:tenants,domain,' . $tenant->id,
                'database' => 'required|string|unique:tenants,database,' . $tenant->id,
                'is_active' => 'boolean',
                'modules' => 'array'
            ]);

            DB::beginTransaction();

            // Update tenant
            $tenant->update([
                'name' => $validated['name'],
                'domain' => $validated['domain'],
                'database' => $validated['database'],
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Update modul
            if ($request->has('modules')) {
                $tenant->modules()->sync($request->modules);
            } else {
                $tenant->modules()->detach();
            }

            // Reload tenant dengan relasi modules
            $tenant->load('modules');

            DB::commit();

            // Jika request AJAX atau expects JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tenant berhasil diperbarui',
                    'data' => [
                        'tenant' => [
                            'id' => $tenant->id,
                            'name' => $tenant->name,
                            'domain' => $tenant->domain,
                            'database' => $tenant->database,
                            'is_active' => $tenant->is_active
                        ],
                        'modules' => $tenant->modules->pluck('id')->toArray(),
                        'redirect_url' => route('superadmin.tenants.index')
                    ]
                ]);
            }

            // Jika request normal
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Jika request AJAX atau expects JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            // Jika request normal
            return back()->with('error', $e->getMessage())->withInput();
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

        return view('roles.superadmin.statistics', compact(
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
        return view('roles.superadmin.tenants.roles.create', compact('tenant'));
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

        return view('roles.superadmin.tenants.roles.edit', compact('tenant', 'role'));
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

        // Cek apakah role adalah tenant-admin
        if ($role->slug === 'tenant-admin') {
            return redirect()->back()
                ->with('error', 'Role Admin Tenant tidak dapat diubah.');
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
            // Cek apakah nama role sudah digunakan di tenant yang sama
            $existingRole = Role::where('tenant_id', $tenant->id)
                ->where('id', '!=', $role->id)
                ->where('name', $request->name)
                ->first();

            if ($existingRole) {
                return redirect()->back()
                    ->with('error', 'Nama role sudah digunakan di tenant ini.')
                    ->withInput();
            }

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

        return view('roles.superadmin.tenants.roles.permissions', compact('tenant', 'role', 'modules', 'rolePermissions'));
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

        // Cek apakah role adalah tenant-admin
        if ($role->slug === 'tenant-admin') {
            return redirect()->back()
                ->with('error', 'Role Admin Tenant tidak dapat diubah hak aksesnya.');
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
            'permissions.*.can_export' => 'boolean',
            'permissions.*.can_import' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Ambil semua modul yang aktif untuk tenant ini
            $activeModules = $tenant->activeModules()
                ->select('modules.id')
                ->pluck('id')
                ->toArray();

            // Hapus semua permission yang ada untuk role ini
            \App\Models\RoleModulePermission::where('role_id', $role->id)->delete();

            // Simpan permission baru
            $permissionsCreated = 0;
            $permissionErrors = [];

            foreach ($request->permissions as $moduleId => $permission) {
                // Pastikan modul aktif untuk tenant
                if (!in_array($moduleId, $activeModules)) {
                    $permissionErrors[] = "Module ID '$moduleId' tidak aktif untuk tenant ini";
                    continue;
                }

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

                // Buat permission baru
                \App\Models\RoleModulePermission::create($permissionData);
                $permissionsCreated++;
            }

            DB::commit();

            // Jika ada error tapi proses tetap berhasil, tampilkan warning
            if (!empty($permissionErrors)) {
                return redirect()->route('superadmin.tenants.roles.permissions.edit', [$tenant, $role])
                    ->with('warning', 'Hak akses berhasil diperbarui dengan beberapa peringatan: ' . implode(', ', $permissionErrors));
            }

            return redirect()->route('superadmin.tenants.roles.permissions.edit', [$tenant, $role])
                ->with('success', 'Hak akses role berhasil diperbarui. Total ' . $permissionsCreated . ' permission ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
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
