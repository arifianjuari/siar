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
                            <form action="<?php echo e(route('superadmin.tenants.destroy', $tenant)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tenant ini?');">
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

</div> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/roles/superadmin/tenants/_table.blade.php ENDPATH**/ ?>