<?php $__env->startSection('title', 'Detail Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Detail Pengguna</h2>
                    <div>
                        <a href="<?php echo e(route('superadmin.users.edit', $user)); ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
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

    <div class="row">
        <!-- User Details -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-primary text-white display-4">
                                <?php echo e(substr($user->name, 0, 1)); ?>

                            </span>
                        </div>
                        <h4><?php echo e($user->name); ?></h4>
                        <p class="text-muted"><?php echo e($user->email); ?></p>
                        
                        <?php if($user->is_active): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">ID</span>
                            <span class="fw-bold"><?php echo e($user->id); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Tenant</span>
                            <span class="fw-bold"><?php echo e($user->tenant ? $user->tenant->name : 'Superadmin'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Role</span>
                            <span class="fw-bold"><?php echo e($user->role ? $user->role->name : '-'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Tanggal Dibuat</span>
                            <span class="fw-bold"><?php echo e($user->created_at->format('d M Y H:i')); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Terakhir Diubah</span>
                            <span class="fw-bold"><?php echo e($user->updated_at->format('d M Y H:i')); ?></span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0">
                    <div class="d-grid gap-2">
                        <?php if($user->id !== auth()->id()): ?>
                            <form method="POST" action="<?php echo e(route('superadmin.users.toggle-active', $user)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-<?php echo e($user->is_active ? 'warning' : 'success'); ?> w-100">
                                    <i class="fas <?php echo e($user->is_active ? 'fa-ban' : 'fa-check-circle'); ?> me-1"></i>
                                    <?php echo e($user->is_active ? 'Nonaktifkan Pengguna' : 'Aktifkan Pengguna'); ?>

                                </button>
                            </form>
                            
                            <form method="POST" action="<?php echo e(route('superadmin.users.reset-password', $user)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-info w-100" onclick="return confirm('Password pengguna ini akan direset. Lanjutkan?')">
                                    <i class="fas fa-key me-1"></i> Reset Password
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                <i class="fas fa-trash me-1"></i> Hapus Pengguna
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Logs -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Riwayat Aktivitas</h5>
                </div>
                <div class="card-body">
                    <?php if($activityLogs->isEmpty()): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas yang tercatat untuk pengguna ini.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                        <th>Model</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-nowrap"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                                            <td>
                                                <span class="badge <?php if(strpos($log->action, 'create') !== false): ?> bg-success 
                                                    <?php elseif(strpos($log->action, 'update') !== false): ?> bg-primary 
                                                    <?php elseif(strpos($log->action, 'delete') !== false): ?> bg-danger 
                                                    <?php else: ?> bg-info <?php endif; ?>">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($log->description); ?></td>
                                            <td><?php echo e($log->model_type); ?> #<?php echo e($log->model_id); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<?php if($user->id !== auth()->id()): ?>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong><?php echo e($user->name); ?></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan semua data pengguna akan hilang.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?php echo e(route('superadmin.users.destroy', $user)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Hapus Pengguna</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<style>
    .avatar-lg {
        height: 5rem;
        width: 5rem;
    }
    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 500;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
</style> 
<?php echo $__env->make('superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/superadmin/users/show.blade.php ENDPATH**/ ?>