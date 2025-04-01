@extends('layouts.app')

@section('title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <!-- Tombol Tambah Referensi dipindahkan ke bawah, sejajar dengan form pencarian -->
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Daftar Dokumen Referensi</h4>
            <div class="d-flex">
                <form action="{{ route('tenant.document-references.index') }}" method="GET" class="d-flex me-2">
                    <input type="text" name="search" class="form-control form-control-sm me-2" 
                          placeholder="Cari..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <a href="{{ route('tenant.document-references.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Referensi
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Nomor Referensi</th>
                            <th>Judul</th>
                            <th>Diterbitkan Oleh</th>
                            <th>Tanggal</th>
                            <th>Unit Terkait</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($references as $reference)
                            <tr>
                                <td>{{ $reference->reference_type }}</td>
                                <td>{{ $reference->reference_number }}</td>
                                <td>{{ Str::limit($reference->title, 50) }}</td>
                                <td>{{ $reference->issued_by }}</td>
                                <td>{{ $reference->issued_date->format('d-m-Y') }}</td>
                                <td>{{ $reference->related_unit ?: '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if($reference->file_url)
                                            <a href="{{ $reference->file_url }}" 
                                               class="btn btn-sm btn-success" title="Buka Link File" target="_blank">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('tenant.document-references.show', $reference->id) }}" 
                                           class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('tenant.document-references.edit', $reference->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete('{{ $reference->id }}')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $reference->id }}" 
                                          action="{{ route('tenant.document-references.destroy', $reference->id) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada dokumen referensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $references->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus dokumen referensi ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush 