<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TenantManagementController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'roles'])->paginate(10);

        if (request()->ajax()) {
            return response()->json([
                'html' => View::make('roles.superadmin.tenants._table', compact('tenants'))->render()
            ]);
        }

        return view('roles.superadmin.tenants.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'domain' => 'required|string|unique:tenants,domain',
                'database' => 'required|string|unique:tenants,database',
                'is_active' => 'boolean',
                'modules' => 'array',
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_password' => 'required|string|min:8'
            ]);

            $tenant = Tenant::create([
                'name' => $validated['name'],
                'domain' => $validated['domain'],
                'database' => $validated['database'],
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Proses modul yang dipilih
            if ($request->has('modules')) {
                $tenant->modules()->sync($request->modules);
            }

            // Buat admin tenant
            $admin = $tenant->users()->create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => bcrypt($validated['admin_password'])
            ]);

            // Assign role admin
            $admin->assignRole('admin');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tenant berhasil dibuat',
                    'tenant' => $tenant
                ]);
            }

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil dibuat.');
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

            if ($request->ajax() || $request->wantsJson()) {
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
                        'modules' => $tenant->modules->pluck('id')->toArray()
                    ]
                ], 200, ['Content-Type' => 'application/json']);
            }

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', 'Tenant berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422, ['Content-Type' => 'application/json']);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tenant berhasil dihapus'
                ]);
            }

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
