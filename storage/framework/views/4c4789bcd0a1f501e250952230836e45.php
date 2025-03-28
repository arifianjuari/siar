<?php $__env->startSection('title', ' | Unit Kerja'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Unit Kerja</h1>
        <a href="<?php echo e(route('tenant.work-units.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> Tambah Unit Kerja
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Card Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-filter me-1"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('tenant.work-units.index')); ?>" method="GET">
                <div class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode unit kerja..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Card Data Unit Kerja -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-building me-1"></i> Daftar Unit Kerja</h5>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if($workUnits->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Belum ada data unit kerja. Silakan tambahkan unit kerja baru.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="25%">Nama Unit</th>
                                <th width="20%">Parent Unit</th>
                                <th width="10%">Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $workUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($workUnits->firstItem() + $index); ?></td>
                                    <td><?php echo e($unit->code ?? '-'); ?></td>
                                    <td><?php echo e($unit->name); ?></td>
                                    <td><?php echo e($unit->parent ? $unit->parent->name : '-'); ?></td>
                                    <td class="text-center">
                                        <?php if($unit->is_active): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Unit Actions">
                                            <a href="<?php echo e(route('tenant.work-units.show', $unit)); ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('tenant.work-units.edit', $unit)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('tenant.work-units.toggle-status', $unit)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm <?php echo e($unit->is_active ? 'btn-secondary' : 'btn-success'); ?>" title="<?php echo e($unit->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                                    <i class="fas <?php echo e($unit->is_active ? 'fa-ban' : 'fa-check'); ?>"></i>
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('tenant.work-units.destroy', $unit)); ?>" method="POST" class="d-inline delete-form">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus unit kerja ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    <?php echo e($workUnits->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Konfirmasi untuk form delete
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/tenant/work_units/index.blade.php ENDPATH**/ ?>