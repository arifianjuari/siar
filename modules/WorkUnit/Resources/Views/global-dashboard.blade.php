@extends('layouts.app')

@section('title', 'Dashboard Unit Kerja')

@php
$hideDefaultHeader = true;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">Dashboard Unit Kerja</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i> Ringkasan informasi dan statistik unit kerja
                            </p>
                        </div>
                        <a href="{{ route('work-units.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Lihat Daftar Unit Kerja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <!-- Total Unit Kerja -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="fas fa-building fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Unit Kerja</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalWorkUnits }}</h2>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Unit Aktif</span>
                            <span class="fw-medium text-success">{{ $totalActiveWorkUnits }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ $totalWorkUnits > 0 ? ($totalActiveWorkUnits / $totalWorkUnits) * 100 : 0 }}%;" 
                                aria-valuenow="{{ $totalActiveWorkUnits }}" 
                                aria-valuemin="0" 
                                aria-valuemax="{{ $totalWorkUnits }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Berdasarkan Jenis Unit -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Berdasarkan Jenis Unit</h6>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Medis</span>
                            <span class="fw-medium">{{ $unitsByType['medical'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalWorkUnits > 0 ? (($unitsByType['medical'] ?? 0) / $totalWorkUnits) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Non-Medis</span>
                            <span class="fw-medium">{{ $unitsByType['non-medical'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $totalWorkUnits > 0 ? (($unitsByType['non-medical'] ?? 0) / $totalWorkUnits) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Pendukung</span>
                            <span class="fw-medium">{{ $unitsByType['supporting'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $totalWorkUnits > 0 ? (($unitsByType['supporting'] ?? 0) / $totalWorkUnits) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Aktivitas -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Status Unit Kerja</h6>
                    <div class="d-flex align-items-center justify-content-center h-75">
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div style="position: relative; width: 120px; height: 120px;">
                                    <!-- Progress circle -->
                                    <svg viewBox="0 0 36 36" class="circular-chart">
                                        <path class="circle-bg"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        />
                                        <path class="circle"
                                            stroke-dasharray="{{ $totalWorkUnits > 0 ? ($totalActiveWorkUnits / $totalWorkUnits) * 100 : 0 }}, 100"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        />
                                    </svg>
                                    <!-- Text in the middle -->
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                        <div class="h3 mb-0">{{ $totalWorkUnits > 0 ? round(($totalActiveWorkUnits / $totalWorkUnits) * 100) : 0 }}%</div>
                                        <div class="small text-muted">Aktif</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Kerja Terbaru & Informasi Lainnya -->
    <div class="row">
        <!-- Daftar Unit Kerja Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="card-title mb-0">Unit Kerja Terbaru</h5>
                    <p class="card-subtitle text-muted small mt-1">5 unit kerja yang terakhir dibuat</p>
                </div>
                <div class="card-body">
                    @if(count($topUnits) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="text-muted small bg-light">
                                    <tr>
                                        <th class="border-0">Unit</th>
                                        <th class="border-0">Kode</th>
                                        <th class="border-0">Jenis</th>
                                        <th class="border-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topUnits as $unit)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $unit->unit_name }}</div>
                                            <div class="small text-muted">{{ $unit->headOfUnit->name ?? 'Tidak ada kepala unit' }}</div>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $unit->unit_code }}</span></td>
                                        <td>
                                            @if($unit->unit_type == 'medical')
                                                <span class="badge bg-success">Medical</span>
                                            @elseif($unit->unit_type == 'non-medical')
                                                <span class="badge bg-primary">Non-Medical</span>
                                            @elseif($unit->unit_type == 'supporting')
                                                <span class="badge bg-secondary">Supporting</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('work-units.dashboard', $unit->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-chart-bar me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fs-2 text-muted mb-2"></i>
                            <p class="mb-0">Belum ada unit kerja yang dibuat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Unit Kerja Tambahan -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="card-title mb-0">Manajemen Unit Kerja</h5>
                    <p class="card-subtitle text-muted small mt-1">Akses cepat ke fungsi manajemen unit kerja</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="row mb-3 g-3">
                        <div class="col-sm-6">
                            <div class="card bg-light h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-plus-circle fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-1">Tambah Unit Kerja</h6>
                                    <p class="small text-muted mb-3">Buat unit kerja baru dalam sistem</p>
                                    <a href="{{ route('work-units.create') }}" class="btn btn-sm btn-outline-primary mt-auto">Tambah Unit</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card bg-light h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-list fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-1">Daftar Unit Kerja</h6>
                                    <p class="small text-muted mb-3">Lihat dan kelola semua unit kerja</p>
                                    <a href="{{ route('work-units.index') }}" class="btn btn-sm btn-outline-primary mt-auto">Lihat Daftar</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary border-0 mt-auto" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading mb-1">Informasi Unit Kerja</h6>
                                <p class="mb-0">Unit kerja merupakan struktur organisasi yang membantu pengelolaan tugas dan tanggung jawab dalam organisasi Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.circular-chart {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    max-height: 100%;
}

.circle-bg {
    fill: none;
    stroke: #eeeeee;
    stroke-width: 3.8;
}

.circle {
    fill: none;
    stroke-width: 3.6;
    stroke-linecap: round;
    stroke: #4F46E5;
    animation: progress 1s ease-out forwards;
}

@keyframes progress {
    0% {
        stroke-dasharray: 0 100;
    }
}
</style>
@endsection

@push('scripts')
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
