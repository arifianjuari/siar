<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <!-- Mobile Sidebar Toggle -->
        <button id="sidebarToggle" class="btn d-md-none me-2">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'SIAR') }}
        </a>
        
        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <!-- Search Form -->
                <li class="nav-item d-none d-md-block">
                    <form class="position-relative ms-4" x-data="{searchOpen: false}" 
                        @click.outside="searchOpen = false">
                        <div class="input-group">
                            <input type="search" class="form-control border-end-0 bg-light" 
                                placeholder="Cari..." aria-label="Cari"
                                @focus="searchOpen = true">
                            <span class="input-group-text bg-light border-start-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        
                        <!-- Search Results Dropdown (for demo) -->
                        <div class="position-absolute top-100 start-0 mt-1 w-100 shadow bg-white rounded-3 p-3" 
                            x-show="searchOpen" style="display: none; z-index: 1000;">
                            <div class="mb-2 border-bottom pb-2">
                                <small class="text-muted">Hasil Pencarian Terbaru</small>
                            </div>
                            <a href="#" class="d-flex align-items-center py-2 text-decoration-none text-dark">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                <span>Dashboard Analytics</span>
                            </a>
                            <a href="#" class="d-flex align-items-center py-2 text-decoration-none text-dark">
                                <i class="fas fa-users me-2 text-success"></i>
                                <span>Manajemen Pengguna</span>
                            </a>
                        </div>
                    </form>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    @php
                        $roleName = 'User';
                        try {
                            if (auth()->check() && auth()->user()->role) {
                                $roleName = auth()->user()->role->name;
                            }
                        } catch (\Exception $e) {
                            // Abaikan error
                        }
                    @endphp

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link d-flex align-items-center" href="#" role="button" 
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

                        <div id="userDropdownMenu" class="position-absolute dropdown-menu dropdown-menu-end shadow border-0" style="display: none; min-width: 240px; right: 0;">
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
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav> 