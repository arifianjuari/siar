<?php

namespace Modules\ActivityManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ActivityManagement\Models\ActionableItem;
use Modules\ActivityManagement\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionableItemController extends Controller
{
    /**
     * Display a listing of actionable items for an activity.
     */
    public function index($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $actionableItems = $activity->actionableItems()
            ->with(['creator', 'updater'])
            ->orderBy('order')
            ->get();

        return view('activity-management::actionable_items.index', compact('activity', 'actionableItems'));
    }

    /**
     * Store a newly created actionable item.
     */
    public function store(Request $request, $uuid)
    {
        try {
            \Log::info('ActionableItem store request', [
                'uuid' => $uuid,
                'data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            $activity = Activity::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'priority' => 'required|in:low,medium,high,critical',
            ]);

            // Check for duplicate submission (same title + user within 2 seconds)
            $recentDuplicate = $activity->actionableItems()
                ->where('title', $validated['title'])
                ->where('created_by', Auth::id())
                ->where('created_at', '>=', now()->subSeconds(2))
                ->first();
                
            if ($recentDuplicate) {
                \Log::warning('Duplicate submission detected', [
                    'title' => $validated['title'],
                    'user_id' => Auth::id(),
                    'existing_id' => $recentDuplicate->id
                ]);
                
                return response()->json([
                    'message' => 'Item berhasil ditambahkan',
                    'item' => $recentDuplicate->load(['creator', 'updater']),
                    'duplicate_prevented' => true
                ]);
            }

            $maxOrder = $activity->actionableItems()->max('order') ?? 0;

            $actionableItem = $activity->actionableItems()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'priority' => $validated['priority'],
                'status' => 'pending',
                'order' => $maxOrder + 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            \Log::info('ActionableItem created successfully', [
                'item_id' => $actionableItem->id,
                'item_uuid' => $actionableItem->uuid
            ]);

            return response()->json([
                'message' => 'Item berhasil ditambahkan',
                'item' => $actionableItem->load(['creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            \Log::error('ActionableItem store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Gagal menyimpan item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified actionable item.
     */
    public function update(Request $request, $uuid, $itemUuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $item = $activity->actionableItems()->where('uuid', $itemUuid)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $item->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Item berhasil diperbarui',
            'item' => $item->load(['creator', 'updater'])
        ]);
    }

    /**
     * Remove the specified actionable item.
     */
    public function destroy($uuid, $itemUuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $item = $activity->actionableItems()->where('uuid', $itemUuid)->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Item berhasil dihapus']);
    }

    /**
     * Toggle the completion status of an actionable item.
     */
    public function toggle($uuid, $itemUuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        $item = $activity->actionableItems()->where('uuid', $itemUuid)->firstOrFail();

        $newStatus = $item->status === 'completed' ? 'pending' : 'completed';

        $item->update([
            'status' => $newStatus,
            'updated_by' => Auth::id(),
            'completed_at' => $newStatus === 'completed' ? now() : null,
            'completed_by' => $newStatus === 'completed' ? Auth::id() : null,
        ]);

        return response()->json([
            'message' => 'Status item berhasil diperbarui',
            'item' => $item->load(['creator', 'updater', 'completer'])
        ]);
    }
}
