@extends('layouts.app')

@section('title', 'Unit Kerja')

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
                            <h1 class="h3 mb-1">Daftar Unit Kerja</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i> Mengelola struktur unit kerja dalam organisasi
                            </p>
                        </div>
                        <a href="{{ route('work-units.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Unit Kerja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1">Berhasil</h6>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1">Error</h6>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Kode Unit</th>
                            <th class="px-4 py-3">Nama Unit</th>
                            <th class="px-4 py-3">Jenis Unit</th>
                            <th class="px-4 py-3">Kepala Unit</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($flattenedWorkUnits as $unit)
                        <tr>
                            <td class="px-4 py-3">
                                <div style="padding-left: {{ $unit->depth * 20 }}px;">
                                    {{ $unit->unit_code }}
                                </div>
                            </td>
                            <td class="px-4 py-3 fw-medium">{{ $unit->unit_name }}</td>
                            <td class="px-4 py-3">
                                @if($unit->unit_type == 'medical')
                                    <span class="badge bg-success">Medical</span>
                                @elseif($unit->unit_type == 'non-medical')
                                    <span class="badge bg-primary">Non-Medical</span>
                                @elseif($unit->unit_type == 'supporting')
                                    <span class="badge bg-secondary">Supporting</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($unit->unit_type) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $unit->headOfUnit->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('work-units.dashboard', $unit->id) }}" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Dashboard">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('work-units.edit', $unit->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('work-units.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Tidak ada data unit kerja.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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