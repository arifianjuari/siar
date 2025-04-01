@extends('layouts.app')

@php $hideDefaultHeader = true; @endphp

@section('content')
    <!-- Card Filter -->
    <div class="card mb-4 shadow-sm border-top-0">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3 text-dark"><i class="fas fa-filter me-1 small"></i> Filter</h6>
                <form action="{{ route('tenant.tags.index') }}" method="GET" class="flex-grow-1">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama tag..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Card Data Tag -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-tags me-1"></i> Daftar Tag</h4>
            <a href="{{ route('tenant.tags.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Tambah Tag
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($tags->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Belum ada data tag. Silakan tambahkan tag baru.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Tag</th>
                                <th width="30%">Parent Tag</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $index => $tag)
                                <tr>
                                    <td class="text-center">{{ $tags->firstItem() + $index }}</td>
                                    <td>{{ $tag->name }}</td>
                                    <td>{{ $tag->parent ? $tag->parent->name : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Tag Actions">
                                            <a href="{{ route('tenant.tags.show', $tag) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.tags.edit', $tag) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tenant.tags.destroy', $tag) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus tag ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Konfirmasi untuk form delete
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus tag ini?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush 