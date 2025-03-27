@extends('superadmin.layout')

@section('title', 'Manajemen Tenant')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Tenant Management') }}</h5>
                    <div>
                        <a href="{{ route('superadmin.tenants.monitor') }}" class="btn btn-info me-2">
                            <i class="fas fa-chart-bar"></i> {{ __('Monitoring') }}
                        </a>
                        <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Tambah Tenant') }}
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
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Domain</th>
                                    <th>Database</th>
                                    <th>Users</th>
                                    <th>Roles</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->id }}</td>
                                        <td>{{ $tenant->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $tenant->domain }}</span></td>
                                        <td><code>{{ $tenant->database }}</code></td>
                                        <td><span class="badge bg-info">{{ $tenant->users_count }}</span></td>
                                        <td><span class="badge bg-warning">{{ $tenant->roles_count }}</span></td>
                                        <td>{!! $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" class="d-inline delete-form" data-tenant-name="{{ $tenant->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada tenant yang ditambahkan.</td>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const tenantName = this.getAttribute('data-tenant-name');
                
                if (confirm(`Anda yakin ingin menghapus tenant "${tenantName}"?\n\nPerhatian: Tindakan ini akan menghapus semua data terkait tenant ini dan tidak dapat dikembalikan.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush 