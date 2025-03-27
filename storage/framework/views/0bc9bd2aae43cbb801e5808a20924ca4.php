<?php $__env->startSection('title', ' | Manajemen Role'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Manajemen Role</h2>
        
        <?php if(\App\Helpers\PermissionHelper::hasPermission('user-management', 'can_create')): ?>
            <a href="<?php echo e(route('modules.user-management.roles.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Tambah Role
            </a>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header bg-white">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="<?php echo e(route('modules.user-management.roles.index')); ?>" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari role..." value="<?php echo e(request('search')); ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Total: <?php echo e($roles->total()); ?> role</span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-3" width="5%">No</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-3"><?php echo e($roles->firstItem() + $index); ?></td>
                                <td><?php echo e($role->name); ?></td>
                                <td><?php echo e($role->description ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php if($role->is_active): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <?php if(hasModulePermission('user-management', auth()->user(), 'can_view')): ?>
                                            <a href="<?php echo e(route('modules.user-management.roles.show', $role->id)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(hasModulePermission('user-management', auth()->user(), 'can_edit')): ?>
                                            <a href="<?php echo e(route('modules.user-management.roles.edit', $role->id)); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(hasModulePermission('user-management', auth()->user(), 'can_delete')): ?>
                                            <form action="<?php echo e(route('modules.user-management.roles.destroy', $role->id)); ?>" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?');" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">Tidak ada data role</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white d-flex justify-content-center">
            <?php echo e($roles->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/UserManagement/roles/index.blade.php ENDPATH**/ ?>