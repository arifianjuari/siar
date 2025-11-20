<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan data dari berbagai modul
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $tenantId = $user->tenant_id;
            
            // Get period filter
            $period = $request->get('period', 'this_month');
            $periodLabel = $this->getPeriodLabel($period);
            
            // Get date range based on period
            $dateRange = $this->getDateRange($period);
            
            // Cache dashboard data untuk performa optimal (10 menit)
            $cacheKey = "dashboard_data_tenant_{$tenantId}_period_{$period}";
            $dashboardData = Cache::remember($cacheKey, 600, function () use ($tenantId, $dateRange) {
                return [
                    'stats' => $this->collectStatistics($tenantId, $dateRange),
                    'riskStats' => $this->getRiskStatistics($tenantId, $dateRange),
                    'corrStats' => $this->getCorrespondenceStatistics($tenantId, $dateRange),
                    'recentRiskReports' => $this->getRecentRiskReports($tenantId),
                    'recentCorrespondences' => $this->getRecentCorrespondences($tenantId),
                    'recentWorkUnits' => $this->getRecentWorkUnits($tenantId),
                ];
            });
            
            return view('pages.dashboard', array_merge($dashboardData, [
                'periodLabel' => $periodLabel
            ]));
            
        } catch (\Exception $e) {
            Log::error('Error loading dashboard: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            
            // Return view with empty data on error
            return view('pages.dashboard', [
                'stats' => $this->getEmptyStats(),
                'riskStats' => ['low' => 0, 'medium' => 0, 'high' => 0, 'extreme' => 0],
                'corrStats' => ['incoming' => 0, 'outgoing' => 0, 'regulasi' => 0],
                'recentRiskReports' => collect([]),
                'recentCorrespondences' => collect([]),
                'recentWorkUnits' => collect([]),
                'periodLabel' => 'Semua data'
            ]);
        }
    }
    
    /**
     * Collect statistics from various modules
     */
    private function collectStatistics($tenantId, $dateRange)
    {
        $stats = [];
        
        // Risk Reports statistics
        if (class_exists('\Modules\RiskManagement\Models\RiskReport')) {
            $query = \Modules\RiskManagement\Models\RiskReport::where('tenant_id', $tenantId);
            
            if ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }
            
            $stats['risk_reports'] = $query->count();
            $stats['risk_resolved'] = (clone $query)->where('status', 'resolved')->count();
        } else {
            $stats['risk_reports'] = 0;
            $stats['risk_resolved'] = 0;
        }
        
        // Work Units statistics
        if (class_exists('\App\Models\WorkUnit')) {
            $stats['work_units'] = \App\Models\WorkUnit::where('tenant_id', $tenantId)->count();
        } else {
            $stats['work_units'] = 0;
        }
        
        // Correspondence statistics
        if (class_exists('\Modules\Correspondence\Models\Correspondence')) {
            $query = \Modules\Correspondence\Models\Correspondence::where('tenant_id', $tenantId);
            
            if ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }
            
            $stats['correspondence'] = $query->count();
            $stats['correspondence_this_month'] = \Modules\Correspondence\Models\Correspondence::where('tenant_id', $tenantId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        } else {
            $stats['correspondence'] = 0;
            $stats['correspondence_this_month'] = 0;
        }
        
        // Users statistics
        if (class_exists('\App\Models\User')) {
            $stats['users'] = \App\Models\User::where('tenant_id', $tenantId)->count();
        } else {
            $stats['users'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Get risk statistics for chart
     */
    private function getRiskStatistics($tenantId, $dateRange)
    {
        $riskStats = ['low' => 0, 'medium' => 0, 'high' => 0, 'extreme' => 0];
        
        if (class_exists('\Modules\RiskManagement\Models\RiskReport')) {
            $query = \Modules\RiskManagement\Models\RiskReport::where('tenant_id', $tenantId);
            
            if ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }
            
            $risks = $query->select('risk_level', DB::raw('count(*) as total'))
                ->groupBy('risk_level')
                ->get();
            
            foreach ($risks as $risk) {
                $level = strtolower($risk->risk_level);
                if (in_array($level, ['rendah', 'low'])) {
                    $riskStats['low'] = $risk->total;
                } elseif (in_array($level, ['sedang', 'medium'])) {
                    $riskStats['medium'] = $risk->total;
                } elseif (in_array($level, ['tinggi', 'high'])) {
                    $riskStats['high'] = $risk->total;
                } elseif (in_array($level, ['ekstrem', 'extreme'])) {
                    $riskStats['extreme'] = $risk->total;
                }
            }
        }
        
        return $riskStats;
    }
    
    /**
     * Get correspondence statistics for chart
     */
    private function getCorrespondenceStatistics($tenantId, $dateRange)
    {
        $corrStats = ['incoming' => 0, 'outgoing' => 0, 'regulasi' => 0];
        
        if (class_exists('\Modules\Correspondence\Models\Correspondence')) {
            $query = \Modules\Correspondence\Models\Correspondence::where('tenant_id', $tenantId);
            
            if ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }
            
            $letters = $query->select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->get();
            
            foreach ($letters as $letter) {
                $type = strtolower($letter->type);
                if (in_array($type, ['incoming', 'masuk'])) {
                    $corrStats['incoming'] = $letter->total;
                } elseif (in_array($type, ['outgoing', 'keluar'])) {
                    $corrStats['outgoing'] = $letter->total;
                } elseif (in_array($type, ['regulasi', 'regulation'])) {
                    $corrStats['regulasi'] = $letter->total;
                }
            }
        }
        
        return $corrStats;
    }
    
    /**
     * Get recent risk reports
     */
    private function getRecentRiskReports($tenantId)
    {
        if (class_exists('\Modules\RiskManagement\Models\RiskReport')) {
            return \Modules\RiskManagement\Models\RiskReport::where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return collect([]);
    }
    
    /**
     * Get recent correspondences
     */
    private function getRecentCorrespondences($tenantId)
    {
        if (class_exists('\Modules\Correspondence\Models\Correspondence')) {
            return \Modules\Correspondence\Models\Correspondence::where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return collect([]);
    }
    
    /**
     * Get recent work units
     */
    private function getRecentWorkUnits($tenantId)
    {
        if (class_exists('\App\Models\WorkUnit')) {
            return \App\Models\WorkUnit::where('tenant_id', $tenantId)
                ->with('headOfUnit')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return collect([]);
    }
    
    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'this_month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'last_month':
                return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
            case 'this_year':
                return [now()->startOfYear(), now()->endOfYear()];
            case 'all':
            default:
                return null;
        }
    }
    
    /**
     * Get period label
     */
    private function getPeriodLabel($period)
    {
        switch ($period) {
            case 'this_month':
                return 'Bulan Ini';
            case 'last_month':
                return 'Bulan Lalu';
            case 'this_year':
                return 'Tahun Ini';
            case 'all':
            default:
                return 'Semua data';
        }
    }
    
    /**
     * Get empty statistics
     */
    private function getEmptyStats()
    {
        return [
            'risk_reports' => 0,
            'risk_resolved' => 0,
            'work_units' => 0,
            'correspondence' => 0,
            'correspondence_this_month' => 0,
            'users' => 0
        ];
    }
}
