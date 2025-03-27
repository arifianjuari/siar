@extends('superadmin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-2">Dashboard Superadmin</h2>
                            <p class="lead mb-0">Selamat datang di panel administrasi superadmin SIAR.</p>
                        </div>
                        <div class="col-auto d-none d-md-block">
                            <a href="{{ route('superadmin.statistics') }}" class="btn btn-light px-4">
                                <i class="fas fa-chart-line me-2"></i>Lihat Statistik
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total Tenant</h6>
                            <h3 class="mb-0 mt-2 fw-bold">{{ \App\Models\Tenant::count() }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="{{ route('superadmin.tenants.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Tenant</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Tenant Aktif</h6>
                            <h3 class="mb-0 mt-2 fw-bold">{{ \App\Models\Tenant::where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        @php 
                            $totalTenants = \App\Models\Tenant::count();
                            $activeTenants = \App\Models\Tenant::where('is_active', true)->count();
                            $activePercentage = $totalTenants > 0 ? ($activeTenants / $totalTenants * 100) : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activePercentage }}%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="{{ route('superadmin.statistics') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Lihat Statistik</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total Modul</h6>
                            <h3 class="mb-0 mt-2 fw-bold">{{ \App\Models\Module::count() }}</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="{{ route('superadmin.modules.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Modul</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-5 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold">Total User</h6>
                            <h3 class="mb-0 mt-2 fw-bold">{{ \App\Models\User::count() }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    <a href="{{ route('superadmin.users.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Kelola Pengguna</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tenant Terbaru -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-building me-2 text-primary"></i>Tenant Terbaru</h5>
                    <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Tenant::latest()->take(5)->get() as $tenant)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ $tenant->name }}</td>
                                    <td><span class="text-primary">{{ $tenant->domain }}</span></td>
                                    <td>{!! $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                    <td>{{ $tenant->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modul Populer -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-star me-2 text-warning"></i>Modul Populer</h5>
                    <a href="{{ route('superadmin.modules.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog me-1"></i>Kelola Modul
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $popularModules = \App\Models\Module::withCount(['tenants' => function($q) {
                            $q->where('tenant_modules.is_active', true);
                        }])->orderBy('tenants_count', 'desc')->take(5)->get();
                    @endphp

                    <ul class="list-group list-group-flush">
                        @forelse($popularModules as $module)
                            <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 stat-icon bg-light">
                                        <i class="fas {{ $module->icon ?? 'fa-cube' }} text-primary"></i>
                                    </div>
                                    <div class="fw-medium">{{ $module->name }}</div>
                                </div>
                                <div>
                                    <span class="badge bg-primary">{{ $module->tenants_count }} tenant</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">Belum ada data modul</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User terbaru -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-info"></i>Pengguna Terbaru</h5>
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-cog me-1"></i>Kelola Pengguna
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama</th>
                                    <th>Email</th>
                                    <th>Tenant</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::with(['tenant', 'role'])->latest()->take(5)->get() as $user)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->tenant->name ?? '-' }}</td>
                                    <td>{{ $user->role->name ?? '-' }}</td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>{!! $user->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ini tempat untuk kode JavaScript tambahan jika diperlukan
    });
</script>
@endpush 