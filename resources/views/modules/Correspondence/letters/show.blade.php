@extends('layouts.app')

@php 
$hideDefaultHeader = true; 
@endphp

@section('title', '')

@push('styles')
<style>
    /* Menghilangkan garis horizontal dan padding atas */
    .content-wrapper main {
        padding-top: 0 !important;
    }
    .content-wrapper main .border-bottom {
        border-bottom: none !important;
    }
    .main-container {
        width: 100%;
        margin: 0.5rem auto;
        padding: 0 1rem;
    }
    .detail-card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background: #fff;
        border: none;
        margin-bottom: 1rem;
        width: 100%;
    }
    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .header-title {
        font-weight: 600;
        margin: 0;
        font-size: 1.1rem;
        color: #2c65e9;
    }
    .card-content {
        padding: 1rem 1.25rem;
    }
    .btn-action {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }
    .btn-action i {
        font-size: 0.8rem;
    }
    .detail-label {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        color: #444;
    }
    .detail-value {
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
    .action-btn-group {
        display: flex;
        gap: 0.5rem;
    }
    .document-content {
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 1rem;
        background-color: #f9f9f9;
        font-size: 0.95rem;
    }
</style>
<!-- CSRF Token untuk Semua Request AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="main-container">
    <!-- Notification sudah ditangani oleh layout utama -->
    <div class="row">
        <!-- Detail Surat -->
        <div class="col-lg-8">
            <div class="card detail-card">
                <div class="card-header-custom">
                    <h4 class="header-title">Informasi Surat</h4>
                    
                    <div class="action-btn-group">
                        <a href="{{ route('modules.correspondence.letters.index') }}" class="btn btn-sm btn-secondary btn-action">
                            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
                        </a>
                        
                        <a href="{{ route('modules.correspondence.letters.export-pdf', $correspondence->id) }}" class="btn btn-sm btn-primary btn-action">
                            <i class="fas fa-print fa-sm mr-1"></i> Cetak Surat
                        </a>
                        
                        @can('export', $correspondence)
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle btn-action" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download fa-sm mr-1"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('modules.correspondence.letters.export-pdf', $correspondence->id) }}">PDF</a>
                                <a class="dropdown-item" href="{{ route('modules.correspondence.letters.export-word', $correspondence->id) }}">Word</a>
                            </div>
                        </div>
                        @endcan
                        
                        <a href="{{ route('modules.correspondence.letters.edit', $correspondence->id) }}" class="btn btn-sm btn-warning btn-action">
                            <i class="fas fa-edit fa-sm mr-1"></i> Edit
                        </a>
                        
                        @can('delete', $correspondence)
                        <form action="{{ route('modules.correspondence.letters.destroy', $correspondence->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                                <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="card-content">
                    <div class="float-right">
                        <span class="badge {{ $correspondence->document_type == 'Regulasi' ? 'badge-primary' : 'badge-success' }}">
                            {{ $correspondence->document_type }}
                        </span>
                        <span class="badge 
                            {{ $correspondence->confidentiality_level == 'Publik' ? 'badge-info' : 
                              ($correspondence->confidentiality_level == 'Internal' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $correspondence->confidentiality_level }}
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Nomor Dokumen</p>
                            <p class="detail-value">{{ $correspondence->document_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Tanggal Dokumen</p>
                            <p class="detail-value">{{ $correspondence->document_date->format('d F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <p class="detail-label">Judul Dokumen</p>
                        <p class="detail-value font-weight-bold">{{ $correspondence->document_title }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="detail-label">Perihal</p>
                        <p class="detail-value">{{ $correspondence->subject }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Pengirim</p>
                            <p class="detail-value">{{ $correspondence->sender_name }}<br>
                            <small class="text-muted">{{ $correspondence->sender_position }}</small></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Penerima</p>
                            <p class="detail-value">{{ $correspondence->recipient_name }}<br>
                            <small class="text-muted">{{ $correspondence->recipient_position }}</small></p>
                        </div>
                    </div>
                    
                    @if($correspondence->cc_list)
                    <div class="mb-3">
                        <p class="detail-label">Tembusan</p>
                        <p class="detail-value">{{ $correspondence->cc_list }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <p class="detail-label">Isi Surat</p>
                        <div class="document-content">
                            {!! nl2br(e($correspondence->body)) !!}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Lokasi Penandatanganan</p>
                            <p class="detail-value">{{ $correspondence->signed_at_location }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="detail-label">Tanggal Penandatanganan</p>
                            <p class="detail-value">{{ $correspondence->signed_at_date->format('d F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <p class="detail-label">Penandatangan</p>
                            <p class="detail-value">
                                {{ $correspondence->signatory_name }}<br>
                                <small class="text-muted">{{ $correspondence->signatory_position }}</small>
                                @if($correspondence->signatory_rank)
                                <br><small class="text-muted">{{ $correspondence->signatory_rank }}</small>
                                @endif
                                @if($correspondence->signatory_nrp)
                                <br><small class="text-muted">NRP: {{ $correspondence->signatory_nrp }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($correspondence->reference_to)
                    <div class="mb-3">
                        <p class="detail-label">Referensi</p>
                        <p class="detail-value">{!! nl2br(e($correspondence->reference_to)) !!}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Dokumen yang Terhubung -->
            @if($correspondence->documents->count() > 0)
            <div class="card detail-card">
                <div class="card-header-custom">
                    <h4 class="header-title">Dokumen Terhubung</h4>
                </div>
                <div class="card-content">
                    <div class="list-group">
                        @foreach($correspondence->documents as $document)
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $document->document_title }}</h5>
                                <small>{{ $document->document_date->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($document->description, 100) }}</p>
                            <small class="text-muted">{{ $document->document_number }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Metadata -->
            <div class="card detail-card">
                <div class="card-header-custom">
                    <h4 class="header-title">Metadata</h4>
                </div>
                <div class="card-content">
                    <div class="mb-3">
                        <p class="detail-label">Versi Dokumen</p>
                        <p class="detail-value">{{ $correspondence->document_version }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="detail-label">Dibuat Oleh</p>
                        <p class="detail-value">{{ $correspondence->creator->name ?? 'Unknown' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="detail-label">Tanggal Pembuatan</p>
                        <p class="detail-value">{{ $correspondence->created_at->format('d F Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="detail-label">Tanggal Update Terakhir</p>
                        <p class="detail-value">{{ $correspondence->updated_at->format('d F Y H:i') }}</p>
                    </div>
                    
                    @if($correspondence->next_review)
                    <div class="mb-3">
                        <p class="detail-label">Jadwal Review Berikutnya</p>
                        <p class="detail-value">{{ $correspondence->next_review->format('d F Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- File Lampiran -->
            <div class="card detail-card">
                <div class="card-header-custom">
                    <h4 class="header-title">File Lampiran</h4>
                </div>
                <div class="card-content">
                    @if($correspondence->document_link)
                    <a href="{{ $correspondence->document_link }}" class="btn btn-primary btn-block" target="_blank">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Link Dokumen
                    </a>
                    <p class="small text-muted mt-2">Link menuju file dokumen yang tersimpan di cloud (Google Drive, dll)</p>
                    @elseif($correspondence->file_path)
                    <a href="{{ asset('storage/' . $correspondence->file_path) }}" class="btn btn-primary btn-block" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Download File
                    </a>
                    @else
                    <p class="text-center text-muted">Tidak ada file lampiran</p>
                    @endif
                </div>
            </div>
            
            <!-- QR Code -->
            <div class="card detail-card">
                <div class="card-header-custom">
                    <h4 class="header-title">QR Code</h4>
                </div>
                <div class="card-content text-center">
                    <object data="{{ route('modules.correspondence.letters.qr-code', $correspondence->id) }}" 
                         type="image/svg+xml" style="width: 200px; height: 200px;" class="mb-2">
                        QR Code - Browser Anda tidak mendukung SVG
                    </object>
                    <p class="small text-muted">Scan untuk melihat dokumen ini</p>
                    <a href="{{ route('modules.correspondence.letters.qr-code', $correspondence->id) }}" 
                       class="btn btn-sm btn-dark mt-2" target="_blank">
                        <i class="fas fa-qrcode mr-1"></i> Lihat QR Code
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Debug information
    console.log('Document loaded');
    
    // Fungsi lain mungkin ada di sini...
</script>
@endpush 