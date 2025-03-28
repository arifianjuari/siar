<?php
    // Mendapatkan modul aktif untuk tenant
    $activeModules = [];
    try {
        if (auth()->check()) {
            $tenant_id = session('tenant_id');
            if ($tenant_id) {
                $tenant = \App\Models\Tenant::find($tenant_id);
                if ($tenant) {
                    $activeModules = $tenant->modules()
                        ->where('tenant_modules.is_active', true)
                        ->orderBy('name')
                        ->get();
                }
            }
        }
    } catch (\Exception $e) {
        // Jika terjadi error, biarkan $activeModules kosong
    }

    // Cek role tenant admin
    $isTenantAdmin = false;
    try {
        if (auth()->check() && auth()->user()->role && auth()->user()->role->slug === 'tenant-admin') {
            $isTenantAdmin = true;
        }
    } catch (\Exception $e) {
        // Abaikan error
    }

    // Cek role superadmin
    $isSuperAdmin = false;
    try {
        if (auth()->check() && auth()->user()->role && auth()->user()->role->slug === 'superadmin') {
            $isSuperAdmin = true;
        }
    } catch (\Exception $e) {
        // Abaikan error
    }
?>

<aside class="sidebar rounded-end p-0" x-data="{ activeDropdown: null }">
    <!-- Tenant Information -->
    <div class="p-4 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <?php
                    $tenantInitial = 'T';
                    $tenantLogo = null;
                    $tenantName = 'Tenant';
                    
                    try {
                        // Dapatkan tenant_id dari session
                        $tenant_id = session('tenant_id');
                        
                        if ($tenant_id) {
                            // Dapatkan tenant dari database
                            $tenant = \App\Models\Tenant::find($tenant_id);
                            
                            if ($tenant) {
                                $tenantName = $tenant->name;
                                $tenantInitial = strtoupper(substr($tenant->name, 0, 1));
                                $tenantLogo = $tenant->logo;
                            }
                        }
                    } catch (\Exception $e) {
                        // Gunakan default
                    }
                ?>
                
                <?php if($tenantLogo): ?>
                    <div class="rounded overflow-hidden d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fff;">
                        <img src="<?php echo e(asset('storage/' . $tenantLogo)); ?>" alt="<?php echo e($tenantName); ?>" class="img-fluid" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                <?php else: ?>
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <span class="fs-4 fw-bold"><?php echo e($tenantInitial); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="mb-0 text-white fw-semibold"><?php echo e($tenantName); ?></h6>
                <?php
                    $roleName = 'Role';
                    try {
                        if (auth()->check() && auth()->user()->role) {
                            $roleName = auth()->user()->role->name ?? 'Role';
                        }
                    } catch (\Exception $e) {
                        // Gunakan default
                    }
                ?>
                <span class="text-white-50 small"><?php echo e($roleName); ?></span>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <div class="p-3">
        <!-- Dashboard - Always available -->
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <span>Dashboard</span>
            </div>
        </a>

        <!-- Tenant Management - untuk tenant admin -->
        <?php if($isTenantAdmin): ?>
            <div class="nav-item dropdown mb-2">
                <button type="button" class="nav-link text-start w-100 <?php echo e(request()->is('tenant*') ? 'active' : ''); ?>" 
                       onclick="toggleTenantDropdown()" 
                       style="border: none; background: none; cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="icon-sidebar">
                            <i class="fas fa-building"></i>
                        </div>
                        <span>Tenant</span>
                        <i class="fas fa-chevron-down ms-auto" id="tenant-dropdown-icon"></i>
                    </div>
                </button>
                <div id="tenantSubmenu" class="collapse" style="padding-left: 1.5rem; margin-top: 5px;">
                    <a href="<?php echo e(url('tenant/profile')); ?>" 
                       class="nav-link <?php echo e(request()->is('tenant/profile*') ? 'active' : ''); ?> my-1">
                        <div class="d-flex align-items-center">
                            <div class="icon-sidebar">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span>Profil</span>
                        </div>
                    </a>
                    <a href="<?php echo e(url('tenant/settings')); ?>" 
                       class="nav-link <?php echo e(request()->is('tenant/settings*') ? 'active' : ''); ?> my-1">
                        <div class="d-flex align-items-center">
                            <div class="icon-sidebar">
                                <i class="fas fa-cog"></i>
                            </div>
                            <span>Pengaturan</span>
                        </div>
                    </a>
                    <a href="<?php echo e(route('tenant.work-units.index')); ?>" 
                       class="nav-link <?php echo e(request()->is('tenant/work-units*') ? 'active' : ''); ?> my-1">
                        <div class="d-flex align-items-center">
                            <div class="icon-sidebar">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <span>Unit Kerja</span>
                        </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Modules Management -->
        <?php if($isTenantAdmin): ?>
        <a href="<?php echo e(route('modules.index')); ?>" class="nav-link <?php echo e(request()->routeIs('modules.index') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i class="fas fa-cubes"></i>
                </div>
                <span>Manajemen Modul</span>
            </div>
        </a>
        <?php endif; ?>

        <!-- Superadmin section -->
        <?php if($isSuperAdmin): ?>
            <div class="sidebar-heading text-uppercase text-white-50 small px-3 mt-4 mb-2">
                Superadmin
            </div>
            
            <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.tenants.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <i class="fas fa-building"></i>
                    </div>
                    <span>Tenant</span>
                </div>
            </a>
            
            <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.modules.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <span>Modul</span>
                </div>
            </a>
        <?php endif; ?>

        <!-- Active Modules Section -->
        <?php if(count($activeModules) > 0): ?>
            <div class="sidebar-heading text-uppercase text-white-50 small px-3 mt-4 mb-2">
                Modul
            </div>
            
            <?php $__currentLoopData = $activeModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($module && !empty($module->slug) && function_exists('hasModulePermission') && hasModulePermission($module->slug)): ?>
                    <?php
                        try {
                            $moduleUrl = '';
                            if (!empty($module->slug)) {
                                if ($module->slug == 'user-management') {
                                    $moduleUrl = 'javascript:void(0);'; // Untuk dropdown, hindari navigasi
                                } elseif ($module->slug == 'product-management') {
                                    $moduleUrl = url('modules/product-management/products');
                                } elseif ($module->slug == 'risk-management') {
                                    $moduleUrl = url('modules/risk-management/risk-reports');
                                } else {
                                    $moduleUrl = url('modules/' . $module->slug);
                                }
                            } else {
                                $moduleUrl = url('dashboard');
                            }
                            
                            $isActive = request()->is('modules/' . $module->slug . '*');
                        } catch (\Exception $e) {
                            $moduleUrl = url('dashboard');
                            $isActive = false;
                        }
                    ?>
                    
                    <?php if($module->slug == 'user-management'): ?>
                        <!-- Menu User Management dengan menu dropdown manual -->
                        <div class="nav-item dropdown mb-2">
                            <button type="button" class="nav-link text-start w-100 <?php echo e($isActive ? 'active' : ''); ?>" 
                                   onclick="toggleUserManagementDropdown()" 
                                   style="border: none; background: none; cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <div class="icon-sidebar">
                                        <?php echo $module->icon_html ?? '<i class="fas fa-users"></i>'; ?>

                                    </div>
                                    <span><?php echo e($module->name); ?></span>
                                    <i class="fas fa-chevron-down ms-auto" id="um-dropdown-icon"></i>
                                </div>
                            </button>
                            <div id="userManagementSubmenu" class="collapse" style="padding-left: 1.5rem; margin-top: 5px;">
                                <a href="<?php echo e(url('modules/user-management/users')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/users*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span>Pengguna</span>
                                    </div>
                                </a>
                                <a href="<?php echo e(url('modules/user-management/roles')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/roles*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i class="fas fa-user-tag"></i>
                                        </div>
                                        <span>Role</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e($moduleUrl); ?>" class="nav-link <?php echo e($isActive ? 'active' : ''); ?> mb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-sidebar">
                                    <?php echo $module->icon_html ?? '<i class="fas fa-folder"></i>'; ?>

                                </div>
                                <span><?php echo e($module->name); ?></span>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
    
    <!-- Bottom Area -->
    <div class="mt-auto p-3 border-top border-secondary">
        <a href="#" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bantuan & Dokumentasi">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i class="fas fa-question-circle"></i>
                </div>
                <span>Bantuan</span>
            </div>
        </a>
    </div>
</aside>

<style>
    .sidebar {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    
    .sidebar .nav-link {
        border-radius: 0.5rem;
        transition: all 0.2s;
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1rem;
    }
    
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .sidebar .nav-link.active {
        background-color: var(--primary-color);
        color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .icon-sidebar {
        width: 24px;
        text-align: center;
        margin-right: 12px;
    }
    
    .sidebar-heading {
        font-size: 10px;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Dropdown styles */
    .sidebar .dropdown-menu {
        padding: 0.5rem 0;
        margin: 0;
        border-radius: 0.5rem;
    }
    
    .sidebar .dropdown-item {
        padding: 0.5rem 1rem 0.5rem 2rem;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .sidebar .dropdown-item:hover,
    .sidebar .dropdown-item:focus {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .sidebar .dropdown-item.active {
        background-color: var(--primary-color);
        color: #fff;
    }
</style>

<!-- Tambahkan script untuk toggle menu dropdown -->
<script>
    // Auto-expand submenu jika halaman active ada di dalamnya
    document.addEventListener('DOMContentLoaded', function() {
        // User Management dropdown
        if (document.querySelector('#userManagementSubmenu a.active')) {
            document.getElementById('userManagementSubmenu').classList.add('show');
            document.getElementById('um-dropdown-icon').classList.add('fa-rotate-180');
        }
        
        // Tenant dropdown
        if (document.querySelector('#tenantSubmenu a.active')) {
            document.getElementById('tenantSubmenu').classList.add('show');
            document.getElementById('tenant-dropdown-icon').classList.add('fa-rotate-180');
        }
    });
    
    function toggleUserManagementDropdown() {
        const submenu = document.getElementById('userManagementSubmenu');
        const icon = document.getElementById('um-dropdown-icon');
        
        submenu.classList.toggle('show');
        icon.classList.toggle('fa-rotate-180');
    }
    
    function toggleTenantDropdown() {
        const submenu = document.getElementById('tenantSubmenu');
        const icon = document.getElementById('tenant-dropdown-icon');
        
        submenu.classList.toggle('show');
        icon.classList.toggle('fa-rotate-180');
    }
</script><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>