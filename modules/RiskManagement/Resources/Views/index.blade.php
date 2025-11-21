@extends('layouts.app')

@section('title', ' | Risk Management')

@section('header')
<!-- Kosong untuk menghilangkan judul halaman default -->
@endsection

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        border: none;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .action-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 2rem;
        background-size: cover;
        background-position: center;
        position: relative;
        z-index: 1;
        color: white;
    }
    
    .action-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: -1;
    }
    
    .action-card.new-report {
        background-image: url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');
    }
    
    .action-card.view-reports {
        background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');
    }
    
    .action-card.dashboard {
        background-image: url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');
    }
    
    .action-card .icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .action-card h3 {
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .action-card p {
        margin-bottom: 1.5rem;
        opacity: 0.9;
    }
    
    .highlight-card {
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        color: white;
        border-radius: 1rem;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .highlight-card h2 {
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }
    
    .highlight-card p {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
    
    .highlight-card .overlay-icon {
        position: absolute;
        right: -30px;
        bottom: -30px;
        font-size: 12rem;
        opacity: 0.1;
        z-index: 1;
    }
    
    .stat-card {
        position: relative;
        padding: 1.5rem;
        border-radius: 1rem;
        overflow: hidden;
        height: 100%;
    }
    
    .stat-card .stat-icon {
        position: absolute;
        right: 1rem;
        top: 1rem;
        font-size: 2.5rem;
        opacity: 0.15;
    }
    
    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-card .stat-label {
        font-size: 1rem;
        opacity: 0.8;
    }
    
    .bg-risk-low {
        background-color: #10B981;
        color: white;
    }
    
    .bg-risk-medium {
        background-color: #F59E0B;
        color: white;
    }
    
    .bg-risk-high {
        background-color: #EF4444;
        color: white;
    }
    
    .quick-access-button {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: white;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        text-decoration: none;
        color: #1F2937;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .quick-access-button:hover {
        transform: translateX(5px);
        background-color: #F9FAFB;
        color: #4F46E5;
    }
    
    .quick-access-button .quick-icon {
        margin-right: 1rem;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .badge-count {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        background-color: #4F46E5;
        color: white;
        margin-left: auto;
    }
    
    .risk-summary {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
    }
    
    .risk-summary .risk-type {
        text-align: center;
        padding: 0.5rem;
        border-radius: 0.5rem;
        width: 32%;
    }
    
    .recent-table th {
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    
    .recent-table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-open {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }
    
    .status-review {
        background-color: rgba(245, 158, 11, 0.1);
        color: #F59E0B;
    }
    
    .status-resolved {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    
    /* Tambahan style untuk breadcrumb */
    .breadcrumb-container {
        padding: 0;
        margin-bottom: 1rem;
    }
    
    .breadcrumb-item {
        font-size: 0.9rem;
    }
    
    .breadcrumb-item.active {
        font-weight: 600;
        color: #4F46E5;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manajemen Risiko</h2>
        <div>
            <!-- Debug information -->
            @if(auth()->check())
            <div class="alert alert-info mb-2">
                Role: {{ auth()->user()->role->slug ?? 'Tidak ada role' }} | 
                UserID: {{ auth()->id() }} |
                Raw Role Slug: '{{ auth()->user()->role->slug ?? 'none' }}'
            </div>
            @endif
            
            @php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant-admin' || 
                                  strtolower($userRole) === 'tenant-admin';
            @endphp
            
            @if(auth()->user()->role && $isTenantAdmin)
            <a href="{{ route('modules.risk-management.analysis-config') }}" class="btn btn-secondary">
                <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
            </a>
            @endif
            <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="btn btn-primary ms-2">
                <i class="fas fa-plus-circle me-1"></i> Laporan Baru
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Risk Management</li>
        </ol>
    </nav>

    <!-- Welcome Banner -->
    <div class="highlight-card mb-4">
        <h2>Manajemen dan Pemantauan Risiko</h2>
        <p>Kelola dan pantau risiko dengan efektif untuk meningkatkan keamanan dan kualitas layanan.</p>
        <div class="d-flex gap-3">
            <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="btn btn-light">
                <i class="fas fa-plus-circle me-2"></i> Buat Laporan Baru
            </a>
            <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-outline-light">
                <i class="fas fa-list me-2"></i> Lihat Semua Laporan
            </a>
            <a href="{{ route('modules.risk-management.dashboard') }}" class="btn btn-outline-light">
                <i class="fas fa-chart-pie me-2"></i> Dashboard Analitik
            </a>
        </div>
        <i class="fas fa-shield-alt overlay-icon"></i>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-8 mb-4">
            <div class="dashboard-card">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-chart-line me-2"></i> Ringkasan Status</h5>
                    
                    <!-- Dummy data for demonstration -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-risk-high">
                                <i class="fas fa-exclamation-circle stat-icon"></i>
                                <div class="stat-value">8</div>
                                <div class="stat-label">Open</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-risk-medium">
                                <i class="fas fa-sync stat-icon"></i>
                                <div class="stat-value">12</div>
                                <div class="stat-label">In Review</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-risk-low">
                                <i class="fas fa-check-circle stat-icon"></i>
                                <div class="stat-value">27</div>
                                <div class="stat-label">Resolved</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="risk-summary mt-4">
                        <div class="risk-type bg-light">
                            <h6 class="mb-2 text-success fw-bold">Risiko Rendah</h6>
                            <h4>18</h4>
                        </div>
                        <div class="risk-type bg-light">
                            <h6 class="mb-2 text-warning fw-bold">Risiko Sedang</h6>
                            <h4>22</h4>
                        </div>
                        <div class="risk-type bg-light">
                            <h6 class="mb-2 text-danger fw-bold">Risiko Tinggi</h6>
                            <h4>7</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-bolt me-2"></i> Akses Cepat</h5>
                    
                    <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="quick-access-button">
                        <div class="quick-icon bg-primary">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>Buat Laporan Baru</span>
                    </a>
                    
                    <a href="{{ route('modules.risk-management.risk-reports.index') }}?status=open" class="quick-access-button">
                        <div class="quick-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <span>Laporan Open</span>
                        <span class="badge-count">8</span>
                    </a>
                    
                    <a href="{{ route('modules.risk-management.risk-reports.index') }}?status=in_review" class="quick-access-button">
                        <div class="quick-icon bg-warning">
                            <i class="fas fa-sync"></i>
                        </div>
                        <span>Dalam Review</span>
                        <span class="badge-count">12</span>
                    </a>
                    
                    <a href="{{ route('modules.risk-management.dashboard') }}" class="quick-access-button">
                        <div class="quick-icon bg-info">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span>Statistik & Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <div class="dashboard-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-white p-4 border-0">
            <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i> Laporan Terbaru</h5>
            <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list me-1"></i> Lihat Semua
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover recent-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Judul</th>
                            <th>Kategori</th>
                            <th>Tingkat Risiko</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dummy data for demonstration -->
                        <tr>
                            <td class="ps-4 fw-medium">Kesalahan Pemberian Obat Pasien</td>
                            <td>Medis</td>
                            <td><span class="badge bg-danger">Tinggi</span></td>
                            <td><span class="status-badge status-open">Open</span></td>
                            <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Kebocoran Air di Ruang Farmasi</td>
                            <td>Fasilitas</td>
                            <td><span class="badge bg-warning text-dark">Sedang</span></td>
                            <td><span class="status-badge status-review">In Review</span></td>
                            <td>{{ now()->subDays(3)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Gangguan Sistem Informasi</td>
                            <td>Non-medis</td>
                            <td><span class="badge bg-success">Rendah</span></td>
                            <td><span class="status-badge status-resolved">Resolved</span></td>
                            <td>{{ now()->subDays(5)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Kerusakan Lift Pasien</td>
                            <td>Fasilitas</td>
                            <td><span class="badge bg-warning text-dark">Sedang</span></td>
                            <td><span class="status-badge status-review">In Review</span></td>
                            <td>{{ now()->subDays(7)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">Prosedur Keselamatan Tidak Diikuti</td>
                            <td>Keselamatan</td>
                            <td><span class="badge bg-danger">Tinggi</span></td>
                            <td><span class="status-badge status-open">Open</span></td>
                            <td>{{ now()->subDays(8)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center py-3 border-top">
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-right me-1"></i> Lihat Semua Laporan
                </a>
            </div>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i> Tip Manajemen Risiko</h5>
                    <p class="mb-1">Manajemen risiko yang efektif melibatkan:</p>
                    <ul class="mb-0">
                        <li class="mb-2">Identifikasi risiko secara proaktif</li>
                        <li class="mb-2">Analisis dan evaluasi tingkat risiko</li>
                        <li class="mb-2">Implementasi pengendalian yang tepat</li>
                        <li class="mb-2">Pemantauan dan peninjauan berkelanjutan</li>
                        <li class="mb-0">Dokumentasi yang baik untuk audit dan pembelajaran</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-book me-2 text-info"></i> Sumber Daya</h5>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>
                            <span>Panduan Manajemen Risiko</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0">
                            <i class="fas fa-file-alt me-2 text-primary"></i>
                            <span>Formulir Penilaian Risiko</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0">
                            <i class="fas fa-video me-2 text-success"></i>
                            <span>Tutorial Pelaporan Risiko</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0">
                            <i class="fas fa-question-circle me-2 text-info"></i>
                            <span>FAQ Manajemen Risiko</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // JavaScript untuk interaktifitas dapat ditambahkan di sini
});
</script>
@endpush 