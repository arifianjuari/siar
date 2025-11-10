<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantUserController extends Controller
{
    public function index(Tenant $tenant)
    {
        $users = $tenant->users()->paginate(10);
        return view('roles.superadmin.tenants.users.index', compact('tenant', 'users'));
    }

    public function create(Tenant $tenant)
    {
        $tenant->load('roles');

        return view('roles.superadmin.tenants.users.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        $user = $tenant->users()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Tenant $tenant, User $user)
    {
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Pengguna tidak ditemukan di tenant ini.');
        }

        $tenant->load('roles');

        return view('roles.superadmin.tenants.users.edit', compact('tenant', 'user'));
    }

    public function update(Request $request, Tenant $tenant, User $user)
    {
        if ($user->tenant_id !== $tenant->id) {
            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('error', 'Pengguna tidak ditemukan di tenant ini.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()
            ->route('superadmin.tenants.users.index', $tenant)
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('tenant-admin')) {
            return back()->with('error', 'Anda tidak dapat menghapus Tenant Admin.');
        }

        $user->delete();

        return redirect()
            ->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function resetPassword(Request $request, Tenant $tenant, User $user)
    {
        $request->validate([
            'new_password' => ['required', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()
            ->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Password pengguna berhasil direset.');
    }
}
