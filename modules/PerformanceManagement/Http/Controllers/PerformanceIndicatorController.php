<?php

namespace Modules\PerformanceManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PerformanceManagement\Models\PerformanceIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        
        $query = PerformanceIndicator::with(['creator'])
            ->where('tenant_id', $tenantId);

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter by measurement type
        if ($request->has('measurement_type') && $request->measurement_type != '') {
            $query->where('measurement_type', $request->measurement_type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $indicators = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('performance-management::indicators.index', compact('indicators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('performance-management::indicators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'measurement_type' => 'required|string',
            'custom_formula' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'category' => 'required|string',
            'is_shared' => 'boolean',
        ]);

        $validated['tenant_id'] = session('tenant_id');
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        PerformanceIndicator::create($validated);

        return redirect()
            ->route('performance-management.indicators.index')
            ->with('success', 'Indikator kinerja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tenantId = session('tenant_id');
        $indicator = PerformanceIndicator::with(['creator', 'updater', 'templates', 'scores'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        return view('performance-management::indicators.show', compact('indicator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenantId = session('tenant_id');
        $indicator = PerformanceIndicator::where('tenant_id', $tenantId)->findOrFail($id);

        return view('performance-management::indicators.edit', compact('indicator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenantId = session('tenant_id');
        $indicator = PerformanceIndicator::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'measurement_type' => 'required|string',
            'custom_formula' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'category' => 'required|string',
            'is_shared' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        $indicator->update($validated);

        return redirect()
            ->route('performance-management.indicators.show', $id)
            ->with('success', 'Indikator kinerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenantId = session('tenant_id');
        $indicator = PerformanceIndicator::where('tenant_id', $tenantId)->findOrFail($id);

        $indicator->delete();

        return redirect()
            ->route('performance-management.indicators.index')
            ->with('success', 'Indikator kinerja berhasil dihapus.');
    }
}
