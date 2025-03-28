<?php $__env->startSection('title', ' | Detail Laporan Risiko'); ?>

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
    
    .section-heading {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #495057;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .info-label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        margin-bottom: 1rem;
    }
    
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .status-open {
        background-color: #dc3545;
        color: white;
    }
    
    .status-in-review {
        background-color: #ffc107;
        color: #000;
    }
    
    .status-resolved {
        background-color: #198754;
        color: white;
    }
    
    .risk-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .risk-high {
        background-color: #dc3545;
        color: white;
    }
    
    .risk-medium {
        background-color: #ffc107;
        color: #000;
    }
    
    .risk-low {
        background-color: #198754;
        color: white;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.index')); ?>" class="text-decoration-none">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="text-decoration-none">Daftar Laporan</a></li>
                    <li class="breadcrumb-item active">Detail Laporan</li>
                </ol>
            </nav>
        </div>
        <div>
            <?php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant-admin' || 
                                  strtolower($userRole) === 'tenant-admin';
            ?>
            
            <?php if(auth()->user()->role && $isTenantAdmin): ?>
                <a href="<?php echo e(route('modules.risk-management.analysis-config')); ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
                </a>
            <?php endif; ?>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', [App\Models\RiskAnalysis::class, $riskReport])): ?>
                <?php if(!$riskReport->analysis): ?>
                    <a href="<?php echo e(route('modules.risk-management.risk-analysis.create', $riskReport->id)); ?>" class="btn btn-primary">
                        <i class="fas fa-chart-line me-1"></i> Buat Analisis
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <?php if(!$riskReport->analysis && $isTenantAdmin): ?>
                    <div class="alert alert-warning d-inline-block py-1 px-3 mb-0 ms-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> Akses analisis perlu dikonfigurasi
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informasi Utama -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo e($riskReport->risk_title); ?></h5>
                    <div>
                        <span class="status-badge status-<?php echo e($riskReport->status); ?>">
                            <?php echo e(ucfirst($riskReport->status)); ?>

                        </span>
                        <span class="risk-badge risk-<?php echo e(strtolower($riskReport->risk_level)); ?> ms-2">
                            Risiko <?php echo e(ucfirst($riskReport->risk_level)); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label">Unit Pelapor</div>
                            <div class="info-value"><?php echo e($riskReport->reporter_unit); ?></div>
                            
                            <div class="info-label">Tipe Risiko</div>
                            <div class="info-value"><?php echo e($riskReport->risk_type); ?></div>
                            
                            <div class="info-label">Kategori Risiko</div>
                            <div class="info-value"><?php echo e($riskReport->risk_category); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Tanggal Kejadian</div>
                            <div class="info-value"><?php echo e($riskReport->occurred_at->format('d/m/Y')); ?></div>
                            
                            <div class="info-label">Dampak</div>
                            <div class="info-value"><?php echo e($riskReport->impact); ?></div>
                            
                            <div class="info-label">Probabilitas</div>
                            <div class="info-value"><?php echo e($riskReport->probability); ?></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Kronologi Singkat</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->chronology); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Detil Kejadian</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->description); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Tindakan Segera</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->immediate_action); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi</div>
                        <div class="info-value mb-0" style="white-space: pre-line"><?php echo e($riskReport->recommendation); ?></div>
                    </div>
                </div>
            </div>

            <!-- Analisis Risiko -->
            <?php if($riskReport->analysis): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Analisis Risiko</h5>
                    <div>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', [$riskReport->analysis])): ?>
                        <a href="<?php echo e(route('modules.risk-management.risk-analysis.show', ['reportId' => $riskReport->id, 'id' => $riskReport->analysis->id])); ?>" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Detail
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', [$riskReport->analysis])): ?>
                        <a href="<?php echo e(route('modules.risk-management.risk-analysis.edit', ['reportId' => $riskReport->id, 'id' => $riskReport->analysis->id])); ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Analisis
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Status Analisis:</strong> <?php echo e($riskReport->analysis->status); ?>

                                <br>
                                <small>Dianalisis oleh <?php echo e($riskReport->analysis->analyst->name); ?> pada <?php echo e($riskReport->analysis->created_at->format('d/m/Y H:i')); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Penyebab Langsung</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->analysis->direct_cause); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Akar Masalah</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->analysis->root_cause); ?></div>
                    </div>

                    <h6 class="section-heading mb-3">Faktor Kontributor</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label">Faktor Manusia</div>
                            <div class="info-value">
                                <?php if(isset($riskReport->analysis->contributor_factors['human_factors'])): ?>
                                    <?php
                                        $humanFactors = [
                                            'knowledge' => 'Pengetahuan',
                                            'fatigue' => 'Kelelahan',
                                            'stress' => 'Stres',
                                            'communication' => 'Komunikasi',
                                            'teamwork' => 'Kerja Tim',
                                            'supervision' => 'Pengawasan',
                                            'experience' => 'Pengalaman',
                                            'attitude' => 'Sikap'
                                        ];
                                    ?>
                                    <?php if(is_array($riskReport->analysis->contributor_factors['human_factors'])): ?>
                                        <?php echo e(implode(', ', array_map(function($item) use ($humanFactors) { 
                                            return $humanFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['human_factors']))); ?>

                                    <?php else: ?>
                                        <?php echo e($humanFactors[$riskReport->analysis->contributor_factors['human_factors']] ?? $riskReport->analysis->contributor_factors['human_factors']); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                            
                            <div class="info-label">Faktor Lingkungan</div>
                            <div class="info-value">
                                <?php if(isset($riskReport->analysis->contributor_factors['environmental'])): ?>
                                    <?php
                                        $environmentalFactors = [
                                            'temperature' => 'Suhu',
                                            'lighting' => 'Pencahayaan',
                                            'noise' => 'Kebisingan',
                                            'space_constraints' => 'Keterbatasan Ruang',
                                            'cleanliness' => 'Kebersihan',
                                            'ventilation' => 'Ventilasi',
                                            'workplace_layout' => 'Tata Letak Tempat Kerja'
                                        ];
                                    ?>
                                    <?php if(is_array($riskReport->analysis->contributor_factors['environmental'])): ?>
                                        <?php echo e(implode(', ', array_map(function($item) use ($environmentalFactors) { 
                                            return $environmentalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['environmental']))); ?>

                                    <?php else: ?>
                                        <?php echo e($environmentalFactors[$riskReport->analysis->contributor_factors['environmental']] ?? $riskReport->analysis->contributor_factors['environmental']); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Faktor Teknis</div>
                            <div class="info-value">
                                <?php if(isset($riskReport->analysis->contributor_factors['technical'])): ?>
                                    <?php
                                        $technicalFactors = [
                                            'equipment_failure' => 'Kegagalan Peralatan',
                                            'software_issues' => 'Masalah Perangkat Lunak',
                                            'maintenance' => 'Pemeliharaan',
                                            'design_issues' => 'Masalah Desain',
                                            'technical_documentation' => 'Dokumentasi Teknis',
                                            'calibration' => 'Kalibrasi',
                                            'compatibility' => 'Kompatibilitas'
                                        ];
                                    ?>
                                    <?php if(is_array($riskReport->analysis->contributor_factors['technical'])): ?>
                                        <?php echo e(implode(', ', array_map(function($item) use ($technicalFactors) { 
                                            return $technicalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['technical']))); ?>

                                    <?php else: ?>
                                        <?php echo e($technicalFactors[$riskReport->analysis->contributor_factors['technical']] ?? $riskReport->analysis->contributor_factors['technical']); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>

                            <div class="info-label">Faktor Organisasi</div>
                            <div class="info-value">
                                <?php if(isset($riskReport->analysis->contributor_factors['organizational'])): ?>
                                    <?php
                                        $organizationalFactors = [
                                            'policies_procedures' => 'Kebijakan & Prosedur',
                                            'staffing' => 'Kepegawaian',
                                            'training' => 'Pelatihan',
                                            'leadership' => 'Kepemimpinan',
                                            'resource_allocation' => 'Alokasi Sumber Daya',
                                            'organizational_culture' => 'Budaya Organisasi',
                                            'communication_systems' => 'Sistem Komunikasi'
                                        ];
                                    ?>
                                    <?php if(is_array($riskReport->analysis->contributor_factors['organizational'])): ?>
                                        <?php echo e(implode(', ', array_map(function($item) use ($organizationalFactors) { 
                                            return $organizationalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['organizational']))); ?>

                                    <?php else: ?>
                                        <?php echo e($organizationalFactors[$riskReport->analysis->contributor_factors['organizational']] ?? $riskReport->analysis->contributor_factors['organizational']); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h6 class="section-heading mb-3">Rekomendasi</h6>
                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Pendek (0-3 bulan)</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->analysis->recommendation_short); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Menengah (3-6 bulan)</div>
                        <div class="info-value" style="white-space: pre-line"><?php echo e($riskReport->analysis->recommendation_medium ?: '-'); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Panjang (6+ bulan)</div>
                        <div class="info-value mb-0" style="white-space: pre-line"><?php echo e($riskReport->analysis->recommendation_long ?: '-'); ?></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', [App\Models\RiskAnalysis::class, $riskReport])): ?>
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-chart-line fa-4x mb-3"></i>
                            <h5>Analisis Risiko Belum Dibuat</h5>
                            <p>Lakukan analisis mendalam untuk identifikasi akar masalah dan rekomendasi penyelesaian.</p>
                        </div>
                        <a href="<?php echo e(route('modules.risk-management.risk-analysis.create', $riskReport->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Buat Analisis Baru
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Informasi Pelapor -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pelapor</h5>
                </div>
                <div class="card-body">
                    <div class="info-label">Dilaporkan oleh</div>
                    <div class="info-value"><?php echo e($riskReport->creator->name); ?></div>

                    <div class="info-label">Tanggal Laporan</div>
                    <div class="info-value"><?php echo e($riskReport->created_at->format('d/m/Y H:i')); ?></div>
                </div>
            </div>

            <!-- Tanda Tangan Digital -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tanda Tangan Digital</h5>
                </div>
                <div class="card-body text-center">
                    <?php if($riskReport->status === 'resolved'): ?>
                        <img src="<?php echo e(route('modules.risk-management.risk-reports.qr-code', $riskReport->id)); ?>" 
                             alt="QR Code" class="img-fluid mb-2" style="max-width: 200px;">
                        <p class="text-muted small mb-0">Scan QR code untuk verifikasi laporan</p>
                    <?php elseif($riskReport->analysis && $riskReport->analysis->analysis_status === 'completed'): ?>
                        <img src="<?php echo e(route('modules.risk-management.risk-analysis.qr-code', [$riskReport->id, $riskReport->analysis->id])); ?>" 
                             alt="QR Code Analisis" class="img-fluid mb-2" style="max-width: 200px;">
                        <p class="text-muted small">Scan QR code untuk verifikasi analisis</p>
                        <p class="text-muted small mb-0">Status: <span class="badge bg-success">Analisis Selesai</span></p>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            QR code akan tersedia setelah laporan disetujui atau analisis selesai
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('modules.risk-management.risk-reports.export-awal', $riskReport->id)); ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-word me-1"></i> Export Laporan Awal
                        </a>
                        
                        <?php if($riskReport->status === 'resolved' || ($riskReport->analysis && $riskReport->analysis->analysis_status === 'completed')): ?>
                        <a href="<?php echo e(route('modules.risk-management.risk-reports.export-akhir', $riskReport->id)); ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF Laporan Final
                        </a>
                        <?php endif; ?>

                        <a href="<?php echo e(route('modules.risk-management.risk-reports.qr-code', $riskReport->id)); ?>" 
                           class="btn btn-dark" target="_blank">
                            <i class="fas fa-qrcode me-1"></i> Generate QR Code Tanda Tangan
                        </a>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', $riskReport)): ?>
                        <a href="<?php echo e(route('modules.risk-management.risk-reports.edit', $riskReport->id)); ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Laporan
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $riskReport)): ?>
                        <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.destroy', $riskReport->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                <i class="fas fa-trash me-1"></i> Hapus Laporan
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-reports/show.blade.php ENDPATH**/ ?>