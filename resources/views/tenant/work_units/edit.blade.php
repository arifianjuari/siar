@extends('layouts.app')

@section('title', ' | Edit Unit Kerja')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Edit Unit Kerja</h1>
        <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-building me-1"></i> Form Edit Unit Kerja</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.work-units.update', $workUnit) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3 row">
                    <label for="name" class="col-sm-2 col-form-label">Nama Unit <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $workUnit->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="code" class="col-sm-2 col-form-label">Kode Unit</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $workUnit->code) }}">
                        <div class="form-text">Kode ini akan digunakan sebagai identifikasi singkat unit (opsional)</div>
                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="parent_id" class="col-sm-2 col-form-label">Parent Unit</label>
                    <div class="col-sm-10">
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">-- Pilih Parent Unit (opsional) --</option>
                            @foreach($parentUnits as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $workUnit->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }} {{ $parent->code ? '('.$parent->code.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $workUnit->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="order" class="col-sm-2 col-form-label">Urutan</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $workUnit->order) }}">
                        <div class="form-text">Urutan penampilan unit</div>
                        @error('order')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="is_active" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $workUnit->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-end">
                        <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection 