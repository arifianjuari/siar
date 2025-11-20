@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Detail Indikator Kinerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Detail Indikator Kinerja</h5>
                        <div>
                            <a href="{{ route('performance-management.indicators.edit', $indicator->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('performance-management.indicators.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Kode Indikator</th>
                                    <td>: <strong>{{ $indicator->code }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Nama Indikator</th>
                                    <td>: {{ $indicator->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>: <span class="badge bg-info">{{ $indicator->category }}</span></td>
                                </tr>
                                <tr>
                                    <th>Jenis Pengukuran</th>
                                    <td>: {{ $indicator->measurement_type }}</td>
                                </tr>
                                <tr>
                                    <th>Satuan</th>
                                    <td>: {{ $indicator->unit ?? '-' }}</td>
                                </tr>
                                @if($indicator->measurement_type === 'Custom' && $indicator->custom_formula)
                                <tr>
                                    <th>Formula Custom</th>
                                    <td>: <code>{{ $indicator->custom_formula }}</code></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status Berbagi</th>
                                    <td>: 
                                        @if($indicator->is_shared)
                                            <span class="badge bg-success">Dibagikan</span>
                                        @else
                                            <span class="badge bg-secondary">Pribadi</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Dibuat Oleh</th>
                                    <td>: {{ $indicator->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>: {{ $indicator->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui Oleh</th>
                                    <td>: {{ $indicator->updater->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Diperbarui</th>
                                    <td>: {{ $indicator->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Template yang menggunakan indikator ini -->
                    <h6 class="mb-3">Template yang Menggunakan Indikator Ini</h6>
                    @if($indicator->templates && $indicator->templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Template</th>
                                        <th>Periode</th>
                                        <th>Target</th>
                                        <th>Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($indicator->templates as $template)
                                        <tr>
                                            <td>
                                                <a href="{{ route('performance-management.templates.show', $template->id) }}">
                                                    {{ $template->name }}
                                                </a>
                                            </td>
                                            <td>{{ $template->period }}</td>
                                            <td>{{ $template->pivot->target ?? '-' }}</td>
                                            <td>{{ $template->pivot->weight ?? '-' }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada template yang menggunakan indikator ini.</p>
                    @endif

                    <hr>

                    <!-- Riwayat Penilaian -->
                    <h6 class="mb-3">Riwayat Penilaian</h6>
                    @if($indicator->scores && $indicator->scores->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Periode</th>
                                        <th>Pegawai</th>
                                        <th>Target</th>
                                        <th>Realisasi</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($indicator->scores as $score)
                                        <tr>
                                            <td>{{ $score->period }}</td>
                                            <td>{{ $score->employee->name ?? '-' }}</td>
                                            <td>{{ $score->target }}</td>
                                            <td>{{ $score->actual }}</td>
                                            <td>
                                                <span class="badge bg-{{ $score->score >= 80 ? 'success' : ($score->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $score->score }}
                                                </span>
                                            </td>
                                            <td>{{ $score->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada penilaian untuk indikator ini.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <form action="{{ route('performance-management.indicators.destroy', $indicator->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus Indikator
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
