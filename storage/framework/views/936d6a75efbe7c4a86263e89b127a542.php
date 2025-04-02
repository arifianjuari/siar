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
    
    .bg-draft {
        background-color: #fd3714;
        color: white;
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
        background-color: #fd7e14;
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
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                <p class="text-muted mb-0 small">Data laporan untuk tahun berjalan <?php echo e(date('Y')); ?></p>
            </div>
        </div>
    </div>
    
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
            <div class="card kpi-card bg-draft">
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
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="yearly">Tahunan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #FF0000;"></div>
                            <span>Risiko Ekstrem</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #FFA500;"></div>
                            <span>Risiko Tinggi</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #FFFF00;"></div>
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
                        <div class="col-3">
                            <div class="p-2 rounded bg-light">
                                <h6 class="text-success mb-1">Rendah</h6>
                                <h4><?php echo e($stats['low_risk'] ?? $stats['rendah'] ?? 0); ?></h4>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2 rounded bg-light">
                                <h6 class="mb-1" style="color: #FFFF00;">Sedang</h6>
                                <h4><?php echo e($stats['medium_risk'] ?? $stats['sedang'] ?? 0); ?></h4>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2 rounded bg-light">
                                <h6 class="mb-1" style="color: #FFA500;">Tinggi</h6>
                                <h4><?php echo e($stats['high_risk'] ?? $stats['tinggi'] ?? 0); ?></h4>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-2 rounded bg-light">
                                <h6 class="mb-1" style="color: #FF0000;">Ekstrem</h6>
                                <h4><?php echo e($stats['extreme_risk'] ?? $stats['ekstrem'] ?? 0); ?></h4>
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
    
    // Debugging data yang diterima
    console.log('Month Labels:', <?php echo json_encode($monthLabels, 15, 512) ?>);
    console.log('Stats:', <?php echo json_encode($stats, 15, 512) ?>);
    
    // Periksa data laporan bulanan berdasarkan kategori
    let extremeRiskMonthlyData = <?php echo json_encode($extremeRiskMonthlyData ?? $ekstremRiskMonthlyData ?? [], 15, 512) ?>;
    let highRiskMonthlyData = <?php echo json_encode($highRiskMonthlyData ?? $tinggiRiskMonthlyData ?? [], 15, 512) ?>;
    let mediumRiskMonthlyData = <?php echo json_encode($mediumRiskMonthlyData ?? $sedangRiskMonthlyData ?? [], 15, 512) ?>;
    let lowRiskMonthlyData = <?php echo json_encode($lowRiskMonthlyData ?? $rendahRiskMonthlyData ?? [], 15, 512) ?>;
    
    // Log tambahan untuk memeriksa data dari backend
    console.log('Data risiko dari backend:');
    console.log('extremeRiskMonthlyData:', <?php echo json_encode($extremeRiskMonthlyData ?? [], 15, 512) ?>);
    console.log('ekstremRiskMonthlyData:', <?php echo json_encode($ekstremRiskMonthlyData ?? [], 15, 512) ?>);
    console.log('highRiskMonthlyData:', <?php echo json_encode($highRiskMonthlyData ?? [], 15, 512) ?>);
    console.log('tinggiRiskMonthlyData:', <?php echo json_encode($tinggiRiskMonthlyData ?? [], 15, 512) ?>);
    
    // Jika data kategori risiko bulanan tidak tersedia, gunakan data dari stats 
    // dan buat data dummy untuk bulan
    if (extremeRiskMonthlyData.length === 0 && highRiskMonthlyData.length === 0 && 
        mediumRiskMonthlyData.length === 0 && lowRiskMonthlyData.length === 0) {
        
        console.log('Data kategori risiko bulanan tidak tersedia, menggunakan data dari stats');
        
        // Dapatkan total untuk setiap kategori dari stats
        const extremeTotal = <?php echo e($stats['extreme_risk'] ?? $stats['ekstrem'] ?? 0); ?>;
        const highTotal = <?php echo e($stats['high_risk'] ?? $stats['tinggi'] ?? 0); ?>;
        const mediumTotal = <?php echo e($stats['medium_risk'] ?? $stats['sedang'] ?? 0); ?>;
        const lowTotal = <?php echo e($stats['low_risk'] ?? $stats['rendah'] ?? 0); ?>;
        
        console.log('Total dari stats:');
        console.log('Ekstrem:', extremeTotal);
        console.log('Tinggi:', highTotal);
        console.log('Sedang:', mediumTotal);
        console.log('Rendah:', lowTotal);
        
        // Jika ada bulan saat ini dalam data, letakkan semua data pada bulan tersebut
        // Jika tidak, letakkan di bulan pertama dari daftar bulan
        const currentMonthName = new Date().toLocaleString('id-ID', { month: 'short' });
        let targetMonthIndex = monthLabels.findIndex(m => m === currentMonthName);
        if (targetMonthIndex === -1) targetMonthIndex = 0;
        
        // Inisialisasi array kosong untuk setiap kategori
        extremeRiskMonthlyData = Array(monthLabels.length).fill(0);
        highRiskMonthlyData = Array(monthLabels.length).fill(0);
        mediumRiskMonthlyData = Array(monthLabels.length).fill(0);
        lowRiskMonthlyData = Array(monthLabels.length).fill(0);
        
        // Tetapkan data total ke bulan yang ditargetkan
        extremeRiskMonthlyData[targetMonthIndex] = extremeTotal;
        highRiskMonthlyData[targetMonthIndex] = highTotal;
        mediumRiskMonthlyData[targetMonthIndex] = mediumTotal;
        lowRiskMonthlyData[targetMonthIndex] = lowTotal;
    }
    
    console.log('Extreme Risk Data:', extremeRiskMonthlyData);
    console.log('High Risk Data:', highRiskMonthlyData);
    console.log('Medium Risk Data:', mediumRiskMonthlyData);
    console.log('Low Risk Data:', lowRiskMonthlyData);
    
    // Mengurutkan bulan sesuai urutan kalender normal (Jan-Des)
    const monthOrder = {
        'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'Mei': 4, 'Jun': 5, 
        'Jul': 6, 'Agt': 7, 'Sep': 8, 'Okt': 9, 'Nov': 10, 'Des': 11
    };
    
    // Membuat array pasangan bulan dan data
    const createDataPairs = (labels, dataArray) => {
        return labels.map((month, index) => {
            return { month, data: dataArray[index] || 0 };
        });
    };
    
    const monthExtremeRiskPairs = createDataPairs(monthLabels, extremeRiskMonthlyData);
    const monthHighRiskPairs = createDataPairs(monthLabels, highRiskMonthlyData);
    const monthMediumRiskPairs = createDataPairs(monthLabels, mediumRiskMonthlyData);
    const monthLowRiskPairs = createDataPairs(monthLabels, lowRiskMonthlyData);
    
    // Mengurutkan berdasarkan urutan bulan
    monthExtremeRiskPairs.sort((a, b) => monthOrder[a.month] - monthOrder[b.month]);
    monthHighRiskPairs.sort((a, b) => monthOrder[a.month] - monthOrder[b.month]);
    monthMediumRiskPairs.sort((a, b) => monthOrder[a.month] - monthOrder[b.month]);
    monthLowRiskPairs.sort((a, b) => monthOrder[a.month] - monthOrder[b.month]);
    
    // Mengekstrak bulan dan data yang sudah diurutkan
    const sortedMonthLabels = monthExtremeRiskPairs.map(pair => pair.month);
    const sortedExtremeRiskData = monthExtremeRiskPairs.map(pair => pair.data);
    const sortedHighRiskData = monthHighRiskPairs.map(pair => pair.data);
    const sortedMediumRiskData = monthMediumRiskPairs.map(pair => pair.data);
    const sortedLowRiskData = monthLowRiskPairs.map(pair => pair.data);
    
    // Data kuartalan - mengelompokkan data bulanan menjadi 4 kuartal
    const quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
    const quarterlyExtremeRiskData = [0, 0, 0, 0]; // 4 kuartal
    const quarterlyHighRiskData = [0, 0, 0, 0]; // 4 kuartal
    const quarterlyMediumRiskData = [0, 0, 0, 0]; // 4 kuartal
    const quarterlyLowRiskData = [0, 0, 0, 0]; // 4 kuartal
    
    // Menghitung data kuartalan dari data bulanan berdasarkan tingkat risiko
    for (let i = 0; i < sortedMonthLabels.length; i++) {
        const month = sortedMonthLabels[i];
        let quarterIndex;
        
        // Pengelompokan bulan berdasarkan nama bulan dalam urutan kalender normal
        if (['Jan', 'Feb', 'Mar'].includes(month)) {
            quarterIndex = 0; // Q1
        } else if (['Apr', 'Mei', 'Jun'].includes(month)) {
            quarterIndex = 1; // Q2
        } else if (['Jul', 'Agt', 'Sep'].includes(month)) {
            quarterIndex = 2; // Q3
        } else {
            quarterIndex = 3; // Q4 (Okt, Nov, Des)
        }
        
        // Tambahkan data ke kuartal yang sesuai untuk setiap tingkat risiko
        quarterlyExtremeRiskData[quarterIndex] += sortedExtremeRiskData[i];
        quarterlyHighRiskData[quarterIndex] += sortedHighRiskData[i];
        quarterlyMediumRiskData[quarterIndex] += sortedMediumRiskData[i];
        quarterlyLowRiskData[quarterIndex] += sortedLowRiskData[i];
    }
    
    // Data tahunan - menggabungkan semua data bulanan berdasarkan tingkat risiko
    const yearlyLabels = ['Tahun Ini'];
    const yearlyExtremeRiskData = [sortedExtremeRiskData.reduce((sum, value) => sum + value, 0)];
    const yearlyHighRiskData = [sortedHighRiskData.reduce((sum, value) => sum + value, 0)];
    const yearlyMediumRiskData = [sortedMediumRiskData.reduce((sum, value) => sum + value, 0)];
    const yearlyLowRiskData = [sortedLowRiskData.reduce((sum, value) => sum + value, 0)];
    
    // Buat Chart dengan data bulanan sebagai default
    let currentLabels = sortedMonthLabels;
    let currentExtremeRiskData = sortedExtremeRiskData;
    let currentHighRiskData = sortedHighRiskData;
    let currentMediumRiskData = sortedMediumRiskData;
    let currentLowRiskData = sortedLowRiskData;
    
    const riskReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: currentLabels,
            datasets: [
                {
                    label: 'Risiko Ekstrem',
                    data: currentExtremeRiskData,
                    backgroundColor: '#FF0000',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Tinggi',
                    data: currentHighRiskData,
                    backgroundColor: '#FFA500',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Risiko Sedang',
                    data: currentMediumRiskData,
                    backgroundColor: '#FFFF00',
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
            labels: ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi', 'Risiko Ekstrem'],
            datasets: [{
                data: [
                    <?php echo e($stats['low_risk'] ?? $stats['rendah'] ?? 0); ?>, 
                    <?php echo e($stats['medium_risk'] ?? $stats['sedang'] ?? 0); ?>, 
                    <?php echo e($stats['high_risk'] ?? $stats['tinggi'] ?? 0); ?>,
                    <?php echo e($stats['extreme_risk'] ?? $stats['ekstrem'] ?? 0); ?>

                ],
                backgroundColor: [
                    '#198754',
                    '#FFFF00',
                    '#FFA500',
                    '#FF0000'
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
                updateChartData(sortedMonthLabels, sortedExtremeRiskData, sortedHighRiskData, sortedMediumRiskData, sortedLowRiskData);
            } else if (period === 'quarterly') {
                updateChartData(quarterlyLabels, quarterlyExtremeRiskData, quarterlyHighRiskData, quarterlyMediumRiskData, quarterlyLowRiskData);
            } else if (period === 'yearly') {
                updateChartData(yearlyLabels, yearlyExtremeRiskData, yearlyHighRiskData, yearlyMediumRiskData, yearlyLowRiskData);
            }
        });
    });
    
    // Fungsi untuk update chart data
    function updateChartData(labels, extremeRisk, highRisk, mediumRisk, lowRisk) {
        riskReportChart.data.labels = labels;
        riskReportChart.data.datasets[0].data = extremeRisk;
        riskReportChart.data.datasets[1].data = highRisk;
        riskReportChart.data.datasets[2].data = mediumRisk;
        riskReportChart.data.datasets[3].data = lowRisk;
        riskReportChart.update();
    }
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/dashboard.blade.php ENDPATH**/ ?>