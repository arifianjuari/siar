@extends('layouts.app')

@section('title', ' | Detail Unit Kerja')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Detail Unit Kerja</h1>
        <div>
            <a href="{{ route('tenant.work-units.edit', $workUnit) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-1"></i> Informasi Unit Kerja</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="30%">ID</th>
                            <td>{{ $workUnit->id }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nama Unit</th>
                            <td>{{ $workUnit->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kode Unit</th>
                            <td>{{ $workUnit->code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Parent Unit</th>
                            <td>{{ $workUnit->parent ? $workUnit->parent->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Deskripsi</th>
                            <td>{{ $workUnit->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Urutan</th>
                            <td>{{ $workUnit->order }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Status</th>
                            <td>
                                @if($workUnit->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Dibuat Pada</th>
                            <td>{{ $workUnit->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Diupdate Pada</th>
                            <td>{{ $workUnit->updated_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-cogs me-1"></i> Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.work-units.edit', $workUnit) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Unit Kerja
                        </a>
                        <form action="{{ route('tenant.work-units.toggle-status', $workUnit) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $workUnit->is_active ? 'btn-secondary' : 'btn-success' }} w-100">
                                <i class="fas {{ $workUnit->is_active ? 'fa-ban' : 'fa-check-circle' }} me-1"></i>
                                {{ $workUnit->is_active ? 'Nonaktifkan Unit' : 'Aktifkan Unit' }}
                            </button>
                        </form>
                        <form action="{{ route('tenant.work-units.destroy', $workUnit) }}" method="POST" id="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Anda yakin ingin menghapus unit kerja ini?')">
                                <i class="fas fa-trash me-1"></i> Hapus Unit Kerja
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sub-Unit / Anak Unit -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-sitemap me-1"></i> Sub-Unit</h5>
                </div>
                <div class="card-body">
                    @if($workUnit->children->count() > 0)
                        <ul class="list-group">
                            @foreach($workUnit->children as $child)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $child->name }}
                                        @if(!$child->is_active)
                                            <span class="badge bg-danger ms-2">Non-Aktif</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('tenant.work-units.show', $child) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> Tidak ada sub-unit.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForm = document.getElementById('delete-form');
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush 