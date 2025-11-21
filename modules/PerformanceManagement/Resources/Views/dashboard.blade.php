@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Dashboard KPI Individu')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Dashboard KPI Individu</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Indikator</h6>
                            <h3 class="mb-0">{{ $stats['total_indicators'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Template</h6>
                            <h3 class="mb-0">{{ $stats['total_templates'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Penilaian</h6>
                            <h3 class="mb-0">{{ $stats['total_scores'] }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Rata-rata Nilai</h6>
                            <h3 class="mb-0">{{ number_format($stats['average_score'], 2) }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Menu Utama</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('performance-management.indicators.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-chart-line"></i> Kelola Indikator Kinerja
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('performance-management.templates.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-file-alt"></i> Kelola Template KPI
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('performance-management.scores.index') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-star"></i> Kelola Penilaian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Indicators -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Indikator Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentIndicators->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Jenis Pengukuran</th>
                                        <th>Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIndicators as $indicator)
                                        <tr>
                                            <td>{{ $indicator->code }}</td>
                                            <td>
                                                <a href="{{ route('performance-management.indicators.show', $indicator->id) }}">
                                                    {{ $indicator->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $indicator->category }}</span>
                                            </td>
                                            <td>{{ $indicator->measurement_type }}</td>
                                            <td>{{ $indicator->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada indikator kinerja.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
