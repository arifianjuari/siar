@extends('layouts.app')

@section('title', 'Dashboard Manajemen Dokumen')

@php
$hideDefaultHeader = true;
@endphp

@push('styles')
<style>
    .kpi-card {
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        border: none;
        box-shadow: 0 3px 5px rgba(0,0,0,0.05);
    }
    
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    
    .kpi-card .card-body {
        padding: 1rem;
    }
    
    .kpi-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .kpi-label {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-bottom: 0;
    }
    
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    .doc-item {
        border-left: 3px solid #eee;
        transition: all 0.2s;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    
    .doc-item:hover {
        background-color: rgba(0,0,0,0.02);
        border-left-color: #0d6efd;
    }
    
    .doc-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .doc-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .doc-category {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        background-color: #f0f0f0;
        color: #444;
        font-weight: 500;
    }
    
    .bg-confidential {
        background: linear-gradient(45deg, #dc3545, #e17982);
        color: white;
    }
    
    .bg-internal {
        background: linear-gradient(45deg, #fd7e14, #ffb380);
        color: white;
    }
    
    .bg-public {
        background: linear-gradient(45deg, #198754, #28a975);
        color: white;
    }
    
    .bg-risk {
        background: linear-gradient(45deg, #0d6efd, #50a3ff);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard Manajemen Dokumen</h2>
            <p class="text-muted mb-0">Pantau dan kelola dokumen dari berbagai sumber</p>
        </div>
        <div>
            <a href="{{ route('modules.document-management.documents.create') }}" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Tambah Dokumen Baru
            </a>
            <a href="{{ route('modules.document-management.documents.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Daftar Dokumen
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <i class="fas fa-file-alt kpi-icon text-primary"></i>
                    <h2 class="kpi-value">{{ $stats['total'] }}</h2>
                    <p class="kpi-label">Total Dokumen</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-public">
                <div class="card-body">
                    <i class="fas fa-globe kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['public'] }}</h2>
                    <p class="kpi-label">Dokumen Publik</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-internal">
                <div class="card-body">
                    <i class="fas fa-building kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['internal'] }}</h2>
                    <p class="kpi-label">Dokumen Internal</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-confidential">
                <div class="card-body">
                    <i class="fas fa-lock kpi-icon"></i>
                    <h2 class="kpi-value">{{ $stats['confidential'] }}</h2>
                    <p class="kpi-label">Dokumen Rahasia</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Documents by Category -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Dokumen berdasarkan Modul</h5>
                </div>
                <div class="card-body">
                    @if(isset($moduleCategories) && count($moduleCategories) > 0)
                        <ul class="list-group">
                            @foreach($moduleCategories as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas {{ $category['icon'] }} me-2 text-{{ $category['color'] }}"></i>
                                        {{ $category['module'] }}
                                    </div>
                                    <span class="badge bg-{{ $category['color'] }} rounded-pill">{{ $category['total'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada dokumen yang dikelompokkan berdasarkan modul.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Pastikan data dokumen sudah memiliki document_number
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Latest Documents -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Dokumen Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($latestDocuments->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($latestDocuments as $document)
                                @php
                                    $detailRoute = $document instanceof \App\Models\Document
                                        ? route('modules.document-management.documents.show', $document->id)
                                        : route('modules.risk-management.risk-reports.show', $document->id);
                                @endphp
                                <li class="list-group-item px-0 py-2 border-bottom">
                                    <a href="{{ $detailRoute }}" class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            @if(Str::startsWith($document->document_number ?? '', 'RIR-') || Str::startsWith($document->document_number ?? '', 'LR/'))
                                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            @else
                                                <i class="fas fa-file-alt text-primary me-2"></i>
                                            @endif
                                            <div>
                                                <span class="fw-medium text-dark">{{ $document->document_title }}</span>
                                                <span class="text-muted ms-2">({{ $document->document_number }})</span>
                                                @if($document->file_path)
                                                    <span class="badge bg-success ms-1">📎</span>
                                                @endif
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada dokumen yang diunggah.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Pastikan telah menambahkan dokumen dengan document_date yang benar
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popular Tags -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Tag Populer</h5>
                </div>
                <div class="card-body">
                    @if(isset($popularTags) && $popularTags->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($popularTags as $tag)
                                <a href="{{ route('modules.document-management.documents-by-tag', $tag->slug) }}" 
                                   class="btn btn-outline-primary rounded-pill btn-sm" 
                                   style="font-size: calc(0.7rem + {{ min($tag->document_count/5, 0.5) }}rem)">
                                    <i class="fas fa-tag me-1"></i>
                                    {{ $tag->name }}
                                    <span class="badge bg-primary rounded-pill ms-1">{{ $tag->document_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Belum ada tag yang digunakan pada dokumen.</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Tag membantu mengorganisasi dokumen Anda berdasarkan topik
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 