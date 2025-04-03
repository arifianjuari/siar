<?php $hideDefaultHeader = true; ?>

<?php $__env->startSection('content'); ?>
    <!-- Card Filter -->
    <div class="card mb-4 shadow-sm border-top-0">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3 text-dark"><i class="fas fa-filter me-1 small"></i> Filter</h6>
                <form action="<?php echo e(route('tenant.tags.index')); ?>" method="GET" class="flex-grow-1">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama tag..." value="<?php echo e(request('search')); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Card Data Tag -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-tags me-1"></i> Daftar Tag</h4>
            <a href="<?php echo e(route('tenant.tags.create')); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Tambah Tag
            </a>
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
            
            <?php if($tags->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Belum ada data tag. Silakan tambahkan tag baru.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Tag</th>
                                <th width="30%">Parent Tag</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($tags->firstItem() + $index); ?></td>
                                    <td><?php echo e($tag->name); ?></td>
                                    <td><?php echo e($tag->parent ? $tag->parent->name : '-'); ?></td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Tag Actions">
                                            <a href="<?php echo e(route('tenant.tags.show', $tag)); ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('tenant.tags.edit', $tag)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('tenant.tags.destroy', $tag)); ?>" method="POST" class="d-inline delete-form">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus tag ini?')">
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
                    <?php echo e($tags->links()); ?>

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
                if (!confirm('Apakah Anda yakin ingin menghapus tag ini?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/tenant/tags/index.blade.php ENDPATH**/ ?>