<?php $__env->startSection('title', 'Detail Modul'); ?>

<?php $__env->startSection('header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <h2 class="mb-0">Detail Modul: <?php echo e($module->name); ?></h2>
    <div>
        <a href="<?php echo e(route('superadmin.modules.edit', $module)); ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
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
        <!-- Informasi Modul -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Informasi Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-1 mb-3">
                            <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?> text-primary"></i>
                        </div>
                        <h3><?php echo e($module->name); ?></h3>
                        <div><code><?php echo e($module->slug); ?></code></div>
                    </div>

                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 40%">ID</th>
                            <td><?php echo e($module->id); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td><?php echo e($module->description ?? 'Tidak ada deskripsi'); ?></td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td><?php echo e($module->created_at->format('d M Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td><?php echo e($module->updated_at->format('d M Y H:i')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Statistik Modul -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Statistik Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-primary"><?php echo e($activeInTenantCount); ?></div>
                        <div class="text-muted">Tenant Menggunakan Modul Ini</div>
                    </div>

                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($percentageActive); ?>%;" aria-valuenow="<?php echo e($percentageActive); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo e($percentageActive); ?>%</div>
                    </div>
                    <div class="text-center text-muted">
                        <small>Persentase tenant yang mengaktifkan modul ini (<?php echo e($activeInTenantCount); ?> dari <?php echo e($totalTenants); ?>)</small>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <?php if($percentageActive < 100): ?>
                            <form action="<?php echo e(route('superadmin.modules.activate-for-all', $module)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i> Aktifkan untuk Semua Tenant
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if($percentageActive > 0): ?>
                            <form action="<?php echo e(route('superadmin.modules.deactivate-for-all', $module)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-times-circle me-2"></i> Nonaktifkan untuk Semua Tenant
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant yang Menggunakan Modul -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Tenant yang Menggunakan Modul Ini</h5>
                </div>
                <div class="card-body">
                    <?php if($module->tenants->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Tenant</th>
                                        <th>Domain</th>
                                        <th>Status Tenant</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $module->tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($tenant->name); ?></td>
                                            <td><code><?php echo e($tenant->domain); ?></code></td>
                                            <td><?php echo $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                            <td>
                                                <a href="<?php echo e(route('superadmin.tenants.show', $tenant)); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="fas fa-info-circle fa-3x"></i>
                            </div>
                            <p>Belum ada tenant yang menggunakan modul ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/roles/superadmin/modules/show.blade.php ENDPATH**/ ?>