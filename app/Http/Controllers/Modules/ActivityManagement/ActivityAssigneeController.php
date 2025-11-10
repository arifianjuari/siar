<?php

namespace App\Http\Controllers\Modules\ActivityManagement;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityAssignee;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityAssigneeController extends Controller
{
    /**
     * Display a listing of assignees for an activity.
     */
    public function index($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $currentAssignees = $activity->assignees()->with(['assignee'])->get();

        // Get potential assignees
        $users = User::where('is_active', true)->get();
        $workUnits = WorkUnit::all();

        return view('modules.activity_management.assignees.index', compact(
            'activity',
            'currentAssignees',
            'users',
            'workUnits'
        ));
    }

    /**
     * Store a newly created assignee in storage.
     */
    public function store(Request $request, $uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'assignee_type' => 'required|in:user,work_unit',
            'assignee_id' => 'required|integer',
            'role' => 'required|in:responsible,accountable,consulted,informed',
        ]);

        // Check if assignee already exists
        $exists = $activity->assignees()
            ->where('assignee_type', $validated['assignee_type'])
            ->where('assignee_id', $validated['assignee_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Penugasan sudah ada untuk target yang dipilih.');
        }

        // Create new assignee
        $activity->assignees()->create([
            'assignee_type' => $validated['assignee_type'],
            'assignee_id' => $validated['assignee_id'],
            'role' => $validated['role'],
            'assigned_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Penugasan berhasil ditambahkan.');
    }

    /**
     * Remove the specified assignee.
     */
    public function destroy($uuid, $assigneeId)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $assignee = $activity->assignees()->findOrFail($assigneeId);

        $assignee->delete();

        return redirect()->back()
            ->with('success', 'Penugasan berhasil dihapus.');
    }
}
