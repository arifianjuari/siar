@extends('layouts.app')

@php $hideDefaultHeader = true; @endphp

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Tag info card -->
            <div class="card shadow mb-4 border-top-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-info-circle me-1"></i> Detail Tag</h4>
                    <div>
                        <a href="{{ route('tenant.tags.edit', $tag) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('tenant.tags.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h4 fw-bold">{{ $tag->name }}</h2>
                        @if($tag->description)
                            <p class="text-muted">{{ $tag->description }}</p>
                        @else
                            <p class="text-muted fst-italic">Tidak ada deskripsi</p>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Detail:</h6>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="25%" class="bg-light">Parent Tag</th>
                                    <td>{{ $tag->parent ? $tag->parent->name : 'Tidak ada' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Urutan</th>
                                    <td>{{ $tag->order }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Dibuat pada</th>
                                    <td>{{ $tag->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Diperbarui pada</th>
                                    <td>{{ $tag->updated_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Sub-Tags -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-tags me-1"></i> Sub-Tags</h4>
                </div>
                <div class="card-body">
                    @if($tag->children->isEmpty())
                        <p class="text-muted fst-italic">Tidak ada sub-tag</p>
                    @else
                        <div class="list-group">
                            @foreach($tag->children as $child)
                                <a href="{{ route('tenant.tags.show', $child) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ $child->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $child->children->count() }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-cogs me-1"></i> Aksi</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.tags.edit', $tag) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i> Edit Tag
                        </a>
                        <form action="{{ route('tenant.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tag ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="fas fa-trash me-2"></i> Hapus Tag
                            </button>
                        </form>
                        <a href="{{ route('tenant.tags.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus tag <strong>{{ $tag->name }}</strong>?</p>
                    @if($tag->children->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i> Tag ini memiliki {{ $tag->children->count() }} sub-tag. Hapus terlebih dahulu sub-tag atau pindahkan ke tag lain.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    @if($tag->children->count() == 0)
                        <form action="{{ route('tenant.tags.destroy', $tag) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Ya, Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 