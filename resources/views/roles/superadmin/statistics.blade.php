@extends('superadmin.layout')

@section('title', 'Statistik Sistem')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Statistik Sistem</h2>
                    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="display-4 mb-2 text-primary">{{ $totalTenants }}</div>
                    <h5 class="text-muted">Total Tenant</h5>
                    <div class="mt-3">
                        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="display-4 mb-2 text-success">{{ $activeTenants }}</div>
                    <h5 class="text-muted">Tenant Aktif</h5>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalTenants > 0 ? ($activeTenants / $totalTenants * 100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $totalTenants > 0 ? round(($activeTenants / $totalTenants * 100), 1) : 0 }}% dari total tenant</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="display-4 mb-2 text-info">{{ $totalUsers }}</div>
                    <h5 class="text-muted">Total User</h5>
                    <small class="text-muted">Rata-rata {{ $totalTenants > 0 ? round($totalUsers / $totalTenants, 1) : 0 }} user per tenant</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="display-4 mb-2 text-warning">{{ $totalModules }}</div>
                    <h5 class="text-muted">Total Modul</h5>
                    <div class="mt-3">
                        <a href="{{ route('superadmin.modules.index') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-cube me-1"></i> Kelola Modul
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Modul Populer -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Modul Paling Populer</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Modul</th>
                                    <th>Jumlah Tenant</th>
                                    <th>Persentase</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularModules as $module)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 bg-light rounded p-2">
                                                <i class="fas {{ $module->icon ?? 'fa-cube' }}"></i>
                                            </div>
                                            <span>{{ $module->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $module->tenants_count }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: {{ $totalTenants > 0 ? ($module->tenants_count / $totalTenants * 100) : 0 }}%"></div>
                                            </div>
                                            <span>{{ $totalTenants > 0 ? round(($module->tenants_count / $totalTenants * 100), 1) : 0 }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('superadmin.modules.show', $module) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('superadmin.modules.index') }}" class="text-decoration-none">
                        Lihat semua modul <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tenant Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tenant Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Modul Aktif</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->activeModules()->count() }}</td>
                                    <td>{{ $tenant->users()->count() }}</td>
                                    <td>{!! $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                    <td>
                                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('superadmin.tenants.index') }}" class="text-decoration-none">
                        Lihat semua tenant <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Sistem -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body border rounded mb-3">
                                <h6 class="card-title border-bottom pb-2 mb-3">Aktivitas Tenant</h6>
                                <div class="d-flex justify-content-around text-center">
                                    <div>
                                        <h5 class="text-success mb-1">{{ $activeTenants }}</h5>
                                        <small class="text-muted">Aktif</small>
                                    </div>
                                    <div>
                                        <h5 class="text-danger mb-1">{{ $totalTenants - $activeTenants }}</h5>
                                        <small class="text-muted">Nonaktif</small>
                                    </div>
                                    <div>
                                        <h5 class="text-primary mb-1">{{ $totalTenants }}</h5>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body border rounded">
                                <h6 class="card-title border-bottom pb-2 mb-3">Distribusi User</h6>
                                <div class="d-flex justify-content-around text-center">
                                    <div>
                                        <h5 class="text-info mb-1">{{ $totalUsers }}</h5>
                                        <small class="text-muted">Total User</small>
                                    </div>
                                    <div>
                                        <h5 class="text-warning mb-1">{{ App\Models\User::whereHas('role', function($q) { $q->where('slug', 'tenant_admin'); })->count() }}</h5>
                                        <small class="text-muted">Admin Tenant</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card-body border rounded h-100">
                                <h6 class="card-title border-bottom pb-2 mb-3">Statistik Modul</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <span>Total Modul Tersedia</span>
                                        <span class="badge bg-primary rounded-pill">{{ $totalModules }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <span>Modul Paling Populer</span>
                                        <span class="badge bg-success rounded-pill">{{ $popularModules->isNotEmpty() ? $popularModules->first()->name : 'Tidak ada' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                        <span>Rata-rata Modul per Tenant</span>
                                        <span class="badge bg-info rounded-pill">{{ $totalTenants > 0 ? round(\App\Models\TenantModule::where('is_active', true)->count() / $totalTenants, 1) : 0 }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagram dan Grafik -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Distribusi Modul per Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Grafik distribusi modul akan ditampilkan di sini menggunakan Chart.js
                    </div>
                    <div style="height: 250px;" class="d-flex align-items-center justify-content-center">
                        <p class="text-muted">Implementasi grafik menggunakan Chart.js</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Pertumbuhan Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Grafik pertumbuhan tenant akan ditampilkan di sini menggunakan Chart.js
                    </div>
                    <div style="height: 250px;" class="d-flex align-items-center justify-content-center">
                        <p class="text-muted">Implementasi grafik menggunakan Chart.js</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 