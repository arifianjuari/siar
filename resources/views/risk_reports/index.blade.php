@extends('layouts.app')

@section('title', ' | Daftar Laporan Risiko')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Laporan Risiko</h1>
        <div>
            <a href="{{ route('modules.risk-management.risk-reports.create') }}" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Buat Laporan Baru
            </a>
            <a href="{{ route('modules.risk-management.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i> Dashboard Statistik
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Form Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('modules.risk-management.risk-reports.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label fw-bold">Status:</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="occurred_at" class="form-label fw-bold">Tanggal Kejadian:</label>
                        <input type="date" name="occurred_at" id="occurred_at" class="form-control" value="{{ request('occurred_at') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="risk_title" class="form-label fw-bold">Judul Risiko:</label>
                        <input type="text" name="risk_title" id="risk_title" class="form-control" value="{{ request('risk_title') }}" placeholder="Cari judul...">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Laporan Risiko -->
    <div class="card shadow">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($riskReports->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada laporan risiko yang ditemukan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="40">No</th>
                                <th>Judul</th>
                                <th>Unit Pelapor</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Tanggal Kejadian</th>
                                <th>Tingkat Risiko</th>
                                <th>Status</th>
                                <th width="280">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riskReports as $index => $report)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $report->risk_title }}</td>
                                    <td>{{ $report->reporter_unit }}</td>
                                    <td>{{ $report->risk_type ?: '-' }}</td>
                                    <td>{{ $report->risk_category }}</td>
                                    <td>{{ $report->occurred_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if(strtolower($report->risk_level) == 'rendah' || strtolower($report->risk_level) == 'low')
                                            <span class="badge bg-success">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium')
                                            <span class="badge bg-warning text-dark">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high')
                                            <span class="badge bg-danger">{{ $report->risk_level }}</span>
                                        @else
                                            {{ $report->risk_level }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->status == 'open')
                                            <span class="badge bg-danger">Open</span>
                                        @elseif($report->status == 'in_review')
                                            <span class="badge bg-warning text-dark">In Review</span>
                                        @else
                                            <span class="badge bg-success">Resolved</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('modules.risk-management.risk-reports.edit', $report->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Form Hapus -->
                                            <form method="POST" action="{{ route('modules.risk-management.risk-reports.destroy', $report->id) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <!-- Ubah Status -->
                                            @if($report->status === 'open')
                                                <a href="{{ route('modules.risk-management.risk-analysis.create', $report->id) }}" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-chart-line me-1"></i> Analisis
                                                </a>
                                            @endif
                                            
                                            @if($report->status === 'in_review')
                                                <form method="POST" action="{{ route('modules.risk-management.risk-reports.approve', $report->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2">
                                            <!-- Export & QR Code -->
                                            <div class="btn-group">
                                                <a href="{{ route('modules.risk-management.risk-reports.export-awal', $report->id) }}" class="btn btn-sm btn-secondary" title="Export Awal">
                                                    <i class="fas fa-file-word me-1"></i> Export Awal
                                                </a>
                                                <a href="{{ route('modules.risk-management.risk-reports.qr-code', $report->id) }}" class="btn btn-sm btn-dark" title="QR Code">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                                @if($report->status === 'resolved')
                                                    <a href="{{ route('modules.risk-management.risk-reports.export-akhir', $report->id) }}" class="btn btn-sm btn-primary" title="Export Akhir">
                                                        <i class="fas fa-file-word me-1"></i> Export Akhir
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection 