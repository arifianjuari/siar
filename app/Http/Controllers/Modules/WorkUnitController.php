<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workUnits = WorkUnit::with('headOfUnit')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->paginate(10);

        return view('modules.WorkUnit.index', compact('workUnits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('modules.WorkUnit.form', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateWorkUnit($request);
        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_active'] = true;

        WorkUnit::create($validated);

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $workUnit = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $users = User::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('modules.WorkUnit.form', compact('workUnit', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workUnit = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $this->validateWorkUnit($request, $workUnit->id);

        $workUnit->update($validated);

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workUnit = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        // Cek apakah memiliki unit kerja anak
        if ($workUnit->children()->count() > 0) {
            return redirect()->route('work-units.index')
                ->with('error', 'Unit kerja tidak dapat dihapus karena memiliki sub-unit.');
        }

        $workUnit->delete();

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil dihapus.');
    }

    /**
     * Validasi data unit kerja.
     */
    private function validateWorkUnit(Request $request, $id = null)
    {
        return $request->validate([
            'unit_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('work_units', 'unit_code')
                    ->where('tenant_id', Auth::user()->tenant_id)
                    ->ignore($id)
            ],
            'unit_name' => 'required|string|max:255',
            'unit_type' => 'required|in:medical,non-medical,supporting',
            'head_of_unit_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:work_units,id',
            'order' => 'nullable|integer',
        ]);
    }
}
