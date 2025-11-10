<?php

namespace App\Http\Controllers\Modules\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Activity;

class UserController extends Controller
{
    /**
     * Inisialisasi controller dan middleware
     */
    public function __construct()
    {
        $this->middleware('module:user-management');
        $this->middleware(function ($request, $next) {
            // Cek apakah user adalah tenant admin
            if (!auth()->user()->role || auth()->user()->role->slug !== 'tenant-admin') {
                Log::warning('Akses ditolak: User bukan tenant admin mencoba mengakses user management', [
                    'user_id' => auth()->id(),
                    'role' => auth()->user()->role ? auth()->user()->role->name : 'No Role',
                    'url' => $request->fullUrl()
                ]);
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses modul user management');
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
        // Cek izin view
        if (!hasModulePermission('user-management', auth()->user(), 'can_view')) {
            Log::warning('Akses ditolak: User mencoba mengakses daftar pengguna tanpa izin', [
                'user_id' => auth()->id(),
                'url' => $request->fullUrl()
            ]);
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk melihat daftar pengguna');
        }

        // Dapatkan tenant saat ini
        $tenantId = session('tenant_id');

        // Otomatis dibatasi oleh tenant_id karena model User menggunakan trait BelongsToTenant
        $users = User::with('role')
            ->where('tenant_id', $tenantId) // Tambahan filter untuk memastikan hanya user di tenant yang sama
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('modules.UserManagement.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Cek izin create
        if (!hasModulePermission('user-management', auth()->user(), 'can_create')) {
            Log::warning('Akses ditolak: User mencoba membuat pengguna baru tanpa izin', [
                'user_id' => auth()->id()
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk membuat pengguna baru');
        }

        // Dapatkan tenant saat ini
        $tenantId = session('tenant_id');

        // Ambil hanya role dalam tenant yang sama, kecuali role superadmin
        $roles = Role::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('slug', '!=', 'superadmin') // Pastikan tidak bisa membuat superadmin
            ->orderBy('name')
            ->get();

        // Ambil work unit yang aktif dalam tenant yang sama
        $workUnits = WorkUnit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('modules.UserManagement.users.create', compact('roles', 'workUnits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Cek izin create
        if (!hasModulePermission('user-management', auth()->user(), 'can_create')) {
            Log::warning('Akses ditolak: User mencoba menyimpan pengguna baru tanpa izin', [
                'user_id' => auth()->id()
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk membuat pengguna baru');
        }

        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'position' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:255',
            'nrp' => 'nullable|string|max:255',
            'supervisor_id' => 'nullable|exists:users,id',
            'employment_status' => 'nullable|in:aktif,resign,cuti,magang'
        ]);

        // Pastikan role yang dipilih masih dalam tenant yang sama
        $role = Role::findOrFail($validated['role_id']);
        if ($role->tenant_id != $tenantId || $role->slug === 'superadmin') {
            return redirect()->back()->withErrors(['role_id' => 'Role tidak valid'])->withInput();
        }

        // Pastikan work unit yang dipilih dalam tenant yang sama jika ada
        if (!empty($validated['work_unit_id'])) {
            $workUnit = WorkUnit::findOrFail($validated['work_unit_id']);
            if ($workUnit->tenant_id != $tenantId) {
                return redirect()->back()->withErrors(['work_unit_id' => 'Unit kerja tidak valid'])->withInput();
            }
        }

        // Pastikan supervisor berada dalam tenant yang sama jika dipilih
        if (!empty($validated['supervisor_id'])) {
            $supervisor = User::findOrFail($validated['supervisor_id']);
            if ($supervisor->tenant_id != $tenantId) {
                return redirect()->back()->withErrors(['supervisor_id' => 'Atasan tidak valid'])->withInput();
            }
        }

        $user = User::create([
            'tenant_id' => $tenantId,
            'role_id' => $validated['role_id'],
            'work_unit_id' => $validated['work_unit_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_by' => auth()->id(),
            'position' => $validated['position'] ?? null,
            'rank' => $validated['rank'] ?? null,
            'nrp' => $validated['nrp'] ?? null,
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'employment_status' => $validated['employment_status'] ?? 'aktif'
        ]);

        return redirect()->route('modules.user-management.users.index')
            ->with('success', 'Pengguna berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Cek izin view
        if (!hasModulePermission('user-management', auth()->user(), 'can_view')) {
            Log::warning('Akses ditolak: User mencoba melihat detail pengguna tanpa izin', [
                'user_id' => auth()->id(),
                'target_user_id' => $id
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk melihat detail pengguna');
        }

        $tenantId = session('tenant_id');
        $user = User::with('role')->findOrFail($id);

        // Pastikan user berada dalam tenant yang sama
        if ($user->tenant_id != $tenantId) {
            Log::warning('Akses ditolak: User mencoba melihat detail pengguna dari tenant lain', [
                'user_id' => auth()->id(),
                'target_user_id' => $id,
                'target_tenant_id' => $user->tenant_id,
                'current_tenant_id' => $tenantId
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Pengguna tidak ditemukan');
        }

        return view('modules.UserManagement.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Cek izin edit
        if (!hasModulePermission('user-management', auth()->user(), 'can_edit')) {
            Log::warning('Akses ditolak: User mencoba mengedit pengguna tanpa izin', [
                'user_id' => auth()->id(),
                'target_user_id' => $id
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk mengedit pengguna');
        }

        $tenantId = session('tenant_id');
        $user = User::findOrFail($id);

        // Pastikan user berada dalam tenant yang sama
        if ($user->tenant_id != $tenantId) {
            Log::warning('Akses ditolak: User mencoba mengedit pengguna dari tenant lain', [
                'user_id' => auth()->id(),
                'target_user_id' => $id,
                'target_tenant_id' => $user->tenant_id,
                'current_tenant_id' => $tenantId
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Pengguna tidak ditemukan');
        }

        // Ambil hanya role dalam tenant yang sama, kecuali role superadmin
        $roles = Role::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('slug', '!=', 'superadmin') // Pastikan tidak bisa mengubah ke role superadmin
            ->orderBy('name')
            ->get();

        // Ambil work unit yang aktif dalam tenant yang sama
        $workUnits = WorkUnit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('modules.UserManagement.users.edit', compact('user', 'roles', 'workUnits'));
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
        // Cek izin edit
        if (!hasModulePermission('user-management', auth()->user(), 'can_edit')) {
            Log::warning('Akses ditolak: User mencoba update pengguna tanpa izin', [
                'user_id' => auth()->id(),
                'target_user_id' => $id
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk mengedit pengguna');
        }

        $tenantId = session('tenant_id');
        $user = User::findOrFail($id);

        // Pastikan user berada dalam tenant yang sama
        if ($user->tenant_id != $tenantId) {
            Log::warning('Akses ditolak: User mencoba update pengguna dari tenant lain', [
                'user_id' => auth()->id(),
                'target_user_id' => $id,
                'target_tenant_id' => $user->tenant_id,
                'current_tenant_id' => $tenantId
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Pengguna tidak ditemukan');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'position' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:255',
            'nrp' => 'nullable|string|max:255',
            'supervisor_id' => 'nullable|exists:users,id',
            'employment_status' => 'nullable|in:aktif,resign,cuti,magang'
        ]);

        // Pastikan role yang dipilih masih dalam tenant yang sama
        $role = Role::findOrFail($validated['role_id']);
        if ($role->tenant_id != $tenantId || $role->slug === 'superadmin') {
            return redirect()->back()->withErrors(['role_id' => 'Role tidak valid'])->withInput();
        }

        // Pastikan work unit yang dipilih dalam tenant yang sama jika ada
        if (!empty($validated['work_unit_id'])) {
            $workUnit = WorkUnit::findOrFail($validated['work_unit_id']);
            if ($workUnit->tenant_id != $tenantId) {
                return redirect()->back()->withErrors(['work_unit_id' => 'Unit kerja tidak valid'])->withInput();
            }
        }

        // Pastikan supervisor berada dalam tenant yang sama dan bukan diri sendiri
        if (!empty($validated['supervisor_id'])) {
            if ($validated['supervisor_id'] == $user->id) {
                return redirect()->back()->withErrors(['supervisor_id' => 'Pengguna tidak bisa menjadi atasan diri sendiri'])->withInput();
            }

            $supervisor = User::findOrFail($validated['supervisor_id']);
            if ($supervisor->tenant_id != $tenantId) {
                return redirect()->back()->withErrors(['supervisor_id' => 'Atasan tidak valid'])->withInput();
            }
        }

        $user->role_id = $validated['role_id'];
        $user->work_unit_id = $validated['work_unit_id'] ?? null;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $request->has('is_active') ? 1 : 0;
        $user->updated_by = auth()->id();
        $user->position = $validated['position'] ?? null;
        $user->rank = $validated['rank'] ?? null;
        $user->nrp = $validated['nrp'] ?? null;
        $user->supervisor_id = $validated['supervisor_id'] ?? null;
        $user->employment_status = $validated['employment_status'] ?? 'aktif';

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('modules.user-management.users.index')
            ->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Cek izin delete - PENGECEKAN GANDA
        $moduleCode = 'user-management';
        $permission = 'can_delete';
        $user = auth()->user();

        // 1. Cek lewat helper function
        if (!hasModulePermission($moduleCode, $user, $permission)) {
            Log::warning('Akses ditolak: User mencoba menghapus pengguna tanpa izin (cek 1)', [
                'user_id' => auth()->id(),
                'target_user_id' => $id,
                'method' => 'destroy',
                'ip' => request()->ip()
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk menghapus pengguna');
        }

        $tenantId = session('tenant_id');
        $targetUser = User::findOrFail($id);

        // Pastikan user berada dalam tenant yang sama
        if ($targetUser->tenant_id != $tenantId) {
            Log::warning('Akses ditolak: User mencoba menghapus pengguna dari tenant lain', [
                'user_id' => auth()->id(),
                'target_user_id' => $id,
                'target_tenant_id' => $targetUser->tenant_id,
                'current_tenant_id' => $tenantId
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Pengguna tidak ditemukan');
        }

        // Pastikan tidak menghapus diri sendiri
        if ($targetUser->id === auth()->id()) {
            Log::warning('Akses ditolak: User mencoba menghapus dirinya sendiri', [
                'user_id' => auth()->id(),
                'target_user_id' => $id
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        // 2. Cek langsung di database (double check)
        try {
            $module = Module::where('slug', $moduleCode)->first();
            if (!$module) {
                Log::warning('Akses ditolak: User mencoba menghapus pengguna tanpa izin (modul tidak ditemukan)', [
                    'user_id' => auth()->id(),
                    'target_user_id' => $id,
                    'module_code' => $moduleCode
                ]);
                return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk menghapus pengguna');
            }

            $hasPermission = DB::table('role_module_permissions')
                ->where('role_id', $user->role_id)
                ->where('module_id', $module->id)
                ->where($permission, 1)
                ->exists();

            if (!$hasPermission) {
                Log::warning('Akses ditolak: User mencoba menghapus pengguna tanpa izin (cek 2)', [
                    'user_id' => auth()->id(),
                    'target_user_id' => $id,
                    'role_id' => $user->role_id,
                    'module_id' => $module->id,
                    'permission' => $permission
                ]);
                return redirect()->route('modules.user-management.users.index')->with('error', 'Anda tidak memiliki izin untuk menghapus pengguna');
            }
        } catch (\Exception $e) {
            Log::error('Error cek izin delete: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'target_user_id' => $id
            ]);
            return redirect()->route('modules.user-management.users.index')->with('error', 'Terjadi kesalahan saat menghapus pengguna');
        }

        $targetUser->delete();

        return redirect()->route('modules.user-management.users.index')
            ->with('success', 'Pengguna berhasil dihapus');
    }
}
