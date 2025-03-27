<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAR') }} | Superadmin @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- Styles -->
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338CA;
            --secondary-color: #10B981;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #3B82F6;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            transition: all 0.3s;
            overflow-y: auto;
        }
        
        .sidebar-logo {
            padding: 1.5rem 1rem;
            color: white;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link {
            padding: 0.8rem 1.25rem;
            color: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-radius: 0;
            margin: 2px 0.5rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 5px;
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            border-radius: 5px;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 1rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
            width: calc(100% - 250px);
        }
        
        /* For mobile view */
        @media (max-width: 991px) {
            .sidebar {
                left: -250px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .content-wrapper {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar-toggler {
                display: block !important;
            }
        }
        
        /* Table responsive */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        
        /* Card styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.5rem;
        }
        
        .btn-outline-primary, .btn-outline-success, .btn-outline-warning, .btn-outline-info {
            border-width: 2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover, .btn-outline-success:hover, .btn-outline-warning:hover, .btn-outline-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            vertical-align: middle;
        }
        
        .table th {
            font-weight: 600;
            border-bottom-width: 1px;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.4rem 0.6rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-color);
        }
        
        /* Custom badge styles */
        .badge.bg-superadmin {
            background-color: var(--danger-color);
            color: white;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Fluid container for all views */
        .container-fluid {
            padding-left: 25px;
            padding-right: 25px;
            width: 100%;
        }
        
        /* Make all tables responsive */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid">
            <button class="btn sidebar-toggler d-lg-none me-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="{{ route('superadmin.dashboard') }}">
                <span class="text-primary me-1">{{ config('app.name', 'SIAR') }}</span> 
                <span class="badge bg-superadmin ms-2">Superadmin</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center">
                            <i class="fas fa-home me-1"></i> Kembali ke Aplikasi
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> Profil Saya
                            </a>
                            
                            <div class="dropdown-divider"></div>
                            
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="py-4">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}" href="{{ route('superadmin.tenants.index') }}">
                            <i class="fas fa-building"></i> Tenant
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.modules.*') ? 'active' : '' }}" href="{{ route('superadmin.modules.index') }}">
                            <i class="fas fa-cube"></i> Modul
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}" href="{{ route('superadmin.users.index') }}">
                            <i class="fas fa-users-cog"></i> Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.statistics') ? 'active' : '' }}" href="{{ route('superadmin.statistics') }}">
                            <i class="fas fa-chart-bar"></i> Statistik
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'SIAR') }}</p>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-wrapper">
            <main class="py-4 mt-5">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    
                    // Jika sidebar ditampilkan, sesuaikan margin dan width content
                    if (sidebar.classList.contains('show') && window.innerWidth < 992) {
                        contentWrapper.style.marginLeft = '250px';
                        contentWrapper.style.width = 'calc(100% - 250px)';
                    } else if (window.innerWidth < 992) {
                        contentWrapper.style.marginLeft = '0';
                        contentWrapper.style.width = '100%';
                    }
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isMobile = window.innerWidth < 992;
                if (isMobile && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    contentWrapper.style.marginLeft = '0';
                    contentWrapper.style.width = '100%';
                }
            });
            
            // Responsive handling when window is resized
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    contentWrapper.style.marginLeft = '250px';
                    contentWrapper.style.width = 'calc(100% - 250px)';
                    sidebar.classList.remove('show');
                } else {
                    if (!sidebar.classList.contains('show')) {
                        contentWrapper.style.marginLeft = '0';
                        contentWrapper.style.width = '100%';
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html> 