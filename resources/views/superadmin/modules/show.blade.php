@extends('superadmin.layout')

@section('title', 'Detail Modul')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Detail Modul: {{ $module->name }}</h2>
                    <div>
                        <a href="{{ route('superadmin.modules.edit', $module) }}" class="btn btn-success me-2">
                            <i class="fas fa-edit me-2"></i> Edit Modul
                        </a>
                        <a href="{{ route('superadmin.modules.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Modul -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-1 mb-3">
                            <i class="fas {{ $module->icon ?? 'fa-cube' }} text-primary"></i>
                        </div>
                        <h3>{{ $module->name }}</h3>
                        <div><code>{{ $module->slug }}</code></div>
                    </div>

                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">ID</span>
                        <strong>{{ $module->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Deskripsi</span>
                        <span>{{ $module->description ?? 'Tidak ada deskripsi' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Dibuat Pada</span>
                        <span>{{ $module->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Diperbarui Pada</span>
                        <span>{{ $module->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistik Modul -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistik Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-primary">{{ $activeInTenantCount }}</div>
                        <div class="text-muted">Tenant Menggunakan Modul Ini</div>
                    </div>

                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentageActive }}%;" aria-valuenow="{{ $percentageActive }}" aria-valuemin="0" aria-valuemax="100">{{ $percentageActive }}%</div>
                    </div>
                    <div class="text-center text-muted">
                        <small>Persentase tenant yang mengaktifkan modul ini ({{ $activeInTenantCount }} dari {{ $totalTenants }})</small>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        @if($percentageActive < 100)
                            <form action="{{ route('superadmin.modules.activate-for-all', $module) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i> Aktifkan untuk Semua Tenant
                                </button>
                            </form>
                        @endif
                        @if($percentageActive > 0)
                            <form action="{{ route('superadmin.modules.deactivate-for-all', $module) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-times-circle me-2"></i> Nonaktifkan untuk Semua Tenant
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant yang Menggunakan Modul -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tenant yang Menggunakan Modul Ini</h5>
                </div>
                <div class="card-body">
                    @if($module->tenants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Tenant</th>
                                        <th>Domain</th>
                                        <th>Status Tenant</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($module->tenants as $tenant)
                                        <tr>
                                            <td>{{ $tenant->name }}</td>
                                            <td><code>{{ $tenant->domain }}</code></td>
                                            <td>{!! $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                            <td>
                                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="fas fa-info-circle fa-3x"></i>
                            </div>
                            <p>Belum ada tenant yang menggunakan modul ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 