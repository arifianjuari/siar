<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WorkUnitController extends Controller
{
    /**
     * Display a listing of the work units.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        $search = $request->get('search');
        $unitType = $request->get('unit_type');

        $workUnits = WorkUnit::forTenant($tenantId)
            ->when($search, function ($query) use ($search) {
                return $query->where('unit_name', 'like', "%{$search}%")
                    ->orWhere('unit_code', 'like', "%{$search}%");
            })
            ->when($unitType, function ($query) use ($unitType) {
                return $query->where('unit_type', $unitType);
            })
            ->with(['parent', 'headOfUnit']) // Eager load parent and head of unit
            ->orderBy('order')
            ->paginate(10);

        Log::info('User melihat daftar unit kerja', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => $tenantId
        ]);

        return view('tenant.work_units.index', compact('workUnits'));
    }

    /**
     * Show the form for creating a new work unit.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        // Get parent units for dropdown
        $parentUnits = WorkUnit::forTenant($tenantId)
            ->active()
            ->orderBy('unit_name')
            ->get();

        // Get users for head of unit dropdown
        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('tenant.work_units.create', compact('parentUnits', 'users'));
    }

    /**
     * Store a newly created work unit in storage.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validatedData = $request->validate([
            'unit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('work_units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'unit_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('work_units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'unit_type' => 'required|string|in:medical,non-medical,supporting',
            'head_of_unit_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:work_units,id',
            'is_active' => 'boolean',
            'order' => 'nullable|integer'
        ]);

        // Set default order if not provided
        if (!isset($validatedData['order'])) {
            $maxOrder = WorkUnit::forTenant($tenantId)->max('order');
            $validatedData['order'] = $maxOrder + 1;
        }

        // Add tenant_id
        $validatedData['tenant_id'] = $tenantId;

        $workUnit = WorkUnit::create($validatedData);

        Log::info('User membuat unit kerja baru', [
            'user_id' => auth()->id(),
            'work_unit_id' => $workUnit->id,
            'tenant_id' => $tenantId
        ]);

        return redirect()
            ->route('tenant.work-units.index')
            ->with('success', 'Unit kerja berhasil dibuat!');
    }

    /**
     * Display the specified work unit.
     */
    public function show(WorkUnit $workUnit)
    {
        $tenantId = session('tenant_id');

        // Ensure the work unit belongs to the current tenant
        if ($workUnit->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Load children and parent
        $workUnit->load(['children', 'parent', 'headOfUnit']);

        return view('tenant.work_units.show', compact('workUnit'));
    }

    /**
     * Show the form for editing the specified work unit.
     */
    public function edit(WorkUnit $workUnit)
    {
        $tenantId = session('tenant_id');

        // Ensure the work unit belongs to the current tenant
        if ($workUnit->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Get parent units for dropdown (excluding this unit and its children)
        $parentUnits = WorkUnit::forTenant($tenantId)
            ->active()
            ->where('id', '!=', $workUnit->id)
            ->orderBy('unit_name')
            ->get();

        // Get users for head of unit dropdown
        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('tenant.work_units.edit', compact('workUnit', 'parentUnits', 'users'));
    }

    /**
     * Update the specified work unit in storage.
     */
    public function update(Request $request, WorkUnit $workUnit)
    {
        $tenantId = session('tenant_id');

        // Ensure the work unit belongs to the current tenant
        if ($workUnit->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        $validatedData = $request->validate([
            'unit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('work_units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($workUnit->id)
            ],
            'unit_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('work_units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($workUnit->id)
            ],
            'unit_type' => 'required|string|in:medical,non-medical,supporting',
            'head_of_unit_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:work_units,id',
            'is_active' => 'boolean',
            'order' => 'nullable|integer'
        ]);

        // Prevent circular references in parent-child relationships
        if ($validatedData['parent_id'] && $workUnit->id == $validatedData['parent_id']) {
            return redirect()
                ->back()
                ->with('error', 'Unit kerja tidak dapat menjadi parent dari dirinya sendiri!')
                ->withInput();
        }

        $workUnit->update($validatedData);

        Log::info('User mengubah unit kerja', [
            'user_id' => auth()->id(),
            'work_unit_id' => $workUnit->id,
            'tenant_id' => $tenantId
        ]);

        return redirect()
            ->route('tenant.work-units.index')
            ->with('success', 'Unit kerja berhasil diperbarui!');
    }

    /**
     * Remove the specified work unit from storage.
     */
    public function destroy(WorkUnit $workUnit)
    {
        $tenantId = session('tenant_id');

        // Ensure the work unit belongs to the current tenant
        if ($workUnit->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Check if has children
        if ($workUnit->children()->count() > 0) {
            return redirect()
                ->route('tenant.work-units.index')
                ->with('error', 'Unit kerja tidak dapat dihapus karena memiliki sub-unit!');
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            $workUnitId = $workUnit->id;
            $workUnitName = $workUnit->unit_name;

            $workUnit->delete();

            Log::info('User menghapus unit kerja', [
                'user_id' => auth()->id(),
                'work_unit_id' => $workUnitId,
                'work_unit_name' => $workUnitName,
                'tenant_id' => $tenantId
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.work-units.index')
                ->with('success', 'Unit kerja berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat menghapus unit kerja', [
                'user_id' => auth()->id(),
                'work_unit_id' => $workUnit->id,
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            return redirect()
                ->route('tenant.work-units.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of work units through drag and drop
     */
    public function updateOrder(Request $request)
    {
        $tenantId = session('tenant_id');

        $workUnits = $request->input('workUnits', []);

        foreach ($workUnits as $workUnit) {
            $unit = WorkUnit::find($workUnit['id']);

            // Ensure the work unit belongs to the current tenant
            if ($unit && $unit->tenant_id == $tenantId) {
                $unit->update([
                    'order' => $workUnit['order'],
                    'parent_id' => $workUnit['parent_id'] ?? null
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle the active status of a work unit
     */
    public function toggleStatus(WorkUnit $workUnit)
    {
        $tenantId = session('tenant_id');

        // Ensure the work unit belongs to the current tenant
        if ($workUnit->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        $workUnit->update([
            'is_active' => !$workUnit->is_active
        ]);

        Log::info('User mengubah status unit kerja', [
            'user_id' => auth()->id(),
            'work_unit_id' => $workUnit->id,
            'status' => $workUnit->is_active ? 'active' : 'inactive',
            'tenant_id' => $tenantId
        ]);

        return redirect()
            ->route('tenant.work-units.index')
            ->with('success', 'Status unit kerja berhasil diperbarui!');
    }
}
