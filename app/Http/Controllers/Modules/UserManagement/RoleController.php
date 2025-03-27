<?php

namespace App\Http\Controllers\Modules\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Inisialisasi controller dan middleware
     */
    public function __construct()
    {
        $this->middleware('module:user-management');
        $this->middleware(function ($request, $next) {
            // Cek apakah user adalah tenant admin
            if (!auth()->user()->role || auth()->user()->role->slug !== 'tenant_admin') {
                \Illuminate\Support\Facades\Log::warning('Akses ditolak: User bukan tenant admin mencoba mengakses role management', [
                    'user_id' => auth()->id(),
                    'role' => auth()->user()->role ? auth()->user()->role->name : 'No Role',
                    'url' => $request->fullUrl()
                ]);
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses modul manajemen role');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Roles otomatis difilter berdasarkan tenant karena trait BelongsToTenant
        $roles = Role::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->paginate(10);

        return view('modules.UserManagement.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Ambil modul yang aktif untuk tenant saat ini
        $modules = Module::whereHas('tenants', function ($query) {
            return $query->where('tenants.id', session('tenant_id'))
                ->where('tenant_modules.is_active', true);
        })
            ->orderBy('name')
            ->get();

        return view('modules.UserManagement.roles.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
            'permissions.*.can_export' => 'boolean',
            'permissions.*.can_import' => 'boolean',
        ]);

        // Tambahkan slug dari nama
        $slug = Str::slug($validated['name']);

        // Buat role baru
        $role = Role::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        // Simpan permissions
        foreach ($validated['permissions'] as $moduleId => $permissions) {
            // Pastikan modul aktif untuk tenant ini
            $moduleExists = Module::whereHas('tenants', function ($query) use ($tenantId, $moduleId) {
                return $query->where('tenants.id', $tenantId)
                    ->where('modules.id', $moduleId)
                    ->where('tenant_modules.is_active', true);
            })->exists();

            if ($moduleExists) {
                RoleModulePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_view' => $permissions['can_view'] ?? false,
                    'can_create' => $permissions['can_create'] ?? false,
                    'can_edit' => $permissions['can_edit'] ?? false,
                    'can_delete' => $permissions['can_delete'] ?? false,
                    'can_export' => $permissions['can_export'] ?? false,
                    'can_import' => $permissions['can_import'] ?? false,
                ]);
            }
        }

        return redirect()->route('modules.user-management.roles.index')
            ->with('success', 'Role berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with(['permissions.module'])->findOrFail($id);

        return view('modules.UserManagement.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::with(['permissions'])->findOrFail($id);

        // Ambil modul yang aktif untuk tenant saat ini
        $modules = Module::whereHas('tenants', function ($query) {
            return $query->where('tenants.id', session('tenant_id'))
                ->where('tenant_modules.is_active', true);
        })
            ->orderBy('name')
            ->get();

        // Susun permissions dalam format yang mudah diakses di view
        $rolePermissions = [];
        foreach ($role->permissions as $permission) {
            $rolePermissions[$permission->module_id] = [
                'can_view' => $permission->can_view,
                'can_create' => $permission->can_create,
                'can_edit' => $permission->can_edit,
                'can_delete' => $permission->can_delete,
                'can_export' => $permission->can_export,
                'can_import' => $permission->can_import,
            ];
        }

        return view('modules.UserManagement.roles.edit', compact('role', 'modules', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|exists:modules,id',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
            'permissions.*.can_export' => 'boolean',
            'permissions.*.can_import' => 'boolean',
        ]);

        // Update role
        $role->name = $validated['name'];
        $role->description = $validated['description'];
        $role->is_active = $validated['is_active'] ?? true;
        $role->save();

        // Hapus semua permission yang ada dan buat ulang
        RoleModulePermission::where('role_id', $role->id)->delete();

        // Simpan permissions baru
        foreach ($validated['permissions'] as $moduleId => $permissions) {
            // Pastikan modul aktif untuk tenant ini
            $moduleExists = Module::whereHas('tenants', function ($query) use ($tenantId, $moduleId) {
                return $query->where('tenants.id', $tenantId)
                    ->where('modules.id', $moduleId)
                    ->where('tenant_modules.is_active', true);
            })->exists();

            if ($moduleExists) {
                RoleModulePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_view' => $permissions['can_view'] ?? false,
                    'can_create' => $permissions['can_create'] ?? false,
                    'can_edit' => $permissions['can_edit'] ?? false,
                    'can_delete' => $permissions['can_delete'] ?? false,
                    'can_export' => $permissions['can_export'] ?? false,
                    'can_import' => $permissions['can_import'] ?? false,
                ]);
            }
        }

        return redirect()->route('modules.user-management.roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Cek izin delete
        $moduleCode = 'user-management';
        $permission = 'can_delete';
        $user = auth()->user();

        // Cek dengan helper function
        if (!hasModulePermission($moduleCode, $user, $permission)) {
            \Illuminate\Support\Facades\Log::warning('Akses ditolak: User mencoba menghapus role tanpa izin', [
                'user_id' => auth()->id(),
                'target_role_id' => $id,
                'method' => 'destroy',
                'ip' => request()->ip()
            ]);
            return redirect()->route('modules.user-management.roles.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus role');
        }

        // Cek langsung di database (double check)
        try {
            $module = \App\Models\Module::where('slug', $moduleCode)->first();
            if ($module && $user->role) {
                $hasPermission = \Illuminate\Support\Facades\DB::table('role_module_permissions')
                    ->where('role_id', $user->role->id)
                    ->where('module_id', $module->id)
                    ->where('can_delete', true)
                    ->exists();

                if (!$hasPermission) {
                    \Illuminate\Support\Facades\Log::warning('Akses ditolak: User mencoba menghapus role tanpa izin (cek DB)', [
                        'user_id' => auth()->id(),
                        'role_id' => $user->role->id,
                        'module_id' => $module->id,
                        'target_role_id' => $id
                    ]);
                    return redirect()->route('modules.user-management.roles.index')
                        ->with('error', 'Anda tidak memiliki izin untuk menghapus role');
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat memeriksa izin delete role: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'target_role_id' => $id,
                'exception' => $e
            ]);
        }

        $role = Role::findOrFail($id);

        // Cek apakah masih ada user yang menggunakan role ini
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Role ini masih digunakan oleh {$userCount} pengguna");
        }

        // Hapus permission terlebih dahulu
        RoleModulePermission::where('role_id', $role->id)->delete();

        // Hapus role
        $role->delete();

        return redirect()->route('modules.user-management.roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}
