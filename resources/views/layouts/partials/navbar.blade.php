<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <!-- Mobile Sidebar Toggle -->
        <button id="sidebarToggle" class="btn d-md-none me-2">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Brand - dengan class baru untuk konsistensi mobile -->
        <a class="navbar-brand d-flex align-items-center flex-shrink-0" href="{{ url('/') }}">
            @php
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
            @endphp
            
            <div class="tenant-branding d-flex align-items-center">
                @if ($tenantLogo && $logoExists)
                    <img src="{{ asset('storage/' . $tenantLogo) }}" alt="{{ $tenantName }}" class="tenant-logo d-inline-block align-top me-2" style="height: 36px; width: auto;">
                @else
                    @php
                        if ($tenantLogo && !$logoExists) {
                            Log::warning('Navbar: Logo path exists but file not found in public storage.', ['logo_path' => $tenantLogo]);
                        }
                    @endphp
                    <div class="tenant-icon d-inline-block align-top d-flex align-items-center justify-content-center rounded-circle bg-primary me-2" style="height: 36px; width: 36px; color: white;">
                        <i class="fas fa-hospital-alt"></i>
                    </div>
                @endif
                <span class="tenant-name fw-semibold d-none d-md-inline-block align-top" style="color: #4F46E5; line-height: 36px;">{{ $tenantName }}</span>
            </div>
        </a>
        
        @auth
        <!-- User Controls - Outside Navbar Collapse -->
        <div class="user-controls ms-auto d-flex align-items-center">
            <!-- Install PWA Button -->
            <button id="installPWA" onclick="installPWA()" class="btn btn-primary btn-sm me-3 d-none" 
                style="animation: pulse 2s infinite;">
                <i class="fas fa-download me-1"></i> Install App
            </button>
            
            @php
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
            @endphp
            
            <!-- Pengaturan Dropdown (hanya untuk tenant admin) -->
            @if($isTenantAdmin)
            <div class="nav-item dropdown me-2 d-flex align-items-center position-relative">
                <a id="settingsDropdown" class="nav-link d-flex align-items-center p-0" href="#" role="button" 
                    onclick="toggleSettingsDropdown(event)">
                    <div class="settings-avatar d-flex align-items-center justify-content-center text-white rounded-circle" style="width: 40px; height: 40px; font-size: 1rem; background-color: #D97706;">
                        <i class="fas fa-cog"></i>
                    </div>
                </a>

                <div id="settingsDropdownMenu" class="position-absolute dropdown-menu shadow border-0" style="display: none; min-width: 240px; z-index: 1030; margin-top: 10px; left: 0;">
                    <a class="dropdown-item py-2" href="{{ url('tenant/profile') }}">
                        <i class="fas fa-id-card me-2 text-muted"></i> Profil RS
                    </a>
                    <a class="dropdown-item py-2" href="{{ url('tenant/settings') }}">
                        <i class="fas fa-cog me-2 text-muted"></i> Umum
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('tenant.work-units.index') }}">
                        <i class="fas fa-sitemap me-2 text-muted"></i> Unit Kerja
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('tenant.tags.index') }}">
                        <i class="fas fa-tags me-2 text-muted"></i> Tag
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('tenant.document-references.index') }}">
                        <i class="fas fa-file-alt me-2 text-muted"></i> Referensi
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('modules.index') }}">
                        <i class="fas fa-cubes me-2 text-muted"></i> Daftar Modul
                    </a>
                </div>
            </div>
            @endif

            <!-- User Dropdown -->
            <div class="nav-item dropdown d-flex align-items-center position-relative">
                <a id="navbarDropdown" class="nav-link d-flex align-items-center p-0" href="#" role="button" 
                    onclick="toggleUserDropdown(event)">
                    <div class="avatar d-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-2" style="width: 40px; height: 40px; font-size: 1rem;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <div class="small text-muted">{{ $roleName }}</div>
                    </div>
                    <i class="fas fa-chevron-down ms-2 small"></i>
                </a>

                <div id="userDropdownMenu" class="position-absolute dropdown-menu dropdown-menu-end shadow border-0" style="display: none; min-width: 240px; right: 0; z-index: 1030; margin-top: 10px;">
                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user me-2 text-muted"></i> Edit Profil
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        @endauth
        
        <!-- Toggle Button - REMOVED -->
        
        <!-- REMOVED navbar-collapse -->
        <div class="d-none d-md-block ms-md-auto">
            <!-- Left Side Of Navbar - Search Form REMOVED -->
            
            <!-- Right Side Of Navbar - Guest Only -->
            @guest
            <div class="ms-auto me-3">
                <ul class="navbar-nav d-flex flex-row">
                    @if (Route::has('login'))
                        <li class="nav-item me-3">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
            @endguest
        </div>
    </div>
</nav> 

<script>
    function toggleUserDropdown(event) {
        event.preventDefault();
        const dropdownMenu = document.getElementById('userDropdownMenu');
        const settingsDropdown = document.getElementById('settingsDropdownMenu');
        
        // Tutup dropdown pengaturan jika terbuka
        if (settingsDropdown && settingsDropdown.style.display === 'block') {
            settingsDropdown.style.display = 'none';
        }
        
        // Toggle dropdown user
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    }
    
    function toggleSettingsDropdown(event) {
        event.preventDefault();
        const dropdownMenu = document.getElementById('settingsDropdownMenu');
        const userDropdown = document.getElementById('userDropdownMenu');
        
        // Tutup dropdown user jika terbuka
        if (userDropdown && userDropdown.style.display === 'block') {
            userDropdown.style.display = 'none';
        }
        
        // Toggle dropdown pengaturan
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    }
    
    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(event) {
        const userDropdown = document.getElementById('userDropdownMenu');
        const settingsDropdown = document.getElementById('settingsDropdownMenu');
        const userButton = document.getElementById('navbarDropdown');
        const settingsButton = document.getElementById('settingsDropdown');
        
        // Jika terbuka dan klik bukan di dropdown atau buttonnya
        if (userDropdown && userDropdown.style.display === 'block' &&
            !userDropdown.contains(event.target) && !userButton.contains(event.target)) {
            userDropdown.style.display = 'none';
        }
        
        if (settingsDropdown && settingsDropdown.style.display === 'block' &&
            !settingsDropdown.contains(event.target) && !settingsButton.contains(event.target)) {
            settingsDropdown.style.display = 'none';
        }
    });
    
    // Menangani perubahan ukuran layar
    window.addEventListener('resize', function() {
        const userDropdown = document.getElementById('userDropdownMenu');
        const settingsDropdown = document.getElementById('settingsDropdownMenu');
        
        // Reset posisi saat ukuran layar berubah
        if (userDropdown) userDropdown.style.display = 'none';
        if (settingsDropdown) settingsDropdown.style.display = 'none';
    });
</script> 

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
</style> 