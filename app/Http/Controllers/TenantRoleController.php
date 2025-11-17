<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantRoleController extends Controller
{
    private $defaultRoles = [
        'tenant-admin' => 'Tenant Admin',
        'manajemen-strategis' => 'Manajemen Strategis',
        'manajemen-eksekutif' => 'Manajemen Eksekutif',
        'manajemen-operasional' => 'Manajemen Operasional',
        'staf' => 'Staf'
    ];

    public function index(Tenant $tenant)
    {
        $roles = $tenant->roles()->withCount('users')->get();
        return view('roles.superadmin.tenants.roles.index', compact('tenant', 'roles'));
    }

    public function create(Tenant $tenant)
    {
        return view('roles.superadmin.tenants.roles.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'role_slug' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (!array_key_exists($value, $this->defaultRoles)) {
                            $fail('Role tidak valid. Pilih salah satu role yang tersedia.');
                        }
                    },
                    // Pastikan slug unik per tenant (fail fast sebelum query insert)
                    Rule::unique('roles', 'slug')->where(function ($query) use ($tenant) {
                        return $query->where('tenant_id', $tenant->id);
                    }),
                ],
                'description' => 'nullable|string',
                'is_active' => 'required|boolean'
            ]);

            // Cek apakah role sudah ada
            $existingRole = Role::where('tenant_id', $tenant->id)
                ->where('slug', $validated['role_slug'])
                ->first();

            if ($existingRole) {
                return back()->with('error', 'Role ' . $this->defaultRoles[$validated['role_slug']] . ' sudah ada di tenant ini.');
            }

            // Buat role dengan pengaman duplikasi
            $role = Role::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'slug' => $validated['role_slug'],
                ],
                [
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'is_active' => $validated['is_active'],
                ]
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil dibuat',
                    'role' => $role
                ]);
            }

            return redirect()->route('superadmin.tenants.roles.index', $tenant)
                ->with('success', 'Role berhasil dibuat.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Tenant $tenant, Role $role)
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

        return view('roles.superadmin.tenants.roles.edit', compact('tenant', 'role'));
    }

    public function update(Request $request, Tenant $tenant, Role $role)
    {
        try {
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

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'required|boolean'
            ]);

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active']
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil diperbarui',
                    'role' => $role
                ]);
            }

            return redirect()->route('superadmin.tenants.roles.index', $tenant)
                ->with('success', 'Role berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant, Role $role)
    {
        // Pastikan role milik tenant yang benar
        if ($role->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Role tidak ditemukan untuk tenant ini.');
        }

        // Cek apakah role adalah tenant-admin
        if ($role->slug === 'tenant-admin') {
            return redirect()->back()
                ->with('error', 'Role Admin Tenant tidak dapat dihapus.');
        }

        try {
            $role->delete();
            return redirect()->route('superadmin.tenants.roles.index', $tenant)
                ->with('success', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }

    public function editPermissions(Tenant $tenant, Role $role)
    {
        $modules = $tenant->modules;
        $permissions = $role->permissions;

        // Siapkan data permission dalam format yang lebih mudah diakses di view
        $rolePermissions = [];
        foreach ($permissions as $permission) {
            $rolePermissions[$permission->module_id] = $permission;
        }

        return view('roles.superadmin.tenants.roles.permissions', compact('tenant', 'role', 'modules', 'permissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, Tenant $tenant, Role $role)
    {
        try {
            $validated = $request->validate([
                'permissions' => 'array'
            ]);

            // Hapus semua permission yang ada untuk role ini
            $role->permissions()->delete();

            // Tambahkan permission baru
            if ($request->permissions) {
                foreach ($request->permissions as $moduleId => $permissions) {
                    if (isset($permissions['module_id'])) {
                        $role->permissions()->create([
                            'module_id' => $permissions['module_id'],
                            'can_view' => isset($permissions['can_view']) ? (int)$permissions['can_view'] : 0,
                            'can_create' => isset($permissions['can_create']) ? (int)$permissions['can_create'] : 0,
                            'can_edit' => isset($permissions['can_edit']) ? (int)$permissions['can_edit'] : 0,
                            'can_delete' => isset($permissions['can_delete']) ? (int)$permissions['can_delete'] : 0,
                            'can_export' => isset($permissions['can_export']) ? (int)$permissions['can_export'] : 0,
                            'can_import' => isset($permissions['can_import']) ? (int)$permissions['can_import'] : 0,
                        ]);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hak akses berhasil diperbarui'
                ]);
            }

            return redirect()->route('superadmin.tenants.roles.index', $tenant)
                ->with('success', 'Hak akses berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
