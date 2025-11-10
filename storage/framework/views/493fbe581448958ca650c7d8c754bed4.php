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

<div class="sidebar-wrapper">
    <aside class="sidebar rounded-end p-0 menu-uniform" x-data="{ activeDropdown: null }">
    <!-- Feather Icons script tidak lagi diperlukan karena ikon sudah inline SVG -->

    <!-- Sidebar Navigation -->
    <div class="p-3">
        <!-- Dashboard - Always available -->
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link sidebar-link <?php echo e(request()->routeIs('dashboard*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <span class="menu-text">Dashboard</span>
            </div>
        </a>

        <!-- Superadmin section -->
        <?php if($isSuperAdmin): ?>
            <div class="sidebar-heading text-uppercase text-white-50 small px-3 mt-4 mb-2">
                Superadmin
            </div>
            
            <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="nav-link sidebar-link <?php echo e(request()->routeIs('superadmin.tenants.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <span class="menu-text">Tenant</span>
                </div>
            </a>
            
            <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="nav-link sidebar-link <?php echo e(request()->routeIs('superadmin.modules.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </div>
                    <span class="menu-text">Modul</span>
                </div>
            </a>

            <a href="<?php echo e(route('superadmin.users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.users.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <span class="menu-text">Pengguna</span>
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
                        // Skip unit kerja dan SPO modules here, we'll show them in a new section
                        if ($module->slug == 'work-units' || $module->slug == 'spo-management') {
                            continue;
                        }
                    ?>
                    
                    <?php
                        try {
                            $moduleUrl = '';
                            if (!empty($module->slug)) {
                                if ($module->slug == 'user-management') {
                                    $moduleUrl = 'javascript:void(0);'; // Untuk dropdown, hindari navigasi
                                } elseif ($module->slug == 'product-management') {
                                    $moduleUrl = url('modules/product-management/products');
                                } elseif ($module->slug == 'risk-management') {
                                    $moduleUrl = url('modules/risk-management/dashboard');
                                } elseif ($module->slug == 'document-management') {
                                    $moduleUrl = url('modules/document-management/dashboard');
                                } elseif ($module->slug == 'work-units') {
                                    $moduleUrl = url('work-units-dashboard'); // URL ke dashboard unit kerja
                                } elseif ($module->slug == 'activity-management') {
                                    $moduleUrl = url('activity-management'); // URL ke dashboard pengelolaan kegiatan
                                } else {
                                    $moduleUrl = url('modules/' . $module->slug);
                                }
                            } else {
                                $moduleUrl = url('dashboard');
                            }
                            
                            $isActive = request()->is('modules/' . $module->slug . '*');
                            
                            // Sesuaikan $isActive khusus untuk work-units jika perlu
                            if ($module->slug == 'work-units') {
                                $isActive = (request()->is('work-units*') || request()->is('work-units-dashboard*')) 
                                          && !request()->is('work-units/spo*');
                            }
                            
                            // Sesuaikan $isActive khusus untuk activity-management
                            if ($module->slug == 'activity-management') {
                                $isActive = request()->is('activity-management*');
                            }
                            
                            // Menentukan ikon yang lebih sesuai berdasarkan slug modul
                            $moduleIcon = '<i data-feather="folder"></i>'; // Default icon
                            
                            // Tentukan icon sesuai dengan jenis modul terlebih dahulu
                            if ($module->slug == 'user-management') {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                            } elseif ($module->slug == 'document-management' || strpos(strtolower($module->name), 'dokumen') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
                            } elseif ($module->slug == 'risk-management' || strpos(strtolower($module->name), 'risiko') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                            } elseif ($module->slug == 'product-management') {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
                            } elseif ($module->slug == 'correspondence' || strpos(strtolower($module->name), 'korespondensi') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
                            } elseif ($module->slug == 'performance-management' || strpos(strtolower($module->name), 'kpi') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>';
                            } elseif (strpos(strtolower($module->name), 'task') !== false || strpos(strtolower($module->name), 'tugas') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                            } elseif (strpos(strtolower($module->name), 'kegiatan') !== false || strpos(strtolower($module->name), 'activity') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
                            } elseif (strpos(strtolower($module->name), 'inventory') !== false || strpos(strtolower($module->name), 'inventaris') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
                            } elseif (strpos(strtolower($module->name), 'finance') !== false || strpos(strtolower($module->name), 'keuangan') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>';
                            } elseif (strpos(strtolower($module->name), 'report') !== false || strpos(strtolower($module->name), 'laporan') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>';
                            } elseif (strpos(strtolower($module->name), 'dashboard') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>';
                            } elseif (strpos(strtolower($module->name), 'setting') !== false || strpos(strtolower($module->name), 'pengaturan') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
                            } elseif (strpos(strtolower($module->name), 'notif') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
                            } elseif (strpos(strtolower($module->name), 'chat') !== false || strpos(strtolower($module->name), 'pesan') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>';
                            } elseif (strpos(strtolower($module->name), 'calendar') !== false || strpos(strtolower($module->name), 'kalender') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
                            } elseif (strpos(strtolower($module->name), 'kendali mutu biaya') !== false || strpos(strtolower($module->name), 'quality cost control') !== false) {
                                $moduleIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
                                $moduleUrl = route('kendali-mutu-biaya.index');
                                $isActive = request()->is('kendali-mutu-biaya*');
                            }
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
                                        <?php echo $moduleIcon; ?>

                                    </div>
                                    <span class="menu-text"><?php echo e($module->name); ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down ms-auto" id="um-dropdown-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                            </button>
                            <div id="userManagementSubmenu" class="collapse" style="padding-left: 1.5rem; margin-top: 5px;">
                                <a href="<?php echo e(url('modules/user-management/users')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/users*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                        </div>
                                        <span class="menu-text">Pengguna</span>
                                    </div>
                                </a>
                                <a href="<?php echo e(url('modules/user-management/roles')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/roles*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-award"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                        </div>
                                        <span class="menu-text">Role</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e($moduleUrl); ?>" class="nav-link sidebar-link <?php echo e($isActive ? 'active' : ''); ?> mb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-sidebar">
                                    <?php echo $moduleIcon; ?>

                                </div>
                                <span class="menu-text"><?php echo e($module->name); ?></span>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        
        <!-- Unit Kerja Section -->
        <div class="sidebar-heading text-uppercase text-white-50 small px-3 mt-4 mb-2">
            Unit Kerja
        </div>
        
        <?php
        try {
            $userWorkUnitId = auth()->user()->work_unit_id ?? null;
        } catch (\Exception $e) {
            $userWorkUnitId = null;
        }
        ?>
        
        <!-- 1. Profil Unit Saya sebagai menu teratas -->
        <?php if($userWorkUnitId): ?>
        <a href="<?php echo e(route('work-units.dashboard', $userWorkUnitId)); ?>" 
           class="nav-link sidebar-link <?php echo e(request()->is('work-units/*/dashboard') && !request()->is('work-units/spo*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <span class="menu-text">Profil Unit Saya</span>
            </div>
        </a>
        <?php endif; ?>
        
        <!-- Manajemen SPO -->
        <a href="<?php echo e(url('work-units/spo/dashboard')); ?>" 
           class="nav-link sidebar-link <?php echo e(request()->is('work-units/spo*') || request()->routeIs('work-units.spo.*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
                <span class="menu-text">Manajemen SPO</span>
            </div>
        </a>
    </div>
    
    <!-- Bottom Area -->
    <div class="mt-auto p-3 border-top border-light bottom-nav">
        <a href="<?php echo e(route('tenant.document-references.index')); ?>" class="nav-link <?php echo e(request()->routeIs('tenant.document-references.*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <span class="menu-text text-truncate" style="max-width: 140px;">Daftar Referensi</span>
            </div>
        </a>
        <a href="<?php echo e(route('pages.help')); ?>" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bantuan & Dokumentasi">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                </div>
                <span class="menu-text text-truncate" style="max-width: 140px;">Bantuan</span>
            </div>
        </a>
    </div>
    </aside>
</div>

<style>
    /* Variabel Warna Baru (Contoh) */
    :root {
        --sidebar-bg: #FFFFFF;
        --sidebar-text-inactive: #6B7280; /* Abu-abu */
        --sidebar-text-active: #4F46E5; /* Indigo */
        --sidebar-icon-inactive: #9CA3AF; /* Abu-abu lebih terang */
        --sidebar-icon-active: #4F46E5; /* Indigo */
        --sidebar-active-border: #4F46E5; /* Indigo */
        --sidebar-hover-bg: #F3F4F6; /* Abu-abu sangat terang */
        --sidebar-heading-text: #6B7280; /* Abu-abu */
    }

    .menu-uniform {
        --menu-font-size: 14px;
    }
    
    .sidebar {
        display: flex;
        flex-direction: column;
        background-color: var(--sidebar-bg) !important; /* Latar belakang putih */
        font-family: Roboto, system-ui, -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif; /* Font bersih */
        border-right: 1px solid #e5e7eb; /* Optional: border pemisah tipis */
        padding-bottom: 90px; /* Tambahkan padding bawah lebih besar untuk menghindari tumpukan dengan menu bottom */
        /* Hapus min-height, height, width, overflow, position agar tidak bentrok dengan .sidebar-wrapper */
    }

    /* Menghapus background pada initial jika tidak ada logo */
    
    .sidebar .nav-link {
        border-radius: 0.5rem;
        transition: all 0.1s;
        color: var(--sidebar-text-inactive) !important; /* Warna teks non-aktif */
        padding: 0.5rem 0.75rem !important; /* Kembalikan padding atau sesuaikan */
        font-size: var(--menu-font-size) !important;
        margin-bottom: 0.5rem; /* Jarak antar menu (sesuaikan dari mb-2 di HTML jika perlu) */
        border-left: 3px solid transparent; /* Placeholder untuk border aktif */
    }

    /* Hapus margin bottom dari elemen a jika sudah diatur di .nav-link */
    .sidebar .p-3 a.mb-2,
    .sidebar .p-3 div.mb-2 {
      margin-bottom: 0 !important;
    }
     /* Tambahkan margin bottom ke wrapper jika itu dropdown */
     .sidebar .p-3 > .nav-item.dropdown {
         margin-bottom: 0.5rem;
     }
     /* Tambahkan margin bottom ke link biasa */
     .sidebar .p-3 > a.nav-link {
         margin-bottom: 0.5rem;
     }


    .sidebar .nav-link:hover {
        background-color: var(--sidebar-hover-bg); /* Latar hover */
        color: var(--sidebar-text-active) !important; /* Warna teks hover */
        border-left-color: var(--sidebar-icon-inactive); /* Warna border hover ringan */
    }
    
    .sidebar .nav-link:hover .icon-sidebar svg {
        color: var(--sidebar-icon-active) !important;
        stroke: var(--sidebar-icon-active) !important;
    }
    
    .sidebar .nav-link.active {
        background-color: transparent !important; /* Hapus background aktif */
        color: var(--sidebar-text-active) !important; /* Warna teks aktif */
        font-weight: 600 !important; /* Tebalkan teks aktif */
        border-left: 3px solid var(--sidebar-active-border) !important; /* Garis aktif */
        box-shadow: none !important; /* Hapus shadow */
    }

    .sidebar .nav-link.active .icon-sidebar svg {
        color: var(--sidebar-icon-active) !important;
        stroke: var(--sidebar-icon-active) !important;
    }

    .sidebar .nav-link.active span.menu-text {
       color: var(--sidebar-text-active) !important;
       font-weight: 600 !important;
    }
    
    .icon-sidebar {
        width: 24px !important;
        text-align: center !important;
        margin-right: 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-sidebar svg {
        width: 18px !important;
        height: 18px !important;
        stroke-width: 2 !important;
        color: var(--sidebar-icon-inactive) !important;
        stroke: var(--sidebar-icon-inactive) !important;
        transition: all 0.2s ease !important;
    }
    
    .sidebar-heading {
        font-size: 10px !important;
        letter-spacing: 1px !important;
        font-weight: 600 !important;
        color: var(--sidebar-heading-text) !important; /* Warna heading */
        text-transform: uppercase !important;
    }
    
    /* Teks menu dasar */
    .menu-text {
        font-size: var(--menu-font-size) !important;
        font-weight: 500 !important; /* Sedikit lebih tebal dari normal */
        line-height: 1.4 !important; /* Sesuaikan line-height jika perlu */
        color: inherit !important; /* Mewarisi warna dari .nav-link */
    }
    
    /* Hapus referensi ke tenant info */
    
    /* Hapus !important yang tidak perlu pada font-size */
    .menu-uniform span, 
    .menu-uniform a, 
    .menu-uniform button {
        font-size: inherit;
    }
    .menu-uniform .nav-link span,
    .menu-uniform .nav-link i {
         font-size: inherit;
    }

    /* Dropdown styles */
     #tenantSubmenu, #userManagementSubmenu {
         background-color: var(--sidebar-hover-bg); /* Latar submenu */
         border-radius: 0.3rem;
         padding-top: 0.5rem;
         padding-bottom: 0.5rem;
         margin-top: 0.25rem;
     }

     #tenantSubmenu .nav-link,
     #userManagementSubmenu .nav-link {
         padding: 0.4rem 1rem !important; /* Padding submenu item (dikurangi) */
         margin-bottom: 0 !important; /* Menghilangkan margin bottom */
         border-left: 3px solid transparent !important; /* Reset border */
         font-size: calc(var(--menu-font-size) - 1px) !important; /* Ukuran font lebih kecil */
     }
     
     #tenantSubmenu .menu-text,
     #userManagementSubmenu .menu-text {
         font-size: calc(var(--menu-font-size) - 1px) !important; /* Font submenu lebih kecil */
         font-weight: 400 !important; /* Font regular, tidak bold */
         line-height: 1.2 !important; /* Line height lebih kecil */
     }

     #tenantSubmenu .nav-link:hover,
     #userManagementSubmenu .nav-link:hover {
         background-color: rgba(0,0,0,0.05); /* Hover lebih gelap sedikit */
         border-left-color: transparent !important;
     }

     #tenantSubmenu .nav-link.active,
     #userManagementSubmenu .nav-link.active {
         background-color: transparent !important;
         border-left-color: transparent !important;
         color: var(--sidebar-text-active) !important;
         font-weight: 400 !important; /* Regular weight, tidak bold */
     }

     #tenantSubmenu .nav-link.active .menu-text,
     #userManagementSubmenu .nav-link.active .menu-text {
         font-weight: 500 !important; /* Sedikit bold pada active submenu */
     }

     /* Atur warna ikon dropdown chevron */
     .sidebar .nav-link svg[data-feather="chevron-down"] {
         color: var(--sidebar-icon-inactive);
         transition: transform 0.3s ease;
     }
     
     .sidebar .nav-link:hover svg[data-feather="chevron-down"] {
         color: var(--sidebar-icon-active);
     }
     
     .sidebar .nav-link.active svg[data-feather="chevron-down"] {
         color: var(--sidebar-icon-active);
     }
     
     .fa-rotate-180 {
         transform: rotate(180deg);
     }


    /* Hapus style dropdown bootstrap default jika ada */
     .sidebar .dropdown-menu { display: none; } /* Sembunyikan jika tidak pakai collapse */

    /* Bottom Area styles */
    .sidebar .bottom-nav {
        position: fixed;
        bottom: 0;
        width: calc(100% - 2px); /* Menyesuaikan lebar agar pas dengan sidebar */
        max-width: inherit;
        background-color: var(--sidebar-bg);
        border-top: 1px solid #e5e7eb;
        z-index: 10;
        margin-bottom: 30px !important; /* Kurangi margin bawah */
        left: 0; /* Sesuaikan posisi agar pas di sidebar */
        padding-left: 1px; /* Sedikit padding kiri untuk alignment */
        box-sizing: border-box;
        overflow: hidden; /* Mencegah konten keluar */
        width: calc(100% - 2px);
        max-width: 230px; /* Batasi lebar maksimal */
    }

    /* Menu teks di bottom area */
    .sidebar .bottom-nav .nav-link .menu-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }
    
    /* Responsif untuk tampilan mobile */

    /* SIDEBAR WRAPPER FIXED STYLES */
    .sidebar-wrapper {
        position: fixed;
        top: var(--header-height, 60px);
        left: 0;
        width: var(--sidebar-width, 250px);
        height: calc(100vh - var(--header-height, 60px));
        overflow-y: auto;
        background-color: var(--sidebar-bg);
        z-index: 1030;
        box-shadow: 1px 0 8px rgba(0, 0, 0, 0.04);
    }

    @media (max-width: 767.98px) {
        .sidebar-wrapper {
            left: -100%;
            transition: left 0.3s ease-in-out;
        }

        .sidebar-wrapper.show {
            left: 0;
        }
        .sidebar .bottom-nav {
            position: static;
            margin-top: auto !important;
            margin-bottom: 0 !important;
            width: 100%;
            max-width: 100%;
            left: auto;
        }
        .sidebar .bottom-nav .nav-link .menu-text {
            max-width: none;
        }
    }

</style>

<script>
    // Refresh CSS dengan membuat style baru untuk ikon - cukup dipanggil sekali
    function applyIconStyles() {
        // Cek jika style sudah ada
        if (document.getElementById('icon-custom-styles')) return;
        
        const newStyle = document.createElement('style');
        newStyle.id = 'icon-custom-styles';
        newStyle.textContent = `
            /* Style ikon dan transisi yang sudah ada */
            .icon-sidebar svg {
                width: 18px !important;
                height: 18px !important;
                stroke-width: 2 !important;
                color: var(--sidebar-icon-inactive) !important;
                stroke: var(--sidebar-icon-inactive) !important;
                transition: all 0.2s ease !important;
            }
            .sidebar .nav-link.active .icon-sidebar svg {
                color: var(--sidebar-icon-active) !important;
                stroke: var(--sidebar-icon-active) !important;
            }
            .sidebar .nav-link:hover .icon-sidebar svg {
                color: var(--sidebar-icon-active) !important;
                stroke: var(--sidebar-icon-active) !important;
            }
            /* Perbaiki transisi untuk nav-link */
            .sidebar .nav-link {
                transition: color 0.1s, background-color 0.1s, border-left-color 0.1s !important;
                /* Mencegah flickering dengan rendering GPU */
                -webkit-backface-visibility: hidden;
                -moz-backface-visibility: hidden;
                -ms-backface-visibility: hidden;
                backface-visibility: hidden;
                -webkit-transform: translateZ(0);
                -moz-transform: translateZ(0);
                -ms-transform: translateZ(0);
                transform: translateZ(0);
            }
            .sidebar .nav-link button, 
            .sidebar .dropdown-toggle {
                transition: all 0.2s ease !important;
                 /* Mencegah flickering dengan rendering GPU */
                -webkit-backface-visibility: hidden;
                -moz-backface-visibility: hidden;
                -ms-backface-visibility: hidden;
                backface-visibility: hidden;
                -webkit-transform: translateZ(0);
                -moz-transform: translateZ(0);
                -ms-transform: translateZ(0);
                transform: translateZ(0);
            }
            .sidebar .dropdown-toggle svg.feather-chevron-down {
                transition: transform 0.3s ease !important;
            }
        `;
        document.head.appendChild(newStyle);
    }

    // Persiapkan saat DOM sudah dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Terapkan style ikon
        applyIconStyles();
        
        // Mengatur status awal dropdown berdasarkan halaman aktif
        const userManagementSubmenu = document.getElementById('userManagementSubmenu');
        const umDropdownIcon = document.getElementById('um-dropdown-icon');
        const workUnitSubmenu = document.getElementById('workUnitSubmenu');
        const wuDropdownIcon = document.getElementById('wu-dropdown-icon');
        
        // User Management dropdown
        if (userManagementSubmenu && umDropdownIcon && userManagementSubmenu.querySelector('a.active')) {
            userManagementSubmenu.classList.add('show');
            umDropdownIcon.style.transform = 'rotate(180deg)';
        }
        
        // Work Unit dropdown - hanya aktif jika submenu di dalamnya aktif dan bukan SPO
        if (workUnitSubmenu && wuDropdownIcon && workUnitSubmenu.querySelector('a.active') && 
            !window.location.pathname.includes('/work-units/spo')) {
            workUnitSubmenu.classList.add('show');
            wuDropdownIcon.style.transform = 'rotate(180deg)';
        }
    });
    
    // Fungsi toggle yang lebih efisien untuk User Management dropdown
    function toggleUserManagementDropdown() {
        const submenu = document.getElementById('userManagementSubmenu');
        const icon = document.getElementById('um-dropdown-icon');
        if (!submenu || !icon) return;
        
        // Toggle class show dengan metode yang lebih efisien
        const isExpanded = submenu.classList.contains('show');
        
        // Jika Bootstrap tersedia
        if (typeof bootstrap !== 'undefined') {
            const bsCollapse = new bootstrap.Collapse(submenu, { toggle: false });
            bsCollapse.toggle();
        } else {
            // Fallback tanpa Bootstrap
            submenu.classList.toggle('show');
        }
        
        // Animasikan ikon tanpa setTimeout (lebih efisien)
        icon.style.transform = isExpanded ? 'rotate(0)' : 'rotate(180deg)';
    }

    // Fungsi toggle yang lebih efisien untuk Work Unit dropdown
    function toggleWorkUnitDropdown() {
        const submenu = document.getElementById('workUnitSubmenu');
        const icon = document.getElementById('wu-dropdown-icon');
        if (!submenu || !icon) return;
        
        // Toggle class show dengan metode yang lebih efisien
        const isExpanded = submenu.classList.contains('show');
        
        // Jika Bootstrap tersedia
        if (typeof bootstrap !== 'undefined') {
            const bsCollapse = new bootstrap.Collapse(submenu, { toggle: false });
            bsCollapse.toggle();
        } else {
            // Fallback tanpa Bootstrap
            submenu.classList.toggle('show');
        }
        
        // Animasikan ikon tanpa setTimeout (lebih efisien)
        icon.style.transform = isExpanded ? 'rotate(0)' : 'rotate(180deg)';
    }
</script><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>