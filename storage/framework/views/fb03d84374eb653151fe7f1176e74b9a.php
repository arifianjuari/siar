<?php $__env->startSection('title', ' | Edit Laporan Risiko'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-section {
        box-shadow: 0 4px 8px rgba(0,0,0,0.03);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        border: none;
    }
    
    .form-section:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .form-section .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4F46E5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-primary {
        background-color: #4F46E5;
        border-color: #4F46E5;
    }
    
    .btn-primary:hover {
        background-color: #4338CA;
        border-color: #4338CA;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .required-field::after {
        content: "*";
        color: #EF4444;
        margin-left: 4px;
    }
    
    /* Progress Bar/Stepper Styles */
    .form-stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        position: relative;
        z-index: 2;
        transition: all 0.3s;
        font-weight: 600;
        color: #6c757d;
    }
    
    .step-title {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .step.active .step-circle {
        background-color: #4F46E5;
        border-color: #4F46E5;
        color: white;
    }
    
    .step.active .step-title {
        color: #4F46E5;
        font-weight: 600;
    }
    
    .step.completed .step-circle {
        background-color: #10B981;
        border-color: #10B981;
        color: white;
    }
    
    .step.completed .step-title {
        color: #10B981;
    }
    
    .step-connector {
        position: absolute;
        top: 20px;
        left: calc(50% + 20px);
        right: calc(50% - 20px);
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }
    
    .step:last-child .step-connector {
        display: none;
    }
    
    .step.completed .step-connector,
    .step.active .step-connector {
        background-color: #4F46E5;
    }
    
    /* Tingkat Risiko Visualization */
    .risk-matrix {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .risk-level-indicator {
        width: 100%;
        height: 10px;
        background: linear-gradient(to right, #10B981, #FBBF24, #EF4444);
        border-radius: 5px;
        margin-bottom: 5px;
        position: relative;
    }
    
    .risk-level-marker {
        width: 20px;
        height: 20px;
        background-color: #4F46E5;
        border-radius: 50%;
        position: absolute;
        top: -5px;
        transform: translateX(-50%);
        left: 50%;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
        border: 2px solid white;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Form Card -->
        <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.update', $riskReport->id)); ?>" class="needs-validation" id="riskReportForm" enctype="multipart/form-data" novalidate>
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <!-- Header dan Tombol Kembali -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-edit me-2"></i>
                    <h1 class="h3 mb-0">Edit Laporan Risiko</h1>
                </div>
                <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
            
            <!-- Intro Section -->
            <div class="card form-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <h5 class="mb-0">Informasi Laporan</h5>
                    </div>
                    <span class="badge bg-primary">ID: <?php echo e($riskReport->id); ?></span>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Terdapat kesalahan dalam pengisian formulir:</h5>
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Status Laporan</h5>
                                <p class="mb-0">
                                    <?php if($riskReport->status == 'Draft'): ?>
                                        <span class="badge bg-danger">Draft</span>
                                    <?php elseif($riskReport->status == 'Ditinjau'): ?>
                                        <span class="badge bg-warning text-dark">Ditinjau</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php endif; ?>
                                    &nbsp; Dibuat pada: <?php echo e($riskReport->created_at->format('d/m/Y H:i')); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 1: Informasi Dasar -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i> Informasi Dasar Insiden</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="document_title" class="form-label required-field">Judul Insiden</label>
                        <input type="text" name="document_title" id="document_title" class="form-control form-control-lg <?php $__errorArgs = ['document_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('document_title', $riskReport->document_title)); ?>" required>
                        <?php $__errorArgs = ['document_title'];
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
                    
                    <div class="mb-4">
                        <label for="document_number" class="form-label required-field">Nomor Dokumen</label>
                        <input type="text" name="document_number" id="document_number" class="form-control <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('document_number', $riskReport->document_number)); ?>" required>
                        <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Format: RIR/TAHUN/NOMOR (contoh: RIR/2025/001)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reporter_unit" class="form-label required-field">Unit Pelapor</label>
                            <select name="reporter_unit" id="reporter_unit" class="form-select <?php $__errorArgs = ['reporter_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php $__currentLoopData = $workUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($unit->name); ?>" <?php echo e(old('reporter_unit', $riskReport->reporter_unit) == $unit->name ? 'selected' : ''); ?>>
                                        <?php echo e($unit->name); ?> <?php echo e($unit->code ? '('.$unit->code.')' : ''); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['reporter_unit'];
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
                        
                        <div class="col-md-6 mb-3">
                            <label for="risk_type" class="form-label required-field">Tipe Risiko</label>
                            <select name="risk_type" id="risk_type" class="form-select <?php $__errorArgs = ['risk_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Tipe Risiko --</option>
                                <option value="KTC" <?php echo e(old('risk_type', $riskReport->risk_type) == 'KTC' ? 'selected' : ''); ?>>Kejadian Tidak Cedera (KTC)</option>
                                <option value="KPC" <?php echo e(old('risk_type', $riskReport->risk_type) == 'KPC' ? 'selected' : ''); ?>>Kejadian Potensial Cedera (KPC)</option>
                                <option value="KNC" <?php echo e(old('risk_type', $riskReport->risk_type) == 'KNC' ? 'selected' : ''); ?>>Kejadian Nyaris Cedera (KNC)</option>
                                <option value="KTD" <?php echo e(old('risk_type', $riskReport->risk_type) == 'KTD' ? 'selected' : ''); ?>>Kejadian Tidak Diharapkan (KTD)</option>
                                <option value="Sentinel" <?php echo e(old('risk_type', $riskReport->risk_type) == 'Sentinel' ? 'selected' : ''); ?>>Kejadian Sentinel</option>
                            </select>
                            <?php $__errorArgs = ['risk_type'];
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="risk_category" class="form-label required-field">Kategori Risiko</label>
                            <select name="risk_category" id="risk_category" class="form-select <?php $__errorArgs = ['risk_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Medis" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Medis' ? 'selected' : ''); ?>>Medis</option>
                                <option value="Non-medis" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Non-medis' ? 'selected' : ''); ?>>Non-medis</option>
                                <option value="Pasien" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Pasien' ? 'selected' : ''); ?>>Pasien</option>
                                <option value="Pengunjung" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Pengunjung' ? 'selected' : ''); ?>>Pengunjung</option>
                                <option value="Fasilitas" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Fasilitas' ? 'selected' : ''); ?>>Fasilitas</option>
                                <option value="Karyawan" <?php echo e(old('risk_category', $riskReport->risk_category) == 'Karyawan' ? 'selected' : ''); ?>>Karyawan</option>
                            </select>
                            <?php $__errorArgs = ['risk_category'];
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
                        
                        <div class="col-md-6 mb-3">
                            <label for="occurred_at" class="form-label required-field">Tanggal Kejadian</label>
                            <input type="date" name="occurred_at" id="occurred_at" class="form-control <?php $__errorArgs = ['occurred_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('occurred_at', $riskReport->occurred_at ? date('Y-m-d', strtotime($riskReport->occurred_at)) : '')); ?>" required>
                            <?php $__errorArgs = ['occurred_at'];
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
                    </div>
                </div>
            </div>
            
            <!-- Section 2: Penilaian Risiko -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Penilaian Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="impact" class="form-label required-field">Dampak</label>
                            <select name="impact" id="impact" class="form-select <?php $__errorArgs = ['impact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Dampak --</option>
                                <option value="ringan" <?php echo e(old('impact', $riskReport->impact) == 'ringan' ? 'selected' : ''); ?>>Ringan</option>
                                <option value="sedang" <?php echo e(old('impact', $riskReport->impact) == 'sedang' ? 'selected' : ''); ?>>Sedang</option>
                                <option value="berat" <?php echo e(old('impact', $riskReport->impact) == 'berat' ? 'selected' : ''); ?>>Berat</option>
                            </select>
                            <?php $__errorArgs = ['impact'];
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

                        <div class="col-md-6 mb-3">
                            <label for="probability" class="form-label required-field">Probabilitas</label>
                            <select name="probability" id="probability" class="form-select <?php $__errorArgs = ['probability'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Probabilitas --</option>
                                <option value="jarang" <?php echo e(old('probability', $riskReport->probability) == 'jarang' ? 'selected' : ''); ?>>Jarang</option>
                                <option value="kadang" <?php echo e(old('probability', $riskReport->probability) == 'kadang' ? 'selected' : ''); ?>>Kadang</option>
                                <option value="sering" <?php echo e(old('probability', $riskReport->probability) == 'sering' ? 'selected' : ''); ?>>Sering</option>
                            </select>
                            <?php $__errorArgs = ['probability'];
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

                        <div class="col-md-6">
                            <label for="risk_level" class="form-label required-field">Tingkat Risiko</label>
                            <select name="risk_level" id="risk_level" class="form-select <?php $__errorArgs = ['risk_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Tingkat Risiko --</option>
                                <option value="rendah" <?php echo e(old('risk_level', $riskReport->risk_level) == 'rendah' ? 'selected' : ''); ?>>Rendah</option>
                                <option value="sedang" <?php echo e(old('risk_level', $riskReport->risk_level) == 'sedang' ? 'selected' : ''); ?>>Sedang</option>
                                <option value="tinggi" <?php echo e(old('risk_level', $riskReport->risk_level) == 'tinggi' ? 'selected' : ''); ?>>Tinggi</option>
                                <option value="ekstrem" <?php echo e(old('risk_level', $riskReport->risk_level) == 'ekstrem' ? 'selected' : ''); ?>>Ekstrem</option>
                            </select>
                            <?php $__errorArgs = ['risk_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            
                            <div class="risk-matrix mt-3">
                                <div class="risk-level-indicator">
                                    <div class="risk-level-marker" id="riskLevelMarker" style="left: 25%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small text-success">Rendah</span>
                                    <span class="small text-warning">Sedang</span>
                                    <span class="small text-danger">Tinggi</span>
                                    <span class="small text-danger fw-bold">Ekstrem</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label required-field">Status</label>
                            <select name="status" id="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="Draft" <?php echo e(old('status', $riskReport->status) == 'Draft' ? 'selected' : ''); ?>>Draft</option>
                                <option value="Ditinjau" <?php echo e(old('status', $riskReport->status) == 'Ditinjau' ? 'selected' : ''); ?>>Ditinjau</option>
                                <option value="Selesai" <?php echo e(old('status', $riskReport->status) == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                            </select>
                            <?php $__errorArgs = ['status'];
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
                    </div>
                </div>
            </div>
            
            <!-- Section 3: Deskripsi Insiden -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-align-left me-2"></i> Deskripsi Insiden</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="chronology" class="form-label required-field">Kronologi Singkat</label>
                        <textarea name="chronology" id="chronology" rows="3" class="form-control <?php $__errorArgs = ['chronology'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Jelaskan secara singkat gambaran kejadian..."><?php echo e(old('chronology', $riskReport->chronology)); ?></textarea>
                        <?php $__errorArgs = ['chronology'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Jelaskan secara singkat gambaran kejadian.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label required-field">Detil Kejadian</label>
                        <textarea name="description" id="description" rows="5" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Jelaskan secara detail kronologi kejadian, lokasi, dan pihak yang terlibat..."><?php echo e(old('description', $riskReport->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Jelaskan secara detail kronologi kejadian, lokasi, dan pihak yang terlibat.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="immediate_action" class="form-label required-field">Tindakan Segera yang Dilakukan</label>
                        <textarea name="immediate_action" id="immediate_action" rows="3" class="form-control <?php $__errorArgs = ['immediate_action'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('immediate_action', $riskReport->immediate_action)); ?></textarea>
                        <?php $__errorArgs = ['immediate_action'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Jelaskan tindakan apa yang segera diambil saat kejadian berlangsung.</div>
                    </div>

                    <div class="mb-3">
                        <label for="recommendation" class="form-label">Rekomendasi</label>
                        <textarea name="recommendation" id="recommendation" rows="3" class="form-control <?php $__errorArgs = ['recommendation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Berikan rekomendasi untuk mencegah kejadian serupa..."><?php echo e(old('recommendation', $riskReport->recommendation)); ?></textarea>
                        <?php $__errorArgs = ['recommendation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Berikan rekomendasi untuk mencegah kejadian serupa di masa mendatang.</div>
                    </div>
                </div>
            </div>
            
            <!-- Section 4: Informasi Dokumen -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Informasi Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="document_type" class="form-label">Tipe Dokumen</label>
                            <select name="document_type" id="document_type" class="form-select <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Tipe Dokumen --</option>
                                <option value="Regulasi" <?php echo e(old('document_type', $riskReport->document_type) == 'Regulasi' ? 'selected' : ''); ?>>Regulasi</option>
                                <option value="Bukti" <?php echo e(old('document_type', $riskReport->document_type) == 'Bukti' ? 'selected' : ''); ?>>Bukti</option>
                            </select>
                            <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Jenis dokumen akan menentukan bagaimana laporan ini dikategorikan di modul Manajemen Dokumen.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="document_version" class="form-label">Versi Dokumen</label>
                            <input type="text" name="document_version" id="document_version" class="form-control <?php $__errorArgs = ['document_version'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('document_version', $riskReport->document_version)); ?>">
                            <?php $__errorArgs = ['document_version'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Misalnya: 1.0, 2.1, dst.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan</label>
                            <select name="confidentiality_level" id="confidentiality_level" class="form-select <?php $__errorArgs = ['confidentiality_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Pilih Tingkat Kerahasiaan --</option>
                                <option value="Publik" <?php echo e(old('confidentiality_level', $riskReport->confidentiality_level) == 'Publik' ? 'selected' : ''); ?>>Publik</option>
                                <option value="Internal" <?php echo e(old('confidentiality_level', $riskReport->confidentiality_level) == 'Internal' ? 'selected' : ''); ?>>Internal</option>
                                <option value="Rahasia" <?php echo e(old('confidentiality_level', $riskReport->confidentiality_level) == 'Rahasia' ? 'selected' : ''); ?>>Rahasia</option>
                            </select>
                            <?php $__errorArgs = ['confidentiality_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Menentukan siapa yang boleh mengakses laporan ini.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="document_file" class="form-label">File Dokumen</label>
                            <input type="file" name="document_file" id="document_file" class="form-control <?php $__errorArgs = ['document_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['document_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            
                            <?php if($riskReport->file_path): ?>
                                <div class="mt-2">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> File telah diunggah: 
                                        <a href="<?php echo e(asset('storage/' . $riskReport->file_path)); ?>" target="_blank">Lihat file</a>
                                    </small>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX. Maks. 10MB.</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="next_review" class="form-label">Tanggal Tinjauan Berikutnya</label>
                            <input type="date" name="next_review" id="next_review" class="form-control <?php $__errorArgs = ['next_review'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('next_review', $riskReport->next_review ? date('Y-m-d', strtotime($riskReport->next_review)) : '')); ?>">
                            <?php $__errorArgs = ['next_review'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Kapan dokumen ini perlu ditinjau kembali.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="review_cycle_months" class="form-label">Siklus Tinjauan (bulan)</label>
                            <input type="number" name="review_cycle_months" id="review_cycle_months" class="form-control <?php $__errorArgs = ['review_cycle_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('review_cycle_months', $riskReport->review_cycle_months)); ?>" min="0" max="60">
                            <?php $__errorArgs = ['review_cycle_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Berapa bulan sekali dokumen ini perlu ditinjau ulang.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update risk level marker position
        const riskLevelSelect = document.getElementById('risk_level');
        const riskLevelMarker = document.getElementById('riskLevelMarker');
        const impactSelect = document.getElementById('impact');
        const probabilitySelect = document.getElementById('probability');
        
        function updateRiskMarker() {
            const level = riskLevelSelect.value;
            
            switch(level) {
                case 'rendah':
                    riskLevelMarker.style.left = '12.5%';
                    break;
                case 'sedang':
                    riskLevelMarker.style.left = '37.5%';
                    break;
                case 'tinggi':
                    riskLevelMarker.style.left = '62.5%';
                    break;
                case 'ekstrem':
                    riskLevelMarker.style.left = '87.5%';
                    break;
                default:
                    riskLevelMarker.style.left = '25%';
            }
        }
        
        function calculateRiskLevel() {
            const impact = impactSelect.value;
            const probability = probabilitySelect.value;
            
            if (!impact || !probability) {
                return;
            }
            
            let riskLevel = '';
            
            // Matriks risiko
            if (impact === 'ringan' && probability === 'jarang') {
                riskLevel = 'rendah';
            } else if (impact === 'ringan' && probability === 'kadang') {
                riskLevel = 'rendah';
            } else if (impact === 'ringan' && probability === 'sering') {
                riskLevel = 'sedang';
            } else if (impact === 'sedang' && probability === 'jarang') {
                riskLevel = 'rendah';
            } else if (impact === 'sedang' && probability === 'kadang') {
                riskLevel = 'sedang';
            } else if (impact === 'sedang' && probability === 'sering') {
                riskLevel = 'tinggi';
            } else if (impact === 'berat' && probability === 'jarang') {
                riskLevel = 'sedang';
            } else if (impact === 'berat' && probability === 'kadang') {
                riskLevel = 'tinggi';
            } else if (impact === 'berat' && probability === 'sering') {
                riskLevel = 'ekstrem';
            }
            
            // Set the risk level select value
            riskLevelSelect.value = riskLevel;
            
            // Update the marker
            updateRiskMarker();
        }
        
        // Initialize marker position
        updateRiskMarker();
        
        // Update risk level when impact or probability changes
        impactSelect.addEventListener('change', calculateRiskLevel);
        probabilitySelect.addEventListener('change', calculateRiskLevel);
        
        // Calculate initial risk level if impact and probability have values
        if (impactSelect.value && probabilitySelect.value) {
            calculateRiskLevel();
        }
        
        // Form validation
        const form = document.getElementById('riskReportForm');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', ['hideDefaultHeader' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-reports/edit.blade.php ENDPATH**/ ?>