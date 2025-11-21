@extends('layouts.app')

@section('title', 'Edit Dokumen')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Edit Dokumen</h2>
            <p class="text-muted mb-0">{{ $document->document_number }}</p>
        </div>
        <div>
            <a href="{{ route('modules.document-management.documents.show', $document->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('modules.document-management.documents.update', $document->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Informasi Dasar</h4>
                        
                        <div class="mb-3">
                            <label for="document_title" class="form-label required">Judul Dokumen</label>
                            <input type="text" class="form-control @error('document_title') is-invalid @enderror" id="document_title" name="document_title" value="{{ old('document_title', $document->document_title) }}" required>
                            @error('document_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_number" class="form-label required">Nomor Dokumen</label>
                            <input type="text" class="form-control @error('document_number') is-invalid @enderror" id="document_number" name="document_number" value="{{ old('document_number', $document->document_number) }}" required>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_date" class="form-label required">Tanggal Dokumen</label>
                            <input type="date" class="form-control @error('document_date') is-invalid @enderror" id="document_date" name="document_date" value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                            @error('document_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label required">Kategori</label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $document->category) }}" required>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="confidentiality_level" class="form-label required">Tingkat Kerahasiaan</label>
                            <select class="form-select @error('confidentiality_level') is-invalid @enderror" id="confidentiality_level" name="confidentiality_level" required>
                                <option value="public" {{ old('confidentiality_level', $document->confidentiality_level) == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="internal" {{ old('confidentiality_level', $document->confidentiality_level) == 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="confidential" {{ old('confidentiality_level', $document->confidentiality_level) == 'confidential' ? 'selected' : '' }}>Confidential</option>
                            </select>
                            @error('confidentiality_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document_file" class="form-label">File Dokumen</label>
                            <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file">
                            <div class="form-text">
                                Format yang didukung: PDF, Word, Excel, atau Gambar. Ukuran maksimal: 10MB.
                                @if($document->file_path)
                                    <br>File saat ini: <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">{{ basename($document->file_path) }}</a>
                                @endif
                            </div>
                            @error('document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Informasi KARS</h4>
                        
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Tipe Dokumen</label>
                            <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" name="document_type">
                                <option value="">-- Pilih Tipe Dokumen --</option>
                                @foreach($documentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('document_type', $document->document_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_scope" class="form-label">Ruang Lingkup</label>
                            <select class="form-select @error('document_scope') is-invalid @enderror" id="document_scope" name="document_scope">
                                <option value="">-- Pilih Ruang Lingkup --</option>
                                @foreach($documentScopes as $value => $label)
                                    <option value="{{ $value }}" {{ old('document_scope', $document->document_scope) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('document_scope')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_regulation" name="is_regulation" {{ old('is_regulation', $document->is_regulation) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_regulation">
                                    Dokumen Regulasi
                                </label>
                            </div>
                            <div class="form-text">Centang jika dokumen ini adalah regulasi (bukan hanya bukti)</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $document->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Dokumen Aktif
                                </label>
                            </div>
                            <div class="form-text">Centang jika dokumen ini masih aktif digunakan</div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Informasi Revisi</h5>
                        
                        <div class="mb-3">
                            <label for="revision_number" class="form-label">Nomor Revisi</label>
                            <input type="text" class="form-control @error('revision_number') is-invalid @enderror" id="revision_number" name="revision_number" value="{{ old('revision_number', $document->revision_number) }}">
                            @error('revision_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="revision_date" class="form-label">Tanggal Revisi</label>
                            <input type="date" class="form-control @error('revision_date') is-invalid @enderror" id="revision_date" name="revision_date" value="{{ old('revision_date', $document->revision_date ? $document->revision_date->format('Y-m-d') : '') }}">
                            @error('revision_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="superseded_by_id" class="form-label">Digantikan Oleh</label>
                            <select class="form-select @error('superseded_by_id') is-invalid @enderror" id="superseded_by_id" name="superseded_by_id">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($documents as $doc)
                                    <option value="{{ $doc->id }}" {{ old('superseded_by_id', $document->superseded_by_id) == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->document_number }} - {{ $doc->document_title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('superseded_by_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <h5 class="mt-4 mb-3">Informasi Penyimpanan Fisik</h5>
                        
                        <div class="mb-3">
                            <label for="storage_location" class="form-label">Lokasi Penyimpanan Fisik</label>
                            <input type="text" class="form-control @error('storage_location') is-invalid @enderror" id="storage_location" name="storage_location" value="{{ old('storage_location', $document->storage_location) }}">
                            @error('storage_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="distribution_note" class="form-label">Catatan Distribusi</label>
                            <textarea class="form-control @error('distribution_note') is-invalid @enderror" id="distribution_note" name="distribution_note" rows="2">{{ old('distribution_note', $document->distribution_note) }}</textarea>
                            @error('distribution_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('modules.document-management.documents.show', $document->id) }}" class="btn btn-outline-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 