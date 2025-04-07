<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-2">Dashboard Superadmin</h2>
                            <p class="lead mb-0">Selamat datang di panel administrasi superadmin SIAR.</p>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <a href="<?php echo e(route('superadmin.statistics')); ?>" class="btn btn-light px-4">
                                <i class="fas fa-chart-line me-2"></i>Lihat Statistik
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total Tenant</h6>
                            <h3 class="mb-0 mt-2 fw-bold"><?php echo e(\App\Models\Tenant::count()); ?></h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Tenant</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Tenant Aktif</h6>
                            <h3 class="mb-0 mt-2 fw-bold"><?php echo e(\App\Models\Tenant::where('is_active', true)->count()); ?></h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <?php 
                            $totalTenants = \App\Models\Tenant::count();
                            $activeTenants = \App\Models\Tenant::where('is_active', true)->count();
                            $activePercentage = $totalTenants > 0 ? ($activeTenants / $totalTenants * 100) : 0;
                        ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($activePercentage); ?>%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="<?php echo e(route('superadmin.statistics')); ?>" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Lihat Statistik</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total Modul</h6>
                            <h3 class="mb-0 mt-2 fw-bold"><?php echo e(\App\Models\Module::count()); ?></h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Modul</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total User</h6>
                            <h3 class="mb-0 mt-2 fw-bold"><?php echo e(\App\Models\User::count()); ?></h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="<?php echo e(route('superadmin.users.index')); ?>" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Pengguna</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tenant Terbaru -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-building me-2 text-primary"></i>Tenant Terbaru</h5>
                    <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = \App\Models\Tenant::latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-3 fw-medium"><?php echo e($tenant->name); ?></td>
                                    <td><span class="text-primary"><?php echo e($tenant->domain); ?></span></td>
                                    <td><?php echo $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                    <td><?php echo e($tenant->created_at->format('d M Y')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permintaan Aktivasi Modul -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-bell me-2 text-danger"></i>
                        Permintaan Aktivasi Modul
                        <?php
                            $pendingRequests = \App\Models\TenantModule::pendingRequests()
                                ->with(['tenant', 'module', 'requester'])
                                ->count();
                        ?>
                        <?php if($pendingRequests > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo e($pendingRequests); ?></span>
                        <?php endif; ?>
                    </h5>
                    <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-check-double me-1"></i>Kelola Permintaan
                    </a>
                </div>
                <div class="card-body">
                    <?php
                        $moduleRequests = \App\Models\TenantModule::pendingRequests()
                            ->with(['tenant', 'module', 'requester'])
                            ->latest('requested_at')
                            ->take(5)
                            ->get();
                    ?>

                    <?php if($moduleRequests->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $moduleRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('superadmin.tenants.edit', $request->tenant_id)); ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <p class="mb-1 fw-medium">
                                            <i class="fas fa-building me-1 text-primary"></i>
                                            <?php echo e($request->tenant->name); ?>

                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas <?php echo e($request->module->icon ?? 'fa-cube'); ?> me-1"></i>
                                            <?php echo e($request->module->name); ?>

                                            <span class="ms-2 text-danger">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo e($request->requested_at->diffForHumans()); ?>

                                            </span>
                                        </p>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="mb-0 text-muted">Tidak ada permintaan aktivasi modul yang tertunda.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modul Populer -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-star me-2 text-warning"></i>Modul Populer</h5>
                    <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog me-1"></i>Kelola Modul
                    </a>
                </div>
                <div class="card-body">
                    <?php
                        $popularModules = \App\Models\Module::withCount(['tenants' => function($q) {
                            $q->where('tenant_modules.is_active', true);
                        }])->orderBy('tenants_count', 'desc')->take(5)->get();
                    ?>

                    <ul class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $popularModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 stat-icon bg-light">
                                        <i class="fas <?php echo e($module->icon ?? 'fa-cube'); ?> text-primary"></i>
                                    </div>
                                    <div class="fw-medium"><?php echo e($module->name); ?></div>
                                </div>
                                <div>
                                    <span class="badge bg-primary"><?php echo e($module->tenants_count); ?> tenant</span>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">Belum ada data modul</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User terbaru -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-info"></i>Pengguna Terbaru</h5>
                    <a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-cog me-1"></i>Kelola Pengguna
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama</th>
                                    <th>Email</th>
                                    <th>Tenant</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = \App\Models\User::with(['tenant', 'role'])->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-3 fw-medium"><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td><?php echo e($user->tenant->name ?? '-'); ?></td>
                                    <td><?php echo e($user->role->name ?? '-'); ?></td>
                                    <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                                    <td><?php echo $user->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'; ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ini tempat untuk kode JavaScript tambahan jika diperlukan
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('superadmin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/superadmin/dashboard.blade.php ENDPATH**/ ?>