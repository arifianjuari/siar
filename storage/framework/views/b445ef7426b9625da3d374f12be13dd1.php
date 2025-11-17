<?php ($hideDefaultHeader = true); ?>

<?php $__env->startSection('title', 'Edit Tenant'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Tenant: <span id="tenant-name"><?php echo e($tenant->name); ?></span></h2>
                    <div>
                        <a href="<?php echo e(route('superadmin.tenants.show', $tenant)); ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-eye me-2"></i> Lihat Detail
                        </a>
                        <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form id="edit-tenant-form" action="<?php echo e(route('superadmin.tenants.update', $tenant)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Informasi Tenant</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Tenant <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $tenant->name)); ?>" required>
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
                                    <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="domain" name="domain" value="<?php echo e(old('domain', $tenant->domain)); ?>" required>
                                    <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">Domain harus unik dan digunakan untuk akses tenant (contoh: company1)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="database" class="form-label">Database <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="database" name="database" value="<?php echo e(old('database', $tenant->database)); ?>" required>
                                    <?php $__errorArgs = ['database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">Nama database untuk tenant ini (contoh: tenant1_db)</div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $tenant->is_active) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_active">Tenant Aktif</label>
                                    <div class="form-text">Tenant harus aktif agar pengguna dapat mengakses</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Modul</h4>
                                <p class="text-muted mb-3">Pilih modul yang akan diaktifkan untuk tenant ini:</p>
                                
                                <div class="row">
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    name="modules[]" 
                                                    value="<?php echo e($module->id); ?>" 
                                                    id="module-<?php echo e($module->id); ?>"
                                                    <?php echo e((is_array(old('modules')) && in_array($module->id, old('modules'))) || 
                                                        (old('modules') === null && in_array($module->id, $activeModuleIds)) ? 'checked' : ''); ?>

                                                >
                                                <label class="form-check-label" for="module-<?php echo e($module->id); ?>">
                                                    <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?> me-2"></i>
                                                    <?php echo e($module->name); ?>

                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php $__errorArgs = ['modules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">Info</h5>
                                        <p class="mb-0">Perubahan status modul akan mempengaruhi akses pengguna tenant ini. Pastikan untuk mengomunikasikan perubahan kepada admin tenant.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
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
<script src="<?php echo e(asset('js/ajax-utils.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-tenant-form');
    const tenantName = document.getElementById('tenant-name');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            const data = {};
            
            // Convert FormData to object
            formData.forEach((value, key) => {
                if (key === 'modules[]') {
                    if (!data.modules) data.modules = [];
                    data.modules.push(value);
                } else {
                    data[key] = value;
                }
            });
            
            // Handle checkbox
            data.is_active = formData.get('is_active') === '1';
            
            // Add _method field for PUT request
            data._method = 'PUT';
            
            // Send as POST request with _method=PUT
            const response = await AjaxUtils.request(this.action, 'POST', data);
            
            if (response.success) {
                const tenantData = response.data.tenant;
                
                // Show success notification
                AjaxUtils.showNotification(response.message, 'success');
                
                // Redirect ke halaman daftar tenant
                if (response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            AjaxUtils.showNotification(error.message, 'danger');
        }
    });
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('roles.superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/roles/superadmin/tenants/edit.blade.php ENDPATH**/ ?>