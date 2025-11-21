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
    
    .module-item {
        cursor: pointer;
        transition: all 0.25s ease;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        border: 1px solid #eee;
    }
    
    .module-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .module-item.active {
        background-color: rgba(13,110,253,0.05);
        border-left: 3px solid #0d6efd;
    }
    
    .module-item .module-icon {
        font-size: 1.25rem;
        width: 30px;
        text-align: center;
    }
    
    .module-item .module-name {
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                <span class="tag-badge">
                    <i class="fas fa-tag me-1"></i>{{ $tag->name }}
                </span>
            </h2>
            <p class="text-muted mb-0">{{ $combinedDocuments->count() }} dokumen ditemukan</p>
        </div>
        <div>
            <a href="{{ route('modules.document-management.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <!-- Ringkasan Modul dengan Tag -->
    @if(isset($moduleSummary) && count($moduleSummary) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Dokumen berdasarkan Modul</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($moduleSummary as $module)
                            <a href="{{ route('modules.document-management.documents-by-tag', ['slug' => $tag->slug, 'module' => $module['slug']]) }}" class="text-decoration-none">
                                <li class="list-group-item module-item {{ $selectedModule == $module['slug'] ? 'active' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="{{ $module['icon'] }} module-icon text-{{ $module['color'] }} me-2"></i>
                                            <span class="module-name">{{ $module['name'] }}</span>
                                        </div>
                                        <span class="badge bg-{{ $module['color'] }} rounded-pill">{{ $module['count'] }}</span>
                                    </div>
                                </li>
                            </a>
                        @endforeach
                        @if($selectedModule != 'all')
                            <a href="{{ route('modules.document-management.documents-by-tag', ['slug' => $tag->slug]) }}" class="text-decoration-none">
                                <li class="list-group-item module-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-layer-group module-icon text-primary me-2"></i>
                                            <span class="module-name">Semua Modul</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $combinedDocuments->count() }}</span>
                                    </div>
                                </li>
                            </a>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="moduleFilter" class="form-label">Filter berdasarkan Modul:</label>
            <select class="form-select" id="moduleFilter">
                <option value="all" {{ $selectedModule == 'all' ? 'selected' : '' }}>Semua Modul</option>
                <option value="document-management" {{ $selectedModule == 'document-management' ? 'selected' : '' }}>Manajemen Dokumen</option>
                <option value="risk-management" {{ $selectedModule == 'risk-management' ? 'selected' : '' }}>Manajemen Risiko</option>
                <option value="correspondence" {{ $selectedModule == 'correspondence' ? 'selected' : '' }}>Korespondensi</option>
                <option value="spo" {{ $selectedModule == 'spo' ? 'selected' : '' }}>SPO</option>
            </select>
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
                            $isDocument = $document instanceof \App\Models\Document;
                            $isRiskReport = $document instanceof \App\Models\RiskReport;
                            $isCorrespondence = $document instanceof \App\Models\Correspondence;
                            $isSPO = $document instanceof \App\Models\SPO;

                            $detailRoute = '#';
                            $iconClass = 'fas fa-file-alt text-secondary';
                            $typeBadge = '<span class="badge bg-secondary">Unknown</span>';
                            $docTitle = $document->document_title ?? $document->title ?? 'Judul Tidak Tersedia';
                            $docNumber = $document->document_number ?? $document->number ?? 'No. Dokumen tidak tersedia';

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
                                                        $level = strtolower($document->confidentiality_level);
                                                        $bgColor = 'bg-secondary';
                                                        if ($level == 'public' || $level == 'publik') {
                                                            $bgColor = 'bg-success';
                                                        } elseif ($level == 'internal') {
                                                            $bgColor = 'bg-warning text-dark';
                                                        } elseif ($level == 'confidential' || $level == 'rahasia') {
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

@push('scripts')
<script>
    document.getElementById('moduleFilter').addEventListener('change', function() {
        const selectedModule = this.value;
        const currentUrl = new URL(window.location.href);
        
        if (selectedModule === 'all') {
            currentUrl.searchParams.delete('module');
        } else {
            currentUrl.searchParams.set('module', selectedModule);
        }
        
        window.location.href = currentUrl.toString();
    });
</script>
@endpush 