<?php $__env->startSection('title', ' | Profil Tenant'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Profil Tenant</h2>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Tenant</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if($tenant->logo): ?>
                        <img src="<?php echo e(asset('storage/' . $tenant->logo)); ?>?v=<?php echo e($tenant->updated_at->timestamp); ?>" alt="<?php echo e($tenant->name); ?>" class="img-fluid rounded mb-3" style="max-height: 150px; object-fit: contain;" onerror="this.onerror=null; this.src='<?php echo e(asset('storage/' . $tenant->logo)); ?>?v=' + new Date().getTime();">
                        <script>
                            // Pre-load logo untuk mencegah masalah loading pertama kali
                            (function() {
                                // Fetch logo dari server dengan force reload
                                fetch('<?php echo e(asset('storage/' . $tenant->logo)); ?>?force_refresh=1&t=<?php echo e(time()); ?>', {
                                    method: 'GET',
                                    cache: 'no-store' // Jangan menggunakan cache
                                }).then(function() {
                                    console.log('Logo pre-fetched successfully');
                                    
                                    // Force refresh logo yang sudah ada
                                    var img = document.querySelector('img[alt="<?php echo e($tenant->name); ?>"]');
                                    if (img) {
                                        img.src = '<?php echo e(asset('storage/' . $tenant->logo)); ?>?v=' + new Date().getTime();
                                    }
                                }).catch(function(err) {
                                    console.error('Error pre-fetching logo:', err);
                                });
                            })();
                        </script>
                    <?php endif; ?>
                    <h4><?php echo e($tenant->name); ?></h4>
                    <?php if($tenant->description): ?>
                        <p class="text-muted"><?php echo e($tenant->description); ?></p>
                    <?php endif; ?>
                </div>
                
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row" style="width: 120px;">Alamat</th>
                            <td><?php echo e($tenant->address ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Kota</th>
                            <td><?php echo e($tenant->city ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Pimpinan RS</th>
                            <td><?php echo e($tenant->ceo ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Pangkat</th>
                            <td><?php echo e($tenant->ceo_rank ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">NRP/NIK</th>
                            <td><?php echo e($tenant->ceo_nrp ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td><?php echo e($tenant->email ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Telepon</th>
                            <td><?php echo e($tenant->phone ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Dibuat Pada</th>
                            <td><?php echo e($tenant->created_at->format('d M Y')); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Profil Tenant</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('tenant.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
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
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" rows="3"><?php echo e(old('description', $tenant->description)); ?></textarea>
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" value="<?php echo e(old('email', $tenant->email)); ?>">
                                <?php $__errorArgs = ['email'];
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
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone" name="phone" value="<?php echo e(old('phone', $tenant->phone)); ?>">
                                <?php $__errorArgs = ['phone'];
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
                    
                    <div class="mb-3">
                        <label for="ceo" class="form-label">Pimpinan RS</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['ceo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ceo" name="ceo" value="<?php echo e(old('ceo', $tenant->ceo)); ?>">
                        <div class="form-text">Nama lengkap Direktur / Pimpinan utama rumah sakit.</div>
                        <?php $__errorArgs = ['ceo'];
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ceo_rank" class="form-label">Pangkat</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ceo_rank'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ceo_rank" name="ceo_rank" value="<?php echo e(old('ceo_rank', $tenant->ceo_rank)); ?>">
                                <div class="form-text">Pangkat Direktur / Pimpinan RS.</div>
                                <?php $__errorArgs = ['ceo_rank'];
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
                                <label for="ceo_nrp" class="form-label">NRP/NIK</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['ceo_nrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ceo_nrp" name="ceo_nrp" value="<?php echo e(old('ceo_nrp', $tenant->ceo_nrp)); ?>">
                                <div class="form-text">NRP/NIK Direktur / Pimpinan RS.</div>
                                <?php $__errorArgs = ['ceo_nrp'];
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
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="address" name="address" rows="2"><?php echo e(old('address', $tenant->address)); ?></textarea>
                        <?php $__errorArgs = ['address'];
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
                        <label for="city" class="form-label">Kota</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="city" name="city" value="<?php echo e(old('city', $tenant->city)); ?>">
                        <?php $__errorArgs = ['city'];
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
                    
                    <div class="mb-4">
                        <label for="logo" class="form-label">Logo</label>
                        <div class="input-group">
                            <input type="file" class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="logo" name="logo" accept="image/jpeg,image/png,image/gif">
                            <?php if($tenant->logo): ?>
                                <a href="<?php echo e(asset('storage/' . $tenant->logo)); ?>?v=<?php echo e($tenant->updated_at->timestamp); ?>" target="_blank" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Lihat Logo
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Format yang diizinkan: JPG, PNG, GIF. Ukuran maksimal: 2MB. Ukuran optimal: 400px lebar.</div>
                        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="logo-preview" class="mt-2 d-none">
                            <img src="#" class="img-thumbnail" style="max-height: 150px; max-width: 200px;">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="letter_head" class="form-label">Kop Surat</label>
                        <textarea class="form-control <?php $__errorArgs = ['letter_head'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="letter_head" name="letter_head" rows="3"><?php echo e(old('letter_head', $tenant->letter_head)); ?></textarea>
                        <div class="form-text">Masukkan teks untuk kop surat yang akan ditampilkan pada dokumen resmi.</div>
                        <?php $__errorArgs = ['letter_head'];
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
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview logo saat dipilih
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logo-preview');
        const logoPreviewImg = logoPreview.querySelector('img');
        
        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Cek ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    logoPreview.classList.add('d-none');
                    return;
                }
                
                // Cek tipe file
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    logoPreview.classList.add('d-none');
                    return;
                }
                
                // Tampilkan preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreviewImg.src = e.target.result;
                    logoPreview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                logoPreview.classList.add('d-none');
            }
        });
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/roles/tenant/profile.blade.php ENDPATH**/ ?>