@extends('layouts.app')

@section('title', 'Dokumen dengan Tag: ' . $tag->name)

@push('styles')
<style>
    .doc-item {
        transition: all 0.2s;
        border-left: 3px solid #eee;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    
    .doc-item:hover {
        border-left-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .doc-item .doc-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.25rem;
    }
    
    .doc-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .tag-badge {
        font-size: 0.75rem;
        border-radius: 1rem;
        padding: 0.35rem 0.65rem;
        background-color: #e9f3ff;
        color: #0d6efd;
        border: 1px solid #b8daff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Dokumen dengan Tag: <span class="tag-badge"><i class="fas fa-tag me-1"></i>{{ $tag->name }}</span></h2>
            <p class="text-muted mb-0">{{ $combinedDocuments->count() }} dokumen ditemukan</p>
        </div>
        <div>
            <a href="{{ route('modules.document-management.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <!-- Document List -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Dokumen</h5>
                @if($tag->description)
                    <span class="badge bg-light text-dark p-2">{{ $tag->description }}</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($combinedDocuments->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($combinedDocuments as $document)
                        @php
                            $isRiskReport = $document instanceof \App\Models\RiskReport;
                            $detailRoute = $isRiskReport 
                                ? route('modules.risk-management.risk-reports.show', $document->id)
                                : route('modules.document-management.documents.show', $document->id);
                            $docDate = $document->document_date ?? $document->created_at;
                            $formattedDate = $docDate ? \Carbon\Carbon::parse($docDate)->format('d M Y') : '-';
                        @endphp
                        <div class="doc-item">
                            <a href="{{ $detailRoute }}" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="doc-title mb-0">
                                            @if($isRiskReport)
                                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            @else
                                                <i class="fas fa-file-alt text-primary me-2"></i>
                                            @endif
                                            {{ $document->document_title }}
                                        </h5>
                                        <div class="doc-meta">
                                            <span class="me-3">{{ $document->document_number ?? 'No. Dokumen tidak tersedia' }}</span>
                                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i> {{ $formattedDate }}</span>
                                            <span>
                                                @if($isRiskReport)
                                                    <span class="badge bg-danger">Laporan Risiko</span>
                                                @else
                                                    <span class="badge bg-primary">Dokumen</span>
                                                @endif
                                                
                                                @if($document->confidentiality_level)
                                                    @php
                                                        $bgColor = 'bg-secondary';
                                                        if (strtolower($document->confidentiality_level) == 'public' || strtolower($document->confidentiality_level) == 'publik') {
                                                            $bgColor = 'bg-success';
                                                        } elseif (strtolower($document->confidentiality_level) == 'internal') {
                                                            $bgColor = 'bg-warning text-dark';
                                                        } elseif (strtolower($document->confidentiality_level) == 'confidential' || strtolower($document->confidentiality_level) == 'rahasia') {
                                                            $bgColor = 'bg-danger';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $bgColor }}">{{ $document->confidentiality_level }}</span>
                                                @endif
                                                
                                                @if($document->file_path)
                                                    <i class="fas fa-paperclip ms-2"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Tidak ada dokumen yang ditemukan dengan tag ini</p>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle me-1"></i> Coba cari dengan tag lain atau kembali ke dashboard
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 