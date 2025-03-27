<?php

namespace App\Http\Controllers\SuperAdmin;

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
        $query = User::with(['tenant', 'role']);

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

        return view('superadmin.users.index', compact('users', 'tenants', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $tenants = Tenant::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.create', compact('tenants', 'roles'));
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
            'tenant_id' => 'nullable|exists:tenants,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $request->tenant_id,
                'role_id' => $request->role_id,
            ]);

            // Buat log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => $request->tenant_id,
                'action' => 'create_user',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => 'Membuat pengguna baru: ' . $user->name,
            ]);

            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil dibuat.');
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
        $user->load(['tenant', 'role']);

        // Dapatkan log aktivitas pengguna
        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('superadmin.users.show', compact('user', 'activityLogs'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $tenants = Tenant::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('superadmin.users.edit', compact('user', 'tenants', 'roles'));
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
            'tenant_id' => 'nullable|exists:tenants,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'tenant_id' => $request->tenant_id,
                'role_id' => $request->role_id,
            ];

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Buat log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => $request->tenant_id,
                'action' => 'update_user',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => 'Memperbarui pengguna: ' . $user->name,
            ]);

            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');
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
        // Mencegah superadmin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek apakah user yang akan dihapus adalah superadmin
        if ($user->hasRole('superadmin')) {
            return redirect()->back()
                ->with('error', 'Pengguna superadmin tidak dapat dihapus karena diperlukan untuk administrasi sistem.');
        }

        try {
            // Simpan informasi untuk log sebelum pengguna dihapus
            $userName = $user->name;
            $tenantId = $user->tenant_id;

            $user->delete();

            // Buat log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => $tenantId,
                'action' => 'delete_user',
                'model_type' => 'User',
                'description' => 'Menghapus pengguna: ' . $userName,
            ]);

            return redirect()->route('superadmin.users.index')
                ->with('success', 'Pengguna berhasil dihapus.');
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
