<?php $__env->startSection('title', ' | Dashboard'); ?>

<?php
$hideDefaultHeader = true;
?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
        height: 100%;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .dashboard-card.risk-low {
        border-left-color: #10B981;
    }
    .dashboard-card.risk-medium {
        border-left-color: #F59E0B;
    }
    .dashboard-card.risk-high {
        border-left-color: #EF4444;
    }
    .dashboard-card.risk-extreme {
        border-left-color: #7F1D1D;
    }
    .dashboard-card.corr-incoming {
        border-left-color: #3B82F6;
    }
    .dashboard-card.corr-outgoing {
        border-left-color: #8B5CF6;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .risk-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .risk-indicator.low {
        background-color: #10B981;
    }
    .risk-indicator.medium {
        background-color: #F59E0B;
    }
    .risk-indicator.high {
        background-color: #EF4444;
    }
    .risk-indicator.extreme {
        background-color: #7F1D1D;
    }
    .chart-container {
        height: 250px;
        position: relative;
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">Dashboard</h1>
                            <p class="text-muted mb-0">
                                <span class="badge bg-primary"><?php echo e(auth()->user()->tenant->name ?? 'System'); ?></span>
                                <span class="ms-2"><i class="fas fa-user me-1"></i> <?php echo e(auth()->user()->name); ?></span>
                                <span class="ms-2"><i class="fas fa-shield-alt me-1"></i> <?php echo e(auth()->user()->role->name ?? 'User'); ?></span>
                            </p>
                        </div>
                        <button id="refreshDashboard" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label for="period" class="col-form-label">Periode:</label>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select id="period" name="period" class="form-select">
                                <option value="all" <?php echo e(request('period') == 'all' ? 'selected' : ''); ?>>Semua</option>
                                <option value="this_month" <?php echo e(request('period', 'this_month') == 'this_month' ? 'selected' : ''); ?>>Bulan Ini</option>
                                <option value="last_month" <?php echo e(request('period') == 'last_month' ? 'selected' : ''); ?>>Bulan Lalu</option>
                                <option value="this_year" <?php echo e(request('period') == 'this_year' ? 'selected' : ''); ?>>Tahun Ini</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkasan -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2"></i> Statistik Ringkasan</h5>
        </div>
        
        <!-- Total Laporan Risiko -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card risk-low h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Laporan Risiko</h6>
                    <div class="stat-value text-dark mb-1"><?php echo e($stats['risk_reports'] ?? 0); ?></div>
                    <div class="text-success small">
                        <i class="fas fa-check-circle me-1"></i> <?php echo e($stats['risk_resolved'] ?? 0); ?> terselesaikan
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Unit Kerja -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100" style="border-left-color: #8B5CF6;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Unit Kerja</h6>
                    <div class="stat-value text-dark mb-1"><?php echo e($stats['work_units'] ?? 0); ?></div>
                    <div class="text-primary small">
                        <i class="fas fa-building me-1"></i> Sudah terdaftar
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Korespondensi -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card corr-incoming h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Korespondensi</h6>
                    <div class="stat-value text-dark mb-1"><?php echo e($stats['correspondence'] ?? 0); ?></div>
                    <div class="text-primary small">
                        <i class="fas fa-calendar-alt me-1"></i> <?php echo e($stats['correspondence_this_month'] ?? 0); ?> dokumen bulan ini
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Users -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100" style="border-left-color: #6366F1;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Users</h6>
                    <div class="stat-value text-dark mb-1"><?php echo e($stats['users'] ?? 0); ?></div>
                    <div class="text-primary small">
                        <i class="fas fa-users me-1"></i> Aktif dalam sistem
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik Visualisasi -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Distribusi Tingkat Risiko</h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar me-1"></i> <?php echo e(isset($periodLabel) ? $periodLabel : 'Semua data'); ?>

                        </span>
                    </div>
                    <div class="chart-container">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Distribusi Jenis Surat</h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar me-1"></i> <?php echo e(isset($periodLabel) ? $periodLabel : 'Semua data'); ?>

                        </span>
                    </div>
                    <div class="chart-container">
                        <canvas id="corrChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Manajemen Risiko -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Manajemen Risiko (Terbaru)</h5>
                    <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat semua
                    </a>
                </div>
                <div class="card-body">
                    <?php if(isset($recentRiskReports) && $recentRiskReports->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $recentRiskReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>" class="text-decoration-none fw-medium"><?php echo e($report->document_title); ?></a>
                                    <span class="badge <?php echo e($report->status == 'open' ? 'bg-primary' : ($report->status == 'in_review' ? 'bg-warning' : 'bg-success')); ?> rounded-pill">
                                        <?php echo e($report->status == 'open' ? 'Terbuka' : ($report->status == 'in_review' ? 'Ditinjau' : 'Selesai')); ?>

                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted"><?php echo e($report->created_at->format('d M Y')); ?></small>
                                        <?php
                                            $riskClass = 'bg-success text-white';
                                            $riskLevel = strtolower($report->risk_level);
                                            if (in_array($riskLevel, ['sedang', 'medium'])) {
                                                $riskClass = 'bg-warning text-dark';
                                            } elseif (in_array($riskLevel, ['tinggi', 'high'])) {
                                                $riskClass = 'bg-danger text-white';
                                            } elseif (in_array($riskLevel, ['ekstrem', 'extreme'])) {
                                                $riskClass = 'bg-dark text-white';
                                            }
                                        ?>
                                        <span class="badge <?php echo e($riskClass); ?> ms-1"><?php echo e($report->risk_level); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada laporan risiko.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Korespondensi -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Korespondensi (Terbaru)</h5>
                    <a href="<?php echo e(route('modules.correspondence.letters.index')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat semua
                    </a>
                </div>
                <div class="card-body">
                    <?php if(isset($recentCorrespondences) && $recentCorrespondences->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $recentCorrespondences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $correspondence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <a href="<?php echo e(route('modules.correspondence.letters.show', $correspondence->id)); ?>" class="text-decoration-none fw-medium">
                                        <?php echo e($correspondence->subject ?? $correspondence->document_title ?? 'Surat #' . $correspondence->id); ?>

                                    </a>
                                    <?php if(isset($correspondence->document_number)): ?>
                                        <span class="badge bg-light text-dark"><?php echo e($correspondence->document_number); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?php echo e($correspondence->document_date ? $correspondence->document_date->format('d M Y') : $correspondence->created_at->format('d M Y')); ?></small>
                                    <div>
                                        <?php if(isset($correspondence->type)): ?>
                                            <?php if($correspondence->type == 'incoming'): ?>
                                                <span class="badge bg-primary">Masuk</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Keluar</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-envelope fa-3x mb-3"></i>
                            <p>Belum ada surat dalam sistem.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Unit Kerja -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Unit Kerja</h5>
                    <a href="<?php echo e(route('work-units.index')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat semua
                    </a>
                </div>
                <div class="card-body">
                    <?php if(isset($recentWorkUnits) && $recentWorkUnits->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $recentWorkUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <a href="<?php echo e(route('work-units.dashboard', $unit->id)); ?>" class="text-decoration-none fw-medium">
                                        <?php echo e($unit->unit_name); ?>

                                    </a>
                                    <span class="badge bg-light text-dark"><?php echo e($unit->unit_code); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Kepala: <?php echo e($unit->headOfUnit->name ?? 'Belum ditentukan'); ?></small>
                                    <div>
                                        <span class="badge <?php echo e($unit->unit_type == 'medical' ? 'bg-success' : 'bg-info'); ?>">
                                            <?php echo e($unit->unit_type); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-building fa-3x mb-3"></i>
                            <p>Belum ada unit kerja dalam sistem.</p>
                        </div>
                    <?php endif; ?>
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
        // Chart untuk Tingkat Risiko
        const riskCtx = document.getElementById('riskChart').getContext('2d');
        const riskChart = new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Rendah', 'Sedang', 'Tinggi', 'Ekstrem'],
                datasets: [{
                    data: [
                        <?php echo e($riskStats['low'] ?? 0); ?>, 
                        <?php echo e($riskStats['medium'] ?? 0); ?>, 
                        <?php echo e($riskStats['high'] ?? 0); ?>, 
                        <?php echo e($riskStats['extreme'] ?? 0); ?>

                    ],
                    backgroundColor: [
                        '#10B981', // Hijau untuk Rendah
                        '#F59E0B', // Kuning untuk Sedang
                        '#EF4444', // Merah untuk Tinggi
                        '#7F1D1D'  // Merah gelap untuk Ekstrem
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Chart untuk Jenis Surat
        const corrCtx = document.getElementById('corrChart').getContext('2d');
        const corrChart = new Chart(corrCtx, {
            type: 'doughnut',
            data: {
                labels: ['Surat Masuk', 'Surat Keluar', 'Regulasi'],
                datasets: [{
                    data: [
                        <?php echo e($corrStats['incoming'] ?? 0); ?>, 
                        <?php echo e($corrStats['outgoing'] ?? 0); ?>,
                        <?php echo e($corrStats['regulasi'] ?? 0); ?>

                    ],
                    backgroundColor: [
                        '#3B82F6', // Biru untuk Surat Masuk
                        '#8B5CF6', // Ungu untuk Surat Keluar
                        '#6B7280'  // Abu-abu untuk Regulasi
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Refresh Dashboard
        document.getElementById('refreshDashboard').addEventListener('click', function() {
            window.location.reload();
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/pages/dashboard.blade.php ENDPATH**/ ?>