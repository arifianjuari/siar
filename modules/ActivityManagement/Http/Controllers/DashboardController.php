<?php

namespace Modules\ActivityManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ActivityManagement\Models\Activity;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard Pengelolaan Kegiatan
     */
    public function index()
    {
        // Mendapatkan statistik kegiatan
        $stats = [
            'total' => Activity::count(),
            'planned' => Activity::where('status', 'planned')->count(),
            'ongoing' => Activity::where('status', 'ongoing')->count(),
            'completed' => Activity::where('status', 'completed')->count(),
            'cancelled' => Activity::where('status', 'cancelled')->count(),
        ];

        // Mendapatkan kegiatan terbaru
        $recentActivities = Activity::with(['workUnit', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Mendapatkan kegiatan prioritas tinggi
        $highPriorityActivities = Activity::with(['workUnit', 'creator'])
            ->where('priority', 'high')
            ->whereIn('status', ['planned', 'ongoing'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Data untuk grafik berdasarkan kategori
        $activitiesByCategory = Activity::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category')
            ->toArray();

        // Data untuk grafik berdasarkan bulan (tahun berjalan)
        $currentYear = date('Y');
        $activitiesByMonth = Activity::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw("SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned"),
            DB::raw("SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing"),
            DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
            DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->get();

        // Format data untuk grafik per kuartal
        $activitiesByQuarter = [];
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $quarter * 3;

            $quarterData = Activity::select(
                DB::raw("SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned"),
                DB::raw("SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing"),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
            )
                ->whereYear('created_at', $currentYear)
                ->whereRaw("MONTH(created_at) BETWEEN $startMonth AND $endMonth")
                ->first();

            $activitiesByQuarter[$quarter] = $quarterData;
        }

        return view('activity-management::dashboard', compact(
            'stats',
            'recentActivities',
            'highPriorityActivities',
            'activitiesByCategory',
            'activitiesByMonth',
            'activitiesByQuarter'
        ));
    }
}
