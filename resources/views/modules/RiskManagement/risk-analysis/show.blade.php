@extends('layouts.app')

@section('title', ' | Detail Analisis Risiko')

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
    
    .section-heading {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #495057;
        display: flex;
        align-items: center;
    }
    
    .section-heading i {
        margin-right: 0.5rem;
        opacity: 0.7;
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
    
    .info-label {
        color: #6c757d;
        font-weight: 500;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        margin-bottom: 1rem;
    }
    
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    .status-badge.draft {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    .status-badge.in-progress {
        background-color: #cff4fc;
        color: #055160;
    }
    
    .status-badge.completed {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .status-badge.reviewed {
        background-color: #e0cffc;
        color: #330072;
    }
    
    .factor-category {
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .factor-list {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 1rem;
    }
    
    .factor-item {
        padding: 0.3rem 0;
        display: flex;
        align-items: center;
    }
    
    .factor-item i {
        color: #0d6efd;
        margin-right: 0.5rem;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
        color: #6c757d;
    }
    
    .breadcrumb-item.active {
        color: #495057;
        font-weight: 600;
    }
    
    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
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
            <li class="breadcrumb-item active" aria-current="page">Detail Analisis</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Analisis Kasus</h2>
            <p class="text-muted mb-0">Detail Analisis untuk Laporan Risiko</p>
        </div>
        <div>
            <a href="{{ route('modules.risk-management.risk-analysis.edit', [$report->id, $analysis->id]) }}" class="btn btn-primary action-btn me-2">
                <i class="fas fa-edit me-1"></i> Edit Analisis
            </a>
            <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-outline-secondary action-btn">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Laporan
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Detail Laporan -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-alt me-2"></i> Laporan Risiko
                    </div>
                    <span>
                        @if($report->risk_level == 'high')
                        <span class="risk-badge risk-high">Risiko Tinggi</span>
                        @elseif($report->risk_level == 'medium')
                        <span class="risk-badge risk-medium">Risiko Sedang</span>
                        @else
                        <span class="risk-badge risk-low">Risiko Rendah</span>
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold mb-3">{{ $report->risk_title }}</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-label">Kategori</div>
                            <div class="info-value">{{ $report->risk_category }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Unit Pelapor</div>
                            <div class="info-value">{{ $report->reporter_unit }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Kejadian</div>
                            <div class="info-value">{{ $report->occurred_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="info-label">Kronologi</div>
                        <p class="info-value mb-0">{{ $report->chronology }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Analisis Penyebab -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-search me-2"></i> Analisis Penyebab
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="section-heading"><i class="fas fa-arrow-right"></i> Penyebab Langsung</h6>
                        <p>{{ $analysis->direct_cause }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="section-heading"><i class="fas fa-sitemap"></i> Akar Masalah</h6>
                        <p>{{ $analysis->root_cause }}</p>
                    </div>
                    
                    <div>
                        <h6 class="section-heading"><i class="fas fa-puzzle-piece"></i> Faktor Kontributor</h6>
                        
                        @if($analysis->contributor_factors && count($analysis->contributor_factors) > 0)
                            @foreach($analysis->contributor_factors as $category => $factors)
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
                                
                                <ul class="factor-list">
                                    @foreach($factors as $factor)
                                        <li class="factor-item">
                                            <i class="fas fa-check-circle"></i>
                                            
                                            @php
                                                $factorLabels = [
                                                    'policies_procedures' => 'Kebijakan dan prosedur tidak jelas',
                                                    'staffing' => 'Kekurangan staf',
                                                    'workload' => 'Beban kerja berlebihan',
                                                    'communication' => 'Komunikasi tidak efektif',
                                                    'leadership' => 'Pengawasan/kepemimpinan tidak memadai',
                                                    'resources' => 'Sumber daya yang terbatas',
                                                    'knowledge' => 'Pengetahuan yang tidak memadai',
                                                    'skills' => 'Keterampilan yang tidak mencukupi',
                                                    'fatigue' => 'Kelelahan',
                                                    'stress' => 'Stres',
                                                    'distraction' => 'Gangguan/Distraksi',
                                                    'complacency' => 'Sikap terlalu percaya diri',
                                                    'equipment_failure' => 'Kegagalan peralatan',
                                                    'software_issues' => 'Masalah perangkat lunak',
                                                    'design_flaws' => 'Kesalahan desain',
                                                    'maintenance' => 'Pemeliharaan yang tidak memadai',
                                                    'compatibility' => 'Masalah kompatibilitas',
                                                    'physical_environment' => 'Lingkungan fisik tidak aman',
                                                    'noise' => 'Kebisingan',
                                                    'lighting' => 'Pencahayaan tidak memadai',
                                                    'temperature' => 'Suhu tidak sesuai',
                                                    'space_constraints' => 'Keterbatasan ruang'
                                                ];
                                            @endphp
                                            
                                            {{ $factorLabels[$factor] ?? $factor }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">Tidak ada faktor kontributor yang diidentifikasi.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Rekomendasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-lightbulb me-2"></i> Rekomendasi
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="section-heading"><i class="fas fa-bolt"></i> Jangka Pendek (0-3 Bulan)</h6>
                        <p>{{ $analysis->recommendation_short }}</p>
                    </div>
                    
                    @if(!empty($analysis->recommendation_medium))
                    <div class="mb-4">
                        <h6 class="section-heading"><i class="fas fa-clock"></i> Jangka Menengah (3-6 Bulan)</h6>
                        <p>{{ $analysis->recommendation_medium }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($analysis->recommendation_long))
                    <div>
                        <h6 class="section-heading"><i class="fas fa-calendar-alt"></i> Jangka Panjang (6+ Bulan)</h6>
                        <p class="mb-0">{{ $analysis->recommendation_long }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Status Analisis -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informasi Analisis
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        @php
                            $statusClass = '';
                            switch($analysis->analysis_status) {
                                case 'draft':
                                    $statusClass = 'draft';
                                    break;
                                case 'in_progress':
                                case 'reviewed':
                                    $statusClass = 'in-progress';
                                    break;
                                case 'completed':
                                    $statusClass = 'completed';
                                    break;
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $analysis->status_label }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Dianalisis Oleh</div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            <span>{{ $analysis->analyst->name ?? 'Tidak diketahui' }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Tanggal Analisis</div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>
                            <span>{{ $analysis->analyzed_at ? $analysis->analyzed_at->format('d M Y, H:i') : 'Belum dianalisis' }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Terakhir Diperbarui</div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <span>{{ $analysis->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('modules.risk-management.risk-analysis.edit', [$report->id, $analysis->id]) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Analisis
                        </a>
                        <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Laporan
                        </a>
                        
                        @if($analysis->analysis_status == 'completed')
                        <a href="{{ route('modules.risk-management.risk-analysis.qr-code', [$report->id, $analysis->id]) }}" class="btn btn-dark" target="_blank">
                            <i class="fas fa-qrcode me-1"></i> Generate QR Code Tanda Tangan
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i> Timeline
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">Laporan Dibuat</div>
                            <div class="small text-muted">{{ $report->created_at->format('d M Y, H:i') }}</div>
                            <div class="small">oleh {{ $report->creator->name ?? 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">Analisis Dibuat</div>
                            <div class="small text-muted">{{ $analysis->created_at->format('d M Y, H:i') }}</div>
                            <div class="small">oleh {{ $analysis->analyst->name ?? 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                    
                    @if($report->reviewed_at)
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">Laporan Ditinjau</div>
                            <div class="small text-muted">{{ $report->reviewed_at->format('d M Y, H:i') }}</div>
                            <div class="small">oleh {{ $report->reviewer->name ?? 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($report->approved_at)
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">Laporan Disetujui</div>
                            <div class="small text-muted">{{ $report->approved_at->format('d M Y, H:i') }}</div>
                            <div class="small">oleh {{ $report->approver->name ?? 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 