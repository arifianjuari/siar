@extends('layouts.app')

@section('title', ' | Roles')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daftar Role</h5>
                        <div>
                            @if(hasModulePermission('user-management', auth()->user(), 'can_create'))
                            <a href="{{ route('superadmin.roles.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Role
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($roles->isEmpty())
                    <div class="alert alert-info">
                        Tidak ada role yang ditemukan.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $index => $role)
                                <tr>
                                    <td>{{ $roles->firstItem() + $index }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td>{{ Str::limit($role->description, 50) }}</td>
                                    <td>
                                        @if($role->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if(hasModulePermission('user-management', auth()->user(), 'can_view'))
                                            <a href="{{ route('superadmin.roles.show', $role->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif

                                            @if(hasModulePermission('user-management', auth()->user(), 'can_update'))
                                            <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(hasModulePermission('user-management', auth()->user(), 'can_delete') && !in_array($role->slug, ['superadmin', 'tenant-admin']))
                                            <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $roles->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi hapus role
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const roleName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                
                if (confirm(`Apakah Anda yakin ingin menghapus role "${roleName}"? Tindakan ini tidak dapat dibatalkan dan dapat memengaruhi pengguna yang memiliki role ini.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush 