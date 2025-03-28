<?php $__env->startSection('title', ' | Detail Role'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Detail Role</h2>
        <div>
            <a href="<?php echo e(route('modules.user-management.roles.edit', $role->id)); ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit Role
            </a>
            <a href="<?php echo e(route('modules.user-management.roles.index')); ?>" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Role</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <th width="130">Nama Role</th>
                            <td><?php echo e($role->name); ?></td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td><?php echo e($role->slug); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td><?php echo e($role->description ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if($role->is_active): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td><?php echo e($role->created_at->format('d M Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td><?php echo e($role->updated_at->format('d M Y H:i')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Hak Akses Modul</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Modul</th>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Create</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete</th>
                                    <th class="text-center">Export</th>
                                    <th class="text-center">Import</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($permission->module->name); ?></td>
                                        <td class="text-center">
                                            <?php if($permission->can_view): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission->can_create): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission->can_edit): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission->can_delete): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission->can_export): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission->can_import): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">Tidak ada hak akses</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/UserManagement/roles/show.blade.php ENDPATH**/ ?>