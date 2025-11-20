<?php

namespace Modules\PerformanceManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PerformanceManagement\Models\PerformanceScore;
use Modules\PerformanceManagement\Models\PerformanceIndicator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        
        $query = PerformanceScore::with(['user', 'indicator', 'evaluator'])
            ->where('tenant_id', $tenantId);

        // Filter by period
        if ($request->has('period') && $request->period != '') {
            $query->where('period', $request->period);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by grade
        if ($request->has('grade') && $request->grade != '') {
            $query->where('grade', $request->grade);
        }

        $scores = $query->orderBy('period', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $users = User::where('tenant_id', $tenantId)->get();

        return view('performance-management::scores.index', compact('scores', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = session('tenant_id');
        $indicators = PerformanceIndicator::where('tenant_id', $tenantId)->get();
        $users = User::where('tenant_id', $tenantId)->get();

        return view('performance-management::scores.create', compact('indicators', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'indicator_id' => 'required|exists:performance_indicators,id',
            'target_value' => 'required|numeric',
            'actual_value' => 'required|numeric',
            'weight' => 'required|numeric|min:0|max:100',
            'score' => 'required|numeric|min:0|max:100',
            'grade' => 'required|string',
            'evaluator_id' => 'nullable|exists:users,id',
            'note' => 'nullable|string',
        ]);

        $validated['tenant_id'] = session('tenant_id');
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        PerformanceScore::create($validated);

        return redirect()
            ->route('performance-management.scores.index')
            ->with('success', 'Nilai kinerja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tenantId = session('tenant_id');
        $score = PerformanceScore::with(['user', 'indicator', 'evaluator', 'creator', 'updater'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        return view('performance-management::scores.show', compact('score'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenantId = session('tenant_id');
        $score = PerformanceScore::where('tenant_id', $tenantId)->findOrFail($id);
        $indicators = PerformanceIndicator::where('tenant_id', $tenantId)->get();
        $users = User::where('tenant_id', $tenantId)->get();

        return view('performance-management::scores.edit', compact('score', 'indicators', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenantId = session('tenant_id');
        $score = PerformanceScore::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'indicator_id' => 'required|exists:performance_indicators,id',
            'target_value' => 'required|numeric',
            'actual_value' => 'required|numeric',
            'weight' => 'required|numeric|min:0|max:100',
            'score' => 'required|numeric|min:0|max:100',
            'grade' => 'required|string',
            'evaluator_id' => 'nullable|exists:users,id',
            'note' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $score->update($validated);

        return redirect()
            ->route('performance-management.scores.show', $id)
            ->with('success', 'Nilai kinerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenantId = session('tenant_id');
        $score = PerformanceScore::where('tenant_id', $tenantId)->findOrFail($id);

        $score->delete();

        return redirect()
            ->route('performance-management.scores.index')
            ->with('success', 'Nilai kinerja berhasil dihapus.');
    }
}
