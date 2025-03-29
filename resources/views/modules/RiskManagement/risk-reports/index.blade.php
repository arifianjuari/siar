@extends('layouts.app')

@section('title', ' | Daftar Laporan Risiko')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Laporan Risiko</h1>
        <div>
            @php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant-admin' || 
                                  strtolower($userRole) === 'tenant-admin';
            @endphp
            
            @if(auth()->user()->role && $isTenantAdmin)
                <a href="{{ route('modules.risk-management.analysis-config') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
                </a>
            @endif
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
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter me-1"></i>
                <span>Filter</span>
            </div>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-up me-1"></i> <span>Sembunyikan</span>
            </button>
        </div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('modules.risk-management.risk-reports.index') }}" id="filterForm">
                <div class="row align-items-end g-2" id="filterRow">
                    <div class="col-lg">
                        <label for="risk_level" class="form-label small mb-0">Tingkat Risiko:</label>
                        <select name="risk_level" id="risk_level" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            <option value="Rendah" {{ request('risk_level') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="Sedang" {{ request('risk_level') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="Tinggi" {{ request('risk_level') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="Ekstrem" {{ request('risk_level') == 'Ekstrem' ? 'selected' : '' }}>Ekstrem</option>
                        </select>
                    </div>
                    
                    <div class="col-lg">
                        <label for="reporter_unit" class="form-label small mb-0">Unit Pelapor:</label>
                        <input type="text" name="reporter_unit" id="reporter_unit" class="form-control form-control-sm" value="{{ request('reporter_unit') }}" placeholder="Unit...">
                    </div>
                    
                    <div class="col-lg">
                        <label for="risk_category" class="form-label small mb-0">Kategori Risiko:</label>
                        <select name="risk_category" id="risk_category" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            <option value="Medis" {{ request('risk_category') == 'Medis' ? 'selected' : '' }}>Medis</option>
                            <option value="Non-medis" {{ request('risk_category') == 'Non-medis' ? 'selected' : '' }}>Non-medis</option>
                            <option value="Pasien" {{ request('risk_category') == 'Pasien' ? 'selected' : '' }}>Pasien</option>
                            <option value="Pengunjung" {{ request('risk_category') == 'Pengunjung' ? 'selected' : '' }}>Pengunjung</option>
                            <option value="Fasilitas" {{ request('risk_category') == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                            <option value="Karyawan" {{ request('risk_category') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                        </select>
                    </div>
                    
                    <div class="col-lg">
                        <label for="date_range" class="form-label small mb-0">Periode:</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="Dari">
                            <span class="input-group-text">-</span>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="Sampai">
                        </div>
                    </div>
                    
                    <div class="col-lg">
                        <label for="risk_title" class="form-label small mb-0">Judul Risiko:</label>
                        <input type="text" name="risk_title" id="risk_title" class="form-control form-control-sm" value="{{ request('risk_title') }}" placeholder="Cari judul...">
                    </div>

                    <div class="col-auto">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-sm me-1">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Laporan Risiko -->
    <div class="card shadow">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i>Daftar Laporan</h5>
            <span class="badge bg-primary">Total: {{ $riskReports->count() }}</span>
        </div>
        <div class="card-body">
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
                                            <span class="badge bg-warning text-dark" style="background-color: #FFFF00 !important;">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high')
                                            <span class="badge text-white" style="background-color: #FFA500 !important;">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'ekstrem' || strtolower($report->risk_level) == 'extreme')
                                            <span class="badge bg-danger" style="background-color: #FF0000 !important;">{{ $report->risk_level }}</span>
                                        @else
                                            {{ $report->risk_level }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->analysis)
                                            @if($report->analysis->analysis_status == 'draft')
                                                <span class="badge bg-danger">Draft</span>
                                            @elseif($report->analysis->analysis_status == 'in_progress' || $report->analysis->analysis_status == 'reviewed')
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            @else
                                                <span class="badge bg-success">Selesai</span>
                                            @endif
                                        @else
                                            @if($report->status == 'Draft')
                                                <span class="badge bg-danger">Draft</span>
                                            @elseif($report->status == 'Ditinjau')
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            @else
                                                <span class="badge bg-success">Selesai</span>
                                            @endif
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
                                            
                                            <!-- Tombol Analisis -->
                                            @if($report->status === 'Draft')
                                                <a href="{{ route('modules.risk-management.risk-analysis.create', $report->id) }}" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Tombol Setujui -->
                                            @if($report->status === 'Ditinjau')
                                                <form method="POST" action="{{ route('modules.risk-management.risk-reports.approve', $report->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <!-- Export Awal -->
                                            <a href="{{ route('modules.risk-management.risk-reports.export-awal', $report->id) }}" class="btn btn-sm btn-secondary" title="Export Awal">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            
                                            <!-- Export Akhir -->
                                            @if($report->status === 'Selesai')
                                                <a href="{{ route('modules.risk-management.risk-reports.export-akhir', $report->id) }}" class="btn btn-sm btn-primary" title="Export PDF Akhir">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @endif
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi toggle untuk form filter
        const toggleFilterBtn = document.getElementById('toggleFilterBtn');
        const filterRow = document.getElementById('filterRow');
        const filterCard = document.querySelector('.card.mb-4.shadow-sm');
        const btnText = toggleFilterBtn.querySelector('span');
        const btnIcon = toggleFilterBtn.querySelector('i');
        
        // Set initial state
        let isFilterVisible = true;
        
        toggleFilterBtn.addEventListener('click', function() {
            if (isFilterVisible) {
                // Sembunyikan filter
                filterCard.querySelector('.card-body').style.display = 'none';
                btnText.textContent = 'Tampilkan';
                btnIcon.classList.remove('fa-chevron-up');
                btnIcon.classList.add('fa-chevron-down');
                isFilterVisible = false;
            } else {
                // Tampilkan filter
                filterCard.querySelector('.card-body').style.display = 'block';
                btnText.textContent = 'Sembunyikan';
                btnIcon.classList.remove('fa-chevron-down');
                btnIcon.classList.add('fa-chevron-up');
                isFilterVisible = true;
            }
        });
        
        // Auto-submit form saat select berubah
        const autoSubmitFields = document.querySelectorAll('#risk_level, #risk_category');
        
        autoSubmitFields.forEach(function(field) {
            field.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>
@endpush 