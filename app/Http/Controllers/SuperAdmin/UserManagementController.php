<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users in the system.
     */
    public function index(Request $request)
    {
        // Dapatkan semua pengguna dengan relasi tenant dan role
        // Superadmin perlu melihat role dari semua tenant, jadi kita bypass tenant scope
        $query = User::with([
            'tenant',
            'role' => function ($query) {
                $query->withoutGlobalScope('tenant_id');
            }
        ]);

        // Filter berdasarkan tenant
        if ($request->filled('tenant')) {
            $query->where('tenant_id', $request->tenant);
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Filter berdasarkan search term
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Urutkan pengguna
        $query->orderBy('created_at', 'desc');

        // Pagination
        $users = $query->paginate(10);

        // Dapatkan data untuk filter dropdown
        $tenants = Tenant::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('roles.superadmin.users.index', compact('users', 'tenants', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $tenants = Tenant::all();
        return view('roles.superadmin.users.create', compact('roles', 'tenants'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'tenant_id' => $request->tenant_id,
            ]);

            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the user details.
     */
    public function show(User $user)
    {
        $user->load([
            'tenant',
            'role' => function ($query) {
                $query->withoutGlobalScope('tenant_id');
            }
        ]);

        // Dapatkan log aktivitas pengguna
        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('roles.superadmin.users.show', compact('user', 'activityLogs'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $allowedRoles = [
            'Superadmin',
            'Tenant Admin',
            'Manajemen Strategis',
            'Manajemen Eksekutif',
            'Manajemen Operasional',
            'Staf'
        ];
        $roles = \App\Models\Role::whereIn('name', $allowedRoles)->get();
        $tenants = \App\Models\Tenant::all();
        return view('roles.superadmin.users.edit', compact('user', 'roles', 'tenants'));
    }

    /**
     * Update the user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'tenant_id' => $request->tenant_id,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the user from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status of a user.
     */
    public function toggleActive(User $user)
    {
        // Mencegah superadmin menonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // Cek apakah user yang akan dinonaktifkan adalah superadmin
        if ($user->hasRole('superadmin') && $user->is_active) {
            return redirect()->back()
                ->with('error', 'Pengguna superadmin tidak dapat dinonaktifkan karena diperlukan untuk administrasi sistem.');
        }

        try {
            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'mengaktifkan' : 'menonaktifkan';

            // Buat log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => $user->tenant_id,
                'action' => 'toggle_user_status',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => 'Mengubah status pengguna: ' . $user->name . ' - ' . $status,
            ]);

            return redirect()->back()
                ->with('success', 'Status pengguna berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user)
    {
        try {
            // Generate random password
            $newPassword = 'Password' . rand(10000, 99999);

            $user->password = Hash::make($newPassword);
            $user->save();

            // Buat log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => $user->tenant_id,
                'action' => 'reset_password',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => 'Mereset password pengguna: ' . $user->name,
            ]);

            // Kirim notifikasi email bisa diimplementasikan di sini

            return redirect()->back()
                ->with('success', 'Password berhasil direset. Password baru: ' . $newPassword);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
