<?php $__env->startSection('title', 'Dashboard Manajemen Risiko'); ?>

<?php $__env->startPush('styles'); ?>
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
    
    .status-filter-btn.draft {
        background-color: #6c757d;
        color: white;
    }
    
    .status-filter-btn.review {
        background-color: #fd7e14;
        color: white;
    }
    
    .status-filter-btn.completed {
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard Manajemen Risiko</h2>
            <p class="text-muted mb-0">Pantau dan analisis laporan risiko</p>
        </div>
        <div>
            <a href="<?php echo e(route('modules.risk-management.risk-reports.create')); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Laporan Baru
            </a>
            <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Daftar Laporan
            </a>
            <?php if(auth()->user()->role && auth()->user()->role->slug === 'tenant-admin'): ?>
            <a href="<?php echo e(route('modules.risk-management.analysis-config')); ?>" class="btn btn-secondary ms-2">
                <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <i class="fas fa-clipboard-list kpi-icon text-primary"></i>
                    <h2 class="kpi-value"><?php echo e($stats['total']); ?></h2>
                    <p class="kpi-label">Total Laporan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-secondary">
                <div class="card-body">
                    <i class="fas fa-file-alt kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['draft']); ?></h2>
                    <p class="kpi-label">Draft</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-warning">
                <div class="card-body">
                    <i class="fas fa-sync kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['review']); ?></h2>
                    <p class="kpi-label">Dalam Tinjauan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-success">
                <div class="card-body">
                    <i class="fas fa-check-circle kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['completed']); ?></h2>
                    <p class="kpi-label">Selesai</p>
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
                                <h4><?php echo e($stats['low_risk']); ?></h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-warning mb-1">Sedang</h6>
                                <h4><?php echo e($stats['medium_risk']); ?></h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-danger mb-1">Tinggi</h6>
                                <h4><?php echo e($stats['high_risk']); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart (Monthly Reports)
    const ctx = document.getElementById('riskReportChart').getContext('2d');
    const monthLabels = <?php echo json_encode($monthLabels, 15, 512) ?>;
    const monthlyData = <?php echo json_encode($monthlyData, 15, 512) ?>;
    
    // Data kuartalan - mengelompokkan data bulanan menjadi 4 kuartal
    const quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
    const quarterlyData = [0, 0, 0, 0]; // 4 kuartal
    
    // Menghitung data kuartalan dari data bulanan
    for (let i = 0; i < monthlyData.length; i++) {
        // Mendapatkan bulan dari label (Jan, Feb, etc.)
        const month = monthLabels[i];
        let quarterIndex;
        
        // Pengelompokan bulan berdasarkan nama bulan yang sebenarnya
        if (['Jan', 'Feb', 'Mar'].includes(month)) {
            quarterIndex = 0; // Q1
        } else if (['Apr', 'Mei', 'Jun'].includes(month)) {
            quarterIndex = 1; // Q2
        } else if (['Jul', 'Agt', 'Sep'].includes(month)) {
            quarterIndex = 2; // Q3
        } else {
            quarterIndex = 3; // Q4 (Okt, Nov, Des)
        }
        
        // Tambahkan data ke kuartal yang sesuai
        quarterlyData[quarterIndex] += monthlyData[i];
    }
    
    // Menghasilkan data untuk tingkat risiko berdasarkan data bulanan
    // Menggunakan proporsi tetap untuk setiap tingkat risiko
    const calculateRiskData = (data) => {
        const highRiskData = [];
        const mediumRiskData = [];
        const lowRiskData = [];
        
        data.forEach(total => {
            if (total === 0) {
                highRiskData.push(0);
                mediumRiskData.push(0);
                lowRiskData.push(0);
            } else {
                // Proporsi untuk setiap tingkat risiko (bisa disesuaikan)
                const highRisk = Math.round(total * 0.2); // 20% risiko tinggi
                const mediumRisk = Math.round(total * 0.5); // 50% risiko sedang
                const lowRisk = total - highRisk - mediumRisk; // sisanya risiko rendah
                
                highRiskData.push(highRisk);
                mediumRiskData.push(mediumRisk);
                lowRiskData.push(lowRisk);
            }
        });
        
        return { highRiskData, mediumRiskData, lowRiskData };
    };
    
    // Hitung data risiko untuk tampilan bulanan dan kuartalan
    const monthlyRiskData = calculateRiskData(monthlyData);
    const quarterlyRiskData = calculateRiskData(quarterlyData);
    
    // Buat Chart dengan data bulanan sebagai default
    let currentLabels = monthLabels;
    let currentHighRiskData = monthlyRiskData.highRiskData;
    let currentMediumRiskData = monthlyRiskData.mediumRiskData;
    let currentLowRiskData = monthlyRiskData.lowRiskData;
    
    const riskReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: currentLabels,
            datasets: [
                {
                    label: 'Risiko Tinggi',
                    data: currentHighRiskData,
                    backgroundColor: '#dc3545',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Sedang',
                    data: currentMediumRiskData,
                    backgroundColor: '#fd7e14',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Rendah',
                    data: currentLowRiskData,
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
                data: [<?php echo e($stats['low_risk']); ?>, <?php echo e($stats['medium_risk']); ?>, <?php echo e($stats['high_risk']); ?>],
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
    
    // Period switch for chart
    const periodButtons = document.querySelectorAll('[data-period]');
    periodButtons.forEach(button => {
        button.addEventListener('click', () => {
            periodButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const period = button.getAttribute('data-period');
            
            // Update chart data berdasarkan period yang dipilih
            if (period === 'monthly') {
                updateChartData(monthLabels, monthlyRiskData.highRiskData, monthlyRiskData.mediumRiskData, monthlyRiskData.lowRiskData);
            } else if (period === 'quarterly') {
                updateChartData(quarterlyLabels, quarterlyRiskData.highRiskData, quarterlyRiskData.mediumRiskData, quarterlyRiskData.lowRiskData);
            }
        });
    });
    
    // Fungsi untuk update chart data
    function updateChartData(labels, highRisk, mediumRisk, lowRisk) {
        riskReportChart.data.labels = labels;
        riskReportChart.data.datasets[0].data = highRisk;
        riskReportChart.data.datasets[1].data = mediumRisk;
        riskReportChart.data.datasets[2].data = lowRisk;
        riskReportChart.update();
    }
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/dashboard.blade.php ENDPATH**/ ?>