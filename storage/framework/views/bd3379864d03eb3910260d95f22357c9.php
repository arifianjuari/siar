<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Manajemen Pengguna</h2>
                    <a href="<?php echo e(route('superadmin.users.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Tambah Pengguna Baru
                    </a>
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

    <!-- Filter & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="<?php echo e(route('superadmin.users.index')); ?>" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="tenant" class="form-label">Tenant</label>
                            <select name="tenant" id="tenant" class="form-select">
                                <option value="">Semua Tenant</option>
                                <?php $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tenant->id); ?>" <?php echo e(request('tenant') == $tenant->id ? 'selected' : ''); ?>>
                                        <?php echo e($tenant->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="">Semua Role</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(request('role') == $role->id ? 'selected' : ''); ?>>
                                        <?php echo e($role->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari nama atau email..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-grid gap-2 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tenant</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td><?php echo e($user->tenant ? $user->tenant->name : 'Superadmin'); ?></td>
                                        <td><?php echo e($user->role ? $user->role->name : '-'); ?></td>
                                        <td>
                                            <?php if($user->is_active): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="<?php echo e(route('superadmin.users.show', $user)); ?>" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('superadmin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-success me-1" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="<?php echo e(route('superadmin.users.toggle-active', $user)); ?>" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-<?php echo e($user->is_active ? 'warning' : 'info'); ?> me-1" data-bs-toggle="tooltip" title="<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                                        <i class="fas <?php echo e($user->is_active ? 'fa-ban' : 'fa-check-circle'); ?>"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="<?php echo e(route('superadmin.users.reset-password', $user)); ?>" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary me-1" onclick="return confirm('Password pengguna ini akan direset. Lanjutkan?')" data-bs-toggle="tooltip" title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </form>
                                                <?php if($user->id !== auth()->id()): ?>
                                                    <form method="POST" action="<?php echo e(route('superadmin.users.destroy', $user)); ?>" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada pengguna yang ditemukan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($users->withQueryString()->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/superadmin/users/index.blade.php ENDPATH**/ ?>