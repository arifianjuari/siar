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
    <div class="card mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-filter me-1"></i> Filter</h6>
            <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body py-2">
                <form action="{{ route('modules.risk-management.risk-reports.index') }}" method="GET" id="filterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label for="status" class="form-label small">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Ditinjau" {{ request('status') == 'Ditinjau' ? 'selected' : '' }}>Ditinjau</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="risk_level" class="form-label small">Tingkat Risiko</label>
                            <select name="risk_level" id="risk_level" class="form-select form-select-sm">
                                <option value="">Semua Tingkat</option>
                                <option value="Rendah" {{ request('risk_level') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="Sedang" {{ request('risk_level') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="Tinggi" {{ request('risk_level') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                <option value="Ekstrem" {{ request('risk_level') == 'Ekstrem' ? 'selected' : '' }}>Ekstrem</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tag" class="form-label small">Tag</label>
                            <select name="tag" id="tag" class="form-select form-select-sm">
                                <option value="">Semua Tag</option>
                                @foreach(App\Models\Tag::where('tenant_id', session('tenant_id'))->orderBy('name')->get() as $tag)
                                    <option value="{{ $tag->slug }}" {{ request('tag') == $tag->slug ? 'selected' : '' }}>{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label small">Kata Kunci</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
                                <th>Tag</th>
                                <th>Status</th>
                                <th width="280">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riskReports as $index => $report)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $report->document_number }}</td>
                                    <td>{{ $report->reporter_unit }}</td>
                                    <td>{{ $report->risk_type ?? 'N/A' }}</td>
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
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($report->tags as $tag)
                                                <a href="{{ route('tenant.tags.documents', $tag->slug) }}" class="badge bg-light text-dark text-decoration-none">
                                                    {{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
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
        // Fungsi toggle collapse filter
        const filterToggleBtn = document.querySelector('[data-bs-target="#filterCollapse"]');
        const filterCollapse = document.getElementById('filterCollapse');
        const filterIcon = filterToggleBtn.querySelector('i');
        
        // Toggle icon saat collapse/expand
        filterCollapse.addEventListener('show.bs.collapse', function () {
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        });
        
        filterCollapse.addEventListener('hide.bs.collapse', function () {
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        });
        
        // Auto-submit form saat select berubah
        const autoSubmitFields = document.querySelectorAll('#status, #risk_level, #tag');
        
        autoSubmitFields.forEach(function(field) {
            field.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>
@endpush 