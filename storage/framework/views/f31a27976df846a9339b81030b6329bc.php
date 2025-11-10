<?php $__env->startSection('title', ' | Detail Laporan Risiko'); ?>

<?php $__env->startPush('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.risk-management.dashboard')); ?>" class="text-decoration-none">Manajemen Risiko</a></li>
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
                    <h5 class="mb-0"><?php echo e($riskReport->document_title); ?></h5>
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

            <!-- Section Rencana Tindak Lanjut - Hubungan dengan Kegiatan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Rencana Tindak Lanjut</h5>
                </div>
                <div class="card-body">
                    <?php if($riskReport->activity): ?>
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Kegiatan terkait:</strong>
                                    <a href="<?php echo e(route('activity-management.activities.show', $riskReport->activity->uuid)); ?>" class="ms-2">
                                        <?php echo e($riskReport->activity->title); ?>

                                    </a>
                                    <span class="badge bg-<?php echo e($riskReport->activity->statusColor); ?>">
                                        <?php echo e($riskReport->activity->statusLabel); ?>

                                    </span>
                                </div>
                                <button class="btn btn-sm btn-danger" id="btn-unlink-activity" data-bs-toggle="modal" data-bs-target="#unlinkActivityModal">
                                    <i class="fas fa-unlink"></i> Lepaskan
                                </button>
                            </div>
                            <div class="mt-2 small">
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar <?php echo e($riskReport->activity->progress_percentage == 100 ? 'bg-success' : 'bg-primary'); ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo e($riskReport->activity->progress_percentage); ?>%" 
                                         aria-valuenow="<?php echo e($riskReport->activity->progress_percentage); ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Progres: <?php echo e($riskReport->activity->progress_percentage); ?>%</span>
                                    <span>
                                        <i class="fas fa-calendar-alt me-1"></i> Tenggat: 
                                        <?php echo e($riskReport->activity->due_date ? $riskReport->activity->due_date->format('d M Y') : 'Tidak ditentukan'); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p>Belum ada kegiatan tindak lanjut untuk risiko ini.</p>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#linkActivityModal">
                                    <i class="fas fa-link"></i> Hubungkan dengan Kegiatan
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createActivityModal">
                                    <i class="fas fa-plus-circle"></i> Buat Kegiatan Baru
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modal untuk menghubungkan dengan kegiatan yang sudah ada -->
            <div class="modal fade" id="linkActivityModal" tabindex="-1" aria-labelledby="linkActivityModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="linkActivityModalLabel">Hubungkan dengan Kegiatan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="link-activity-form">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label for="activity_id" class="form-label">Pilih Kegiatan</label>
                                    <select class="form-select" id="activity_id" name="activity_id" required>
                                        <option value="">-- Pilih Kegiatan --</option>
                                        <?php $__currentLoopData = $activities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($activity->id); ?>"><?php echo e($activity->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Hubungkan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal untuk membuat kegiatan baru -->
            <div class="modal fade" id="createActivityModal" tabindex="-1" aria-labelledby="createActivityModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createActivityModalLabel">Buat Kegiatan Tindak Lanjut</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="<?php echo e(route('modules.risk-management.risk-reports.create-activity', $riskReport->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="title" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo e(old('title', 'Tindak Lanjut Risiko: ' . $riskReport->document_title)); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo e(old('description', "Tindak lanjut untuk risiko:\n" . $riskReport->document_title . "\n\nKronologi risiko:\n" . Str::limit($riskReport->chronology, 200))); ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e(old('start_date', date('Y-m-d'))); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e(old('end_date', date('Y-m-d', strtotime('+1 week')))); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>Rendah</option>
                                            <option value="medium" <?php echo e(old('priority') == 'medium' ? 'selected' : ''); ?>>Sedang</option>
                                            <option value="high" <?php echo e(old('priority', 'high') == 'high' ? 'selected' : ''); ?>>Tinggi</option>
                                            <option value="critical" <?php echo e(old('priority') == 'critical' ? 'selected' : ''); ?>>Kritis</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">Buat Kegiatan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal konfirmasi pelepasan kegiatan -->
            <div class="modal fade" id="unlinkActivityModal" tabindex="-1" aria-labelledby="unlinkActivityModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="unlinkActivityModalLabel">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin melepaskan hubungan dengan kegiatan ini?</p>
                            <p class="text-danger small">Catatan: Tindakan ini tidak akan menghapus kegiatan, hanya melepaskan hubungan dengan risiko ini.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger" id="confirm-unlink-btn">Ya, Lepaskan</button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <?php elseif($riskReport->analysis && in_array($riskReport->analysis->analysis_status, ['completed', 'in_progress'])): ?> 
                        <img src="<?php echo e(route('modules.risk-management.risk-analysis.qr-code', [$riskReport->id, $riskReport->analysis->id])); ?>" 
                             alt="QR Code Analisis" class="img-fluid mb-2" style="max-width: 200px;">
                        <p class="text-muted small">Scan QR code untuk verifikasi analisis</p>
                        <p class="text-muted small mb-0">Status: 
                            <?php if($riskReport->analysis->analysis_status === 'completed'): ?>
                                <span class="badge bg-success">Analisis Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Analisis Dalam Proses</span> 
                            <?php endif; ?>
                        </p>
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

                        <!-- Dokumen Terkait -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Dokumen Terkait</h5>
                            </div>
                            <div class="card-body">
                                <?php if($riskReport->documents->count() > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $riskReport->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item border-0 p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo e($doc->document_title); ?></strong><br>
                                                <small class="text-muted">No: <?php echo e($doc->document_number); ?></small>
                                                <?php if($doc->file_path): ?>
                                                <div>
                                                    <a href="<?php echo e(asset('storage/' . $doc->file_path)); ?>" class="text-primary" target="_blank">
                                                        <i class="fas fa-paperclip me-1"></i> Lihat File
                                                    </a>
                                                </div>
                                                <?php else: ?>
                                                <div>
                                                    <span class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> Tanpa file</span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?php echo e(route('modules.document-management.documents.show', $doc->id)); ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail Dokumen">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <?php else: ?>
                                    <?php if($riskReport->file_path): ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo e($riskReport->document_title); ?></strong><br>
                                            <small class="text-muted">No: <?php echo e($riskReport->document_number); ?></small>
                                            <div>
                                                <a href="<?php echo e(asset('storage/' . $riskReport->file_path)); ?>" class="text-primary" target="_blank">
                                                    <i class="fas fa-paperclip me-1"></i> Lihat File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted fst-italic mb-0">Belum ada dokumen yang terhubung.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tag Management Section -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Tag</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <?php $__currentLoopData = $riskReport->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-<?php echo e($tag->id); ?>">
                                        <a href="<?php echo e(route('tenant.tags.documents', $tag->slug)); ?>" class="text-decoration-none text-white">
                                            <?php echo e($tag->name); ?>

                                        </a>
                                        <button 
                                            type="button" 
                                            class="btn-close btn-close-white ms-2" 
                                            style="font-size: 0.7rem;" 
                                            onclick="hapusTagLangsung(<?php echo e($tag->id); ?>, <?php echo e($riskReport->id); ?>, 'App\\Models\\RiskReport')"
                                            aria-label="Close">
                                        </button>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <form id="formTambahTag" action="<?php echo e(route('tenant.tags.attach-document')); ?>" method="POST" class="d-flex gap-2 mt-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="document_id" value="<?php echo e($riskReport->id); ?>">
                                    <input type="hidden" name="document_type" value="App\Models\RiskReport">
                                    <select name="tag_id" id="selectTag" class="form-select form-select-sm" required>
                                        <option value="">Pilih Tag</option>
                                        <?php $__currentLoopData = App\Models\Tag::forTenant(session('tenant_id'))->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tag->id); ?>" data-slug="<?php echo e($tag->slug); ?>"><?php echo e($tag->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Tambah Tag</button>
                                </form>
                            </div>
                        </div>

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

<script>
    // Fungsi untuk menghapus tag langsung tanpa konfirmasi
    function hapusTagLangsung(tagId, documentId, documentType) {
        // Dapatkan CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Hapus tag dari DOM sebelum request selesai (untuk UX yang lebih cepat)
        const tagElement = document.getElementById('tag-item-' + tagId);
        if (tagElement) {
            tagElement.style.opacity = '0.5'; // Visual feedback saat proses penghapusan
        }
        
        // Buat form data untuk endpoint baru
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        
        // Kirim request dengan fetch API ke endpoint baru
        fetch('/tenant/tags/delete-tag', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Hapus tag dari DOM jika belum dihapus
                if (tagElement) {
                    tagElement.remove();
                }
            } else {
                console.error('Gagal menghapus tag:', response.statusText);
                // Kembalikan tampilan tag jika terjadi error
                if (tagElement) {
                    tagElement.style.opacity = '1';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Kembalikan tampilan tag jika terjadi error
            if (tagElement) {
                tagElement.style.opacity = '1';
            }
        });
    }
    
    // Tangani submit form tambah tag
    document.getElementById('formTambahTag').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Dapatkan CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Dapatkan data dari form
        const selectElement = document.getElementById('selectTag');
        const tagId = selectElement.value;
        
        if (!tagId) return;
        
        const tagName = selectElement.options[selectElement.selectedIndex].text;
        const tagSlug = selectElement.options[selectElement.selectedIndex].dataset.slug;
        const documentId = document.querySelector('input[name="document_id"]').value;
        const documentType = document.querySelector('input[name="document_type"]').value;
        
        // Buat form data
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        formData.append('_token', csrfToken);
        
        // Kirim request dengan fetch API
        fetch('<?php echo e(route('tenant.tags.attach-document')); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Tambahkan tag baru ke DOM
                const tagsContainer = document.querySelector('.d-flex.flex-wrap.gap-2.mb-2');
                const newTagHtml = `
                    <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-${tagId}">
                        <a href="/tenant/tags/${tagSlug}/documents" class="text-decoration-none text-white">
                            ${tagName}
                        </a>
                        <button 
                            type="button" 
                            class="btn-close btn-close-white ms-2" 
                            style="font-size: 0.7rem;" 
                            onclick="hapusTagLangsung(${tagId}, ${documentId}, '${documentType}')"
                            aria-label="Close">
                        </button>
                    </div>
                `;
                tagsContainer.insertAdjacentHTML('beforeend', newTagHtml);
                
                // Reset pilihan dropdown
                selectElement.value = '';
            } else {
                console.error('Gagal menambahkan tag:', response.statusText);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Script untuk form activity
    document.addEventListener('DOMContentLoaded', function() {
        // Handle link activity form submission
        const linkActivityForm = document.getElementById('link-activity-form');
        if (linkActivityForm) {
            linkActivityForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Dapatkan CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Dapatkan data dari form
                const activityId = document.getElementById('activity_id').value;
                
                if (!activityId) {
                    return;
                }
                
                // Buat form data
                const formData = new FormData();
                formData.append('activity_id', activityId);
                formData.append('_token', csrfToken);
                
                // Kirim request dengan fetch API
                fetch('<?php echo e(route('modules.risk-management.risk-reports.link-activity', $riskReport->id)); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem');
                });
            });
        }
        
        // Handle unlink activity button
        const confirmUnlinkBtn = document.getElementById('confirm-unlink-btn');
        if (confirmUnlinkBtn) {
            confirmUnlinkBtn.addEventListener('click', function() {
                // Dapatkan CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Kirim request dengan fetch API
                fetch('<?php echo e(route('modules.risk-management.risk-reports.unlink-activity', $riskReport->id)); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _token: csrfToken })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem');
                });
            });
        }
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/modules/RiskManagement/risk-reports/show.blade.php ENDPATH**/ ?>