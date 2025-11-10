<?php $__env->startSection('title', 'Dashboard Pengelolaan Kegiatan'); ?>

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
    
    .bg-planned {
        background-color: #6c757d;
        color: white;
    }
    
    .bg-ongoing {
        background-color: #0d6efd;
        color: white;
    }
    
    .bg-completed {
        background-color: #198754;
        color: white;
    }
    
    .bg-cancelled {
        background-color: #dc3545;
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
    
    .activity-card {
        border-radius: 10px;
        transition: all 0.2s;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .activity-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .priority-indicator {
        width: 4px;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        border-radius: 10px 0 0 10px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard Pengelolaan Kegiatan</h2>
            <p class="text-muted mb-0">Pantau dan kelola kegiatan organisasi secara terpusat</p>
        </div>
        <div>
            <a href="<?php echo e(route('activity-management.activities.create')); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Kegiatan Baru
            </a>
            <a href="<?php echo e(route('activity-management.activities.index')); ?>" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Daftar Kegiatan
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                <p class="text-muted mb-0 small">Data kegiatan untuk tahun berjalan <?php echo e(date('Y')); ?></p>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <i class="fas fa-clipboard-list kpi-icon text-primary"></i>
                    <h2 class="kpi-value"><?php echo e($stats['total'] ?? 0); ?></h2>
                    <p class="kpi-label">Total Kegiatan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-planned">
                <div class="card-body">
                    <i class="fas fa-file-alt kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['planned'] ?? 0); ?></h2>
                    <p class="kpi-label">Direncanakan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-ongoing">
                <div class="card-body">
                    <i class="fas fa-spinner kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['ongoing'] ?? 0); ?></h2>
                    <p class="kpi-label">Sedang Berlangsung</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card bg-completed">
                <div class="card-body">
                    <i class="fas fa-check-circle kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['completed'] ?? 0); ?></h2>
                    <p class="kpi-label">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Grafik Kegiatan Berdasarkan Status -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Statistik Kegiatan</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary status-chart-filter active" data-period="monthly">Bulanan</button>
                        <button type="button" class="btn btn-sm btn-outline-primary status-chart-filter" data-period="quarterly">Kuartalan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Kegiatan -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Kegiatan berdasarkan Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kegiatan Terbaru -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kegiatan Terbaru</h5>
                    <a href="<?php echo e(route('activity-management.activities.index')); ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $recentActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item p-3 border-0">
                            <div class="d-flex position-relative">
                                <div class="priority-indicator bg-<?php echo e($activity->priorityColor ?? 'secondary'); ?>"></div>
                                <div class="ps-3 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">
                                            <a href="<?php echo e(route('activity-management.activities.show', $activity->uuid)); ?>" class="text-dark stretched-link">
                                                <?php echo e($activity->title); ?>

                                            </a>
                                        </h6>
                                        <span class="badge bg-<?php echo e($activity->statusColor ?? 'secondary'); ?>"><?php echo e($activity->statusLabel ?? 'Status'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <div>
                                            <i class="fas fa-building me-1"></i> <?php echo e($activity->workUnit->unit_name ?? 'Tidak ada unit'); ?>

                                            <span class="ms-2">
                                                <i class="fas fa-calendar me-1"></i> <?php echo e($activity->start_date ? $activity->start_date->format('d M Y') : 'N/A'); ?>

                                            </span>
                                        </div>
                                        <div>
                                            <i class="fas fa-user me-1"></i> <?php echo e($activity->creator->name ?? 'Pengguna'); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item p-4 text-center text-muted">
                            <i class="fas fa-info-circle mb-2 fs-3"></i>
                            <p class="mb-0">Belum ada kegiatan yang dibuat</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kegiatan Mendesak -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Prioritas Tinggi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $highPriorityActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item p-3 border-0">
                            <div class="d-flex position-relative">
                                <div class="priority-indicator bg-danger"></div>
                                <div class="ps-3">
                                    <h6 class="mb-1">
                                        <a href="<?php echo e(route('activity-management.activities.show', $activity->uuid)); ?>" class="text-dark stretched-link">
                                            <?php echo e($activity->title); ?>

                                        </a>
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <div>
                                            <i class="fas fa-calendar-alt me-1"></i> <?php echo e($activity->due_date ? $activity->due_date->format('d M Y') : 'N/A'); ?>

                                        </div>
                                        <span class="badge bg-<?php echo e($activity->statusColor ?? 'secondary'); ?>"><?php echo e($activity->statusLabel ?? 'Status'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item p-4 text-center text-muted">
                            <i class="fas fa-check-circle mb-2 fs-3"></i>
                            <p class="mb-0">Tidak ada kegiatan prioritas tinggi saat ini</p>
                        </div>
                        <?php endif; ?>
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
        // Data untuk grafik bulanan dari server
        const monthlyData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Direncanakan',
                    data: [
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <?php echo e($activitiesByMonth->firstWhere('month', $i)->planned ?? 0); ?><?php echo e($i < 12 ? ',' : ''); ?>

                        <?php endfor; ?>
                    ],
                    backgroundColor: '#6c757d'
                },
                {
                    label: 'Sedang Berlangsung',
                    data: [
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <?php echo e($activitiesByMonth->firstWhere('month', $i)->ongoing ?? 0); ?><?php echo e($i < 12 ? ',' : ''); ?>

                        <?php endfor; ?>
                    ],
                    backgroundColor: '#0d6efd'
                },
                {
                    label: 'Selesai',
                    data: [
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <?php echo e($activitiesByMonth->firstWhere('month', $i)->completed ?? 0); ?><?php echo e($i < 12 ? ',' : ''); ?>

                        <?php endfor; ?>
                    ],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Dibatalkan',
                    data: [
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <?php echo e($activitiesByMonth->firstWhere('month', $i)->cancelled ?? 0); ?><?php echo e($i < 12 ? ',' : ''); ?>

                        <?php endfor; ?>
                    ],
                    backgroundColor: '#dc3545'
                }
            ]
        };
        
        const quarterlyData = {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [
                {
                    label: 'Direncanakan',
                    data: [
                        <?php echo e($activitiesByQuarter[1]->planned ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[2]->planned ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[3]->planned ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[4]->planned ?? 0); ?>

                    ],
                    backgroundColor: '#6c757d'
                },
                {
                    label: 'Sedang Berlangsung',
                    data: [
                        <?php echo e($activitiesByQuarter[1]->ongoing ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[2]->ongoing ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[3]->ongoing ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[4]->ongoing ?? 0); ?>

                    ],
                    backgroundColor: '#0d6efd'
                },
                {
                    label: 'Selesai',
                    data: [
                        <?php echo e($activitiesByQuarter[1]->completed ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[2]->completed ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[3]->completed ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[4]->completed ?? 0); ?>

                    ],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Dibatalkan',
                    data: [
                        <?php echo e($activitiesByQuarter[1]->cancelled ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[2]->cancelled ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[3]->cancelled ?? 0); ?>,
                        <?php echo e($activitiesByQuarter[4]->cancelled ?? 0); ?>

                    ],
                    backgroundColor: '#dc3545'
                }
            ]
        };
        
        // Chart Aktivitas Status
        const activityStatsCtx = document.getElementById('activityStatsChart').getContext('2d');
        let activityStatsChart = new Chart(activityStatsCtx, {
            type: 'bar',
            data: monthlyData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4]
                        }
                    }
                }
            }
        });
        
        // Data untuk grafik kategori dari server
        const categoryLabels = [<?php $__currentLoopData = $activitiesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>'<?php echo e($category); ?>',<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>];
        const categoryValues = [<?php $__currentLoopData = $activitiesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($total); ?>,<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>];
        const categoryColors = [
            '#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#6c757d', 
            '#0dcaf0', '#ffc107', '#dc3545', '#343a40', '#20c997'
        ];
        
        // Jika tidak ada data kategori, tampilkan placeholder
        const categoryData = categoryLabels.length > 0 ? {
            labels: categoryLabels,
            datasets: [{
                data: categoryValues,
                backgroundColor: categoryColors.slice(0, categoryLabels.length),
                borderWidth: 0
            }]
        } : {
            labels: ['Belum Ada Data'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e9ecef'],
                borderWidth: 0
            }]
        };
        
        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: categoryData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '60%'
            }
        });
        
        // Filter chart periode
        document.querySelectorAll('.status-chart-filter').forEach(button => {
            button.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                
                // Ubah status active pada tombol
                document.querySelectorAll('.status-chart-filter').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Ubah data chart
                if (period === 'monthly') {
                    activityStatsChart.data = monthlyData;
                } else if (period === 'quarterly') {
                    activityStatsChart.data = quarterlyData;
                }
                
                activityStatsChart.update();
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', ['hideDefaultHeader' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/activity_management/dashboard.blade.php ENDPATH**/ ?>