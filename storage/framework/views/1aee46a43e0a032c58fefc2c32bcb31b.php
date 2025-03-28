<?php $__env->startSection('title', ' | Daftar Laporan Risiko'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Laporan Risiko</h1>
        <div>
            <?php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant_admin' || 
                                  $userRole === 'Tenant_Admin' || 
                                  strtolower($userRole) === 'tenant_admin';
            ?>
            
            <?php if(auth()->user()->role && $isTenantAdmin): ?>
                <a href="<?php echo e(route('modules.risk-management.analysis-config')); ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('modules.risk-management.risk-reports.create')); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Buat Laporan Baru
            </a>
            <a href="<?php echo e(route('modules.risk-management.dashboard')); ?>" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i> Dashboard Statistik
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Form Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label small mb-1">Status:</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>Open</option>
                            <option value="in_review" <?php echo e(request('status') == 'in_review' ? 'selected' : ''); ?>>In Review</option>
                            <option value="resolved" <?php echo e(request('status') == 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="occurred_at" class="form-label small mb-1">Tanggal Kejadian:</label>
                        <input type="date" name="occurred_at" id="occurred_at" class="form-control form-control-sm" value="<?php echo e(request('occurred_at')); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="risk_title" class="form-label small mb-1">Judul Risiko:</label>
                        <input type="text" name="risk_title" id="risk_title" class="form-control form-control-sm" value="<?php echo e(request('risk_title')); ?>" placeholder="Cari judul...">
                    </div>

                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Laporan Risiko -->
    <div class="card shadow">
        <div class="card-body">
            <?php if($riskReports->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada laporan risiko yang ditemukan.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="40">No</th>
                                <th>Judul</th>
                                <th>Unit Pelapor</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Tanggal Kejadian</th>
                                <th>Tingkat Risiko</th>
                                <th>Status</th>
                                <th width="280">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $riskReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($report->risk_title); ?></td>
                                    <td><?php echo e($report->reporter_unit); ?></td>
                                    <td><?php echo e($report->risk_type ?: '-'); ?></td>
                                    <td><?php echo e($report->risk_category); ?></td>
                                    <td><?php echo e($report->occurred_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <?php if(strtolower($report->risk_level) == 'rendah' || strtolower($report->risk_level) == 'low'): ?>
                                            <span class="badge bg-success"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium'): ?>
                                            <span class="badge bg-warning text-dark"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high'): ?>
                                            <span class="badge bg-danger"><?php echo e($report->risk_level); ?></span>
                                        <?php else: ?>
                                            <?php echo e($report->risk_level); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($report->analysis): ?>
                                            <?php if($report->analysis->analysis_status == 'draft'): ?>
                                                <span class="badge bg-secondary">Draft</span>
                                            <?php elseif($report->analysis->analysis_status == 'in_progress' || $report->analysis->analysis_status == 'reviewed'): ?>
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if($report->status == 'open'): ?>
                                                <span class="badge bg-danger">Open</span>
                                            <?php elseif($report->status == 'in_review'): ?>
                                                <span class="badge bg-warning text-dark">In Review</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Resolved</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.edit', $report->id)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Form Hapus -->
                                            <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.destroy', $report->id)); ?>" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Tombol Analisis -->
                                            <?php if($report->status === 'open'): ?>
                                                <a href="<?php echo e(route('modules.risk-management.risk-analysis.create', $report->id)); ?>" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Tombol Setujui -->
                                            <?php if($report->status === 'in_review'): ?>
                                                <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.approve', $report->id)); ?>" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <!-- Export Awal -->
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.export-awal', $report->id)); ?>" class="btn btn-sm btn-secondary" title="Export Awal">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.qr-code', $report->id)); ?>" class="btn btn-sm btn-dark" title="QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            
                                            <!-- Export Akhir -->
                                            <?php if($report->status === 'resolved'): ?>
                                                <a href="<?php echo e(route('modules.risk-management.risk-reports.export-akhir', $report->id)); ?>" class="btn btn-sm btn-primary" title="Export PDF Akhir">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-reports/index.blade.php ENDPATH**/ ?>