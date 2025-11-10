@extends('layouts.app')

@section('title', $typeTitle)

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
    
    .type-public {
        background-color: #d1e7dd;
        color: #0f5132;
        border-color: #a3cfbb;
    }
    
    .type-internal {
        background-color: #fff3cd;
        color: #664d03;
        border-color: #ffecb5;
    }
    
    .type-confidential {
        background-color: #f8d7da;
        color: #842029;
        border-color: #f5c2c7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                @php
                    $typeClass = '';
                    $typeIcon = 'fas fa-file-alt';
                    
                    if ($type === 'public') {
                        $typeClass = 'type-public';
                        $typeIcon = 'fas fa-globe';
                    } elseif ($type === 'internal') {
                        $typeClass = 'type-internal';
                        $typeIcon = 'fas fa-building';
                    } elseif ($type === 'confidential') {
                        $typeClass = 'type-confidential';
                        $typeIcon = 'fas fa-lock';
                    }
                @endphp
                <span class="tag-badge {{ $typeClass }}"><i class="{{ $typeIcon }} me-1"></i>{{ $typeTitle }}</span>
            </h2>
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
                <div>
                    @if($type !== 'all')
                        <a href="{{ route('modules.document-management.documents-by-type', 'all') }}" class="btn btn-sm btn-outline-secondary me-2">
                            Semua Dokumen
                        </a>
                    @endif
                    @if($type !== 'public')
                        <a href="{{ route('modules.document-management.documents-by-type', 'public') }}" class="btn btn-sm btn-outline-success me-2">
                            Publik
                        </a>
                    @endif
                    @if($type !== 'internal')
                        <a href="{{ route('modules.document-management.documents-by-type', 'internal') }}" class="btn btn-sm btn-outline-warning me-2">
                            Internal
                        </a>
                    @endif
                    @if($type !== 'confidential')
                        <a href="{{ route('modules.document-management.documents-by-type', 'confidential') }}" class="btn btn-sm btn-outline-danger">
                            Rahasia
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($combinedDocuments->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($combinedDocuments as $document)
                        @php
                            $isRiskReport = $document instanceof \App\Models\RiskReport;
                            $isDocument = $document instanceof \App\Models\Document;
                            $isCorrespondence = $document instanceof \App\Models\Correspondence;
                            $isSPO = $document instanceof \App\Models\SPO;
                            
                            $detailRoute = '#';
                            $iconClass = 'fas fa-file-alt text-primary';
                            $typeBadge = '<span class="badge bg-primary">Dokumen</span>';
                            
                            if ($isDocument) {
                                $detailRoute = route('modules.document-management.documents.show', $document->id);
                                $iconClass = 'fas fa-file-alt text-primary';
                                $typeBadge = '<span class="badge bg-primary">Dokumen</span>';
                            } elseif ($isRiskReport) {
                                $detailRoute = route('modules.risk-management.risk-reports.show', $document->id);
                                $iconClass = 'fas fa-exclamation-triangle text-danger';
                                $typeBadge = '<span class="badge bg-danger">Laporan Risiko</span>';
                            } elseif ($isCorrespondence) {
                                $detailRoute = route('modules.correspondence.letters.show', $document->id);
                                $iconClass = 'fas fa-envelope text-info';
                                $typeBadge = '<span class="badge bg-info">Korespondensi</span>';
                            } elseif ($isSPO) {
                                $detailRoute = route('modules.spo.show', $document->id);
                                $iconClass = 'fas fa-clipboard-list text-success';
                                $typeBadge = '<span class="badge bg-success">SPO</span>';
                            }
                            
                            $docDate = $document->document_date ?? $document->letter_date ?? $document->effective_date ?? $document->created_at;
                            $formattedDate = $docDate ? \Carbon\Carbon::parse($docDate)->format('d M Y') : '-';
                            
                            $docTitle = $document->document_title ?? $document->title ?? 'Judul Tidak Tersedia';
                            $docNumber = $document->document_number ?? $document->number ?? 'No. Dokumen tidak tersedia';
                        @endphp
                        <div class="doc-item">
                            <a href="{{ $detailRoute }}" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="doc-title mb-0">
                                            <i class="{{ $iconClass }} me-2"></i>
                                            {{ $docTitle }}
                                        </h5>
                                        <div class="doc-meta">
                                            <span class="me-3">{{ $docNumber }}</span>
                                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i> {{ $formattedDate }}</span>
                                            <span>
                                                {!! $typeBadge !!}
                                                
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
                    <p class="mb-0 text-muted">Tidak ada dokumen yang ditemukan dengan tingkat kerahasiaan ini</p>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle me-1"></i> Coba pilih tingkat kerahasiaan lain atau kembali ke dashboard
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 