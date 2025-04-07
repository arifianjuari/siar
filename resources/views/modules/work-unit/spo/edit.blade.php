@extends('layouts.app', ['hideDefaultHeader' => true])


@section('title', 'Edit SPO: ' . $spo->document_title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Dokumen SPO</h1>
        
        <div>
            <a href="{{ route('work-units.spo.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            @if ($spo->file_path)
            <a href="{{ $spo->file_path }}" class="btn btn-info ms-2" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Dokumen
            </a>
            @endif
        </div>
    </div>
    
    <!-- Form Edit SPO -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit SPO</h6>
            <span class="badge {{ $spo->status_validasi == 'Draft' ? 'bg-secondary' : 
                                ($spo->status_validasi == 'Disetujui' ? 'bg-success' : 
                                ($spo->status_validasi == 'Kadaluarsa' ? 'bg-danger' : 'bg-warning')) }} 
                    px-3 py-2 rounded-pill">
                Status: {{ $spo->status_validasi }}
            </span>
        </div>
        <div class="card-body">
            <form action="{{ route('work-units.spo.update', $spo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Informasi Dokumen</h5>
                        
                        <div class="mb-3">
                            <label for="document_title" class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('document_title') is-invalid @enderror" 
                                id="document_title" name="document_title" value="{{ old('document_title', $spo->document_title) }}" required>
                            @error('document_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="document_type" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                                <select class="form-select @error('document_type') is-invalid @enderror" 
                                    id="document_type" name="document_type" required>
                                    @foreach($documentTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('document_type', $spo->document_type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="document_version" class="form-label">Versi Dokumen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('document_version') is-invalid @enderror" 
                                    id="document_version" name="document_version" value="{{ old('document_version', $spo->document_version) }}" required>
                                @error('document_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_number" class="form-label">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('document_number') is-invalid @enderror" 
                                id="document_number" name="document_number" value="{{ old('document_number', $spo->document_number) }}" required>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="document_date" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('document_date') is-invalid @enderror" 
                                    id="document_date" name="document_date" value="{{ old('document_date', $spo->document_date->format('Y-m-d')) }}" required>
                                @error('document_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                                <select class="form-select @error('confidentiality_level') is-invalid @enderror" 
                                    id="confidentiality_level" name="confidentiality_level" required>
                                    @foreach($confidentialityLevels as $value => $label)
                                        <option value="{{ $value }}" {{ old('confidentiality_level', $spo->confidentiality_level) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('confidentiality_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="work_unit_id" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                            <select class="form-select @error('work_unit_id') is-invalid @enderror" 
                                id="work_unit_id" name="work_unit_id" required>
                                <option value="">Pilih Unit Kerja</option>
                                @foreach($workUnits as $workUnit)
                                    <option value="{{ $workUnit->id }}" {{ old('work_unit_id', $spo->work_unit_id) == $workUnit->id ? 'selected' : '' }}>
                                        {{ $workUnit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('work_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="linked_unit" class="form-label">Unit Kerja Terkait</label>
                            <select class="form-select select2 @error('linked_unit') is-invalid @enderror" 
                                id="linked_unit" name="linked_unit[]" multiple>
                                @foreach($linkedUnits as $unit)
                                    <option value="{{ $unit->id }}" 
                                        {{ (is_array(old('linked_unit')) && in_array($unit->id, old('linked_unit'))) || 
                                            (old('linked_unit') === null && is_array($spoLinkedUnits) && in_array($unit->id, $spoLinkedUnits)) ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih multiple unit kerja.</div>
                            @error('linked_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status_validasi" class="form-label">Status Dokumen <span class="text-danger">*</span></label>
                            <select class="form-select @error('status_validasi') is-invalid @enderror" 
                                id="status_validasi" name="status_validasi" required>
                                @foreach($statusValidasi as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_validasi', $spo->status_validasi) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Perubahan status menjadi 'Disetujui' akan mencatat informasi persetujuan saat ini.
                            </div>
                            @error('status_validasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="review_cycle_months" class="form-label">Siklus Review (bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('review_cycle_months') is-invalid @enderror" 
                                id="review_cycle_months" name="review_cycle_months" 
                                value="{{ old('review_cycle_months', $spo->review_cycle_months) }}" 
                                min="1" max="60" required>
                            @error('review_cycle_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="file_url" class="form-label">Link Dokumen</label>
                            <input type="url" class="form-control @error('file_url') is-invalid @enderror" 
                                id="file_url" name="file_url" value="{{ old('file_url', $spo->file_path) }}" 
                                placeholder="https://drive.google.com/file/d/xxx/view">
                            <div class="form-text">
                                Masukkan link Google Drive atau link lainnya ke dokumen SPO (opsional)
                            </div>
                            @error('file_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Isi Dokumen</h5>
                        
                        <div class="mb-3">
                            <label for="definition" class="form-label">Pengertian</label>
                            <textarea class="form-control @error('definition') is-invalid @enderror" 
                                id="definition" name="definition" rows="3">{{ old('definition', $spo->definition) }}</textarea>
                            @error('definition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Tujuan</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                id="purpose" name="purpose" rows="3">{{ old('purpose', $spo->purpose) }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="policy" class="form-label">Kebijakan</label>
                            <textarea class="form-control @error('policy') is-invalid @enderror" 
                                id="policy" name="policy" rows="3">{{ old('policy', $spo->policy) }}</textarea>
                            @error('policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="procedure" class="form-label">Prosedur</label>
                            <textarea class="form-control @error('procedure') is-invalid @enderror" 
                                id="procedure" name="procedure" rows="8">{{ old('procedure', $spo->procedure) }}</textarea>
                            @error('procedure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reference" class="form-label">Referensi</label>
                            <textarea class="form-control @error('reference') is-invalid @enderror" 
                                id="reference" name="reference" rows="3">{{ old('reference', $spo->reference) }}</textarea>
                            <div class="form-text">Masukkan referensi dokumen atau sumber yang digunakan dalam pembuatan SPO ini.</div>
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('work-units.spo.show', $spo) }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Select2 untuk unit kerja terkait
        if (typeof $.fn.select2 !== 'undefined') {
            $('#linked_unit').select2({
                placeholder: "Pilih Unit Kerja Terkait",
                allowClear: true
            });
        }
    });
</script>
@endpush
@endsection 