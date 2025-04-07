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

<aside class="sidebar rounded-end p-0 menu-uniform" x-data="{ activeDropdown: null }">
    <!-- Load Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Sidebar Navigation -->
    <div class="p-3">
        <!-- Dashboard - Always available -->
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i data-feather="home"></i>
                </div>
                <span class="menu-text">Dashboard</span>
            </div>
        </a>

        <!-- Superadmin section -->
        <?php if($isSuperAdmin): ?>
            <div class="sidebar-heading text-uppercase text-white-50 small px-3 mt-4 mb-2">
                Superadmin
            </div>
            
            <a href="<?php echo e(route('superadmin.tenants.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.tenants.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <i data-feather="briefcase"></i>
                    </div>
                    <span class="menu-text">Tenant</span>
                </div>
            </a>
            
            <a href="<?php echo e(route('superadmin.modules.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.modules.*') ? 'active' : ''); ?> mb-2">
                <div class="d-flex align-items-center">
                    <div class="icon-sidebar">
                        <i data-feather="grid"></i>
                    </div>
                    <span class="menu-text">Modul</span>
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
                                    $moduleUrl = url('modules/risk-management/dashboard');
                                } elseif ($module->slug == 'document-management') {
                                    $moduleUrl = url('modules/document-management/dashboard');
                                } elseif ($module->slug == 'work-units') {
                                    $moduleUrl = url('work-units-dashboard'); // URL ke dashboard unit kerja
                                } else {
                                    $moduleUrl = url('modules/' . $module->slug);
                                }
                            } else {
                                $moduleUrl = url('dashboard');
                            }
                            
                            $isActive = request()->is('modules/' . $module->slug . '*');
                            
                            // Sesuaikan $isActive khusus untuk work-units jika perlu
                            if ($module->slug == 'work-units') {
                                $isActive = request()->is('work-units*') || request()->is('work-units-dashboard*');
                            }
                            
                            // Menentukan ikon yang lebih sesuai berdasarkan slug modul
                            $moduleIcon = '<i data-feather="folder"></i>'; // Default icon
                            
                            // Gunakan ikon dari database jika tersedia, jika tidak gunakan ikon berdasarkan jenis modul
                            if (!empty($module->icon_html)) {
                                $moduleIcon = $module->icon_html;
                            } elseif (!empty($module->icon)) { // Tambahkan cek untuk kolom icon
                                $moduleIcon = '<i data-feather="' . $module->icon . '"></i>'; // Atau gunakan class icon jika bukan feather
                            } else {
                                if ($module->slug == 'user-management') {
                                    $moduleIcon = '<i data-feather="users"></i>';
                                } elseif ($module->slug == 'document-management') {
                                    $moduleIcon = '<i data-feather="file-text"></i>';
                                } elseif ($module->slug == 'risk-management') {
                                    $moduleIcon = '<i data-feather="alert-triangle"></i>';
                                } elseif ($module->slug == 'product-management') {
                                    $moduleIcon = '<i data-feather="shopping-bag"></i>';
                                } elseif ($module->slug == 'correspondence' || strpos(strtolower($module->name), 'korespondensi') !== false) {
                                    $moduleIcon = '<i data-feather="mail"></i>';
                                } elseif ($module->slug == 'work-units') {
                                    $moduleIcon = '<i data-feather="archive"></i>'; // Ikon untuk Unit Kerja
                                } elseif (strpos(strtolower($module->name), 'dokumen') !== false) {
                                    $moduleIcon = '<i data-feather="file-text"></i>';
                                } elseif (strpos(strtolower($module->name), 'risiko') !== false) {
                                    $moduleIcon = '<i data-feather="alert-triangle"></i>';
                                } elseif (strpos(strtolower($module->name), 'task') !== false || strpos(strtolower($module->name), 'tugas') !== false) {
                                    $moduleIcon = '<i data-feather="check-square"></i>';
                                } elseif (strpos(strtolower($module->name), 'inventory') !== false || strpos(strtolower($module->name), 'inventaris') !== false) {
                                    $moduleIcon = '<i data-feather="package"></i>';
                                } elseif (strpos(strtolower($module->name), 'finance') !== false || strpos(strtolower($module->name), 'keuangan') !== false) {
                                    $moduleIcon = '<i data-feather="dollar-sign"></i>';
                                } elseif (strpos(strtolower($module->name), 'report') !== false || strpos(strtolower($module->name), 'laporan') !== false) {
                                    $moduleIcon = '<i data-feather="bar-chart-2"></i>';
                                } elseif (strpos(strtolower($module->name), 'dashboard') !== false) {
                                    $moduleIcon = '<i data-feather="pie-chart"></i>';
                                } elseif (strpos(strtolower($module->name), 'setting') !== false || strpos(strtolower($module->name), 'pengaturan') !== false) {
                                    $moduleIcon = '<i data-feather="settings"></i>';
                                } elseif (strpos(strtolower($module->name), 'notif') !== false) {
                                    $moduleIcon = '<i data-feather="bell"></i>';
                                } elseif (strpos(strtolower($module->name), 'chat') !== false || strpos(strtolower($module->name), 'pesan') !== false) {
                                    $moduleIcon = '<i data-feather="message-circle"></i>';
                                } elseif (strpos(strtolower($module->name), 'calendar') !== false || strpos(strtolower($module->name), 'kalender') !== false) {
                                    $moduleIcon = '<i data-feather="calendar"></i>';
                                }
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
                                    <i data-feather="chevron-down" class="ms-auto" id="um-dropdown-icon" style="width: 16px; height: 16px;"></i>
                                </div>
                            </button>
                            <div id="userManagementSubmenu" class="collapse" style="padding-left: 1.5rem; margin-top: 5px;">
                                <a href="<?php echo e(url('modules/user-management/users')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/users*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i data-feather="user"></i>
                                        </div>
                                        <span class="menu-text">Pengguna</span>
                                    </div>
                                </a>
                                <a href="<?php echo e(url('modules/user-management/roles')); ?>" 
                                   class="nav-link <?php echo e(request()->is('*user-management/roles*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i data-feather="shield"></i>
                                        </div>
                                        <span class="menu-text">Role</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php elseif($module->slug == 'work-units'): ?>
                        <!-- Menu Unit Kerja dengan submenu -->
                        <div class="nav-item dropdown mb-2">
                            <button type="button" class="nav-link text-start w-100 <?php echo e($isActive ? 'active' : ''); ?>" 
                                   onclick="toggleWorkUnitDropdown()" 
                                   style="border: none; background: none; cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <div class="icon-sidebar">
                                        <?php echo $moduleIcon; ?>

                                    </div>
                                    <span class="menu-text"><?php echo e($module->name); ?></span>
                                    <i data-feather="chevron-down" class="ms-auto" id="wu-dropdown-icon" style="width: 16px; height: 16px;"></i>
                                </div>
                            </button>
                            <div id="workUnitSubmenu" class="collapse" style="padding-left: 1.5rem; margin-top: 5px;">
                                <a href="<?php echo e(route('work-units.index')); ?>" 
                                   class="nav-link <?php echo e(request()->routeIs('work-units.index') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i data-feather="list"></i>
                                        </div>
                                        <span class="menu-text">Daftar Unit Kerja</span>
                                    </div>
                                </a>
                                
                                <?php
                                try {
                                    $userWorkUnitId = auth()->user()->work_unit_id ?? null;
                                } catch (\Exception $e) {
                                    $userWorkUnitId = null;
                                }
                                ?>
                                
                                <?php if($userWorkUnitId): ?>
                                <a href="<?php echo e(route('work-units.dashboard', $userWorkUnitId)); ?>" 
                                   class="nav-link <?php echo e(request()->is('work-units/*/dashboard') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i data-feather="clipboard"></i>
                                        </div>
                                        <span class="menu-text">Profil Unit Saya</span>
                                    </div>
                                </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo e(route('work-units.spo.index')); ?>" 
                                   class="nav-link <?php echo e(request()->routeIs('work-units.spo.*') ? 'active' : ''); ?> my-1">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-sidebar">
                                            <i data-feather="file-text"></i>
                                        </div>
                                        <span class="menu-text">SPO</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e($moduleUrl); ?>" class="nav-link <?php echo e($isActive ? 'active' : ''); ?> mb-2">
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
    </div>
    
    <!-- Bottom Area -->
    <div class="mt-auto p-3 border-top border-light bottom-nav">
        <a href="<?php echo e(route('tenant.document-references.index')); ?>" class="nav-link <?php echo e(request()->routeIs('tenant.document-references.*') ? 'active' : ''); ?> mb-2">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i data-feather="file-text"></i>
                </div>
                <span class="menu-text text-truncate" style="max-width: 140px;">Daftar Referensi</span>
            </div>
        </a>
        <a href="<?php echo e(route('pages.help')); ?>" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bantuan & Dokumentasi">
            <div class="d-flex align-items-center">
                <div class="icon-sidebar">
                    <i data-feather="help-circle"></i>
                </div>
                <span class="menu-text text-truncate" style="max-width: 140px;">Bantuan</span>
            </div>
        </a>
    </div>
</aside>

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
        min-height: 100%;
        background-color: var(--sidebar-bg) !important; /* Latar belakang putih */
        font-family: Roboto, system-ui, -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif; /* Font bersih */
        border-right: 1px solid #e5e7eb; /* Optional: border pemisah tipis */
        padding-bottom: 90px; /* Tambahkan padding bawah lebih besar untuk menghindari tumpukan dengan menu bottom */
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
        width: 18px;
        height: 18px;
        stroke-width: 2;
        color: var(--sidebar-icon-inactive) !important;
        transition: color 0.2s;
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
    @media (max-width: 767.98px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 260px;
            height: 100vh;
            z-index: 1030;
            transition: all 0.3s ease;
            overflow-y: auto;
            padding-bottom: 0 !important; /* Hapus padding bottom pada mobile */
        }
        
        .sidebar-wrapper.show .sidebar {
            left: 0;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar .bottom-nav {
            position: static; /* Ubah dari fixed ke static pada mobile */
            margin-top: auto !important;
            margin-bottom: 0 !important;
            width: 100%;
            max-width: 100%;
            left: auto;
        }
        
        .sidebar .bottom-nav .nav-link .menu-text {
            max-width: none; /* Hapus batasan max-width pada mobile */
        }
        
        /* Memastikan tampilan scroll pada mobile dan ukuran minimum */
        .sidebar-wrapper {
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            z-index: 1040;
            transition: all 0.3s ease;
        }
        
        .sidebar-wrapper.show {
            left: 0;
        }
    }

</style>

<!-- Tambahkan script untuk toggle menu dropdown -->
<script>
    // Auto-expand submenu jika halaman active ada di dalamnya
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather Icons
        feather.replace();
        
        // User Management dropdown
        if (document.querySelector('#userManagementSubmenu a.active')) {
            document.getElementById('userManagementSubmenu').classList.add('show');
            document.getElementById('um-dropdown-icon').style.transform = 'rotate(180deg)';
        }
        
        // Work Unit dropdown
        if (document.querySelector('#workUnitSubmenu a.active')) {
            document.getElementById('workUnitSubmenu').classList.add('show');
            document.getElementById('wu-dropdown-icon').style.transform = 'rotate(180deg)';
        }
    });
    
    function toggleUserManagementDropdown() {
        const submenu = document.getElementById('userManagementSubmenu');
        const icon = document.getElementById('um-dropdown-icon');
        
        submenu.classList.toggle('show');
        if (submenu.classList.contains('show')) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0)';
        }
    }

    function toggleWorkUnitDropdown() {
        const submenu = document.getElementById('workUnitSubmenu');
        const icon = document.getElementById('wu-dropdown-icon');
        
        submenu.classList.toggle('show');
        if (submenu.classList.contains('show')) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0)';
        }
    }
</script><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>