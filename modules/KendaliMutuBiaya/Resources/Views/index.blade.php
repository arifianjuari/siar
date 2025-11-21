@extends('layouts.app')

@section('title', 'Kendali Mutu Kendali Biaya (KMKB)')

@section('content')
<div class="py-4">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <h2 class="fs-2 fw-bold mb-0">Daftar Clinical Pathway</h2>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('kendali-mutu-biaya.create') }}" class="btn btn-success d-inline-flex align-items-center">
                            <i class="fas fa-plus me-2"></i> Buat CP Baru
                        </a>
                        <a href="{{ route('kendali-mutu-biaya.rekap') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="fas fa-chart-bar me-2"></i> Rekap Evaluasi
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle me-2"></i>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                        </div>
                        <div>
                            {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Filter Section -->
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <form action="{{ route('kendali-mutu-biaya.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="search" class="form-label">Pencarian</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                            class="form-control" placeholder="Cari nama CP...">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select name="category" id="category" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        <option value="Medis" {{ request('category') == 'Medis' ? 'selected' : '' }}>Medis</option>
                                        <option value="Bedah" {{ request('category') == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                                        <option value="Obstetri" {{ request('category') == 'Obstetri' ? 'selected' : '' }}>Obstetri</option>
                                        <option value="Anak" {{ request('category') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                        <option value="Lainnya" {{ request('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-filter me-2"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Clinical Pathways Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nama CP</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Tgl Mulai</th>
                                <th scope="col">Jumlah Langkah</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clinicalPathways as $cp)
                            <tr>
                                <td class="fw-medium">{{ $cp->name }}</td>
                                <td>{{ $cp->category }}</td>
                                <td>{{ $cp->start_date->format('d-m-Y') }}</td>
                                <td>{{ $cp->steps->count() }}</td>
                                <td>
                                    <span class="badge {{ $cp->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $cp->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('kendali-mutu-biaya.edit', $cp->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('kendali-mutu-biaya.tariffs', $cp->id) }}" class="btn btn-sm btn-outline-info" title="Kelola Tarif">
                                            <i class="fas fa-dollar-sign"></i>
                                        </a>
                                        <a href="{{ route('kendali-mutu-biaya.evaluate', $cp->id) }}" class="btn btn-sm btn-outline-success" title="Evaluasi">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <a href="{{ route('kendali-mutu-biaya.pdf', $cp->id) }}" class="btn btn-sm btn-outline-secondary" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('kendali-mutu-biaya.destroy', $cp->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus CP ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center py-3">
                                        <i class="fas fa-folder-open text-muted fs-1 mb-3"></i>
                                        <p class="text-muted">Tidak ada Clinical Pathway yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    <nav aria-label="Page navigation">
                        {{ $clinicalPathways->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
