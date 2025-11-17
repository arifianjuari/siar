<?php $__env->startSection('title', 'Tambah Tenant Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Tambah Tenant Baru</h2>
                    <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
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
                    <form id="tenant-form" action="<?php echo e(route('superadmin.tenants.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

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
                                
                                <div class="mb-3">
                                    <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="domain" name="domain" value="<?php echo e(old('domain')); ?>" required>
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
unset($__errorArgs, $__bag); ?>" id="database" name="database" value="<?php echo e(old('database')); ?>" required>
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
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?php echo e(old('is_active') ? 'checked' : ''); ?>>
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
                                                    <?php echo e((is_array(old('modules')) && in_array($module->id, old('modules'))) ? 'checked' : ''); ?>

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

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Admin Tenant</h4>
                                <p class="text-muted mb-3">Buat akun administrator untuk tenant ini:</p>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Nama Admin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['admin_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="admin_name" name="admin_name" value="<?php echo e(old('admin_name')); ?>" required>
                                    <?php $__errorArgs = ['admin_name'];
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
                                    <label for="admin_email" class="form-label">Email Admin <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['admin_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="admin_email" name="admin_email" value="<?php echo e(old('admin_email')); ?>" required>
                                    <?php $__errorArgs = ['admin_email'];
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
                                    <label for="admin_password" class="form-label">Password Admin <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?php $__errorArgs = ['admin_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="admin_password" name="admin_password" required>
                                    <?php $__errorArgs = ['admin_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text">Password minimal 8 karakter</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">Perhatian!</h5>
                                        <p class="mb-0">Dengan membuat tenant baru, Anda akan membuat database dan struktur data terpisah. Pastikan informasi yang dimasukkan sudah benar.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Buat Tenant
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
document.getElementById('tenant-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (response) => {
        const contentType = response.headers.get('content-type') || '';
        let data = null;

        if (contentType.includes('application/json')) {
            try { data = await response.json(); } catch (_) { /* ignore parse error */ }
        }

        if (response.ok) {
            if (data && data.success) {
                alert('Tenant berhasil dibuat!');
                window.location.href = '<?php echo e(route("superadmin.tenants.index")); ?>';
                return;
            }

            // Jika server mengembalikan HTML (redirect/halaman), anggap sukses
            if (contentType.includes('text/html')) {
                window.location.href = '<?php echo e(route("superadmin.tenants.index")); ?>';
                return;
            }

            alert('Terjadi kesalahan: ' + ((data && data.message) ? data.message : 'Respon tidak valid'));
            return;
        }

        // Tidak OK (4xx/5xx)
        let message = (data && data.message) ? data.message : '';
        if (!message) {
            try { message = await response.text(); } catch (_) { message = 'Unknown error'; }
        }
        alert('Terjadi kesalahan: ' + message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + (error && error.message ? error.message : error));
    });
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('roles.superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/roles/superadmin/tenants/create.blade.php ENDPATH**/ ?>