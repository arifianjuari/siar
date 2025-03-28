<?php $__env->startSection('title', ' | Pengaturan Tenant'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Pengaturan Tenant</h2>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pengaturan Umum</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('tenant.settings.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Zona Waktu</label>
                                <select class="form-select <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="timezone" name="timezone">
                                    <option value="Asia/Jakarta" <?php echo e(old('timezone', $tenant->settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : ''); ?>>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" <?php echo e(old('timezone', $tenant->settings['timezone'] ?? '') == 'Asia/Makassar' ? 'selected' : ''); ?>>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" <?php echo e(old('timezone', $tenant->settings['timezone'] ?? '') == 'Asia/Jayapura' ? 'selected' : ''); ?>>Asia/Jayapura (WIT)</option>
                                </select>
                                <?php $__errorArgs = ['timezone'];
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
                                <label for="language" class="form-label">Bahasa</label>
                                <select class="form-select <?php $__errorArgs = ['language'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="language" name="language">
                                    <option value="id" <?php echo e(old('language', $tenant->settings['language'] ?? 'id') == 'id' ? 'selected' : ''); ?>>Indonesia</option>
                                    <option value="en" <?php echo e(old('language', $tenant->settings['language'] ?? '') == 'en' ? 'selected' : ''); ?>>English</option>
                                </select>
                                <?php $__errorArgs = ['language'];
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
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Format Tanggal</label>
                                <select class="form-select <?php $__errorArgs = ['date_format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="date_format" name="date_format">
                                    <option value="d/m/Y" <?php echo e(old('date_format', $tenant->settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : ''); ?>>DD/MM/YYYY (31/12/2023)</option>
                                    <option value="m/d/Y" <?php echo e(old('date_format', $tenant->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : ''); ?>>MM/DD/YYYY (12/31/2023)</option>
                                    <option value="Y-m-d" <?php echo e(old('date_format', $tenant->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : ''); ?>>YYYY-MM-DD (2023-12-31)</option>
                                    <option value="d M Y" <?php echo e(old('date_format', $tenant->settings['date_format'] ?? '') == 'd M Y' ? 'selected' : ''); ?>>DD MMM YYYY (31 Dec 2023)</option>
                                </select>
                                <?php $__errorArgs = ['date_format'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="time_format" class="form-label">Format Waktu</label>
                                <select class="form-select <?php $__errorArgs = ['time_format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="time_format" name="time_format">
                                    <option value="H:i" <?php echo e(old('time_format', $tenant->settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : ''); ?>>24 Jam (14:30)</option>
                                    <option value="h:i A" <?php echo e(old('time_format', $tenant->settings['time_format'] ?? '') == 'h:i A' ? 'selected' : ''); ?>>12 Jam (02:30 PM)</option>
                                </select>
                                <?php $__errorArgs = ['time_format'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Mata Uang</label>
                                <select class="form-select <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="currency" name="currency">
                                    <option value="IDR" <?php echo e(old('currency', $tenant->settings['currency'] ?? 'IDR') == 'IDR' ? 'selected' : ''); ?>>Rupiah (IDR)</option>
                                    <option value="USD" <?php echo e(old('currency', $tenant->settings['currency'] ?? '') == 'USD' ? 'selected' : ''); ?>>US Dollar (USD)</option>
                                    <option value="EUR" <?php echo e(old('currency', $tenant->settings['currency'] ?? '') == 'EUR' ? 'selected' : ''); ?>>Euro (EUR)</option>
                                </select>
                                <?php $__errorArgs = ['currency'];
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
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/tenant/settings.blade.php ENDPATH**/ ?>