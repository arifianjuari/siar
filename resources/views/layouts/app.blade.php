<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="app" class="wrapper">
        @include('layouts.partials.navbar')

        <div class="container-fluid px-0">
            <div class="row g-0">
                @auth
                    <!-- Sidebar (only for authenticated users) -->
                    <div class="col-12 col-md-3 col-lg-2 bg-dark sidebar-wrapper">
                        @include('layouts.partials.sidebar')
                    </div>
                    
                    <!-- Main Content -->
                    <div class="col-12 col-md-9 col-lg-10 content-wrapper ms-auto" x-data="{ userHasInteracted: false }">
                        <main class="p-4">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-check-circle me-2"></i></strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-circle me-2"></i></strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i></strong> Terdapat kesalahan dalam input:
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <!-- Page Header -->
                            @hasSection('header')
                                <div class="mb-4 pb-3 border-bottom">
                                    @yield('header')
                                </div>
                            @elseif(!isset($hideDefaultHeader) && !Route::is('*.risk-analysis.*') && !Route::is('modules.risk-management.dashboard'))
                                <div class="mb-4 pb-3 border-bottom">
                                    <h1 class="h3 mb-0">@yield('title', 'Dashboard')</h1>
                                </div>
                            @endif
                            
                            <!-- Page Content -->
                            @yield('content')
                        </main>
                    </div>
                @else
                    <!-- Full Width Content for guests -->
                    <div class="col-12">
                        <main class="py-4">
                            <div class="container">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong><i class="fas fa-check-circle me-2"></i></strong> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong><i class="fas fa-exclamation-circle me-2"></i></strong> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                @yield('content')
                            </div>
                        </main>
                    </div>
                @endauth
            </div>
        </div>
        
        @auth
            @include('layouts.partials.footer')
        @else
            @include('layouts.partials.footer')
        @endauth
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
    @stack('scripts')
</body>
</html> 