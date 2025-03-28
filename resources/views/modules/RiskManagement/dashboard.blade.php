@extends('layouts.app')

@section('title', 'Dashboard Manajemen Risiko')

@push('styles')
<style>
    .kpi-card {
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        border: none;
        box-shadow: 0 3px 5px rgba(0,0,0,0.05);
    }
    
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    
    .kpi-card .card-body {
        padding: 1rem;
    }
    
    .kpi-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .kpi-label {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-bottom: 0;
    }
    
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    .status-filter-btn {
        border-radius: 30px;
        padding: 0.4rem 1.2rem;
        font-weight: 500;
        border: none;
        margin-right: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        font-size: 0.85rem;
    }
    
    .status-filter-btn:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .status-filter-btn.active {
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.15);
    }
    
    .status-filter-btn.all {
        background-color: #6c757d;
        color: white;
    }
    
    .status-filter-btn.open {
        background-color: #dc3545; 
        color: white;
    }
    
    .status-filter-btn.review {
        background-color: #fd7e14;
        color: white;
    }
    
    .status-filter-btn.resolved {
        background-color: #198754;
        color: white;
    }
    
    .bg-risk-high {
        background: linear-gradient(45deg, #dc3545, #e17982);
        color: white;
    }
    
    .bg-risk-medium {
        background: linear-gradient(45deg, #fd7e14, #ffb380);
        color: white;
    }
    
    .bg-risk-low {
        background: linear-gradient(45deg, #198754, #28a975);
        color: white;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-right: 1.2rem;
        font-size: 0.85rem;
    }
    
    .legend-color {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        margin-right: 0.4rem;
    }
    
    .task-item {
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #f9f9f9;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        border-left: 4px solid #ddd;
        font-size: 0.9rem;
    }
    
    .task-item:hover {
        background-color: #f0f0f0;
        transform: translateX(3px);
    }
    
    .task-item.high {
        border-left-color: #dc3545;
    }
    
    .task-item.medium {
        border-left-color: #fd7e14;
    }
    
    .task-item.low {
        border-left-color: #198754;
    }
    
    .status-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .status-open {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .status-in-review {
        background-color: rgba(253, 126, 20, 0.1);
        color: #fd7e14;
    }
    
    .status-resolved {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
    
    .recent-reports-table th {
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }
    
    .recent-reports-table td {
        vertical-align: middle;
        padding: 0.6rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .report-title {
        font-weight: 500;
    }

    .category-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        background-color: #e9ecef;
        color: #495057;
    }
    
    .risk-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .risk-high {
        background-color: #dc3545;
        color: white;
    }
    
    .risk-medium {
        background-color: #fd7e14;
        color: white;
    }
    
    .risk-low {
        background-color: #198754;
        color: white;
    }
    
    .container-fluid {
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    h2 {
        font-size: 1.6rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
    
    h4 {
        font-size: 1.3rem;
    }
    
    h6 {
        font-size: 0.8rem;
    }
    
    .card-header {
        padding: 0.75rem 1rem;
    }
    
    .row {
        margin-bottom: 0.75rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
    
    .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .py-4 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard Manajemen Risiko</h2>
        <div>
            <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Laporan Baru
            </a>
            <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Daftar Laporan
            </a>
            @if(auth()->user()->role && auth()->user()->role->slug === 'tenant-admin')
            <a href="{{ route('modules.risk-management.analysis-config') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
            </a>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fw-bold">Statistik dan Analisis</h2>
            <p class="text-muted mb-0">Pantau dan analisis laporan risiko</p>
        </div>
        <div class="d-flex">
            <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus-circle me-1"></i> Buat Laporan Baru
            </a>
            <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i> Semua Laporan
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <i class="fas fa-clipboard-list kpi-icon text-primary"></i>
                    <h2 class="kpi-value">{{ $stats['total'] }}</h2>
                    <p class="kpi-label">Total Laporan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-risk-high">
                <div class="card-body">
                    <i class="fas fa-exclamation-circle kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['open'] }}</h2>
                    <p class="kpi-label">Laporan Open</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-risk-medium">
                <div class="card-body">
                    <i class="fas fa-sync kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['in_review'] }}</h2>
                    <p class="kpi-label">Dalam Review</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-risk-low">
                <div class="card-body">
                    <i class="fas fa-check-circle kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['resolved'] }}</h2>
                    <p class="kpi-label">Laporan Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Chart -->
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <h5 class="mb-0 fw-bold">Trend Laporan Risiko</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-period="monthly">Bulanan</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="quarterly">Kuartalan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #dc3545;"></div>
                            <span>Risiko Tinggi</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #fd7e14;"></div>
                            <span>Risiko Sedang</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #198754;"></div>
                            <span>Risiko Rendah</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="riskReportChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Risk Level Overview -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-2">
                    <h5 class="mb-0 fw-bold">Ringkasan Tingkat Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-2">
                        <canvas id="riskLevelChart" style="max-height: 200px;"></canvas>
                    </div>
                    
                    <div class="row mt-3 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-success mb-1">Rendah</h6>
                                <h4>{{ $stats['low_risk'] }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-warning mb-1">Sedang</h6>
                                <h4>{{ $stats['medium_risk'] }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-danger mb-1">Tinggi</h6>
                                <h4>{{ $stats['high_risk'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <h5 class="mb-0 fw-bold">Laporan Terbaru</h5>
            <div class="d-flex">
                <button class="status-filter-btn all active" data-filter="all">Semua</button>
                <button class="status-filter-btn open" data-filter="open">Open</button>
                <button class="status-filter-btn review" data-filter="in_review">In Review</button>
                <button class="status-filter-btn resolved" data-filter="resolved">Resolved</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table recent-reports-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">Judul</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Tingkat Risiko</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                        <tr class="report-row" data-status="{{ $report['status'] }}">
                            <td class="ps-3 report-title">{{ $report['title'] }}</td>
                            <td><span class="category-badge">{{ $report['category'] }}</span></td>
                            <td>
                                @if($report['risk_level'] == 'high')
                                <span class="risk-badge risk-high">Tinggi</span>
                                @elseif($report['risk_level'] == 'medium')
                                <span class="risk-badge risk-medium">Sedang</span>
                                @else
                                <span class="risk-badge risk-low">Rendah</span>
                                @endif
                            </td>
                            <td>
                                @if($report['status'] == 'open')
                                <span class="status-badge status-open">Open</span>
                                @elseif($report['status'] == 'in_review')
                                <span class="status-badge status-in-review">In Review</span>
                                @else
                                <span class="status-badge status-resolved">Resolved</span>
                                @endif
                            </td>
                            <td>{{ $report['created_at']->format('d/m/Y') }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('modules.risk-management.risk-reports.show', $report['id']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="py-2 text-center border-top">
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="text-decoration-none" style="font-size: 0.85rem;">
                    <i class="fas fa-arrow-right me-1"></i> Lihat Semua Laporan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart (Monthly Reports)
    const ctx = document.getElementById('riskReportChart').getContext('2d');
    const monthLabels = @json($monthLabels);
    const monthlyData = @json($monthlyData);
    
    // Generate random data for risk levels
    const highRiskData = monthlyData.map(val => Math.floor(val * 0.3));
    const mediumRiskData = monthlyData.map(val => Math.floor(val * 0.5));
    const lowRiskData = monthlyData.map(val => val - highRiskData[monthlyData.indexOf(val)] - mediumRiskData[monthlyData.indexOf(val)]);
    
    const riskReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Risiko Tinggi',
                    data: highRiskData,
                    backgroundColor: '#dc3545',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Sedang',
                    data: mediumRiskData,
                    backgroundColor: '#fd7e14',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Rendah',
                    data: lowRiskData,
                    backgroundColor: '#198754',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
    
    // Risk Level Overview
    const ctxDoughnut = document.getElementById('riskLevelChart').getContext('2d');
    const riskLevelChart = new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            datasets: [{
                data: [{{ $stats['low_risk'] }}, {{ $stats['medium_risk'] }}, {{ $stats['high_risk'] }}],
                backgroundColor: [
                    '#198754',
                    '#fd7e14',
                    '#dc3545'
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Filter reports by status
    const statusButtons = document.querySelectorAll('.status-filter-btn');
    const reportRows = document.querySelectorAll('.report-row');
    
    statusButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');
            
            // Toggle active class on buttons
            statusButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Filter rows
            reportRows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Period switch for chart (just for demo, not actually changing data)
    const periodButtons = document.querySelectorAll('[data-period]');
    periodButtons.forEach(button => {
        button.addEventListener('click', () => {
            periodButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // In a real app, would fetch and update chart data here
            // For this demo, we'll just show a message
            console.log('Would switch to ' + button.getAttribute('data-period') + ' view');
        });
    });
});
</script>
@endpush 