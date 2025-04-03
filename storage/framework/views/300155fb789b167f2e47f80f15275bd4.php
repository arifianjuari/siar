<?php $__env->startSection('title', 'Unit Kerja'); ?>

<?php
$hideDefaultHeader = true;
?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">Daftar Unit Kerja</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i> Mengelola struktur unit kerja dalam organisasi
                            </p>
                        </div>
                        <a href="<?php echo e(route('work-units.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Unit Kerja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1">Berhasil</h6>
                    <p class="mb-0"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1">Error</h6>
                    <p class="mb-0"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Kode Unit</th>
                            <th class="px-4 py-3">Nama Unit</th>
                            <th class="px-4 py-3">Jenis Unit</th>
                            <th class="px-4 py-3">Kepala Unit</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $flattenedWorkUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div style="padding-left: <?php echo e($unit->depth * 20); ?>px;">
                                    <?php echo e($unit->unit_code); ?>

                                </div>
                            </td>
                            <td class="px-4 py-3 fw-medium"><?php echo e($unit->unit_name); ?></td>
                            <td class="px-4 py-3">
                                <?php if($unit->unit_type == 'medical'): ?>
                                    <span class="badge bg-success">Medical</span>
                                <?php elseif($unit->unit_type == 'non-medical'): ?>
                                    <span class="badge bg-primary">Non-Medical</span>
                                <?php elseif($unit->unit_type == 'supporting'): ?>
                                    <span class="badge bg-secondary">Supporting</span>
                                <?php else: ?>
                                    <span class="badge bg-info"><?php echo e(ucfirst($unit->unit_type)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3"><?php echo e($unit->headOfUnit->name ?? '-'); ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('work-units.dashboard', $unit->id)); ?>" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Dashboard">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="<?php echo e(route('work-units.edit', $unit->id)); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($unit->id); ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo e($unit->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo e($unit->id); ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo e($unit->id); ?>">Konfirmasi Hapus Unit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus unit kerja <strong><?php echo e($unit->unit_name); ?> (<?php echo e($unit->unit_code); ?>)</strong>?</p>
                                                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="<?php echo e(route('work-units.destroy', $unit->id)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger">Hapus Unit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Tidak ada data unit kerja.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/WorkUnit/index.blade.php ENDPATH**/ ?>