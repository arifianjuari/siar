<?php $__env->startSection('title', 'Dashboard SPO'); ?>

<?php
$hideDefaultHeader = true;
?>

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
    
    .doc-item {
        border-left: 3px solid #eee;
        transition: all 0.2s;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    
    .doc-item:hover {
        background-color: rgba(0,0,0,0.02);
        border-left-color: #0d6efd;
    }
    
    .doc-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .doc-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .bg-draft {
        background: linear-gradient(45deg, #6c757d, #adb5bd);
        color: white;
    }
    
    .bg-approved {
        background: linear-gradient(45deg, #198754, #28a975);
        color: white;
    }
    
    .bg-expired {
        background: linear-gradient(45deg, #dc3545, #e17982);
        color: white;
    }
    
    .bg-revision {
        background: linear-gradient(45deg, #fd7e14, #ffb380);
        color: white;
    }
    
    .list-group-item.hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: rgba(0,0,0,0.02);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard SPO</h2>
            <p class="text-muted mb-0">Pantau dan kelola Standar Prosedur Operasional</p>
        </div>
        <div>
            <a href="<?php echo e(route('work-units.spo.create')); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Tambah SPO Baru
            </a>
            <a href="<?php echo e(route('work-units.spo.index')); ?>" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Daftar SPO
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <i class="fas fa-clipboard-list kpi-icon text-primary"></i>
                    <h2 class="kpi-value"><?php echo e($stats['total']); ?></h2>
                    <p class="kpi-label">Total SPO</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-approved">
                <div class="card-body">
                    <i class="fas fa-check-circle kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['approved']); ?></h2>
                    <p class="kpi-label">SPO Disetujui</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-draft">
                <div class="card-body">
                    <i class="fas fa-edit kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['draft']); ?></h2>
                    <p class="kpi-label">SPO Draft</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-expired">
                <div class="card-body">
                    <i class="fas fa-exclamation-circle kpi-icon"></i>
                    <h2 class="kpi-value"><?php echo e($stats['expired']); ?></h2>
                    <p class="kpi-label">SPO Kadaluarsa</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- SPO by Document Type -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> SPO berdasarkan Tipe</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($typeStats) && count($typeStats) > 0): ?>
                        <ul class="list-group">
                            <?php $__currentLoopData = $typeStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center hover-effect">
                                    <div>
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        <?php echo e($type); ?>

                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo e($count); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada SPO yang dibuat.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Anda dapat membuat SPO baru dengan mengklik tombol Tambah SPO Baru
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest SPOs -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i> SPO Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if($latestSPOs->count() > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $latestSPOs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item px-0 py-2 border-bottom">
                                    <a href="<?php echo e(route('work-units.spo.show', $spo->id)); ?>" class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clipboard-list text-primary me-2"></i>
                                            <div>
                                                <span class="fw-medium text-dark"><?php echo e($spo->document_title); ?></span>
                                                <span class="text-muted ms-2">(<?php echo e($spo->document_number); ?>)</span>
                                                <?php if($spo->file_path): ?>
                                                    <span class="badge bg-success ms-1">📎</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada SPO yang dibuat.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Pastikan telah menambahkan SPO dengan document_date yang benar
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Top Work Units -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i> Unit Kerja dengan SPO Terbanyak</h5>
                </div>
                <div class="card-body">
                    <?php if($topWorkUnits->count() > 0): ?>
                        <ul class="list-group">
                            <?php $__currentLoopData = $topWorkUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center hover-effect">
                                    <div>
                                        <i class="fas fa-building me-2 text-info"></i>
                                        <?php echo e($unit->unit_name); ?>

                                    </div>
                                    <span class="badge bg-info rounded-pill"><?php echo e($unit->spo_count); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada unit kerja dengan SPO.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SPOs needing review -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> SPO yang Perlu Ditinjau</h5>
                </div>
                <div class="card-body">
                    <?php if($needReview->count() > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $needReview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item px-0 py-2 border-bottom">
                                    <a href="<?php echo e(route('work-units.spo.show', $spo->id)); ?>" class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                            <div>
                                                <span class="fw-medium text-dark"><?php echo e($spo->document_title); ?></span>
                                                <div class="small text-muted">
                                                    <i class="far fa-calendar-alt me-1"></i> Review: <?php echo e($spo->next_review ? \Carbon\Carbon::parse($spo->next_review)->format('d M Y') : '-'); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Tidak ada SPO yang perlu ditinjau dalam waktu dekat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/work-unit/spo/dashboard.blade.php ENDPATH**/ ?>