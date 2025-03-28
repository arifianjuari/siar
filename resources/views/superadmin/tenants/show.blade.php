@extends('superadmin.layout')

@section('title', 'Detail Tenant')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Detail Tenant: {{ $tenant->name }}</h2>
                    <div>
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-success me-2">
                            <i class="fas fa-edit me-2"></i> Edit Tenant
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline-primary">
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
        <!-- Informasi Tenant -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">ID</span>
                        <strong>{{ $tenant->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Nama</span>
                        <strong>{{ $tenant->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Domain</span>
                        <strong>{{ $tenant->domain }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Database</span>
                        <code>{{ $tenant->database }}</code>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Status</span>
                        {!! $tenant->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Dibuat Pada</span>
                        <span>{{ $tenant->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Diperbarui Pada</span>
                        <span>{{ $tenant->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistik Tenant -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistik Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Total Pengguna</span>
                        <span class="badge bg-info">{{ $userCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Modul Aktif</span>
                        <span class="badge bg-primary">{{ $activeModules }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Peran</span>
                        <span class="badge bg-warning">{{ $tenant->roles->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manajemen Modul -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Manajemen Modul</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Modul</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenant->modules as $module)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 bg-light rounded p-2">
                                                    <i class="fas {{ $module->icon ?? 'fa-cube' }}"></i>
                                                </div>
                                                <strong>{{ $module->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $module->description ?? 'Tidak ada deskripsi' }}</td>
                                        <td>
                                            <span id="module-status-{{ $module->id }}" class="badge {{ $module->pivot->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $module->pivot->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm {{ $module->pivot->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} toggle-module"
                                                data-module-id="{{ $module->id }}"
                                                data-tenant-id="{{ $tenant->id }}"
                                                data-current-status="{{ $module->pivot->is_active ? '1' : '0' }}"
                                            >
                                                {{ $module->pivot->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Admin Tenant -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adminUsers as $admin)
                                    <tr>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{!! $admin->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $admin->id }}">
                                                <i class="fas fa-key me-1"></i> Reset Password
                                            </button>

                                            <!-- Modal Reset Password -->
                                            <div class="modal fade" id="resetPasswordModal{{ $admin->id }}" tabindex="-1" aria-labelledby="resetPasswordModalLabel{{ $admin->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('superadmin.tenants.reset-admin-password', $tenant) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $admin->id }}">
                                                            
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="resetPasswordModalLabel{{ $admin->id }}">Reset Password Admin</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Anda akan mereset password untuk admin <strong>{{ $admin->name }}</strong>.</p>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="new_password" class="form-label">Password Baru</label>
                                                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                                    <div class="form-text">Password minimal 8 karakter.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning">Reset Password</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Manajemen Role -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manajemen Role</h5>
                    <a href="{{ route('superadmin.tenants.roles.create', $tenant) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Role Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Pengguna</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenant->roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td><code>{{ $role->slug }}</code></td>
                                        <td>{{ $role->description ?? 'Tidak ada deskripsi' }}</td>
                                        <td>{!! $role->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td>
                                        <td><span class="badge bg-info">{{ $role->users->count() }}</span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.tenants.roles.edit', [$tenant, $role]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <a href="{{ route('superadmin.tenants.roles.permissions.edit', [$tenant, $role]) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Atur Hak Akses">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                
                                                @if($role->slug !== 'tenant-admin')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Modal Konfirmasi Hapus Role -->
                                                    <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteRoleModalLabel{{ $role->id }}">Konfirmasi Hapus Role</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Anda yakin ingin menghapus role <strong>{{ $role->name }}</strong>?</p>
                                                                    @if($role->users->count() > 0)
                                                                        <div class="alert alert-warning">
                                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                                            Role ini sedang digunakan oleh {{ $role->users->count() }} pengguna. Hapus atau pindahkan pengguna terlebih dahulu.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <form action="{{ route('superadmin.tenants.roles.destroy', [$tenant, $role]) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger" {{ $role->users->count() > 0 ? 'disabled' : '' }}>Hapus</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            Belum ada role yang dibuat untuk tenant ini.
                                        </td>
                                    </tr>
                                @endforelse
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
        // Toggle module status
        const toggleButtons = document.querySelectorAll('.toggle-module');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const moduleId = this.dataset.moduleId;
                const tenantId = this.dataset.tenantId;
                const currentStatus = this.dataset.currentStatus;
                const newStatus = currentStatus === '1' ? '0' : '1';
                
                // Update UI optimistically
                const statusBadge = document.getElementById(`module-status-${moduleId}`);
                if (newStatus === '1') {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'Aktif';
                    this.className = 'btn btn-sm btn-outline-danger toggle-module';
                    this.textContent = 'Nonaktifkan';
                } else {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = 'Nonaktif';
                    this.className = 'btn btn-sm btn-outline-success toggle-module';
                    this.textContent = 'Aktifkan';
                }
                
                this.dataset.currentStatus = newStatus;
                
                // Send AJAX request
                fetch(`/superadmin/tenants/${tenantId}/toggle-module`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        module_id: moduleId,
                        is_active: newStatus === '1'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success handling (optional toast notification)
                        console.log('Module status updated successfully');
                    } else {
                        // Revert UI changes on error
                        console.error('Error updating module status:', data.message);
                        this.dataset.currentStatus = currentStatus;
                        
                        if (currentStatus === '1') {
                            statusBadge.className = 'badge bg-success';
                            statusBadge.textContent = 'Aktif';
                            this.className = 'btn btn-sm btn-outline-danger toggle-module';
                            this.textContent = 'Nonaktifkan';
                        } else {
                            statusBadge.className = 'badge bg-danger';
                            statusBadge.textContent = 'Nonaktif';
                            this.className = 'btn btn-sm btn-outline-success toggle-module';
                            this.textContent = 'Aktifkan';
                        }
                    }
                })
                .catch(error => {
                    console.error('AJAX request failed:', error);
                });
            });
        });
    });
</script>
@endpush 