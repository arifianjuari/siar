@extends('layouts.app')

@section('title', 'Manajemen Modul')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="mb-0">Manajemen Modul</h2>
    <div>
        <form action="{{ route('superadmin.modules.sync') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success me-2" title="Sync modules from filesystem">
                <i class="fas fa-sync me-2"></i> Sync dari Filesystem
                @if(isset($discoveredCount) && $discoveredCount > 0)
                    <span class="badge bg-light text-dark">{{ $discoveredCount }}</span>
                @endif
            </button>
        </form>
        <a href="{{ route('superadmin.modules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Tambah Manual
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
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

    <!-- Discovered Modules Alert -->
    @if(isset($filesystemModules) && count($filesystemModules) > 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-folder me-2"></i> Modul Terdeteksi dari Filesystem</h5>
            <p class="mb-2">Ditemukan <strong>{{ $discoveredCount }}</strong> modul di folder <code>modules/</code></p>
            <ul class="mb-2">
                @foreach($filesystemModules as $fsModule)
                    <li>
                        <strong>{{ $fsModule['name'] }}</strong>
                        @if($fsModule['exists_in_db'])
                            <span class="badge bg-success">Sudah di Database</span>
                        @else
                            <span class="badge bg-warning">Belum di Database</span>
                        @endif
                        @if(isset($fsModule['version']))
                            <small class="text-muted">(v{{ $fsModule['version'] }})</small>
                        @endif
                    </li>
                @endforeach
            </ul>
            <p class="mb-0">Klik tombol <strong>"Sync dari Filesystem"</strong> untuk menambahkan modul yang belum terdaftar ke database.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Modul di Database</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Ikon</th>
                                    <th>Tenant Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($modules as $module)
                                    <tr>
                                        <td>{{ $module->id }}</td>
                                        <td>{{ $module->name }}</td>
                                        <td><code>{{ $module->slug }}</code></td>
                                        <td>
                                            <i class="fas {{ $module->icon ?? 'fa-cube' }}"></i>
                                            @if($module->icon)
                                                <small class="text-muted">{{ $module->icon }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $module->tenants_count }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.modules.show', $module) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.modules.edit', $module) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModuleModal{{ $module->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal Konfirmasi Hapus -->
                                            <div class="modal fade" id="deleteModuleModal{{ $module->id }}" tabindex="-1" aria-labelledby="deleteModuleModalLabel{{ $module->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModuleModalLabel{{ $module->id }}">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Anda yakin ingin menghapus modul <strong>{{ $module->name }}</strong>?</p>
                                                            <p class="text-danger">Perhatian: Modul yang sudah digunakan oleh tenant tidak dapat dihapus.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('superadmin.modules.destroy', $module) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada modul yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $modules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 