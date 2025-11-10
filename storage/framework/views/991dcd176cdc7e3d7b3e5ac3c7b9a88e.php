<?php $__env->startSection('title', 'Hak Akses Modul Role'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Pengaturan Hak Akses Modul: <?php echo e($role->name); ?></h2>
                    <div>
                        <a href="<?php echo e(route('superadmin.tenants.roles.edit', [$tenant, $role])); ?>" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-pen me-2"></i> Edit Role
                        </a>
                        <a href="<?php echo e(route('superadmin.tenants.show', $tenant)); ?>" class="btn btn-outline-primary">
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
    
    <?php if(session('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo e(session('warning')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hak Akses Role: <span class="fw-bold"><?php echo e($role->name); ?></span></h5>
                        <div class="d-flex gap-2">
                            <button type="button" id="check-all" class="btn btn-sm btn-outline-primary">Centang Semua</button>
                            <button type="button" id="uncheck-all" class="btn btn-sm btn-outline-secondary">Hapus Semua Centang</button>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Pilih akses yang diizinkan untuk role <?php echo e($role->name); ?> di tenant <?php echo e($tenant->name); ?></p>
                </div>
                
                <div class="card-body">
                    <form id="permission-form" action="<?php echo e(route('superadmin.tenants.roles.permissions.update', [$tenant, $role])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="30%">Modul</th>
                                        <th>Hak Akses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr id="module-row-<?php echo e($module->id); ?>">
                                        <td><?php echo e($module->name); ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-3">
                                                <input type="hidden" name="permissions[<?php echo e($module->id); ?>][module_id]" value="<?php echo e($module->id); ?>">
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input view-permission" 
                                                        id="can_view_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_view]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? 'checked' : ''); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_view_<?php echo e($module->id); ?>">Lihat</label>
                                                </div>
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input other-permission" 
                                                        id="can_create_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_create]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_create === 1 ? 'checked' : ''); ?>

                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? '' : 'disabled'); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_create_<?php echo e($module->id); ?>">Tambah</label>
                                                </div>
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input other-permission" 
                                                        id="can_edit_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_edit]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_edit === 1 ? 'checked' : ''); ?>

                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? '' : 'disabled'); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_edit_<?php echo e($module->id); ?>">Edit</label>
                                                </div>
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input other-permission" 
                                                        id="can_delete_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_delete]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_delete === 1 ? 'checked' : ''); ?>

                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? '' : 'disabled'); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_delete_<?php echo e($module->id); ?>">Hapus</label>
                                                </div>
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input other-permission" 
                                                        id="can_export_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_export]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_export === 1 ? 'checked' : ''); ?>

                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? '' : 'disabled'); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_export_<?php echo e($module->id); ?>">Export</label>
                                                </div>
                                                
                                                <div class="form-check me-3">
                                                    <input type="checkbox" class="form-check-input other-permission" 
                                                        id="can_import_<?php echo e($module->id); ?>" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_import]" 
                                                        value="1"
                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_import === 1 ? 'checked' : ''); ?>

                                                        <?php echo e(isset($rolePermissions[$module->id]) && (int)$rolePermissions[$module->id]->can_view === 1 ? '' : 'disabled'); ?>

                                                        data-module-id="<?php echo e($module->id); ?>">
                                                    <label class="form-check-label" for="can_import_<?php echo e($module->id); ?>">Import</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <?php if(count($modules) === 0): ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada modul yang tersedia</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Form buttons -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <a href="<?php echo e(route('superadmin.tenants.show', $tenant)); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Hak Akses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submit debugging
        const form = document.getElementById('permission-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Pastikan semua checkbox yang tidak dicentang mengirim nilai 0
                document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
                    if (!checkbox.checked) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = checkbox.name;
                        hiddenInput.value = '0';
                        form.appendChild(hiddenInput);
                    }
                });

                console.log('Form Submitted');
                let formData = new FormData(form);
                let formDataObj = {};
                
                for (let [key, value] of formData.entries()) {
                    formDataObj[key] = value;
                }
                
                console.log('Form Data:', formDataObj);
            });
        }

        // Check-uncheck all permissions
        document.getElementById('check-all').addEventListener('click', function() {
            document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
                checkbox.checked = true;
                checkbox.disabled = false;
            });
        });

        document.getElementById('uncheck-all').addEventListener('click', function() {
            document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
                checkbox.checked = false;
                if (!checkbox.classList.contains('view-permission')) {
                    checkbox.disabled = true;
                }
            });
        });

        // Mengelola checkbox view-permission untuk mengaktifkan/menonaktifkan checkbox lain
        const viewPermissions = document.querySelectorAll('.view-permission');
        viewPermissions.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const moduleId = this.getAttribute('data-module-id');
                const otherPermissions = document.querySelectorAll(`.other-permission[data-module-id="${moduleId}"]`);
                
                otherPermissions.forEach(function(otherCheckbox) {
                    if (!checkbox.checked) {
                        otherCheckbox.checked = false;
                        otherCheckbox.disabled = true;
                    } else {
                        otherCheckbox.disabled = false;
                    }
                });
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('roles.superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/roles/superadmin/tenants/roles/permissions.blade.php ENDPATH**/ ?>