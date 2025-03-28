<?php $__env->startSection('title', 'Manajemen Tenant'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo e(__('Tenant Management')); ?></h5>
                    <div>
                        <a href="<?php echo e(route('superadmin.tenants.monitor')); ?>" class="btn btn-info me-2">
                            <i class="fas fa-chart-bar"></i> <?php echo e(__('Monitoring')); ?>

                        </a>
                        <a href="<?php echo e(route('superadmin.tenants.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('Tambah Tenant')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Domain</th>
                                    <th>Database</th>
                                    <th>Users</th>
                                    <th>Roles</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($tenant->id); ?></td>
                                        <td><?php echo e($tenant->name); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo e($tenant->domain); ?></span></td>
                                        <td><code><?php echo e($tenant->database); ?></code></td>
                                        <td><span class="badge bg-info"><?php echo e($tenant->users_count); ?></span></td>
                                        <td><span class="badge bg-warning"><?php echo e($tenant->roles_count); ?></span></td>
                                        <td><?php echo $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('superadmin.tenants.show', $tenant)); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('superadmin.tenants.edit', $tenant)); ?>" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('superadmin.tenants.destroy', $tenant)); ?>" method="POST" class="d-inline delete-form" data-tenant-name="<?php echo e($tenant->name); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada tenant yang ditambahkan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($tenants->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const tenantName = this.getAttribute('data-tenant-name');
                
                if (confirm(`Anda yakin ingin menghapus tenant "${tenantName}"?\n\nPerhatian: Tindakan ini akan menghapus semua data terkait tenant ini dan tidak dapat dikembalikan.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/superadmin/tenants/index.blade.php ENDPATH**/ ?>