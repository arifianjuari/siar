<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan daftar aktivitas log
     */
    public function index(Request $request)
    {
        // Inisialisasi query
        $query = Activity::with('causer');

        // Filter berdasarkan tenant jika bukan superadmin
        if (auth()->user()->role && auth()->user()->role->slug !== 'superadmin') {
            $query->where(function ($q) {
                $q->whereJsonContains('properties->tenant_id', auth()->user()->tenant_id)
                    ->orWhere('causer_id', auth()->id());
            });
        }

        // Filter berdasarkan parameter
        if ($request->has('log_name') && $request->log_name) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        if ($request->has('causer_id') && $request->causer_id) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil data aktivitas
        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());

        // Ambil daftar user untuk filter
        $users = User::when(auth()->user()->role->slug !== 'superadmin', function ($q) {
            return $q->where('tenant_id', auth()->user()->tenant_id);
        })->get();

        // Daftar jenis event
        $events = Activity::distinct()
            ->pluck('event')
            ->filter()
            ->values();

        // Daftar nama log
        $logNames = Activity::distinct()
            ->pluck('log_name')
            ->filter()
            ->values();

        return view('activity-logs.index', compact('activities', 'users', 'events', 'logNames'));
    }

    /**
     * Tampilkan detail aktivitas log
     */
    public function show(Activity $activity)
    {
        // Pastikan user hanya bisa melihat activity log miliknya atau tenant-nya
        if (auth()->user()->role->slug !== 'superadmin') {
            if (
                !$activity->properties->has('tenant_id') ||
                $activity->properties['tenant_id'] != auth()->user()->tenant_id
            ) {
                abort(403, 'Anda tidak memiliki akses untuk melihat detail aktivitas ini.');
            }
        }

        return view('activity-logs.show', compact('activity'));
    }

    /**
     * Hapus aktivitas log
     */
    public function destroy(Activity $activity)
    {
        // Superadmin only
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403, 'Anda tidak memiliki izin untuk menghapus log aktivitas.');
        }

        $activity->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', 'Log aktivitas berhasil dihapus.');
    }

    /**
     * Hapus semua aktivitas log yang lebih lama dari X hari
     */
    public function purge(Request $request)
    {
        // Superadmin only
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403, 'Anda tidak memiliki izin untuk menghapus log aktivitas.');
        }

        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $days = $request->days;
        $date = now()->subDays($days);

        // Hapus aktivitas
        $count = Activity::where('created_at', '<', $date)->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', "Berhasil menghapus {$count} log aktivitas yang lebih lama dari {$days} hari.");
    }
}
