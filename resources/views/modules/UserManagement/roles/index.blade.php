@extends('layouts.app')

@section('title', ' | Manajemen Role')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Manajemen Role</h2>
        
        @if(\App\Helpers\PermissionHelper::hasPermission('user-management', 'can_create'))
            <a href="{{ route('modules.user-management.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Tambah Role
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-white">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('modules.user-management.roles.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari role..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Total: {{ $roles->total() }} role</span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-3" width="5%">No</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $role)
                            <tr>
                                <td class="px-3">{{ $roles->firstItem() + $index }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td class="text-center">
                                    @if($role->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @canView('user-management')
                                            <a href="{{ route('modules.user-management.roles.show', $role->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcanView
                                        
                                        @canEdit('user-management')
                                            <a href="{{ route('modules.user-management.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcanEdit
                                        
                                        @canDelete('user-management')
                                            <form action="{{ route('modules.user-management.roles.destroy', $role->id) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanDelete
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">Tidak ada data role</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white d-flex justify-content-center">
            {{ $roles->links() }}
        </div>
    </div>
@endsection 