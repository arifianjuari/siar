@extends('layouts.app')

@section('title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <!-- Tombol Kembali dipindahkan ke header card -->
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Form Tambah Dokumen Referensi</h4>
            <a href="{{ route('tenant.document-references.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.document-references.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference_type" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select @error('reference_type') is-invalid @enderror" id="reference_type" name="reference_type" required>
                                <option value="" selected disabled>-- Pilih Jenis Dokumen --</option>
                                @foreach($referenceTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('reference_type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reference_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Nomor Referensi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" value="{{ old('reference_number') }}" required>
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="issued_by" class="form-label">Diterbitkan Oleh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('issued_by') is-invalid @enderror" 
                                   id="issued_by" name="issued_by" value="{{ old('issued_by') }}" required>
                            @error('issued_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="issued_date" class="form-label">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('issued_date') is-invalid @enderror" 
                                   id="issued_date" name="issued_date" value="{{ old('issued_date') }}" required>
                            @error('issued_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="related_unit" class="form-label">Unit Terkait</label>
                    <input type="text" class="form-control @error('related_unit') is-invalid @enderror" 
                           id="related_unit" name="related_unit" value="{{ old('related_unit') }}">
                    @error('related_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="file_url" class="form-label">Link File</label>
                    <input type="text" class="form-control @error('file_url') is-invalid @enderror" 
                           id="file_url" name="file_url" value="{{ old('file_url') }}">
                    <small class="text-muted">Masukkan URL atau path lengkap ke file terkait (misal: link Google Drive)</small>
                    @error('file_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="tags" class="form-label">Tag (Kata Kunci)</label>
                    <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                        @if(old('tags'))
                            @foreach(old('tags') as $tag)
                                <option value="{{ $tag }}" selected>{{ $tag }}</option>
                            @endforeach
                        @endif
                    </select>
                    <small class="text-muted">Tekan Enter untuk menambahkan tag baru</small>
                    @error('tags')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="mb-3">
                    <label for="description" class="form-label">Keterangan Tambahan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-light me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Select2 untuk Tag (dengan fitur tambah tag baru)
        $('#tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: 'Masukkan tag...',
            width: '100%'
        });
    });
</script>
@endpush 