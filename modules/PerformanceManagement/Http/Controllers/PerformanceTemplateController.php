<?php

namespace Modules\PerformanceManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PerformanceManagement\Models\PerformanceTemplate;
use Modules\PerformanceManagement\Models\PerformanceIndicator;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        
        $query = PerformanceTemplate::with(['role', 'indicator'])
            ->where('tenant_id', $tenantId);

        // Filter by role
        if ($request->has('role_id') && $request->role_id != '') {
            $query->where('role_id', $request->role_id);
        }

        $templates = $query->orderBy('role_id')
            ->orderBy('position')
            ->paginate(15);

        $roles = Role::where('tenant_id', $tenantId)->get();

        return view('performance-management::templates.index', compact('templates', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = session('tenant_id');
        $indicators = PerformanceIndicator::where('tenant_id', $tenantId)->get();
        $roles = Role::where('tenant_id', $tenantId)->get();

        return view('performance-management::templates.create', compact('indicators', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'indicator_id' => 'required|exists:performance_indicators,id',
            'weight' => 'required|numeric|min:0|max:100',
            'default_target_value' => 'nullable|numeric',
            'position' => 'nullable|integer',
        ]);

        $validated['tenant_id'] = session('tenant_id');
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // Auto set position if not provided
        if (!isset($validated['position'])) {
            $maxPosition = PerformanceTemplate::where('tenant_id', $validated['tenant_id'])
                ->where('role_id', $validated['role_id'])
                ->max('position') ?? 0;
            $validated['position'] = $maxPosition + 1;
        }

        PerformanceTemplate::create($validated);

        return redirect()
            ->route('performance-management.templates.index')
            ->with('success', 'Template KPI berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tenantId = session('tenant_id');
        $template = PerformanceTemplate::with(['role', 'indicator', 'creator', 'updater'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        return view('performance-management::templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenantId = session('tenant_id');
        $template = PerformanceTemplate::where('tenant_id', $tenantId)->findOrFail($id);
        $indicators = PerformanceIndicator::where('tenant_id', $tenantId)->get();
        $roles = Role::where('tenant_id', $tenantId)->get();

        return view('performance-management::templates.edit', compact('template', 'indicators', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenantId = session('tenant_id');
        $template = PerformanceTemplate::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'indicator_id' => 'required|exists:performance_indicators,id',
            'weight' => 'required|numeric|min:0|max:100',
            'default_target_value' => 'nullable|numeric',
            'position' => 'nullable|integer',
        ]);

        $validated['updated_by'] = Auth::id();

        $template->update($validated);

        return redirect()
            ->route('performance-management.templates.show', $id)
            ->with('success', 'Template KPI berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenantId = session('tenant_id');
        $template = PerformanceTemplate::where('tenant_id', $tenantId)->findOrFail($id);

        $template->delete();

        return redirect()
            ->route('performance-management.templates.index')
            ->with('success', 'Template KPI berhasil dihapus.');
    }
}
