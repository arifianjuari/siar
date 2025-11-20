<?php

namespace Modules\ActivityManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ActivityManagement\Models\Activity;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('activity-management', 'can_view')) {
                abort(403, 'Akses tidak diizinkan');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('activity-management', 'can_create')) {
                abort(403, 'Akses tidak diizinkan untuk menambah kegiatan');
            }
            return $next($request);
        })->only(['create', 'store']);

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('activity-management', 'can_edit')) {
                abort(403, 'Akses tidak diizinkan untuk mengedit kegiatan');
            }
            return $next($request);
        })->only(['edit', 'update']);

        $this->middleware(function ($request, $next) {
            if (!PermissionHelper::hasPermission('activity-management', 'can_delete')) {
                abort(403, 'Akses tidak diizinkan untuk menghapus kegiatan');
            }
            return $next($request);
        })->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Activity::query();

        // Search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('category', 'like', "%{$request->search}%");
            });
        }

        // Status filter
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->priority && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // Work unit filter
        if ($request->work_unit_id && $request->work_unit_id != '0') {
            $query->where('work_unit_id', $request->work_unit_id);
        }

        // Category filter
        if ($request->category && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Assigned to me filter
        if ($request->assigned_to_me) {
            $userId = Auth::id();
            $query->whereHas('assignees', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Created by me filter
        if ($request->created_by_me) {
            $query->where('created_by', Auth::id());
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(10);

        $workUnits = \App\Models\WorkUnit::all();
        $categories = Activity::distinct('category')->pluck('category')->toArray();

        return view('activity-management::index', compact('activities', 'workUnits', 'categories'));
    }

    public function create()
    {
        $parentActivities = Activity::where('parent_id', null)->get();
        $workUnits = \App\Models\WorkUnit::all();
        $users = \App\Models\User::where('is_active', true)
            ->where('tenant_id', session('tenant_id'))
            ->get();

        return view('activity-management::create', compact('parentActivities', 'workUnits', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,critical',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'parent_id' => 'nullable|exists:activities,id',
            'user_assignees' => 'nullable|array',
            'user_assignees.*' => 'exists:users,id',
            'work_unit_assignees' => 'nullable|array',
            'work_unit_assignees.*' => 'exists:work_units,id',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['tenant_id'] = session('tenant_id');
        $validated['created_by'] = Auth::id();
        // Set the status to a valid enum value from the schema
        $validated['status'] = 'draft';

        if (!$validated['due_date']) {
            $validated['due_date'] = $validated['end_date'];
        }

        $activity = Activity::create($validated);

        // Create user assignees
        if (!empty($request->user_assignees)) {
            foreach ($request->user_assignees as $userId) {
                \App\Models\ActivityAssignee::create([
                    'activity_id' => $activity->id,
                    'assignee_type' => 'user',
                    'assignee_id' => $userId,
                    'role' => 'responsible',
                    'assigned_by' => Auth::id(),
                ]);
            }
        }

        // Create work unit assignees
        if (!empty($request->work_unit_assignees)) {
            foreach ($request->work_unit_assignees as $unitId) {
                \App\Models\ActivityAssignee::create([
                    'activity_id' => $activity->id,
                    'assignee_type' => 'work_unit',
                    'assignee_id' => $unitId,
                    'role' => 'responsible',
                    'assigned_by' => Auth::id(),
                ]);
            }
        }

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($activity)
            ->withProperties([
                'tenant_id' => session('tenant_id'),
                'old' => null,
                'new' => $validated
            ])
            ->log('created');

        return redirect()->route('activity-management.activities.show', $activity->uuid)
            ->with('success', 'Kegiatan berhasil dibuat');
    }

    public function show($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        return view('activity-management::show', compact('activity'));
    }

    public function edit($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $parentActivities = Activity::where('parent_id', null)
            ->where('id', '!=', $activity->id)
            ->get();
        $workUnits = \App\Models\WorkUnit::all();

        return view('activity-management::edit', compact('activity', 'parentActivities', 'workUnits'));
    }

    public function update(Request $request, $uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,critical',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'parent_id' => 'nullable|exists:activities,id',
            'progress_percentage' => 'required|integer|min:0|max:100'
        ]);

        if (!$validated['due_date']) {
            $validated['due_date'] = $validated['end_date'];
        }

        $oldData = $activity->toArray();
        $activity->update($validated);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($activity)
            ->withProperties([
                'tenant_id' => session('tenant_id'),
                'old' => $oldData,
                'new' => $activity->toArray()
            ])
            ->log('updated');

        return redirect()->route('activity-management.activities.show', $activity->uuid)
            ->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        // Store activity data for logging before deletion
        $activityData = $activity->toArray();

        $activity->delete();

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'tenant_id' => session('tenant_id'),
                'old' => $activityData,
                'new' => null
            ])
            ->log('deleted');

        return redirect()->route('activity-management.activities.index')
            ->with('success', 'Kegiatan berhasil dihapus');
    }

    /**
     * Update activity status
     */
    public function updateStatus(Request $request, $uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:draft,planned,pending,ongoing,completed,cancelled',
            'note' => 'nullable|string'
        ]);

        // Store old data for logging
        $oldData = $activity->toArray();
        $oldStatus = $activity->status;

        // Update status
        $activity->status = $validated['status'];

        // Record who made the status change
        $activity->updated_by = Auth::id();

        // Record completion/cancellation information
        if ($validated['status'] == 'completed' && $oldStatus != 'completed') {
            $activity->completed_at = now();
            $activity->completed_by = Auth::id();

            // Set progress to 100% if completed
            $activity->progress_percentage = 100;
        } elseif ($validated['status'] == 'cancelled' && $oldStatus != 'cancelled') {
            $activity->cancelled_at = now();
            $activity->cancelled_by = Auth::id();
        }

        $activity->save();

        // Log status change
        $logEntry = \App\Models\ActivityStatusLog::create([
            'activity_id' => $activity->id,
            'changed_by' => Auth::id(),
            'log_type' => 'status_changed',
            'from_value' => $oldStatus,
            'to_value' => $validated['status'],
            'note' => $validated['note'] ?? "Status diubah dari {$oldStatus} menjadi {$validated['status']}",
            'created_at' => now()
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($activity)
            ->withProperties([
                'tenant_id' => session('tenant_id'),
                'old' => $oldData,
                'new' => $activity->toArray(),
                'status_log_id' => $logEntry->id
            ])
            ->log('status_updated');

        return redirect()->route('activity-management.activities.show', $activity->uuid)
            ->with('success', 'Status kegiatan berhasil diperbarui');
    }
    
    /**
     * Update activity progress percentage
     */
    public function updateProgress(Request $request, $uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100'
        ]);

        // Store old progress for logging
        $oldProgress = $activity->progress_percentage;

        // Update progress
        $activity->progress_percentage = $validated['progress_percentage'];
        $activity->updated_by = Auth::id();
        $activity->save();

        // Log progress change
        activity()
            ->causedBy(Auth::user())
            ->performedOn($activity)
            ->withProperties([
                'tenant_id' => session('tenant_id'),
                'old_progress' => $oldProgress,
                'new_progress' => $validated['progress_percentage']
            ])
            ->log('progress_updated');

        return response()->json([
            'success' => true,
            'message' => 'Progres berhasil diperbarui menjadi ' . $validated['progress_percentage'] . '%',
            'progress_percentage' => $validated['progress_percentage']
        ]);
    }
}
