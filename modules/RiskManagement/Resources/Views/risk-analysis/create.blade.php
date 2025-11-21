@extends('layouts.app')

@section('title', ' | Buat Analisis Risiko')

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem 1.25rem;
        font-weight: 600;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .factor-category {
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .factor-option {
        margin-bottom: 0.25rem;
    }
    
    .factor-label {
        font-weight: normal;
        cursor: pointer;
    }
    
    .risk-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .risk-high {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .risk-medium {
        background-color: #fff3cd;
        color: #664d03;
    }
    
    .risk-low {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
        color: #6c757d;
    }
    
    .breadcrumb-item.active {
        color: #495057;
        font-weight: 600;
    }
    
    textarea.form-control {
        min-height: 6rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.risk-management.dashboard') }}">Manajemen Risiko</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.risk-management.risk-reports.index') }}">Daftar Laporan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}">{{ Str::limit($report->risk_title, 30) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buat Analisis</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Analisis Kasus</h2>
            <p class="text-muted mb-0">Analisis Mendalam untuk Laporan Risiko</p>
        </div>
        <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Laporan
        </a>
    </div>

    <!-- Form Analisis Risiko -->
    <form action="{{ route('modules.risk-management.risk-analysis.store', $report->id) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Detail Laporan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i> Detail Laporan Risiko
                    </div>
                    <div class="card-body">
                        <!-- Informasi Utama -->
                        <div class="border-bottom pb-3 mb-3">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Nomor Laporan</label>
                                <div class="form-control-plaintext fw-bold">{{ $report->document_number }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Judul Risiko</label>
                                <div class="form-control-plaintext fw-bold">{{ $report->document_title }}</div>
                            </div>
                        </div>

                        <!-- Informasi Risiko -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">INFORMASI RISIKO</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Tipe Risiko</label>
                                    <div class="form-control-plaintext">{{ $report->risk_type ?? '-' }}</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Kategori</label>
                                    <div class="form-control-plaintext">{{ $report->risk_category }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Tingkat Risiko</label>
                                    <div>
                                        @if(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high')
                                        <span class="risk-badge risk-high">Tinggi</span>
                                        @elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium')
                                        <span class="risk-badge risk-medium">Sedang</span>
                                        @else
                                        <span class="risk-badge risk-low">Rendah</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Dampak</label>
                                    <div class="form-control-plaintext">{{ $report->impact }}</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label text-muted small mb-1">Probabilitas</label>
                                    <div class="form-control-plaintext">{{ $report->probability }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Kejadian -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">DETAIL KEJADIAN</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Kronologi</label>
                                <div class="form-control-plaintext">{{ $report->chronology }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Deskripsi</label>
                                <div class="form-control-plaintext">{{ $report->description }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Tanggal Kejadian</label>
                                    <div class="form-control-plaintext">{{ $report->occurred_at ? $report->occurred_at->format('d M Y') : '-' }}</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Unit Pelapor</label>
                                    <div class="form-control-plaintext">{{ $report->reporter_unit }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tindakan dan Rekomendasi -->
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="text-muted mb-3">TINDAKAN & REKOMENDASI</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Tindakan Segera</label>
                                <div class="form-control-plaintext">{{ $report->immediate_action ?? '-' }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Rekomendasi</label>
                                <div class="form-control-plaintext">{{ $report->recommendation ?? '-' }}</div>
                            </div>
                        </div>

                        <!-- Status dan Tracking -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">STATUS & TRACKING</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Status Laporan</label>
                                <div>
                                    @switch($report->status)
                                        @case('Draft')
                                            <span class="badge bg-info">Draft</span>
                                            @break
                                        @case('Ditinjau')
                                            <span class="badge bg-warning">Dalam Peninjauan</span>
                                            @break
                                        @case('Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $report->status }}</span>
                                    @endswitch
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Dibuat Oleh</label>
                                    <div class="form-control-plaintext">
                                        {{ $report->creator ? $report->creator->name : '-' }}
                                        @if($report->created_at)
                                            <small class="text-muted d-block">{{ $report->created_at->format('d M Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($report->reviewer)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Ditinjau Oleh</label>
                                    <div class="form-control-plaintext">
                                        {{ $report->reviewer->name }}
                                        @if($report->reviewed_at)
                                            <small class="text-muted d-block">{{ $report->reviewed_at->format('d M Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($report->approver)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Disetujui Oleh</label>
                                    <div class="form-control-plaintext">
                                        {{ $report->approver->name }}
                                        @if($report->approved_at)
                                            <small class="text-muted d-block">{{ $report->approved_at->format('d M Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Lampiran -->
                        @if($report->documents && count($report->documents) > 0)
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">LAMPIRAN</h6>
                            <div class="list-group">
                                @foreach($report->documents as $document)
                                <a href="{{ route('modules.risk-management.risk-reports.download-attachment', [$report->id, $document->id]) }}" 
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-paperclip me-2"></i>{{ $document->original_name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Analisis Penyebab -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-search me-2"></i> Analisis Penyebab
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="direct_cause" class="form-label">Penyebab Langsung <span class="text-danger">*</span></label>
                            <textarea id="direct_cause" name="direct_cause" class="form-control @error('direct_cause') is-invalid @enderror" required>{{ old('direct_cause') }}</textarea>
                            @error('direct_cause')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Faktor atau kondisi yang secara langsung menyebabkan kejadian risiko.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="root_cause" class="form-label">Akar Masalah <span class="text-danger">*</span></label>
                            <textarea id="root_cause" name="root_cause" class="form-control @error('root_cause') is-invalid @enderror" required>{{ old('root_cause') }}</textarea>
                            @error('root_cause')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Analisis mendalam tentang akar masalah yang mendasari kejadian risiko.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Faktor Kontributor</label>
                            <small class="d-block text-muted mb-2">Pilih faktor-faktor yang berkontribusi pada terjadinya risiko.</small>
                            
                            <div class="row">
                                @foreach($contributorFactors as $category => $factors)
                                <div class="col-md-6">
                                    <div class="factor-category">
                                        @switch($category)
                                            @case('organizational')
                                                <i class="fas fa-sitemap me-1"></i> Faktor Organisasi
                                                @break
                                            @case('human_factors')
                                                <i class="fas fa-user me-1"></i> Faktor Manusia
                                                @break
                                            @case('technical')
                                                <i class="fas fa-tools me-1"></i> Faktor Teknis
                                                @break
                                            @case('environmental')
                                                <i class="fas fa-tree me-1"></i> Faktor Lingkungan
                                                @break
                                            @default
                                                <i class="fas fa-list me-1"></i> {{ Str::title(str_replace('_', ' ', $category)) }}
                                        @endswitch
                                    </div>
                                    
                                    @foreach($factors as $key => $label)
                                    <div class="factor-option form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="factor_{{ $category }}_{{ $key }}" 
                                               name="contributor_factors[{{ $category }}][]" 
                                               value="{{ $key }}"
                                               {{ in_array($key, old("contributor_factors.{$category}", [])) ? 'checked' : '' }}>
                                        <label class="form-check-label factor-label" for="factor_{{ $category }}_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rekomendasi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-lightbulb me-2"></i> Rekomendasi
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="recommendation_short" class="form-label">Rekomendasi Jangka Pendek <span class="text-danger">*</span></label>
                            <textarea id="recommendation_short" name="recommendation_short" class="form-control @error('recommendation_short') is-invalid @enderror" required>{{ old('recommendation_short') }}</textarea>
                            @error('recommendation_short')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tindakan segera untuk mengatasi masalah (0-3 bulan).</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommendation_medium" class="form-label">Rekomendasi Jangka Menengah</label>
                            <textarea id="recommendation_medium" name="recommendation_medium" class="form-control @error('recommendation_medium') is-invalid @enderror">{{ old('recommendation_medium') }}</textarea>
                            @error('recommendation_medium')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tindakan dalam 3-6 bulan ke depan.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommendation_long" class="form-label">Rekomendasi Jangka Panjang</label>
                            <textarea id="recommendation_long" name="recommendation_long" class="form-control @error('recommendation_long') is-invalid @enderror">{{ old('recommendation_long') }}</textarea>
                            @error('recommendation_long')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Perubahan sistem atau kebijakan jangka panjang (6+ bulan).</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Status Analisis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Status Analisis
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="analysis_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="analysis_status" name="analysis_status" class="form-select @error('analysis_status') is-invalid @enderror" required>
                                <option value="draft" {{ old('analysis_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="in_progress" {{ old('analysis_status') == 'in_progress' || old('analysis_status') == 'reviewed' ? 'selected' : '' }}>Ditinjau</option>
                                <option value="completed" {{ old('analysis_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('analysis_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Simpan Analisis
                        </button>
                        
                        <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-outline-secondary w-100">
                            Batal
                        </a>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Panduan Analisis Kasus</h6>
                        <p class="small mb-2">Analisis kasus membantu mengidentifikasi penyebab dan faktor yang berkontribusi pada terjadinya insiden, serta menentukan tindakan yang tepat untuk mencegah kejadian serupa di masa depan.</p>
                        <ul class="small ps-3 mb-0">
                            <li class="mb-1">Identifikasi penyebab langsung dan akar masalah</li>
                            <li class="mb-1">Pertimbangkan semua faktor yang berkontribusi</li>
                            <li class="mb-1">Buat rekomendasi yang SMART (Spesifik, Terukur, Dapat Dicapai, Relevan, dan Terikat Waktu)</li>
                            <li>Tetapkan prioritas tindakan berdasarkan dampak dan kemudahan implementasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kode JavaScript tambahan jika diperlukan
});
</script>
@endpush 