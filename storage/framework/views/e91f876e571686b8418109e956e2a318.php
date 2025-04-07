<?php $__env->startSection('title', ' | Edit Role'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Edit Role</h2>
        <a href="<?php echo e(route('modules.user-management.roles.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('modules.user-management.roles.update', $role->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Role</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $role->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug Role <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="slug" name="slug" required>
                                <option value="">Pilih Slug Role</option>
                                <option value="superadmin" <?php echo e(old('slug', $role->slug) == 'superadmin' ? 'selected' : ''); ?>>superadmin</option>
                                <option value="tenant-admin" <?php echo e(old('slug', $role->slug) == 'tenant-admin' ? 'selected' : ''); ?>>tenant-admin</option>
                                <option value="manajemen-strategis" <?php echo e(old('slug', $role->slug) == 'manajemen-strategis' ? 'selected' : ''); ?>>manajemen-strategis</option>
                                <option value="manajemen-eksekutif" <?php echo e(old('slug', $role->slug) == 'manajemen-eksekutif' ? 'selected' : ''); ?>>manajemen-eksekutif</option>
                                <option value="manajemen-operasional" <?php echo e(old('slug', $role->slug) == 'manajemen-operasional' ? 'selected' : ''); ?>>manajemen-operasional</option>
                                <option value="staf" <?php echo e(old('slug', $role->slug) == 'staf' ? 'selected' : ''); ?>>staf</option>
                            </select>
                            <div class="form-text">Pilih slug yang sesuai dengan peran pengguna</div>
                            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" rows="3"><?php echo e(old('description', $role->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $role->is_active) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Hak Akses Modul <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th rowspan="2" class="align-middle">Modul</th>
                                        <th colspan="6" class="text-center">Hak Akses</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 10%">View</th>
                                        <th class="text-center" style="width: 10%">Create</th>
                                        <th class="text-center" style="width: 10%">Edit</th>
                                        <th class="text-center" style="width: 10%">Delete</th>
                                        <th class="text-center" style="width: 10%">Export</th>
                                        <th class="text-center" style="width: 10%">Import</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php echo e($module->name); ?>

                                                <input type="hidden" name="permissions[<?php echo e($module->id); ?>][module_id]" value="<?php echo e($module->id); ?>">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_view" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_view]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_view', $rolePermissions[$module->id]['can_view'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_create" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_create]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_create', $rolePermissions[$module->id]['can_create'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_edit" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_edit]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_edit', $rolePermissions[$module->id]['can_edit'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_delete" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_delete]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_delete', $rolePermissions[$module->id]['can_delete'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_export" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_export]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_export', $rolePermissions[$module->id]['can_export'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        id="perm_<?php echo e($module->id); ?>_import" 
                                                        name="permissions[<?php echo e($module->id); ?>][can_import]" 
                                                        value="1" 
                                                        <?php echo e((old('permissions.'.$module->id.'.can_import', $rolePermissions[$module->id]['can_import'] ?? false)) ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Jika ada checkbox 'View' yang dicentang/uncentang
        document.querySelectorAll('input[name$="[can_view]"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Get the module id from the checkbox id
                const moduleId = this.id.split('_')[1];
                
                // If unchecked, uncheck all other permissions
                if (!this.checked) {
                    document.querySelectorAll(`input[id^="perm_${moduleId}_"]:not([id="perm_${moduleId}_view"])`).forEach(function(cb) {
                        cb.checked = false;
                        cb.disabled = true;
                    });
                } else {
                    // If checked, enable other permissions
                    document.querySelectorAll(`input[id^="perm_${moduleId}_"]:not([id="perm_${moduleId}_view"])`).forEach(function(cb) {
                        cb.disabled = false;
                    });
                }
            });
            
            // Initialize on page load
            checkbox.dispatchEvent(new Event('change'));
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/UserManagement/roles/edit.blade.php ENDPATH**/ ?>