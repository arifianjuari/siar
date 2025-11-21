@extends('layouts.app')

@section('title', 'Daftar Dokumen')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Daftar Dokumen</h2>
            <p class="text-muted mb-0">Kelola dokumen dari berbagai sumber</p>
        </div>
        <div>
            <a href="{{ route('modules.document-management.documents.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Unggah Dokumen
            </a>
            <a href="{{ route('modules.document-management.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('modules.document-management.documents.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Cari Dokumen</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Judul, nomor, deskripsi..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan</label>
                        <select class="form-select" id="confidentiality_level" name="confidentiality_level">
                            <option value="">Semua Level</option>
                            <option value="public" {{ request('confidentiality_level') == 'public' ? 'selected' : '' }}>Publik</option>
                            <option value="internal" {{ request('confidentiality_level') == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="confidential" {{ request('confidentiality_level') == 'confidential' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="tag" class="form-label">Tag</label>
                        <select class="form-select" id="tag" name="tag">
                            <option value="">Semua Tag</option>
                            @foreach($availableTags as $tag)
                                <option value="{{ $tag->slug }}" {{ request('tag') == $tag->slug ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('modules.document-management.documents.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor Dokumen</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Kerahasiaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $document)
                            <tr>
                                <td>{{ $document->document_number }}</td>
                                <td>
                                    <div class="fw-medium">{{ $document->document_title }}</div>
                                    @if($document->tags->count() > 0)
                                        <div class="mt-1">
                                            @foreach($document->tags as $tag)
                                                <a href="{{ route('modules.document-management.documents.index', ['tag' => $tag->slug]) }}" 
                                                   class="badge bg-light text-dark text-decoration-none">
                                                    {{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($document->category) }}</span>
                                </td>
                                <td>{{ $document->document_date ? $document->document_date->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $document->confidentiality_level == 'public' ? 'success' : ($document->confidentiality_level == 'internal' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($document->confidentiality_level) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('modules.document-management.documents.show', $document->id) }}" 
                                           class="btn btn-sm btn-info" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('modules.document-management.documents.edit', $document->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('modules.document-management.documents.destroy', $document->id) }}" 
                                              style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus dokumen ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-alt fa-2x mb-3"></i>
                                        <p>Belum ada dokumen yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 