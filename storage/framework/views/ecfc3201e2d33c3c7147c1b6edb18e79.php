<?php $__env->startSection('title', ' | Tambah Role Baru'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Tambah Role Baru</h2>
        
        <a href="<?php echo e(route('modules.user-management.roles.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <form action="<?php echo e(route('modules.user-management.roles.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
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
                    </div>
                    
                    <div class="col-md-6">
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
                                <option value="superadmin" <?php echo e(old('slug') == 'superadmin' ? 'selected' : ''); ?>>superadmin</option>
                                <option value="tenant-admin" <?php echo e(old('slug') == 'tenant-admin' ? 'selected' : ''); ?>>tenant-admin</option>
                                <option value="manajemen-strategis" <?php echo e(old('slug') == 'manajemen-strategis' ? 'selected' : ''); ?>>manajemen-strategis</option>
                                <option value="manajemen-eksekutif" <?php echo e(old('slug') == 'manajemen-eksekutif' ? 'selected' : ''); ?>>manajemen-eksekutif</option>
                                <option value="manajemen-operasional" <?php echo e(old('slug') == 'manajemen-operasional' ? 'selected' : ''); ?>>manajemen-operasional</option>
                                <option value="staf" <?php echo e(old('slug') == 'staf' ? 'selected' : ''); ?>>staf</option>
                            </select>
                            <div class="form-text">Pilih slug yang paling mendekati dengan peran pengguna</div>
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
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" value="<?php echo e(old('description')); ?>">
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
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5>Pengaturan Hak Akses</h5>
                    <hr>
                    
                    <?php if($modules->isEmpty()): ?>
                        <div class="alert alert-warning">
                            Tidak ada modul aktif yang tersedia untuk tenant ini.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="15%">Modul</th>
                                        <th class="text-center" width="10%">Lihat</th>
                                        <th class="text-center" width="10%">Tambah</th>
                                        <th class="text-center" width="10%">Edit</th>
                                        <th class="text-center" width="10%">Hapus</th>
                                        <th class="text-center" width="10%">Ekspor</th>
                                        <th class="text-center" width="10%">Impor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="module_<?php echo e($module->id); ?>">
                                                    <strong><?php echo e($module->name); ?></strong>
                                                </label>
                                                <input type="hidden" name="permissions[<?php echo e($module->id); ?>][module_id]" value="<?php echo e($module->id); ?>">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_view_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_view]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_view") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_create_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_create]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_create") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_edit_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_edit]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_edit") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_delete_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_delete]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_delete") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_export_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_export]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_export") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_import_<?php echo e($module->id); ?>" 
                                                           name="permissions[<?php echo e($module->id); ?>][can_import]" 
                                                           value="1" 
                                                           <?php echo e(old("permissions.{$module->id}.can_import") ? 'checked' : ''); ?>>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="my-3">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">Batalkan Semua</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tombol pilih semua permission
        document.getElementById('selectAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
        
        // Tombol batalkan semua permission
        document.getElementById('deselectAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/UserManagement/roles/create.blade.php ENDPATH**/ ?>