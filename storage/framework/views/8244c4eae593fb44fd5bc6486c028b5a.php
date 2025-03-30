<div class="mb-3">
    <label class="form-label">Dokumen Terkait</label>
    <div class="card">
        <div class="card-body bg-light">
            <?php if($documents->count() > 0): ?>
                <div class="alert alert-info mb-3">
                    <small><i class="fas fa-info-circle me-1"></i> Dokumen yang dipilih akan tertampil di dashboard manajemen dokumen.</small>
                </div>
                <div class="row">
                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" 
                                    name="document_ids[]" 
                                    value="<?php echo e($doc->id); ?>"
                                    id="doc-<?php echo e($doc->id); ?>"
                                    <?php if(isset($riskReport) && $riskReport->documents->contains($doc->id)): ?> checked <?php endif; ?>>
                                <label class="form-check-label" for="doc-<?php echo e($doc->id); ?>">
                                    <?php echo e($doc->document_title); ?>

                                    <small class="d-block text-muted"><?php echo e($doc->document_number); ?></small>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total dokumen tersedia: <?php echo e($documents->count()); ?></small>
                    <?php if(isset($riskReport)): ?>
                        <small class="d-block text-muted">Dokumen terkait dengan laporan ini: <?php echo e($riskReport->documents->count()); ?></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i> Belum ada dokumen yang tersedia. 
                    <a href="<?php echo e(route('modules.document-management.documents.create')); ?>" class="alert-link" target="_blank">Tambahkan dokumen baru</a> terlebih dahulu.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-reports/partials/_document_selector.blade.php ENDPATH**/ ?>