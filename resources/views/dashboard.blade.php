@extends('layouts.app')

@section('title', ' | Dashboard')

@push('styles')
<style>
    .dashboard-stat-card {
        transition: all 0.3s;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .dashboard-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
</style>
@endpush

@php
    // Helper functions for functionality support
    if (!function_exists('getCurrentTenant')) {
        function getCurrentTenant() {
            try {
                $tenant_id = session('tenant_id');
                if ($tenant_id) {
                    return \App\Models\Tenant::find($tenant_id);
                }
            } catch (\Exception $e) {
                // Do nothing
            }
            return null;
        }
    }

    if (!function_exists('hasModulePermission')) {
        function hasModulePermission($moduleCode, $permission = 'can_view') {
            return auth()->user()->hasPermission($moduleCode, $permission);
        }
    }
    
    $userRole = auth()->user()->role->slug ?? '';
    $isSuperAdmin = $userRole === 'superadmin';
    $isTenantAdmin = $userRole === 'tenant-admin';
@endphp

@section('header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="mb-0 fw-bold">Dashboard</h2>
            <p class="text-muted mb-0">Selamat datang kembali, {{ auth()->user()->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar me-1"></i> Periode: 30 Hari Terakhir
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-day me-2"></i> Hari Ini</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-week me-2"></i> Minggu Ini</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt me-2"></i> Bulan Ini</a></li>
                    <li><a class="dropdown-item active" href="#"><i class="fas fa-calendar me-2"></i> 30 Hari Terakhir</a></li>
                </ul>
            </div>
            
            <button class="btn btn-primary">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>
@endsection

@section('content')
    @if(app()->environment('local'))
    <!-- Info Autentikasi - hanya ditampilkan di environment local -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Info Autentikasi</h5>
                <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#authInfoCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="card-body collapse show" id="authInfoCollapse">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-2"><strong><i class="fas fa-user me-2"></i> User:</strong></p>
                    <p class="ps-4 mb-0">{{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-2"><strong><i class="fas fa-user-tag me-2"></i> Role:</strong></p>
                    <p class="ps-4 mb-0">{{ auth()->user()->role->name ?? 'Tidak ada role' }} ({{ auth()->user()->role->slug ?? '-' }})</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-2"><strong><i class="fas fa-building me-2"></i> Tenant:</strong></p>
                    <p class="ps-4 mb-0">{{ auth()->user()->tenant->name ?? 'Tidak ada tenant' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($isSuperAdmin)
        <!-- Superadmin Dashboard -->
        <div class="row">
            @if(hasModulePermission('tenant_management'))
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small fw-semibold">Total Tenant</h6>
                                @php
                                    $totalTenants = \App\Models\Tenant::count();
                                    $newTenants = \App\Models\Tenant::where('created_at', '>=', now()->subDays(30))->count();
                                @endphp
                                <h3 class="mb-0 fw-bold">{{ $totalTenants }}</h3>
                                <p class="mb-0 small {{ $newTenants > 0 ? 'text-success' : 'text-muted' }}">
                                    @if($newTenants > 0)
                                        <i class="fas fa-arrow-up me-1"></i> {{ $newTenants }} baru
                                    @else
                                        <i class="fas fa-minus me-1"></i> Tidak ada tenant baru
                                    @endif
                                </p>
                            </div>
                            <div class="stat-icon bg-primary text-white">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('superadmin.tenants.index') }}" class="text-decoration-none stretched-link">
                            Kelola Tenant <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @if(hasModulePermission('module_management'))
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small fw-semibold">Total Modul</h6>
                                @php
                                    $totalModules = \App\Models\Module::count();
                                    $newModules = \App\Models\Module::where('created_at', '>=', now()->subDays(30))->count();
                                @endphp
                                <h3 class="mb-0 fw-bold">{{ $totalModules }}</h3>
                                <p class="mb-0 small {{ $newModules > 0 ? 'text-success' : 'text-muted' }}">
                                    @if($newModules > 0)
                                        <i class="fas fa-arrow-up me-1"></i> {{ $newModules }} baru
                                    @else
                                        <i class="fas fa-cube me-1"></i> Tersedia
                                    @endif
                                </p>
                            </div>
                            <div class="stat-icon bg-success text-white">
                                <i class="fas fa-cube"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('superadmin.modules.index') }}" class="text-decoration-none stretched-link">
                            Kelola Modul <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @if(hasModulePermission('tenant_management'))
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small fw-semibold">Tenant Aktif</h6>
                                @php
                                    $activeTenants = \App\Models\Tenant::where('is_active', true)->count();
                                    $percentage = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100) : 0;
                                @endphp
                                <h3 class="mb-0 fw-bold">{{ $activeTenants }}</h3>
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-percentage me-1"></i> {{ $percentage }}% aktif
                                </p>
                            </div>
                            <div class="stat-icon bg-info text-white">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <span class="text-decoration-none">
                            Status <i class="fas fa-info-circle ms-1"></i>
                        </span>
                    </div>
                </div>
            </div>
            @endif

            @if(hasModulePermission('user_management'))
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase small fw-semibold">Total Admin</h6>
                                @php
                                    $tenantAdmins = \App\Models\User::whereHas('role', function($q) {
                                        $q->where('slug', 'tenant-admin');
                                    })->count();
                                @endphp
                                <h3 class="mb-0 fw-bold">{{ $tenantAdmins }}</h3>
                                <p class="mb-0 small text-muted"><i class="fas fa-user-shield me-1"></i> Tenant Admins</p>
                            </div>
                            <div class="stat-icon bg-warning text-white">
                                <i class="fas fa-user-shield"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <span class="text-decoration-none">
                            Tenant Admins <i class="fas fa-users-cog ms-1"></i>
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Charts & Visualisasi Data -->
        <div class="row mt-2">
            @if(hasModulePermission('tenant_management'))
            <!-- Chart Tenant Growth -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pertumbuhan Tenant</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary active">Bulanan</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">Tahunan</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $hasData = \App\Models\Tenant::count() > 0;
                        @endphp
                        @if($hasData)
                            <canvas id="tenantGrowthChart" height="300"></canvas>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-chart-line fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">Belum ada data pertumbuhan tenant</h6>
                                <p class="small text-muted mb-0">Data akan muncul saat tenant mulai terdaftar dalam sistem</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            @if(hasModulePermission('module_management'))
            <!-- Chart Module Popularity -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">Popularitas Modul</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $hasModuleData = \App\Models\TenantModule::count() > 0;
                        @endphp
                        @if($hasModuleData)
                            <canvas id="modulePopularityChart" height="300"></canvas>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-chart-pie fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">Belum ada data popularitas modul</h6>
                                <p class="small text-muted mb-0">Data akan muncul saat modul diaktifkan oleh tenant</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Superadmin Activity & Stats -->
        <div class="row">
            @if(hasModulePermission('tenant_management'))
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tenant Terbaru</h5>
                            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $recentTenants = \App\Models\Tenant::orderBy('created_at', 'desc')->take(5)->get();
                        @endphp
                        
                        @if($recentTenants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Nama</th>
                                            <th class="border-0">Domain</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Dibuat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTenants as $tenant)
                                        <tr>
                                            <td>{{ $tenant->name }}</td>
                                            <td>{{ $tenant->domain }}</td>
                                            <td>
                                                @if($tenant->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>{{ $tenant->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-building fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">Belum ada tenant dalam sistem</h6>
                                <p class="small text-muted mb-0">Tenant yang Anda buat akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            @if(hasModulePermission('module_management'))
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">Modul Populer</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach(\App\Models\Module::withCount(['tenants' => function($q) { $q->where('tenant_modules.is_active', true); }])->orderBy('tenants_count', 'desc')->take(5)->get() as $module)
                                <li class="list-group-item border-0 px-3 py-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 bg-light rounded p-2">
                                            <i class="fas {{ $module->icon ?? 'fa-cube' }}"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $module->name }}</p>
                                            <p class="mb-0 small text-muted">{{ $module->description ?? 'Modul '.$module->name }}</p>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $module->tenants_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Other Superadmin widgets can be included here -->
    @elseif($isTenantAdmin)
        <!-- Include dasboard_widgets for Tenant Admin -->
        @include('layouts.partials.dashboard_widgets')
        
        <!-- Tambahan widgets untuk Tenant Admin bisa ditambahkan di sini -->
    @else
        <!-- Regular User Dashboard -->
        @include('layouts.partials.dashboard_widgets')
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Superadmin Charts
        @if($isSuperAdmin)
        @if(hasModulePermission('tenant_management'))
        // Tenant Growth Chart
        const tenantGrowthCtx = document.getElementById('tenantGrowthChart').getContext('2d');
        new Chart(tenantGrowthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Tenant baru',
                    data: [12, 19, 13, 15, 22, 27, 30, 25, 18, 23, 26, 31],
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        @endif
        
        @if(hasModulePermission('module_management'))
        // Module Popularity Chart
        const modulePopularityCtx = document.getElementById('modulePopularityChart').getContext('2d');
        new Chart(modulePopularityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Manajemen User', 'Manajemen Risiko', 'Laporan', 'Keuangan', 'Lainnya'],
                datasets: [{
                    data: [25, 20, 30, 15, 10],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            padding: 15
                        }
                    }
                },
                cutout: '70%'
            }
        });
        @endif
        @endif
    });
</script>
@endpush 