@extends('layouts.app')

@section('title', $document->document_title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header dan Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Detail Dokumen</h2>
            <p class="text-muted mb-0">{{ $document->document_number }}</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->can('edit', $document) && $document->is_active)
                <a href="{{ route('modules.document-management.documents.edit', $document->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Dokumen
                </a>
                
                <form method="POST" action="{{ route('modules.document-management.documents.revise', $document->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-copy me-1"></i> Buat Revisi
                    </button>
                </form>
            @endif
            
            <a href="{{ route('modules.document-management.documents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Document Information -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Judul Dokumen</div>
                        <div class="col-md-8">{{ $document->document_title }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nomor Dokumen</div>
                        <div class="col-md-8">{{ $document->document_number }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kategori</div>
                        <div class="col-md-8">{{ ucfirst($document->category) }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Dokumen</div>
                        <div class="col-md-8">{{ $document->document_date ? $document->document_date->format('d M Y') : 'N/A' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tingkat Kerahasiaan</div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $document->confidentiality_level == 'public' ? 'success' : ($document->confidentiality_level == 'internal' ? 'warning' : 'danger') }}">
                                {{ ucfirst($document->confidentiality_level) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($document->document_type)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tipe Dokumen</div>
                        <div class="col-md-8">
                            @php
                                $typeLabels = [
                                    'policy' => 'Kebijakan',
                                    'guideline' => 'Pedoman',
                                    'spo' => 'SPO',
                                    'program' => 'Program',
                                    'evidence' => 'Bukti'
                                ];
                            @endphp
                            {{ $typeLabels[$document->document_type] ?? ucfirst($document->document_type) }}
                        </div>
                    </div>
                    @endif
                    
                    @if($document->document_scope)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Ruang Lingkup</div>
                        <div class="col-md-8">
                            {{ $document->document_scope == 'rumahsakit' ? 'Rumah Sakit' : 'Unit Kerja' }}
                        </div>
                    </div>
                    @endif
                    
                    @if($document->description)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Deskripsi</div>
                        <div class="col-md-8">{{ $document->description }}</div>
                    </div>
                    @endif
                    
                    @if($document->is_regulation)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Regulasi</div>
                        <div class="col-md-8">
                            <span class="badge bg-primary">Ya</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status</div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $document->is_active ? 'success' : 'secondary' }}">
                                {{ $document->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revision Info -->
            @if($document->revision_number || $document->revision_date || $document->supersededBy)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Revisi</h5>
                </div>
                <div class="card-body">
                    @if($document->revision_number)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nomor Revisi</div>
                        <div class="col-md-8">{{ $document->revision_number }}</div>
                    </div>
                    @endif
                    
                    @if($document->revision_date)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Revisi</div>
                        <div class="col-md-8">{{ $document->revision_date->format('d M Y') }}</div>
                    </div>
                    @endif
                    
                    @if($document->supersededBy)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Digantikan Oleh</div>
                        <div class="col-md-8">
                            <a href="{{ route('modules.document-management.documents.show', $document->supersededBy->id) }}">
                                {{ $document->supersededBy->document_title }} ({{ $document->supersededBy->document_number }})
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if($document->supersedes->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Menggantikan</div>
                        <div class="col-md-8">
                            <ul class="list-unstyled">
                                @foreach($document->supersedes as $superseded)
                                <li>
                                    <a href="{{ route('modules.document-management.documents.show', $superseded->id) }}">
                                        {{ $superseded->document_title }} ({{ $superseded->document_number }})
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Document File -->
            @if($document->file_path)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">File Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file fa-2x me-3 text-primary"></i>
                        <div>
                            <p class="mb-0">{{ basename($document->file_path) }}</p>
                            <small class="text-muted">Diunggah pada: {{ $document->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Tags Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    @if($document->tags->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($document->tags as $tag)
                                <a href="{{ route('modules.document-management.documents.index', ['tag' => $tag->slug]) }}" class="badge bg-info text-decoration-none">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada tag.</p>
                    @endif
                </div>
            </div>
            
            <!-- Additional Info Cards -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Info Tambahan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Diunggah Oleh</div>
                        <div class="col-md-7">{{ $document->uploadedBy->name ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Tanggal Unggah</div>
                        <div class="col-md-7">{{ $document->created_at->format('d M Y H:i') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Terakhir Diubah</div>
                        <div class="col-md-7">{{ $document->updated_at->format('d M Y H:i') }}</div>
                    </div>
                    
                    @if($document->last_evaluated_at)
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Evaluasi Terakhir</div>
                        <div class="col-md-7">{{ $document->last_evaluated_at->format('d M Y') }}</div>
                    </div>
                    @endif
                    
                    @if($document->evaluatedBy)
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Dievaluasi Oleh</div>
                        <div class="col-md-7">{{ $document->evaluatedBy->name ?? 'N/A' }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Physical Storage Info -->
            @if($document->storage_location || $document->distribution_note)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Penyimpanan Fisik</h5>
                </div>
                <div class="card-body">
                    @if($document->storage_location)
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Lokasi Penyimpanan</div>
                        <div class="col-md-7">{{ $document->storage_location }}</div>
                    </div>
                    @endif
                    
                    @if($document->distribution_note)
                    <div class="row mb-3">
                        <div class="col-md-5 fw-bold">Catatan Distribusi</div>
                        <div class="col-md-7">{{ $document->distribution_note }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 