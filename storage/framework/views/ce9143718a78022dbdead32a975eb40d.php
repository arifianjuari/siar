<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <?php echo $__env->make('layouts.partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="app" class="wrapper">
        <?php echo $__env->make('layouts.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="container-fluid px-0">
            <div class="row g-0">
                <?php if(auth()->guard()->check()): ?>
                    <!-- Sidebar (only for authenticated users) -->
                    <div class="col-12 col-md-3 col-lg-2 bg-dark sidebar-wrapper">
                        <?php echo $__env->make('layouts.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="col-12 col-md-9 col-lg-10 content-wrapper ms-auto" x-data="{ userHasInteracted: false }">
                        <main class="p-4">
                            <?php if(session('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-check-circle me-2"></i></strong> <?php echo e(session('success')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(session('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-circle me-2"></i></strong> <?php echo e(session('error')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i></strong> Terdapat kesalahan dalam input:
                                    <ul class="mb-0 mt-2">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Page Header -->
                            <?php if (! empty(trim($__env->yieldContent('header')))): ?>
                                <div class="mb-4 pb-3 border-bottom">
                                    <?php echo $__env->yieldContent('header'); ?>
                                </div>
                            <?php else: ?>
                                <div class="mb-4 pb-3 border-bottom">
                                    <h1 class="h3 mb-0"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Page Content -->
                            <?php echo $__env->yieldContent('content'); ?>
                        </main>
                    </div>
                <?php else: ?>
                    <!-- Full Width Content for guests -->
                    <div class="col-12">
                        <main class="py-4">
                            <div class="container">
                                <?php if(session('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong><i class="fas fa-check-circle me-2"></i></strong> <?php echo e(session('success')); ?>

                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(session('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-2"></i></strong> <?php echo e(session('error')); ?>

                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php echo $__env->yieldContent('content'); ?>
                            </div>
                        </main>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if(auth()->guard()->check()): ?>
            <?php echo $__env->make('layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi toggle menu dropdown user
        function toggleUserDropdown(event) {
            event.preventDefault();
            var dropdownMenu = document.getElementById('userDropdownMenu');
            var isDisplayed = dropdownMenu.style.display === 'block';
            
            // Tutup dropdown jika sudah terbuka, buka jika belum
            dropdownMenu.style.display = isDisplayed ? 'none' : 'block';
            
            // Klik di luar dropdown akan menutup dropdown
            if (!isDisplayed) {
                document.addEventListener('click', closeDropdownOutside);
            }
        }
        
        // Fungsi untuk menutup dropdown jika klik di luar area dropdown
        function closeDropdownOutside(event) {
            var dropdown = document.getElementById('navbarDropdown');
            var dropdownMenu = document.getElementById('userDropdownMenu');
            
            // Periksa jika klik di luar dropdown
            if (!dropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
                document.removeEventListener('click', closeDropdownOutside);
            }
        }
        
        // Inisialisasi sidebar toggle untuk tampilan mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                    sidebarWrapper.classList.toggle('show');
                    
                    // Add overlay when sidebar is shown
                    if (sidebarWrapper.classList.contains('show')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'sidebar-overlay position-fixed top-0 left-0 w-100 h-100 bg-dark bg-opacity-50';
                        overlay.style.zIndex = '1019';
                        document.body.appendChild(overlay);
                        
                        overlay.addEventListener('click', function() {
                            sidebarWrapper.classList.remove('show');
                            this.remove();
                        });
                    } else {
                        const overlay = document.querySelector('.sidebar-overlay');
                        if (overlay) overlay.remove();
                    }
                });
            }
            
            // Toggle dropdown untuk menu di sidebar
            window.toggleTenantDropdown = function() {
                const menu = document.getElementById('tenantSubmenu');
                const icon = document.getElementById('tenant-dropdown-icon');
                
                if (menu) {
                    menu.classList.toggle('collapse');
                    if (icon) {
                        icon.classList.toggle('fa-chevron-up');
                        icon.classList.toggle('fa-chevron-down');
                    }
                }
            };
            
            window.toggleUserManagementDropdown = function() {
                const menu = document.getElementById('userManagementSubmenu');
                const icon = document.getElementById('um-dropdown-icon');
                
                if (menu) {
                    menu.classList.toggle('collapse');
                    if (icon) {
                        icon.classList.toggle('fa-chevron-up');
                        icon.classList.toggle('fa-chevron-down');
                    }
                }
            };
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/app.blade.php ENDPATH**/ ?>