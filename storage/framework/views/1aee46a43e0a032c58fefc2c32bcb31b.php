<?php $__env->startSection('title', ' | Daftar Laporan Risiko'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Laporan Risiko</h1>
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
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-filter me-1"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Draft" <?php echo e(request('status') == 'Draft' ? 'selected' : ''); ?>>Draft</option>
                            <option value="Ditinjau" <?php echo e(request('status') == 'Ditinjau' ? 'selected' : ''); ?>>Ditinjau</option>
                            <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="risk_level" class="form-label">Tingkat Risiko</label>
                        <select name="risk_level" id="risk_level" class="form-select">
                            <option value="">Semua Tingkat</option>
                            <option value="Rendah" <?php echo e(request('risk_level') == 'Rendah' ? 'selected' : ''); ?>>Rendah</option>
                            <option value="Sedang" <?php echo e(request('risk_level') == 'Sedang' ? 'selected' : ''); ?>>Sedang</option>
                            <option value="Tinggi" <?php echo e(request('risk_level') == 'Tinggi' ? 'selected' : ''); ?>>Tinggi</option>
                            <option value="Ekstrem" <?php echo e(request('risk_level') == 'Ekstrem' ? 'selected' : ''); ?>>Ekstrem</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tag" class="form-label">Tag</label>
                        <select name="tag" id="tag" class="form-select">
                            <option value="">Semua Tag</option>
                            <?php $__currentLoopData = App\Models\Tag::where('tenant_id', session('tenant_id'))->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tag->slug); ?>" <?php echo e(request('tag') == $tag->slug ? 'selected' : ''); ?>><?php echo e($tag->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Kata Kunci</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-secondary me-2">Reset</a>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Laporan Risiko -->
    <div class="card shadow">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i>Daftar Laporan</h5>
            <span class="badge bg-primary">Total: <?php echo e($riskReports->count()); ?></span>
        </div>
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
                                <th>Tag</th>
                                <th>Status</th>
                                <th width="280">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $riskReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($report->document_number); ?></td>
                                    <td><?php echo e($report->reporter_unit); ?></td>
                                    <td><?php echo e($report->risk_type ?? 'N/A'); ?></td>
                                    <td><?php echo e($report->risk_category); ?></td>
                                    <td><?php echo e($report->occurred_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <?php if(strtolower($report->risk_level) == 'rendah' || strtolower($report->risk_level) == 'low'): ?>
                                            <span class="badge bg-success"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium'): ?>
                                            <span class="badge bg-warning text-dark" style="background-color: #FFFF00 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high'): ?>
                                            <span class="badge text-white" style="background-color: #FFA500 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'ekstrem' || strtolower($report->risk_level) == 'extreme'): ?>
                                            <span class="badge bg-danger" style="background-color: #FF0000 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php else: ?>
                                            <?php echo e($report->risk_level); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php $__currentLoopData = $report->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('tenant.tags.documents', $tag->slug)); ?>" class="badge bg-light text-dark text-decoration-none">
                                                    <?php echo e($tag->name); ?>

                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($report->analysis): ?>
                                            <?php if($report->analysis->analysis_status == 'draft'): ?>
                                                <span class="badge bg-danger">Draft</span>
                                            <?php elseif($report->analysis->analysis_status == 'in_progress' || $report->analysis->analysis_status == 'reviewed'): ?>
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if($report->status == 'Draft'): ?>
                                                <span class="badge bg-danger">Draft</span>
                                            <?php elseif($report->status == 'Ditinjau'): ?>
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Selesai</span>
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
                                            <?php if($report->status === 'Draft'): ?>
                                                <a href="<?php echo e(route('modules.risk-management.risk-analysis.create', $report->id)); ?>" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Tombol Setujui -->
                                            <?php if($report->status === 'Ditinjau'): ?>
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
                                            
                                            <!-- Export Akhir -->
                                            <?php if($report->status === 'Selesai'): ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi toggle untuk form filter
        const toggleFilterBtn = document.getElementById('toggleFilterBtn');
        const filterRow = document.getElementById('filterRow');
        const filterCard = document.querySelector('.card.mb-4.shadow-sm');
        const btnText = toggleFilterBtn.querySelector('span');
        const btnIcon = toggleFilterBtn.querySelector('i');
        
        // Set initial state
        let isFilterVisible = true;
        
        toggleFilterBtn.addEventListener('click', function() {
            if (isFilterVisible) {
                // Sembunyikan filter
                filterCard.querySelector('.card-body').style.display = 'none';
                btnText.textContent = 'Tampilkan';
                btnIcon.classList.remove('fa-chevron-up');
                btnIcon.classList.add('fa-chevron-down');
                isFilterVisible = false;
            } else {
                // Tampilkan filter
                filterCard.querySelector('.card-body').style.display = 'block';
                btnText.textContent = 'Sembunyikan';
                btnIcon.classList.remove('fa-chevron-down');
                btnIcon.classList.add('fa-chevron-up');
                isFilterVisible = true;
            }
        });
        
        // Auto-submit form saat select berubah
        const autoSubmitFields = document.querySelectorAll('#risk_level, #risk_category');
        
        autoSubmitFields.forEach(function(field) {
            field.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/RiskManagement/risk-reports/index.blade.php ENDPATH**/ ?>