<?php $hideDefaultHeader = true; ?>

<?php $__env->startSection('content'); ?>
    <!-- Card Filter -->
    <div class="card mb-4 shadow-sm border-top-0">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3 text-dark"><i class="fas fa-filter me-1 small"></i> Filter</h6>
                <form action="<?php echo e(route('tenant.work-units.index')); ?>" method="GET" class="flex-grow-1">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode unit kerja..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="unit_type" class="form-select">
                                <option value="">Semua Tipe Unit</option>
                                <option value="medical" <?php echo e(request('unit_type') == 'medical' ? 'selected' : ''); ?>>Medical</option>
                                <option value="non-medical" <?php echo e(request('unit_type') == 'non-medical' ? 'selected' : ''); ?>>Non-Medical</option>
                                <option value="supporting" <?php echo e(request('unit_type') == 'supporting' ? 'selected' : ''); ?>>Supporting</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                            <a href="<?php echo e(route('tenant.work-units.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Card Data Unit Kerja -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-building me-1"></i> Daftar Unit Kerja</h4>
            <a href="<?php echo e(route('tenant.work-units.create')); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Tambah Unit
            </a>
        </div>
        <div class="card-body">
            <?php if($workUnits->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Belum ada data unit kerja. Silakan tambahkan unit kerja baru.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="3%">No</th>
                                <th width="10%">Kode</th>
                                <th width="20%">Nama Unit</th>
                                <th width="15%">Tipe</th>
                                <th width="15%">Kepala Unit</th>
                                <th width="15%">Parent Unit</th>
                                <th width="7%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $workUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($workUnits->firstItem() + $index); ?></td>
                                    <td><?php echo e($unit->unit_code ?? '-'); ?></td>
                                    <td><?php echo e($unit->unit_name); ?></td>
                                    <td>
                                        <?php if($unit->unit_type == 'medical'): ?>
                                            <span class="badge bg-success">Medical</span>
                                        <?php elseif($unit->unit_type == 'non-medical'): ?>
                                            <span class="badge bg-info">Non-Medical</span>
                                        <?php elseif($unit->unit_type == 'supporting'): ?>
                                            <span class="badge bg-secondary">Supporting</span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($unit->headOfUnit->name ?? '-'); ?></td>
                                    <td><?php echo e($unit->parent ? $unit->parent->unit_name : '-'); ?></td>
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
                    <?php echo e($workUnits->appends(request()->query())->links()); ?>

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