@extends('roles.superadmin.layout')

@section('title', 'Daftar Pengguna Tenant')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Daftar Pengguna {{ $tenant->name }}</h2>
                    <div>
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <a href="{{ route('superadmin.tenants.users.create', $tenant) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Tambah Pengguna
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role->name }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('superadmin.tenants.users.edit', [$tenant, $user]) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Delete Modal -->
                                            @if($user->id !== auth()->id())
                                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">Konfirmasi Hapus</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Apakah Anda yakin ingin menghapus pengguna <strong>{{ $user->name }}</strong>?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <form action="{{ route('superadmin.tenants.users.destroy', [$tenant, $user]) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data pengguna</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 