@extends('layouts.app')

@section('title', ' | Dashboard')

@php
$hideDefaultHeader = true;
@endphp

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
        height: 100%;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .dashboard-card.risk-low {
        border-left-color: #10B981;
    }
    .dashboard-card.risk-medium {
        border-left-color: #F59E0B;
    }
    .dashboard-card.risk-high {
        border-left-color: #EF4444;
    }
    .dashboard-card.risk-extreme {
        border-left-color: #7F1D1D;
    }
    .dashboard-card.corr-incoming {
        border-left-color: #3B82F6;
    }
    .dashboard-card.corr-outgoing {
        border-left-color: #8B5CF6;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .risk-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .risk-indicator.low {
        background-color: #10B981;
    }
    .risk-indicator.medium {
        background-color: #F59E0B;
    }
    .risk-indicator.high {
        background-color: #EF4444;
    }
    .risk-indicator.extreme {
        background-color: #7F1D1D;
    }
    .chart-container {
        height: 250px;
        position: relative;
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">Dashboard</h1>
                            <p class="text-muted mb-0">
                                <span class="badge bg-primary">{{ auth()->user()->tenant->name ?? 'System' }}</span>
                                <span class="ms-2"><i class="fas fa-user me-1"></i> {{ auth()->user()->name }}</span>
                                <span class="ms-2"><i class="fas fa-shield-alt me-1"></i> {{ auth()->user()->role->name ?? 'User' }}</span>
                            </p>
                        </div>
                        <button id="refreshDashboard" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('dashboard') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label for="period" class="col-form-label">Periode:</label>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select id="period" name="period" class="form-select">
                                <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="this_month" {{ request('period', 'this_month') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                                <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkasan -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2"></i> Statistik Ringkasan</h5>
        </div>
        
        <!-- Total Laporan Risiko -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card risk-low h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Laporan Risiko</h6>
                    <div class="stat-value text-dark mb-1">{{ $stats['risk_reports'] ?? 0 }}</div>
                    <div class="text-success small">
                        <i class="fas fa-check-circle me-1"></i> {{ $stats['risk_resolved'] ?? 0 }} terselesaikan
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Unit Kerja -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100" style="border-left-color: #8B5CF6;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Unit Kerja</h6>
                    <div class="stat-value text-dark mb-1">{{ $stats['work_units'] ?? 0 }}</div>
                    <div class="text-primary small">
                        <i class="fas fa-building me-1"></i> Sudah terdaftar
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Korespondensi -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card corr-incoming h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Korespondensi</h6>
                    <div class="stat-value text-dark mb-1">{{ $stats['correspondence'] ?? 0 }}</div>
                    <div class="text-primary small">
                        <i class="fas fa-calendar-alt me-1"></i> {{ $stats['correspondence_this_month'] ?? 0 }} dokumen bulan ini
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Users -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100" style="border-left-color: #6366F1;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Users</h6>
                    <div class="stat-value text-dark mb-1">{{ $stats['users'] ?? 0 }}</div>
                    <div class="text-primary small">
                        <i class="fas fa-users me-1"></i> Aktif dalam sistem
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik Visualisasi -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Distribusi Tingkat Risiko</h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar me-1"></i> {{ isset($periodLabel) ? $periodLabel : 'Semua data' }}
                        </span>
                    </div>
                    <div class="chart-container">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Dokumen Korespondensi</h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar me-1"></i> {{ isset($periodLabel) ? $periodLabel : 'Semua data' }}
                        </span>
                    </div>
                    <div class="chart-container">
                        <canvas id="correspondenceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Laporan Terbaru -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-file-alt me-2"></i> Laporan Risiko Terbaru</h5>
                    
                    @if(isset($latestReports) && count($latestReports) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nomor</th>
                                    <th>Judul</th>
                                    <th>Tingkat Risiko</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestReports as $report)
                                <tr>
                                    <td>{{ $report->reference_number }}</td>
                                    <td>{{ Str::limit($report->title, 50) }}</td>
                                    <td>
                                        @php
                                            $riskClass = '';
                                            $riskLabel = '';
                                            
                                            switch($report->risk_level) {
                                                case 'low':
                                                    $riskClass = 'bg-success';
                                                    $riskLabel = 'Rendah';
                                                    break;
                                                case 'medium':
                                                    $riskClass = 'bg-warning';
                                                    $riskLabel = 'Sedang';
                                                    break;
                                                case 'high':
                                                    $riskClass = 'bg-danger';
                                                    $riskLabel = 'Tinggi';
                                                    break;
                                                case 'extreme':
                                                    $riskClass = 'bg-dark';
                                                    $riskLabel = 'Ekstrem';
                                                    break;
                                                default:
                                                    $riskClass = 'bg-secondary';
                                                    $riskLabel = 'Belum dinilai';
                                            }
                                        @endphp
                                        <span class="badge {{ $riskClass }}">{{ $riskLabel }}</span>
                                    </td>
                                    <td>{{ $report->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($report->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($report->status === 'submitted')
                                            <span class="badge bg-info">Diajukan</span>
                                        @elseif($report->status === 'reviewed')
                                            <span class="badge bg-primary">Diulas</span>
                                        @elseif($report->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($report->status === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @elseif($report->status === 'implemented')
                                            <span class="badge bg-dark">Dijalankan</span>
                                        @elseif($report->status === 'monitored')
                                            <span class="badge bg-warning">Dimonitor</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $report->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('superadmin.risk-management.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Belum ada laporan risiko yang dibuat.
                    </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('superadmin.risk-management.reports.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Lihat Semua Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dokumentasi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-book me-2"></i> Panduan Singkat</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="mb-2"><i class="fas fa-file-alt me-2 text-primary"></i> Laporan Risiko</h6>
                                <p class="text-muted">
                                    Buat dan kelola laporan risiko untuk mencatat, menilai, dan menangani risiko yang diidentifikasi. 
                                    Untuk membuat laporan baru, gunakan menu Manajemen Risiko.
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="mb-2"><i class="fas fa-building me-2 text-primary"></i> Unit Kerja</h6>
                                <p class="text-muted">
                                    Kelola unit kerja dan anggotanya untuk mendelegasikan tanggung jawab dan tugas terkait manajemen risiko.
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="mb-2"><i class="fas fa-exchange-alt me-2 text-primary"></i> Korespondensi</h6>
                                <p class="text-muted">
                                    Dokumentasikan komunikasi terkait manajemen risiko, termasuk surat masuk dan surat keluar.
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="mb-2"><i class="fas fa-chart-line me-2 text-primary"></i> Analisis</h6>
                                <p class="text-muted">
                                    Dapatkan wawasan dan laporan terkait kinerja manajemen risiko organisasi Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('pages.help') }}" class="btn btn-outline-primary">
                            <i class="fas fa-question-circle me-1"></i> Lihat Panduan Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk grafik distribusi risiko
        const riskData = {
            labels: ['Rendah', 'Sedang', 'Tinggi', 'Ekstrem'],
            datasets: [{
                label: 'Jumlah Laporan',
                data: [
                    {{ $stats['risk_low'] ?? 0 }}, 
                    {{ $stats['risk_medium'] ?? 0 }}, 
                    {{ $stats['risk_high'] ?? 0 }}, 
                    {{ $stats['risk_extreme'] ?? 0 }}
                ],
                backgroundColor: [
                    '#10B981', // Hijau untuk risiko rendah
                    '#F59E0B', // Kuning untuk risiko sedang
                    '#EF4444', // Merah untuk risiko tinggi
                    '#7F1D1D'  // Merah tua untuk risiko ekstrem
                ],
                borderWidth: 0
            }]
        };
        
        // Konfigurasi grafik distribusi risiko
        const riskConfig = {
            type: 'doughnut',
            data: riskData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        };
        
        // Inisialisasi grafik distribusi risiko
        const riskChart = new Chart(
            document.getElementById('riskDistributionChart'),
            riskConfig
        );
        
        // Data untuk grafik korespondensi
        const corrData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Surat Masuk',
                    data: {{ json_encode($stats['corr_incoming'] ?? array_fill(0, 12, 0)) }},
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Surat Keluar',
                    data: {{ json_encode($stats['corr_outgoing'] ?? array_fill(0, 12, 0)) }},
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        };
        
        // Konfigurasi grafik korespondensi
        const corrConfig = {
            type: 'line',
            data: corrData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12
                        }
                    }
                }
            }
        };
        
        // Inisialisasi grafik korespondensi
        const corrChart = new Chart(
            document.getElementById('correspondenceChart'),
            corrConfig
        );
        
        // Refresh dashboard
        document.getElementById('refreshDashboard').addEventListener('click', function() {
            window.location.reload();
        });
    });
</script>
@endpush
@endsection 