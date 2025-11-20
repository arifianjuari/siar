<?php

namespace Modules\PerformanceManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PerformanceManagement\Models\PerformanceIndicator;
use Modules\PerformanceManagement\Models\PerformanceScore;
use Modules\PerformanceManagement\Models\PerformanceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard Performance Management
     */
    public function index()
    {
        $tenantId = session('tenant_id');
        
        // Statistik umum
        $stats = [
            'total_indicators' => PerformanceIndicator::where('tenant_id', $tenantId)->count(),
            'total_templates' => PerformanceTemplate::where('tenant_id', $tenantId)->count(),
            'total_scores' => PerformanceScore::where('tenant_id', $tenantId)->count(),
            'average_score' => PerformanceScore::where('tenant_id', $tenantId)->avg('score') ?? 0,
        ];

        // Indikator terbaru
        $recentIndicators = PerformanceIndicator::with(['creator'])
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Distribusi nilai per kategori
        $scoresByCategory = PerformanceScore::select('performance_indicators.category', DB::raw('AVG(performance_scores.score) as avg_score'))
            ->join('performance_indicators', 'performance_scores.indicator_id', '=', 'performance_indicators.id')
            ->where('performance_scores.tenant_id', $tenantId)
            ->groupBy('performance_indicators.category')
            ->get()
            ->pluck('avg_score', 'category')
            ->toArray();

        // Distribusi grade
        $gradeDistribution = PerformanceScore::select('grade', DB::raw('count(*) as total'))
            ->where('tenant_id', $tenantId)
            ->groupBy('grade')
            ->get()
            ->pluck('total', 'grade')
            ->toArray();

        return view('performance-management::dashboard', compact(
            'stats',
            'recentIndicators',
            'scoresByCategory',
            'gradeDistribution'
        ));
    }
}
