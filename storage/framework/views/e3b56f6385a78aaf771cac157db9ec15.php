<?php $__env->startSection('title', ' | Edit Analisis Risiko'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem 1.25rem;
        font-weight: 600;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .factor-category {
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .factor-option {
        margin-bottom: 0.25rem;
    }
    
    .factor-label {
        font-weight: normal;
        cursor: pointer;
    }
    
    .risk-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .risk-high {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .risk-medium {
        background-color: #fff3cd;
        color: #664d03;
    }
    
    .risk-low {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
        color: #6c757d;
    }
    
    .breadcrumb-item.active {
        color: #495057;
        font-weight: 600;
    }
    
    textarea.form-control {
        min-height: 6rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.index')); ?>">Manajemen Risiko</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>">Daftar Laporan</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>"><?php echo e(Str::limit($report->risk_title, 30)); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Analisis</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Analisis Kasus</h2>
            <p class="text-muted mb-0">Perbarui Analisis untuk Laporan Risiko</p>
        </div>
        <div>
            <a href="<?php echo e(route('modules.risk-management.risk-analysis.show', [$report->id, $analysis->id])); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i> Lihat Analisis
            </a>
            <a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan
            </a>
        </div>
    </div>

    <!-- Form Edit Analisis Risiko -->
    <form action="<?php echo e(route('modules.risk-management.risk-analysis.update', [$report->id, $analysis->id])); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Detail Laporan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i> Detail Laporan Risiko
                    </div>
                    <div class="card-body">
                        <!-- Informasi Utama -->
                        <div class="border-bottom pb-3 mb-3">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Nomor Laporan</label>
                                <div class="form-control-plaintext fw-bold"><?php echo e($report->riskreport_number); ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Judul Risiko</label>
                                <div class="form-control-plaintext fw-bold"><?php echo e($report->risk_title); ?></div>
                            </div>
                        </div>

                        <!-- Informasi Risiko -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">INFORMASI RISIKO</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Tipe Risiko</label>
                                    <div class="form-control-plaintext"><?php echo e($report->risk_type ?? '-'); ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Kategori</label>
                                    <div class="form-control-plaintext"><?php echo e($report->risk_category); ?></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Tingkat Risiko</label>
                                    <div>
                                        <?php if($report->risk_level == 'high'): ?>
                                        <span class="risk-badge risk-high">Tinggi</span>
                                        <?php elseif($report->risk_level == 'medium'): ?>
                                        <span class="risk-badge risk-medium">Sedang</span>
                                        <?php else: ?>
                                        <span class="risk-badge risk-low">Rendah</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Dampak</label>
                                    <div class="form-control-plaintext"><?php echo e($report->impact); ?></div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Probabilitas</label>
                                    <div class="form-control-plaintext"><?php echo e($report->probability); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Kejadian -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">DETAIL KEJADIAN</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Kronologi</label>
                                <div class="form-control-plaintext"><?php echo e($report->chronology); ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Deskripsi</label>
                                <div class="form-control-plaintext"><?php echo e($report->description); ?></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Tanggal Kejadian</label>
                                    <div class="form-control-plaintext"><?php echo e($report->occurred_at ? $report->occurred_at->format('d M Y') : '-'); ?></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Unit Pelapor</label>
                                    <div class="form-control-plaintext"><?php echo e($report->reporter_unit); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tindakan dan Rekomendasi -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">TINDAKAN & REKOMENDASI</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Tindakan Segera</label>
                                <div class="form-control-plaintext"><?php echo e($report->immediate_action ?? '-'); ?></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Rekomendasi</label>
                                <div class="form-control-plaintext"><?php echo e($report->recommendation ?? '-'); ?></div>
                            </div>
                        </div>

                        <!-- Status dan Tracking -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">STATUS & TRACKING</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Status Laporan</label>
                                <div>
                                    <?php switch($report->status):
                                        case ('open'): ?>
                                            <span class="badge bg-info">Terbuka</span>
                                            <?php break; ?>
                                        <?php case ('in_review'): ?>
                                            <span class="badge bg-warning">Dalam Peninjauan</span>
                                            <?php break; ?>
                                        <?php case ('resolved'): ?>
                                            <span class="badge bg-success">Selesai</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-secondary"><?php echo e($report->status); ?></span>
                                    <?php endswitch; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Dibuat Oleh</label>
                                    <div class="form-control-plaintext">
                                        <?php echo e($report->creator ? $report->creator->name : '-'); ?>

                                        <?php if($report->created_at): ?>
                                            <small class="text-muted d-block"><?php echo e($report->created_at->format('d M Y H:i')); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if($report->reviewer): ?>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Ditinjau Oleh</label>
                                    <div class="form-control-plaintext">
                                        <?php echo e($report->reviewer->name); ?>

                                        <?php if($report->reviewed_at): ?>
                                            <small class="text-muted d-block"><?php echo e($report->reviewed_at->format('d M Y H:i')); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if($report->approver): ?>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Disetujui Oleh</label>
                                    <div class="form-control-plaintext">
                                        <?php echo e($report->approver->name); ?>

                                        <?php if($report->approved_at): ?>
                                            <small class="text-muted d-block"><?php echo e($report->approved_at->format('d M Y H:i')); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Lampiran -->
                        <?php if($report->attachments && count($report->attachments) > 0): ?>
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">LAMPIRAN</h6>
                            <div class="list-group">
                                <?php $__currentLoopData = $report->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('modules.risk-management.risk-reports.download-attachment', [$report->id, $attachment->id])); ?>" 
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-paperclip me-2"></i><?php echo e($attachment->original_name); ?>

                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Analisis Penyebab -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-search me-2"></i> Analisis Penyebab
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="direct_cause" class="form-label">Penyebab Langsung <span class="text-danger">*</span></label>
                            <textarea id="direct_cause" name="direct_cause" class="form-control <?php $__errorArgs = ['direct_cause'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('direct_cause', $analysis->direct_cause)); ?></textarea>
                            <?php $__errorArgs = ['direct_cause'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Faktor atau kondisi yang secara langsung menyebabkan kejadian risiko.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="root_cause" class="form-label">Akar Masalah <span class="text-danger">*</span></label>
                            <textarea id="root_cause" name="root_cause" class="form-control <?php $__errorArgs = ['root_cause'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('root_cause', $analysis->root_cause)); ?></textarea>
                            <?php $__errorArgs = ['root_cause'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Analisis mendalam tentang akar masalah yang mendasari kejadian risiko.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Faktor Kontributor</label>
                            <small class="d-block text-muted mb-2">Pilih faktor-faktor yang berkontribusi pada terjadinya risiko.</small>
                            
                            <div class="row">
                                <?php $__currentLoopData = $contributorFactors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $factors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="factor-category">
                                        <?php switch($category):
                                            case ('organizational'): ?>
                                                <i class="fas fa-sitemap me-1"></i> Faktor Organisasi
                                                <?php break; ?>
                                            <?php case ('human_factors'): ?>
                                                <i class="fas fa-user me-1"></i> Faktor Manusia
                                                <?php break; ?>
                                            <?php case ('technical'): ?>
                                                <i class="fas fa-tools me-1"></i> Faktor Teknis
                                                <?php break; ?>
                                            <?php case ('environmental'): ?>
                                                <i class="fas fa-tree me-1"></i> Faktor Lingkungan
                                                <?php break; ?>
                                            <?php default: ?>
                                                <i class="fas fa-list me-1"></i> <?php echo e(Str::title(str_replace('_', ' ', $category))); ?>

                                        <?php endswitch; ?>
                                    </div>
                                    
                                    <?php $__currentLoopData = $factors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="factor-option form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="factor_<?php echo e($category); ?>_<?php echo e($key); ?>" 
                                               name="contributor_factors[<?php echo e($category); ?>][]" 
                                               value="<?php echo e($key); ?>"
                                               <?php echo e(isset($analysis->contributor_factors[$category]) && in_array($key, $analysis->contributor_factors[$category]) ? 'checked' : ''); ?>>
                                        <label class="form-check-label factor-label" for="factor_<?php echo e($category); ?>_<?php echo e($key); ?>">
                                            <?php echo e($label); ?>

                                        </label>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rekomendasi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-lightbulb me-2"></i> Rekomendasi
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="recommendation_short" class="form-label">Rekomendasi Jangka Pendek <span class="text-danger">*</span></label>
                            <textarea id="recommendation_short" name="recommendation_short" class="form-control <?php $__errorArgs = ['recommendation_short'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('recommendation_short', $analysis->recommendation_short)); ?></textarea>
                            <?php $__errorArgs = ['recommendation_short'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Tindakan segera untuk mengatasi masalah (0-3 bulan).</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommendation_medium" class="form-label">Rekomendasi Jangka Menengah</label>
                            <textarea id="recommendation_medium" name="recommendation_medium" class="form-control <?php $__errorArgs = ['recommendation_medium'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('recommendation_medium', $analysis->recommendation_medium)); ?></textarea>
                            <?php $__errorArgs = ['recommendation_medium'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Tindakan dalam 3-6 bulan ke depan.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommendation_long" class="form-label">Rekomendasi Jangka Panjang</label>
                            <textarea id="recommendation_long" name="recommendation_long" class="form-control <?php $__errorArgs = ['recommendation_long'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('recommendation_long', $analysis->recommendation_long)); ?></textarea>
                            <?php $__errorArgs = ['recommendation_long'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Perubahan sistem atau kebijakan jangka panjang (6+ bulan).</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Status Analisis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Status Analisis
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="analysis_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="analysis_status" name="analysis_status" class="form-select <?php $__errorArgs = ['analysis_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="draft" <?php echo e(old('analysis_status', $analysis->analysis_status) == 'draft' ? 'selected' : ''); ?>>Draft</option>
                                <option value="in_progress" <?php echo e(old('analysis_status', $analysis->analysis_status) == 'in_progress' || old('analysis_status', $analysis->analysis_status) == 'reviewed' ? 'selected' : ''); ?>>Ditinjau</option>
                                <option value="completed" <?php echo e(old('analysis_status', $analysis->analysis_status) == 'completed' ? 'selected' : ''); ?>>Selesai</option>
                            </select>
                            <?php $__errorArgs = ['analysis_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-text text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i> Analisis terakhir diperbarui pada <?php echo e($analysis->updated_at->format('d M Y, H:i')); ?>

                                <?php if($analysis->analyst): ?>
                                 oleh <?php echo e($analysis->analyst->name); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                        
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('modules.risk-management.risk-analysis.show', [$report->id, $analysis->id])); ?>" class="btn btn-outline-secondary w-50">
                                <i class="fas fa-eye me-1"></i> Lihat
                            </a>
                            <a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>" class="btn btn-outline-secondary w-50">
                                Batal
                            </a>
                        </div>
                        
                        <?php if($analysis->analysis_status == 'completed'): ?>
                        <div class="mt-3">
                            <a href="<?php echo e(route('modules.risk-management.risk-analysis.qr-code', [$report->id, $analysis->id])); ?>" class="btn btn-dark w-100" target="_blank">
                                <i class="fas fa-qrcode me-1"></i> Generate QR Code Tanda Tangan
                            </a>
                            <small class="d-block text-muted mt-1 text-center">QR Code untuk menandatangani penyelesaian analisis</small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Panduan Analisis Kasus</h6>
                        <p class="small mb-2">Analisis kasus membantu mengidentifikasi penyebab dan faktor yang berkontribusi pada terjadinya insiden, serta menentukan tindakan yang tepat untuk mencegah kejadian serupa di masa depan.</p>
                        <ul class="small ps-3 mb-0">
                            <li class="mb-1">Identifikasi penyebab langsung dan akar masalah</li>
                            <li class="mb-1">Pertimbangkan semua faktor yang berkontribusi</li>
                            <li class="mb-1">Buat rekomendasi yang SMART (Spesifik, Terukur, Dapat Dicapai, Relevan, dan Terikat Waktu)</li>
                            <li>Tetapkan prioritas tindakan berdasarkan dampak dan kemudahan implementasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kode JavaScript tambahan jika diperlukan
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-analysis/edit.blade.php ENDPATH**/ ?>