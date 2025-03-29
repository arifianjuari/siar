@extends('layouts.app')

@section('title', ' | Edit Tag')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Edit Tag</h1>
        <a href="{{ route('tenant.tags.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-1"></i> Form Edit Tag</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.tags.update', $tag) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3 row">
                    <label for="name" class="col-sm-2 col-form-label">Nama <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tag->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $tag->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="parent_id" class="col-sm-2 col-form-label">Parent Tag</label>
                    <div class="col-sm-10">
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">-- Pilih Parent Tag (Opsional) --</option>
                            @foreach($parentTags as $parentTag)
                                <option value="{{ $parentTag->id }}" {{ old('parent_id', $tag->parent_id) == $parentTag->id ? 'selected' : '' }}>{{ $parentTag->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted">Pilih parent tag jika ini adalah sub-tag dari tag lain.</small>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="order" class="col-sm-2 col-form-label">Urutan</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $tag->order) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted">Menentukan urutan penampilan.</small>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 