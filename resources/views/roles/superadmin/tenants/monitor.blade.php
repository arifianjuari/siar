@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Monitoring Tenant') }}</h5>
                    <div>
                        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Kembali') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Filter dan Pencarian -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form action="{{ route('superadmin.tenants.monitor') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari tenant..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <a href="{{ route('superadmin.tenants.monitor') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                                <a href="{{ route('superadmin.tenants.monitor', ['status' => 'active']) }}" class="btn {{ request('status') == 'active' ? 'btn-primary' : 'btn-outline-primary' }}">Aktif</a>
                                <a href="{{ route('superadmin.tenants.monitor', ['status' => 'inactive']) }}" class="btn {{ request('status') == 'inactive' ? 'btn-primary' : 'btn-outline-primary' }}">Non-Aktif</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Tenant -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('Tenant') }}</th>
                                    <th>{{ __('Domain') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Jumlah User') }}</th>
                                    <th>{{ __('Modul Aktif') }}</th>
                                    <th>{{ __('Admin') }}</th>
                                    <th>{{ __('Login Terakhir Admin') }}</th>
                                    <th>{{ __('Aktivitas (7 hari)') }}</th>
                                    <th>{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->name }}</td>
                                        <td>{{ $tenant->domain }}</td>
                                        <td>
                                            @if($tenant->is_active)
                                                <span class="badge bg-success">{{ __('Aktif') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Non-Aktif') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $tenant->users_count }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse($tenant->modules as $module)
                                                    <span class="badge bg-primary" title="{{ $module->description }}">
                                                        {{ $module->name }}
                                                        @if(isset($tenant->module_data_counts[$module->name]) && $tenant->module_data_counts[$module->name] !== 'N/A')
                                                            <span class="badge bg-light text-dark">{{ $tenant->module_data_counts[$module->name] }}</span>
                                                        @endif
                                                    </span>
                                                @empty
                                                    <span class="badge bg-secondary">{{ __('Tidak ada') }}</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            @forelse($tenant->admin_users as $admin)
                                                <div>{{ $admin->name }}</div>
                                            @empty
                                                <span class="text-muted">{{ __('Tidak ada') }}</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if($tenant->last_admin_login)
                                                <span title="{{ $tenant->last_admin_login->format('d M Y H:i') }}">
                                                    {{ $tenant->last_admin_login->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ __('Belum pernah') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $tenant->recent_activities }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('superadmin.tenants.monitor.show', $tenant) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-line"></i> {{ __('Detail') }}
                                                </a>
                                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('Tidak ada data tenant.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tenants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 