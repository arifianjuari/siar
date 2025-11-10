<?php $__env->startSection('title', ' '); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Detail Tenant: <?php echo e($tenant->name); ?></h2>
                    <div>
                        <a href="<?php echo e(route('superadmin.tenants.edit', $tenant)); ?>" class="btn btn-success me-2">
                            <i class="fas fa-edit me-2"></i> Edit Tenant
                        </a>
                        <form action="<?php echo e(route('superadmin.tenants.destroy', $tenant)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tenant ini?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="fas fa-trash me-2"></i> Hapus Tenant
                            </button>
                        </form>
                        <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
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
        <!-- Informasi Tenant -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">ID</span>
                        <strong><?php echo e($tenant->id); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Nama</span>
                        <strong><?php echo e($tenant->name); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Domain</span>
                        <strong><?php echo e($tenant->domain); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Database</span>
                        <code><?php echo e($tenant->database); ?></code>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Status</span>
                        <?php echo $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?>

                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Dibuat Pada</span>
                        <span><?php echo e($tenant->created_at->format('d M Y H:i')); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Diperbarui Pada</span>
                        <span><?php echo e($tenant->updated_at->format('d M Y H:i')); ?></span>
                    </div>
                </div>
            </div>

            <!-- Statistik Tenant -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistik Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Total Pengguna</span>
                        <span class="badge bg-info"><?php echo e($userCount); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Modul Aktif</span>
                        <span class="badge bg-primary"><?php echo e($activeModules); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Peran</span>
                        <span class="badge bg-warning"><?php echo e($tenant->roles->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manajemen Modul -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Manajemen Modul</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Modul</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tenant->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 bg-light rounded p-2">
                                                    <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?>"></i>
                                                </div>
                                                <strong><?php echo e($module->name); ?></strong>
                                            </div>
                                        </td>
                                        <td><?php echo e($module->description ?? 'Tidak ada deskripsi'); ?></td>
                                        <td>
                                            <span id="module-status-<?php echo e($module->id); ?>" class="badge <?php echo e($module->pivot->is_active ? 'bg-success' : 'bg-danger'); ?>">
                                                <?php echo e($module->pivot->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm <?php echo e($module->pivot->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?> toggle-module"
                                                data-module-id="<?php echo e($module->id); ?>"
                                                data-tenant-id="<?php echo e($tenant->id); ?>"
                                                data-current-status="<?php echo e($module->pivot->is_active ? '1' : '0'); ?>"
                                            >
                                                <?php echo e($module->pivot->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Admin Tenant -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $adminUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($admin->name); ?></td>
                                        <td><?php echo e($admin->email); ?></td>
                                        <td><?php echo $admin->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?php echo e($admin->id); ?>">
                                                <i class="fas fa-key me-1"></i> Reset Password
                                            </button>

                                            <!-- Modal Reset Password -->
                                            <div class="modal fade" id="resetPasswordModal<?php echo e($admin->id); ?>" tabindex="-1" aria-labelledby="resetPasswordModalLabel<?php echo e($admin->id); ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('superadmin.tenants.reset-admin-password', $tenant)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="user_id" value="<?php echo e($admin->id); ?>">
                                                            
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="resetPasswordModalLabel<?php echo e($admin->id); ?>">Reset Password Admin</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Anda akan mereset password untuk admin <strong><?php echo e($admin->name); ?></strong>.</p>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="new_password" class="form-label">Password Baru</label>
                                                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                                    <div class="form-text">Password minimal 8 karakter.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning">Reset Password</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Manajemen Role -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manajemen Role</h5>
                    <a href="<?php echo e(route('superadmin.tenants.roles.create', $tenant)); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Role Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Pengguna</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tenant->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($role->name); ?></td>
                                        <td><code><?php echo e($role->slug); ?></code></td>
                                        <td><?php echo e($role->description ?? 'Tidak ada deskripsi'); ?></td>
                                        <td><?php echo $role->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                        <td><span class="badge bg-info"><?php echo e($role->users->count()); ?></span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('superadmin.tenants.roles.edit', [$tenant, $role])); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <a href="<?php echo e(route('superadmin.tenants.roles.permissions.edit', [$tenant, $role])); ?>" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Atur Hak Akses">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                
                                                <?php if($role->slug !== 'tenant-admin'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal<?php echo e($role->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Modal Konfirmasi Hapus Role -->
                                                    <div class="modal fade" id="deleteRoleModal<?php echo e($role->id); ?>" tabindex="-1" aria-labelledby="deleteRoleModalLabel<?php echo e($role->id); ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteRoleModalLabel<?php echo e($role->id); ?>">Konfirmasi Hapus Role</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Anda yakin ingin menghapus role <strong><?php echo e($role->name); ?></strong>?</p>
                                                                    <?php if($role->users->count() > 0): ?>
                                                                        <div class="alert alert-warning">
                                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                                            Role ini sedang digunakan oleh <?php echo e($role->users->count()); ?> pengguna. Hapus atau pindahkan pengguna terlebih dahulu.
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <form action="<?php echo e(route('superadmin.tenants.roles.destroy', [$tenant, $role])); ?>" method="POST">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                        <button type="submit" class="btn btn-danger" <?php echo e($role->users->count() > 0 ? 'disabled' : ''); ?>>Hapus</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            Belum ada role yang dibuat untuk tenant ini.
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
    
    <!-- Manajemen Pengguna -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manajemen Pengguna</h5>
                    <a href="<?php echo e(route('superadmin.tenants.users.create', $tenant)); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Pengguna Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Login Terakhir</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td><?php echo e($user->role->name); ?></td>
                                        <td><?php echo $user->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                        <td><?php echo e($user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum Pernah Login'); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('superadmin.tenants.users.edit', [$tenant, $user])); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?php echo e($user->id); ?>">
                                                    <i class="fas fa-key"></i>
                                                </button>

                                                <?php if($user->id !== auth()->id() && !$user->hasRole('tenant-admin')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?php echo e($user->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Modal Reset Password -->
                                            <div class="modal fade" id="resetPasswordModal<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('superadmin.tenants.users.reset-password', [$tenant, $user])); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reset Password Pengguna</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Anda akan mereset password untuk pengguna <strong><?php echo e($user->name); ?></strong>.</p>
                                                                <div class="mb-3">
                                                                    <label for="new_password<?php echo e($user->id); ?>" class="form-label">Password Baru</label>
                                                                    <input type="password" class="form-control" id="new_password<?php echo e($user->id); ?>" name="new_password" required>
                                                                    <div class="form-text">Password minimal 8 karakter.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning">Reset Password</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Konfirmasi Hapus -->
                                            <?php if($user->id !== auth()->id() && !$user->hasRole('tenant-admin')): ?>
                                                <div class="modal fade" id="deleteUserModal<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Konfirmasi Hapus Pengguna</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Anda yakin ingin menghapus pengguna <strong><?php echo e($user->name); ?></strong>?</p>
                                                                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <form action="<?php echo e(route('superadmin.tenants.users.destroy', [$tenant, $user])); ?>" method="POST">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            Belum ada pengguna yang terdaftar di tenant ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($users->hasPages()): ?>
                        <div class="d-flex justify-content-end mt-3">
                            <?php echo e($users->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle module status
        const toggleButtons = document.querySelectorAll('.toggle-module');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const moduleId = this.dataset.moduleId;
                const tenantId = this.dataset.tenantId;
                const currentStatus = this.dataset.currentStatus;
                const newStatus = currentStatus === '1' ? '0' : '1';
                
                // Update UI optimistically
                const statusBadge = document.getElementById(`module-status-${moduleId}`);
                if (newStatus === '1') {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'Aktif';
                    this.className = 'btn btn-sm btn-outline-danger toggle-module';
                    this.textContent = 'Nonaktifkan';
                } else {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = 'Nonaktif';
                    this.className = 'btn btn-sm btn-outline-success toggle-module';
                    this.textContent = 'Aktifkan';
                }
                
                this.dataset.currentStatus = newStatus;
                
                // Send AJAX request
                fetch(`/superadmin/tenants/${tenantId}/toggle-module`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        module_id: moduleId,
                        is_active: newStatus === '1'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success handling (optional toast notification)
                        console.log('Module status updated successfully');
                    } else {
                        // Revert UI changes on error
                        console.error('Error updating module status:', data.message);
                        this.dataset.currentStatus = currentStatus;
                        
                        if (currentStatus === '1') {
                            statusBadge.className = 'badge bg-success';
                            statusBadge.textContent = 'Aktif';
                            this.className = 'btn btn-sm btn-outline-danger toggle-module';
                            this.textContent = 'Nonaktifkan';
                        } else {
                            statusBadge.className = 'badge bg-danger';
                            statusBadge.textContent = 'Nonaktif';
                            this.className = 'btn btn-sm btn-outline-success toggle-module';
                            this.textContent = 'Aktifkan';
                        }
                    }
                })
                .catch(error => {
                    console.error('AJAX request failed:', error);
                });
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/roles/superadmin/tenants/show.blade.php ENDPATH**/ ?>