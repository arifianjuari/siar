<?php $__env->startSection('title', 'Detail Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Pengguna</h5>
                        <div>
                            <a href="<?php echo e(route('modules.user-management.users.edit', $user->id)); ?>" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-pencil-alt me-1"></i> Edit
                            </a>
                            <a href="<?php echo e(route('modules.user-management.users.index')); ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?php echo e($user->id); ?></td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td><?php echo e($user->name); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo e($user->email); ?></td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td><?php echo e($user->role->name ?? 'Tidak ada role'); ?></td>
                                </tr>
                                <tr>
                                    <th>Unit Kerja</th>
                                    <td><?php echo e($user->workUnit->unit_name ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if($user->is_active): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terakhir Login</th>
                                    <td><?php echo e($user->last_login_at ? $user->last_login_at->format('d-m-Y H:i:s') : 'Belum pernah login'); ?></td>
                                </tr>
                                <tr>
                                    <th>IP Terakhir Login</th>
                                    <td><?php echo e($user->last_login_ip ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td><?php echo e($user->created_at->format('d-m-Y H:i:s')); ?></td>
                                </tr>
                                <tr>
                                    <th>Diperbarui Pada</th>
                                    <td><?php echo e($user->updated_at->format('d-m-Y H:i:s')); ?></td>
                                </tr>
                                <?php if($user->creator): ?>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td><?php echo e($user->creator->name); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($user->updater): ?>
                                <tr>
                                    <th>Diperbarui Oleh</th>
                                    <td><?php echo e($user->updater->name); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/UserManagement/users/show.blade.php ENDPATH**/ ?>