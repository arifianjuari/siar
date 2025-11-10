<?php $__env->startSection('title', ' | Daftar Modul'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Manajemen Modul</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Modul</li>
            </ol>
        </nav>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Modul Aktif -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modul Aktif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Modul</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tenantModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 bg-light rounded p-2">
                                                    <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?> text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($module->name); ?></h6>
                                                    <small class="text-muted"><?php echo e($module->code ?? 'No code'); ?> / <?php echo e($module->slug ?? 'No slug'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($module->description ?? 'Modul ' . $module->name); ?></td>
                                        <td>
                                            <?php if($module->pivot->is_active): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Dalam Pengajuan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($module->pivot->is_active): ?>
                                                <?php if(!empty($module->slug)): ?>
                                                    <?php
                                                        $moduleUrl = '';
                                                        try {
                                                            if (!empty($module->slug)) {
                                                                if ($module->slug == 'user-management') {
                                                                    $moduleUrl = url('modules/user-management/users');
                                                                } elseif ($module->slug == 'product-management') {
                                                                    $moduleUrl = url('modules/product-management/products');
                                                                } elseif ($module->slug == 'risk-management') {
                                                                    $moduleUrl = url('modules/risk-management');
                                                                } elseif ($module->slug == 'document-management') {
                                                                    $moduleUrl = url('modules/document-management/dashboard');
                                                                } elseif ($module->slug == 'correspondence') {
                                                                    $moduleUrl = url('modules/correspondence');
                                                                } elseif ($module->slug == 'dashboard') {
                                                                    $moduleUrl = url('dashboard');
                                                                } else {
                                                                    $moduleUrl = url('modules/' . $module->slug);
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            $moduleUrl = url('dashboard');
                                                        }
                                                    ?>
                                                    <a href="<?php echo e($moduleUrl); ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i> Akses
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-exclamation-circle me-1"></i> Modul Belum Tersedia
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-info">Menunggu Persetujuan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i> Belum ada modul aktif
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modul Tersedia -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Modul Tersedia</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Modul</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $inactiveModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 bg-light rounded p-2">
                                                    <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?> text-secondary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($module->name); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($module->description ?? 'Modul ' . $module->name); ?></td>
                                        <td><span class="badge bg-secondary">Tidak Aktif</span></td>
                                        <td>
                                            <form action="<?php echo e(route('modules.request-activation')); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="module_id" value="<?php echo e($module->id); ?>">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-paper-plane me-1"></i> Ajukan Modul
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i> Semua modul sudah diaktifkan atau dalam pengajuan
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panduan Pengajuan -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Panduan Pengajuan Modul</h5>
                </div>
                <div class="card-body">
                    <ol class="ps-3">
                        <li class="mb-2">Pilih modul yang ingin diaktifkan dari daftar <strong>Modul Tersedia</strong></li>
                        <li class="mb-2">Klik tombol <strong>Ajukan Modul</strong> untuk mengirim permintaan</li>
                        <li class="mb-2">Permintaan akan dikirim ke Superadmin untuk diproses</li>
                        <li class="mb-2">Status pengajuan dapat dipantau melalui tabel <strong>Modul Aktif</strong></li>
                    </ol>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> Pengajuan modul memerlukan persetujuan dari Superadmin. Harap menunggu hingga pengajuan disetujui.
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/index.blade.php ENDPATH**/ ?>