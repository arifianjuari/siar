@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Indikator Kinerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Daftar Indikator Kinerja</h5>
                        <a href="{{ route('performance-management.indicators.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Indikator
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter dan pencarian -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('performance-management.indicators.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select name="category" id="category" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        <option value="Kinerja Utama" {{ request('category') == 'Kinerja Utama' ? 'selected' : '' }}>Kinerja Utama</option>
                                        <option value="Kinerja Pendukung" {{ request('category') == 'Kinerja Pendukung' ? 'selected' : '' }}>Kinerja Pendukung</option>
                                        <option value="Kinerja Individu" {{ request('category') == 'Kinerja Individu' ? 'selected' : '' }}>Kinerja Individu</option>
                                        <option value="Kinerja Tim" {{ request('category') == 'Kinerja Tim' ? 'selected' : '' }}>Kinerja Tim</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="measurement_type" class="form-label">Jenis Pengukuran</label>
                                    <select name="measurement_type" id="measurement_type" class="form-select">
                                        <option value="">Semua Jenis</option>
                                        <option value="Persentase" {{ request('measurement_type') == 'Persentase' ? 'selected' : '' }}>Persentase</option>
                                        <option value="Angka" {{ request('measurement_type') == 'Angka' ? 'selected' : '' }}>Angka</option>
                                        <option value="Rasio" {{ request('measurement_type') == 'Rasio' ? 'selected' : '' }}>Rasio</option>
                                        <option value="Waktu" {{ request('measurement_type') == 'Waktu' ? 'selected' : '' }}>Waktu</option>
                                        <option value="Custom" {{ request('measurement_type') == 'Custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari kode atau nama indikator..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('performance-management.indicators.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-sync"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Indikator -->
                    @if($indicators->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Jenis Pengukuran</th>
                                        <th>Satuan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($indicators as $indicator)
                                        <tr>
                                            <td><strong>{{ $indicator->code }}</strong></td>
                                            <td>
                                                <a href="{{ route('performance-management.indicators.show', $indicator->id) }}">
                                                    {{ $indicator->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $indicator->category }}</span>
                                            </td>
                                            <td>{{ $indicator->measurement_type }}</td>
                                            <td>{{ $indicator->unit ?? '-' }}</td>
                                            <td>
                                                @if($indicator->is_shared)
                                                    <span class="badge bg-success">Dibagikan</span>
                                                @else
                                                    <span class="badge bg-secondary">Pribadi</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('performance-management.indicators.show', $indicator->id) }}" class="btn btn-info" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('performance-management.indicators.edit', $indicator->id) }}" class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('performance-management.indicators.destroy', $indicator->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $indicators->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada indikator kinerja.</p>
                            <a href="{{ route('performance-management.indicators.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Indikator Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
