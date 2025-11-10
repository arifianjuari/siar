<?php $__env->startSection('title', 'Dashboard Manajemen Dokumen'); ?>

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
        padding: 0.75rem;
    }
    
    .kpi-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        opacity: 0.8;
    }
    
    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.1rem;
        line-height: 1;
    }
    
    .kpi-label {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-bottom: 0;
    }
    
    .stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    
    .stats-item {
        flex: 1;
        min-width: 140px;
        background-color: white;
        border-radius: 0.5rem;
        padding: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: row;
        align-items: center;
        transition: all 0.2s;
    }
    
    .stats-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .stats-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 1.25rem;
        color: white;
    }
    
    .stats-icon.total {
        background-color: #0d6efd;
    }
    
    .stats-icon.public {
        background-color: #198754;
    }
    
    .stats-icon.internal {
        background-color: #fd7e14;
    }
    
    .stats-icon.confidential {
        background-color: #dc3545;
    }
    
    .stats-info .value {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .stats-info .label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    .doc-item {
        border-left: 3px solid #eee;
        transition: all 0.2s;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.25rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    
    .doc-item:hover {
        background-color: rgba(0,0,0,0.02);
        border-left-color: #0d6efd;
    }
    
    .doc-title {
        font-weight: 600;
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
    .doc-meta {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .doc-date {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    .doc-category {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        background-color: #f0f0f0;
        color: #444;
        font-weight: 500;
    }
    
    .bg-confidential {
        background: linear-gradient(45deg, #dc3545, #e17982);
        color: white;
    }
    
    .bg-internal {
        background: linear-gradient(45deg, #fd7e14, #ffb380);
        color: white;
    }
    
    .bg-public {
        background: linear-gradient(45deg, #198754, #28a975);
        color: white;
    }
    
    .bg-risk {
        background: linear-gradient(45deg, #0d6efd, #50a3ff);
        color: white;
    }
    
    .list-group-item.hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: rgba(0,0,0,0.02);
    }
    
    .latest-docs-card {
        max-height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .latest-docs-body {
        flex: 1;
        overflow-y: auto;
        max-height: 500px;
    }
    
    .list-group-flush .list-group-item {
        padding: 0.4rem 0;
        border-bottom-width: 1px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard Manajemen Dokumen</h2>
            <p class="text-muted mb-0">Pantau dan kelola dokumen dari berbagai sumber</p>
        </div>
        <div>
            <a href="<?php echo e(route('modules.document-management.documents.create')); ?>" class="btn btn-success btn-sm me-2">
                <i class="fas fa-plus-circle me-1"></i> Tambah Dokumen
            </a>
            <a href="<?php echo e(route('modules.document-management.documents.index')); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-list me-1"></i> Daftar Dokumen
            </a>
        </div>
    </div>

    <!-- KPI Cards - Compact Version -->
    <div class="stats-row">
        <a href="<?php echo e(route('modules.document-management.documents-by-type', 'all')); ?>" class="stats-item text-decoration-none">
            <div class="stats-icon total">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-info">
                <div class="value"><?php echo e($stats['total']); ?></div>
                <div class="label">Total Dokumen</div>
            </div>
        </a>
        
        <a href="<?php echo e(route('modules.document-management.documents-by-type', 'public')); ?>" class="stats-item text-decoration-none">
            <div class="stats-icon public">
                <i class="fas fa-globe"></i>
            </div>
            <div class="stats-info">
                <div class="value"><?php echo e($stats['public']); ?></div>
                <div class="label">Dokumen Publik</div>
            </div>
        </a>
        
        <a href="<?php echo e(route('modules.document-management.documents-by-type', 'internal')); ?>" class="stats-item text-decoration-none">
            <div class="stats-icon internal">
                <i class="fas fa-building"></i>
            </div>
            <div class="stats-info">
                <div class="value"><?php echo e($stats['internal']); ?></div>
                <div class="label">Dokumen Internal</div>
            </div>
        </a>
        
        <a href="<?php echo e(route('modules.document-management.documents-by-type', 'confidential')); ?>" class="stats-item text-decoration-none">
            <div class="stats-icon confidential">
                <i class="fas fa-lock"></i>
            </div>
            <div class="stats-info">
                <div class="value"><?php echo e($stats['confidential']); ?></div>
                <div class="label">Dokumen Rahasia</div>
            </div>
        </a>
    </div>
    
    <div class="row">
        <!-- Documents by Category -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Dokumen berdasarkan Modul</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($moduleCategories) && count($moduleCategories) > 0): ?>
                        <ul class="list-group">
                            <?php $__currentLoopData = $moduleCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($category['module'] === 'work-units.spo' 
                                        ? route('work-units.spo.dashboard') 
                                        : route('modules.' . $category['module'] . '.dashboard')); ?>" class="text-decoration-none">
                                    <li class="list-group-item d-flex justify-content-between align-items-center hover-effect" 
                                        style="cursor: pointer; transition: all 0.2s ease;">
                                        <div>
                                            <i class="fas <?php echo e($category['icon']); ?> me-2 text-<?php echo e($category['color']); ?>"></i>
                                            <?php echo e($category['display_name']); ?>

                                        </div>
                                        <span class="badge bg-<?php echo e($category['color']); ?> rounded-pill"><?php echo e($category['total']); ?></span>
                                    </li>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada dokumen yang dikelompokkan berdasarkan modul.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Pastikan data dokumen sudah memiliki document_number
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest Documents -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 latest-docs-card">
                <div class="card-header py-2">
                    <h5 class="mb-0 fs-6"><i class="fas fa-clock me-2"></i> Dokumen Terbaru</h5>
                </div>
                <div class="card-body p-2 latest-docs-body">
                    <?php if($latestDocuments->count() > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $latestDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $detailRoute = '#';
                                    $iconClass = 'fas fa-file-alt text-primary';
                                    
                                    if ($document instanceof \App\Models\Document) {
                                        $detailRoute = route('modules.document-management.documents.show', $document->id);
                                        $iconClass = 'fas fa-file-alt text-primary';
                                    } elseif ($document instanceof \App\Models\RiskReport) {
                                        $detailRoute = route('modules.risk-management.risk-reports.show', $document->id);
                                        $iconClass = 'fas fa-exclamation-triangle text-danger';
                                    } elseif ($document instanceof \App\Models\SPO) {
                                        $detailRoute = route('work-units.spo.show', $document->id);
                                        $iconClass = 'fas fa-clipboard-list text-success';
                                    } elseif ($document instanceof \App\Models\Correspondence) {
                                        $detailRoute = route('modules.correspondence.letters.show', $document->id);
                                        $iconClass = 'fas fa-envelope text-info';
                                    }
                                ?>
                                <li class="list-group-item px-0 py-1 border-bottom">
                                    <a href="<?php echo e($detailRoute); ?>" class="d-flex justify-content-between align-items-start text-decoration-none">
                                        <div class="d-flex align-items-start">
                                            <i class="<?php echo e($iconClass); ?> me-2 mt-1"></i>
                                            <div>
                                                <div class="doc-title text-dark"><?php echo e($document->document_title); ?></div>
                                                <div class="doc-meta">
                                                    <i class="far fa-calendar-alt me-1"></i><?php echo e($document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d M Y H:i') : '-'); ?> 
                                                    <span class="mx-1">•</span> 
                                                    <?php echo e($document->document_number); ?>

                                                    <?php if($document->file_path): ?>
                                                        <i class="fas fa-paperclip ms-1"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-2 mt-1">
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-0 text-muted small">Belum ada dokumen yang diunggah.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popular Tags -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Tag Populer</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($popularTags) && $popularTags->count() > 0): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php $__currentLoopData = $popularTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('modules.document-management.documents-by-tag', $tag->slug)); ?>" 
                                   class="btn btn-outline-primary rounded-pill btn-sm" 
                                   style="font-size: calc(0.7rem + <?php echo e(min($tag->document_count/5, 0.5)); ?>rem)">
                                    <i class="fas fa-tag me-1"></i>
                                    <?php echo e($tag->name); ?>

                                    <span class="badge bg-primary rounded-pill ms-1"><?php echo e($tag->document_count); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada tag yang digunakan pada dokumen.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Tag membantu mengorganisasi dokumen Anda berdasarkan topik
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/DocumentManagement/dashboard.blade.php ENDPATH**/ ?>