@extends('layouts.app')

@section('title', 'Profil Unit Kerja')

@php
$hideDefaultHeader = true;
@endphp

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
        height: 100%;
        border-radius: 0.5rem;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.1);
    }
    .dashboard-card.primary {
        border-left-color: #4F46E5;
    }
    .dashboard-card.success {
        border-left-color: #10B981;
    }
    .dashboard-card.warning {
        border-left-color: #F59E0B;
    }
    .dashboard-card.danger {
        border-left-color: #EF4444;
    }
    .dashboard-card.info {
        border-left-color: #3B82F6;
    }
    .profile-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .profile-cover {
        min-height: 120px;
        background-color: #4F46E5;
        background-image: linear-gradient(135deg, #48453b 0%, #a7772f 100%);
        position: relative;
        border-radius: 0.75rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        color: #fff;
    }
    .profile-cover-content {
        flex-grow: 1;
        margin-left: 1rem;
    }
    .profile-cover-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
    }
    .profile-cover .btn {
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        transition: background-color 0.2s ease;
    }
    .profile-cover .btn:hover {
        background-color: rgba(255, 255, 255, 0.3);
        color: #fff;
    }
    .activity-item {
        position: relative;
        padding-left: 25px;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        transition: background-color 0.2s ease-in-out;
    }
    .activity-item:hover {
        background-color: #f8f9fa;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    .activity-item::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 18px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #6366F1;
        border: 2px solid #fff;
    }
    .activity-item:last-child::before {
        height: 18px;
    }
    .module-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
    }
    .card-header {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .card-footer {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .list-group-item:last-child {
        border-bottom: none;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Profil Unit Kerja -->
    <div class="profile-cover rounded-3 mb-3">
        <div class="profile-cover-actions">
            <a href="{{ route('work-units.edit', $workUnit->id) }}" class="btn btn-sm" title="Edit Unit">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#contactModal" title="Kontak">
                <i class="fas fa-address-book"></i>
            </a>
            <a href="{{ route('work-units.index') }}" class="btn btn-sm" title="Kembali ke Daftar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        
        <div class="d-flex align-items-center">
            <img src="{{ $workUnit->headOfUnit && $workUnit->headOfUnit->profile_photo ? asset('storage/'.$workUnit->headOfUnit->profile_photo) : asset('images/default-avatar.png') }}" alt="Profile" class="profile-img">
            
            <div class="profile-cover-content">
                <h1 class="text-white mb-0 fw-bold">{{ $workUnit->unit_name }}</h1>
                <p class="text-white-50 mb-0">
                    <span class="badge bg-white text-primary me-2">{{ $workUnit->headOfUnit->name ?? 'Kepala Unit Belum Ditentukan' }}</span>
                    <span><i class="fas fa-sitemap me-1"></i> {{ ucfirst($workUnit->unit_type) }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ringkasan Aktivitas Unit (Lajur Kiri) -->
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">Ringkasan Aktivitas Unit</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <!-- Modul Risiko -->
                        <div class="col-md-12">
                            <div class="card dashboard-card h-100 border-0 shadow-sm warning">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2 bg-warning bg-opacity-10 text-warning module-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-0 small">Risiko</h6>
                                            <h4 class="mb-0">{{ $riskStats['total'] ?? 0 }}</h4>
                                        </div>
                                    </div>
                                    <a href="{{ route('modules.risk-management.risk-reports.index') }}?work_unit_id={{ $workUnit->id }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modul Korespondensi -->
                        <div class="col-md-12">
                            <div class="card dashboard-card h-100 border-0 shadow-sm info">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2 bg-info bg-opacity-10 text-info module-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-0 small">Korespondensi</h6>
                                            <h4 class="mb-0">{{ $correspondenceStats['total'] ?? 0 }}</h4>
                                        </div>
                                    </div>
                                    <a href="{{ route('modules.correspondence.letters.index') }}?work_unit_id={{ $workUnit->id }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modul Dokumen -->
                        <div class="col-md-12">
                            <div class="card dashboard-card h-100 border-0 shadow-sm primary">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2 bg-primary bg-opacity-10 text-primary module-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-0 small">Dokumen</h6>
                                            <h4 class="mb-0">{{ $documentStats['total'] ?? 0 }}</h4>
                                        </div>
                                    </div>
                                    <a href="{{ route('modules.document-management.documents.index') }}?work_unit_id={{ $workUnit->id }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Manajemen Risiko -->
            <div class="card border-0 shadow-sm h-100 mb-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Manajemen Risiko Terbaru</h5>
                    <a href="{{ route('modules.risk-management.risk-reports.index') }}?work_unit_id={{ $workUnit->id }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($riskReports) && $riskReports->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($riskReports as $report)
                            <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="list-group-item px-3 py-2 text-decoration-none">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-medium text-dark">{{ Str::limit($report->document_title, 50) }}</span>
                                    <span class="badge {{ $report->status == 'open' ? 'bg-primary' : ($report->status == 'in_review' ? 'bg-warning' : 'bg-success') }} rounded-pill ms-2">{{ $report->status == 'open' ? 'Terbuka' : ($report->status == 'in_review' ? 'Ditinjau' : 'Selesai') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted me-2">{{ $report->created_at->format('d M Y') }}</small>
                                        @php
                                            $riskClass = 'bg-success';
                                            $riskLevel = strtolower($report->risk_level);
                                            if (in_array($riskLevel, ['sedang', 'medium'])) {
                                                $riskClass = 'bg-warning';
                                            } elseif (in_array($riskLevel, ['tinggi', 'high'])) {
                                                $riskClass = 'bg-danger';
                                            } elseif (in_array($riskLevel, ['ekstrem', 'extreme'])) {
                                                $riskClass = 'bg-dark';
                                            }
                                        @endphp
                                        <span class="badge {{ $riskClass }} rounded-pill">{{ $report->risk_level }}</span>
                                    </div>
                                    @if($report->analysis)
                                        <span class="badge bg-info rounded-pill text-white"><i class="fas fa-microscope me-1"></i>Dianalisis</span>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-lg mb-2"></i>
                            <p class="small">Belum ada laporan risiko untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Korespondensi -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Korespondensi Terbaru</h5>
                    <a href="{{ route('modules.correspondence.letters.index') }}?work_unit_id={{ $workUnit->id }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($correspondences) && $correspondences->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($correspondences as $correspondence)
                            <a href="{{ route('modules.correspondence.letters.show', $correspondence->id) }}" class="list-group-item px-3 py-2 text-decoration-none">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-medium text-dark">{{ Str::limit($correspondence->subject ?? $correspondence->document_title ?? 'Surat #' . $correspondence->id, 50) }}</span>
                                    @if(isset($correspondence->document_number))
                                        <span class="badge bg-light text-dark rounded-pill ms-2">{{ $correspondence->document_number }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $correspondence->document_date ? $correspondence->document_date->format('d M Y') : $correspondence->created_at->format('d M Y') }}</small>
                                    <div>
                                        @if(isset($correspondence->document_type))
                                            <span class="badge bg-light text-dark rounded-pill me-1">{{ $correspondence->document_type }}</span>
                                        @endif
                                        @if(isset($correspondence->type))
                                            @if($correspondence->type == 'incoming')
                                                <span class="badge bg-primary rounded-pill">Masuk</span>
                                            @else
                                                <span class="badge bg-info rounded-pill">Keluar</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-envelope fa-lg mb-2"></i>
                            <p class="small">Belum ada surat untuk periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Aktivitas Terbaru (Lajur Kanan) -->
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> {{ $periodLabel ?? 'Filter' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="{{ route('work-units.dashboard', $workUnit->id) }}?period=all">Semua Data</a></li>
                            <li><a class="dropdown-item" href="{{ route('work-units.dashboard', $workUnit->id) }}?period=this_month">Bulan Ini</a></li>
                            <li><a class="dropdown-item" href="{{ route('work-units.dashboard', $workUnit->id) }}?period=last_month">Bulan Lalu</a></li>
                            <li><a class="dropdown-item" href="{{ route('work-units.dashboard', $workUnit->id) }}?period=this_year">Tahun Ini</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0" style="height: calc(100vh - 280px); overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $activity)
                            <a href="{{ $activity->url ?? '#' }}" class="list-group-item activity-item p-3 text-decoration-none">
                                <div class="d-flex align-items-start">
                                    <div class="ms-2 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-medium text-dark">{{ $activity->title }}</h6>
                                            <small class="text-muted flex-shrink-0 ms-2">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted small">{{ $activity->description }}</p>
                                        <div>
                                            <span class="badge bg-light text-dark me-1">{{ $activity->module }}</span>
                                            @if(isset($activity->status))
                                                <span class="badge {{ $activity->status_class }} rounded-pill">{{ $activity->status_text }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-info-circle text-muted fs-1 mb-3"></i>
                                <p class="text-muted">Belum ada aktivitas tercatat untuk periode ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kontak -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">Kontak Unit Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="fw-medium">{{ $workUnit->unit_name }}</div>
                    <div class="text-muted">{{ $workUnit->unit_code }}</div>
                </div>
                
                @if($workUnit->headOfUnit)
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-shrink-0">
                        <img src="{{ $workUnit->headOfUnit->profile_photo ? asset('storage/'.$workUnit->headOfUnit->profile_photo) : asset('images/default-avatar.png') }}" alt="Profile" width="50" height="50" class="rounded-circle">
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">{{ $workUnit->headOfUnit->name }}</h6>
                        <div class="text-muted small">Kepala Unit</div>
                    </div>
                </div>
                @endif
                
                <div class="mb-3">
                    <div class="text-muted mb-2">Detail Kontak</div>
                    <div class="mb-2">
                        <i class="fas fa-envelope me-2 text-muted"></i>
                        <span>{{ $workUnit->email ?? ($workUnit->headOfUnit->email ?? 'Email tidak tersedia') }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-phone me-2 text-muted"></i>
                        <span>{{ $workUnit->phone ?? ($workUnit->headOfUnit->phone ?? 'Telepon tidak tersedia') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                        <span>{{ $workUnit->address ?? 'Alamat tidak tersedia' }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush 