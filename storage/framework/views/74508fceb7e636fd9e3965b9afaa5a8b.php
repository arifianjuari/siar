<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <!-- Mobile Sidebar Toggle -->
        <?php if(auth()->guard()->check()): ?>
        <button class="sidebar-toggle btn d-md-none me-2">
            <i class="fas fa-bars"></i>
        </button>
        <?php endif; ?>
        
        <!-- Brand - dengan class baru untuk konsistensi mobile -->
        <a class="navbar-brand d-flex align-items-center flex-shrink-0" href="<?php echo e(url('/')); ?>">
            <?php
                $tenantLogo = null;
                $tenantName = 'SIAR';
                $logoExists = false; // Variable baru untuk status logo
                
                try {
                    $tenant = null;
                    if (auth()->check() && auth()->user()->tenant) {
                        $tenant = auth()->user()->tenant;
                    } else {
                        $tenant = getCurrentTenant();
                    }
                    
                    if ($tenant) {
                        $tenantLogo = $tenant->logo;
                        $tenantName = $tenant->name;
                        if ($tenantLogo) {
                            $logoExists = Storage::disk('public')->exists($tenantLogo);
                        }
                    }

                    // Logging ditambahkan di sini
                    Log::info('Navbar Tenant Info:', [
                        'tenant_id' => $tenant ? $tenant->id : null,
                        'tenant_name' => $tenantName,
                        'logo_path' => $tenantLogo,
                        'logo_exists' => $logoExists,
                        'asset_url' => $tenantLogo ? asset('storage/' . $tenantLogo) : null,
                        'app_url' => config('app.url'),
                        'request_host' => request()->getHost()
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error getting tenant info in navbar', ['exception' => $e]);
                }
            ?>
            
            <div class="tenant-branding d-flex align-items-center">
                <?php if($tenantLogo && $logoExists): ?>
                    <img src="<?php echo e(asset('storage/' . $tenantLogo)); ?>?v=<?php echo e(isset($tenant) && $tenant->updated_at ? $tenant->updated_at->timestamp : time()); ?>" 
                         alt="<?php echo e($tenantName); ?>" 
                         class="tenant-logo d-inline-block align-top me-2" 
                         style="height: 36px; width: auto;" 
                         onerror="this.onerror=null; this.src='<?php echo e(asset('storage/' . $tenantLogo)); ?>?v=' + new Date().getTime();">
                    
                    <script>
                        // Buat variable global untuk logo timestamp
                        window.logoTimestamp = <?php echo e(isset($tenant) && $tenant->updated_at ? $tenant->updated_at->timestamp : time()); ?>;
                        
                        // Fungsi untuk refresh logo dengan parameter baru
                        function refreshNavbarLogo(retryCount) {
                            retryCount = retryCount || 0;
                            var maxRetries = 3;
                            
                            // Ambil elemen logo
                            var navLogo = document.querySelector('.tenant-logo');
                            if (!navLogo) return;
                            
                            // Simpan URL original (tanpa query params)
                            var originalSrc = navLogo.src.split('?')[0];
                            
                            // Prefetch logo dengan cache busting
                            fetch(originalSrc + '?force_refresh=1&t=' + (new Date().getTime()), {
                                method: 'GET',
                                cache: 'no-store',
                                headers: {
                                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                                    'Pragma': 'no-cache',
                                    'Expires': '0'
                                }
                            }).then(function(response) {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.blob();
                            }).then(function(blob) {
                                // Buat object URL dari blob dan update logo
                                var objectURL = URL.createObjectURL(blob);
                                navLogo.src = objectURL;
                                console.log('Logo navbar berhasil diperbarui dengan object URL');
                            }).catch(function(err) {
                                console.error('Error memuat logo navbar:', err);
                                
                                // Retry jika masih di bawah batas percobaan
                                if (retryCount < maxRetries) {
                                    setTimeout(function() {
                                        refreshNavbarLogo(retryCount + 1);
                                    }, 500); // Tunggu 500ms sebelum retry
                                } else {
                                    // Fallback ke cara lama jika gagal
                                    navLogo.src = originalSrc + '?v=' + window.logoTimestamp;
                                }
                            });
                        }
                        
                        // Execute immediate untuk prefetch logo
                        (function() {
                            // Prefetch logo saat script dijalankan
                            var logoUrl = '<?php echo e(asset('storage/' . $tenantLogo)); ?>?force_refresh=1&t=<?php echo e(time()); ?>';
                            
                            // Buat link prefetch
                            var link = document.createElement('link');
                            link.rel = 'prefetch';
                            link.href = logoUrl;
                            document.head.appendChild(link);
                            
                            // Update logo saat DOM sudah ready
                            if (document.readyState === 'loading') {
                                document.addEventListener('DOMContentLoaded', refreshNavbarLogo);
                            } else {
                                refreshNavbarLogo();
                            }
                            
                            // Juga update saat window selesai loading
                            window.addEventListener('load', refreshNavbarLogo);
                        })();
                        
                        // Registrasi service worker untuk caching logo
                        if ('serviceWorker' in navigator) {
                            navigator.serviceWorker.register('/logoCache.js')
                                .then(registration => {
                                    console.log('Logo Service Worker terdaftar dengan scope:', registration.scope);
                                })
                                .catch(error => {
                                    console.error('Registrasi Logo Service Worker gagal:', error);
                                });
                        }
                    </script>
                <?php else: ?>
                    <?php
                        if ($tenantLogo && !$logoExists) {
                            Log::warning('Navbar: Logo path exists but file not found in public storage.', ['logo_path' => $tenantLogo]);
                        }
                    ?>
                    <div class="tenant-icon d-inline-block align-top d-flex align-items-center justify-content-center rounded-circle bg-primary me-2" style="height: 36px; width: 36px; color: white;">
                        <i class="fas fa-hospital-alt"></i>
                    </div>
                <?php endif; ?>
                <span class="tenant-name fw-semibold d-none d-md-inline-block align-top" style="color: #000000; line-height: 36px;"><?php echo e($tenantName); ?></span>
            </div>
        </a>
        
        <?php if(auth()->guard()->check()): ?>
        <!-- User Controls - Outside Navbar Collapse -->
        <div class="user-controls ms-auto d-flex align-items-center">
            <!-- Install PWA Button -->
            <button id="installPWA" onclick="installPWA()" class="btn btn-primary btn-sm me-3 d-none" 
                style="animation: pulse 2s infinite;">
                <i class="fas fa-download me-1"></i> Install App
            </button>
            
            <?php
                $roleName = 'User';
                try {
                    if (auth()->check() && auth()->user()->role) {
                        $roleName = auth()->user()->role->name;
                    }
                } catch (\Exception $e) {
                    // Abaikan error
                }
                
                $isTenantAdmin = false;
                try {
                    if (auth()->check() && auth()->user()->role && auth()->user()->role->slug === 'tenant-admin') {
                        $isTenantAdmin = true;
                    }
                } catch (\Exception $e) {
                    // Abaikan error
                }
            ?>
            
            <!-- Pengaturan Dropdown (hanya untuk tenant admin) -->
            <?php if($isTenantAdmin): ?>
            <div class="nav-item dropdown me-2 d-flex align-items-center position-relative">
                <a id="settingsDropdown" class="nav-link d-flex align-items-center p-0" href="#" role="button" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="settings-avatar d-flex align-items-center justify-content-center text-white rounded-circle" style="width: 40px; height: 40px; font-size: 1rem; background-color: #D97706;">
                        <i class="fas fa-cog"></i>
                    </div>
                </a>

                <ul class="dropdown-menu shadow border-0" aria-labelledby="settingsDropdown" style="min-width: 240px; z-index: 1030; margin-top: 10px; left: 0;">
                    <li><a class="dropdown-item py-2" href="<?php echo e(url('tenant/profile')); ?>">
                        <i class="fas fa-id-card me-2 text-muted"></i> Profil RS
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(url('tenant/settings')); ?>">
                        <i class="fas fa-cog me-2 text-muted"></i> Umum
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('tenant.work-units.index')); ?>">
                        <i class="fas fa-sitemap me-2 text-muted"></i> Unit Kerja
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('tenant.tags.index')); ?>">
                        <i class="fas fa-tags me-2 text-muted"></i> Tag
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('tenant.document-references.index')); ?>">
                        <i class="fas fa-file-alt me-2 text-muted"></i> Referensi
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('modules.index')); ?>">
                        <i class="fas fa-cubes me-2 text-muted"></i> Daftar Modul
                    </a></li>
                </ul>
            </div>
            <?php endif; ?>

            <!-- User Dropdown -->
            <div class="nav-item dropdown d-flex align-items-center position-relative">
                <a id="navbarDropdown" class="nav-link d-flex align-items-center p-0" href="#" role="button" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar d-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-2" style="width: 40px; height: 40px; font-size: 1rem;">
                        <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-semibold"><?php echo e(Auth::user()->name); ?></div>
                        <div class="small text-muted"><?php echo e($roleName); ?></div>
                    </div>
                    <i class="fas fa-chevron-down ms-2 small"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown" style="min-width: 240px; z-index: 1030; margin-top: 10px;">
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('profile.edit')); ?>">
                        <i class="fas fa-user me-2 text-muted"></i> Edit Profil
                    </a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <li><a class="dropdown-item py-2 text-danger" href="<?php echo e(route('logout')); ?>"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a></li>

                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Toggle Button - REMOVED -->
        
        <!-- REMOVED navbar-collapse -->
        <div class="d-none d-md-block ms-md-auto">
            <!-- Left Side Of Navbar - Search Form REMOVED -->
            
            <!-- Right Side Of Navbar - Guest Only -->
            <?php if(auth()->guard()->guest()): ?>
            <div class="ms-auto me-3">
                <ul class="navbar-nav d-flex flex-row">
                    <?php if(Route::has('login')): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if(Route::has('register')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('register')); ?>"><?php echo e(__('Register')); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav> 
<div style="height: var(--header-height, 60px);"></div>



<style>
    /* Dropdown menu positioning */
    #settingsDropdownMenu, #userDropdownMenu {
        top: 100%;
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Add a small arrow to the top of the dropdown menu */
    #settingsDropdownMenu::before, #userDropdownMenu::before {
        content: '';
        position: absolute;
        top: -8px;
        width: 16px;
        height: 16px;
        background-color: white;
        transform: rotate(45deg);
        border-top: 1px solid rgba(0,0,0,0.05);
        border-left: 1px solid rgba(0,0,0,0.05);
        z-index: -1;
    }
    
    #settingsDropdownMenu::before {
        left: 12px;
    }
    
    #userDropdownMenu::before {
        right: 12px;
    }
    
    /* Make dropdown items more compact */
    .dropdown-item {
        font-size: 0.9rem;
    }
    
    /* User controls positioning */
    .user-controls {
        position: relative;
        z-index: 1020;
    }
    
    /* Mobile specific styles */
    @media (max-width: 767.98px) {
        .navbar-nav .d-flex {
            flex-direction: row !important;
            justify-content: flex-end !important;
            width: auto !important;
        }
        
        .mobile-dropdown {
            position: fixed !important;
            top: auto !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            max-height: 80vh;
            overflow-y: auto;
            margin: 0 !important;
            border-radius: 0 !important;
            z-index: 1050 !important;
            transform: none !important;
            bottom: 0;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
        
        /* Hide the arrow in mobile view */
        #settingsDropdownMenu::before,
        #userDropdownMenu::before {
            display: none;
        }
        
        /* Add a handle/indicator at top of mobile dropdown */
        .mobile-dropdown::after {
            content: '';
            display: block;
            width: 36px;
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            margin: 8px auto;
        }
        
        /* Bigger touch targets for mobile */
        .mobile-dropdown .dropdown-item {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
    }
    
    /* Ensure dropdown position in expanded navbar */
    @media (min-width: 768px) {
        .navbar-expand-md .navbar-nav .dropdown-menu {
            position: absolute;
        }
        
        /* Position user controls on desktop */
        .user-controls {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
    }
</style> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/navbar.blade.php ENDPATH**/ ?>