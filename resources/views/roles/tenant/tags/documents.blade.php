@extends('layouts.app')

@section('title', ' | Dokumen dengan Tag: ' . $tag->name)

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Dokumen dengan Tag: <span class="badge bg-primary">{{ $tag->name }}</span></h1>
        <div>
            <a href="{{ route('tenant.tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tag
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-file-alt me-1"></i> Laporan Risiko</h5>
        </div>
        <div class="card-body">
            @if($riskReports->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada laporan risiko dengan tag ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nomor Dokumen</th>
                                <th width="35%">Judul</th>
                                <th width="15%">Unit Pelapor</th>
                                <th width="10%">Tingkat Risiko</th>
                                <th width="10%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riskReports as $index => $report)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $report->document_number }}</td>
                                    <td>{{ $report->document_title }}</td>
                                    <td>{{ $report->reporter_unit }}</td>
                                    <td>
                                        @if(strtolower($report->risk_level) == 'rendah' || strtolower($report->risk_level) == 'low')
                                            <span class="badge bg-success">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium')
                                            <span class="badge bg-warning text-dark">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high')
                                            <span class="badge bg-danger">{{ $report->risk_level }}</span>
                                        @elseif(strtolower($report->risk_level) == 'ekstrem' || strtolower($report->risk_level) == 'extreme')
                                            <span class="badge bg-danger">{{ $report->risk_level }}</span>
                                        @else
                                            {{ $report->risk_level }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->status === 'Draft')
                                            <span class="badge bg-secondary">{{ $report->status }}</span>
                                        @elseif($report->status === 'Ditinjau')
                                            <span class="badge bg-warning text-dark">{{ $report->status }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $report->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('modules.risk-management.risk-reports.show', $report->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if(isset($documents) && $documents->count() > 0)
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-folder me-1"></i> Dokumen Lainnya</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nomor Dokumen</th>
                            <th width="50%">Judul</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $index => $document)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $document->document_number }}</td>
                                <td>{{ $document->title }}</td>
                                <td>{{ $document->document_date ? $document->document_date->format('d/m/Y') : $document->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endsection 