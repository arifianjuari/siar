<?php $__env->startSection('title', 'Dokumen dengan Tag: ' . $tag->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .doc-item {
        transition: all 0.2s;
        border-left: 3px solid #eee;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    
    .doc-item:hover {
        border-left-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .doc-item .doc-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.25rem;
    }
    
    .doc-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .tag-badge {
        font-size: 0.75rem;
        border-radius: 1rem;
        padding: 0.35rem 0.65rem;
        background-color: #e9f3ff;
        color: #0d6efd;
        border: 1px solid #b8daff;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0"><?php echo e($combinedDocuments->count()); ?> dokumen ditemukan</p>
        </div>
        <div>
            <a href="<?php echo e(route('modules.document-management.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="moduleFilter" class="form-label">Filter berdasarkan Modul:</label>
            <select class="form-select" id="moduleFilter">
                <option value="all" <?php echo e($selectedModule == 'all' ? 'selected' : ''); ?>>Semua Modul</option>
                <option value="document-management" <?php echo e($selectedModule == 'document-management' ? 'selected' : ''); ?>>Manajemen Dokumen</option>
                <option value="risk-management" <?php echo e($selectedModule == 'risk-management' ? 'selected' : ''); ?>>Manajemen Risiko</option>
                <option value="correspondence" <?php echo e($selectedModule == 'correspondence' ? 'selected' : ''); ?>>Korespondensi</option>
            </select>
        </div>
    </div>
    
    <!-- Document List -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Dokumen</h5>
                <?php if($tag->description): ?>
                    <span class="badge bg-light text-dark p-2"><?php echo e($tag->description); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if($combinedDocuments->count() > 0): ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $combinedDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isDocument = $document instanceof \App\Models\Document;
                            $isRiskReport = $document instanceof \App\Models\RiskReport;
                            $isCorrespondence = $document instanceof \App\Models\Correspondence;
                            
                            $detailRoute = '#';
                            $iconClass = 'fas fa-file-alt text-secondary';
                            $typeBadge = '<span class="badge bg-secondary">Unknown</span>';
                            
                            if ($isDocument) {
                                $detailRoute = route('modules.document-management.documents.show', $document->id);
                                $iconClass = 'fas fa-file-alt text-primary';
                                $typeBadge = '<span class="badge bg-primary">Dokumen</span>';
                            } elseif ($isRiskReport) {
                                $detailRoute = route('modules.risk-management.risk-reports.show', $document->id);
                                $iconClass = 'fas fa-exclamation-triangle text-danger';
                                $typeBadge = '<span class="badge bg-danger">Laporan Risiko</span>';
                            } elseif ($isCorrespondence) {
                                $detailRoute = route('modules.correspondence.letters.show', $document->id);
                                $iconClass = 'fas fa-envelope text-info';
                                $typeBadge = '<span class="badge bg-info">Korespondensi</span>';
                            }

                            $docDate = $document->document_date ?? $document->created_at;
                            $formattedDate = $docDate ? \Carbon\Carbon::parse($docDate)->format('d M Y') : '-';
                        ?>
                        <div class="doc-item">
                            <a href="<?php echo e($detailRoute); ?>" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="doc-title mb-0">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <?php echo e($document->document_title); ?>

                                        </h5>
                                        <div class="doc-meta">
                                            <span class="me-3"><?php echo e($document->document_number ?? 'No. Dokumen tidak tersedia'); ?></span>
                                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i> <?php echo e($formattedDate); ?></span>
                                            <span>
                                                <?php echo $typeBadge; ?>

                                                
                                                <?php if($document->confidentiality_level): ?>
                                                    <?php
                                                        $bgColor = 'bg-secondary';
                                                        if (strtolower($document->confidentiality_level) == 'public' || strtolower($document->confidentiality_level) == 'publik') {
                                                            $bgColor = 'bg-success';
                                                        } elseif (strtolower($document->confidentiality_level) == 'internal') {
                                                            $bgColor = 'bg-warning text-dark';
                                                        } elseif (strtolower($document->confidentiality_level) == 'confidential' || strtolower($document->confidentiality_level) == 'rahasia') {
                                                            $bgColor = 'bg-danger';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo e($bgColor); ?>"><?php echo e($document->confidentiality_level); ?></span>
                                                <?php endif; ?>
                                                
                                                <?php if($document->file_path): ?>
                                                    <i class="fas fa-paperclip ms-2"></i>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Tidak ada dokumen yang ditemukan dengan tag ini</p>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle me-1"></i> Coba cari dengan tag lain atau kembali ke dashboard
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('moduleFilter').addEventListener('change', function() {
        const selectedModule = this.value;
        const currentUrl = new URL(window.location.href);
        
        if (selectedModule === 'all') {
            currentUrl.searchParams.delete('module');
        } else {
            currentUrl.searchParams.set('module', selectedModule);
        }
        
        window.location.href = currentUrl.toString();
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/DocumentManagement/documents-by-tag.blade.php ENDPATH**/ ?>