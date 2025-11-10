@extends('layouts.app')

@php $hideDefaultHeader = true; @endphp

@section('content')
    <div class="card shadow border-top-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-building me-1"></i> Tambah Unit Kerja</h4>
            <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.work-units.store') }}" method="POST">
                @csrf
                
                <div class="mb-3 row">
                    <label for="unit_name" class="col-sm-2 col-form-label">Nama Unit <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('unit_name') is-invalid @enderror" id="unit_name" name="unit_name" value="{{ old('unit_name') }}" required autofocus>
                        @error('unit_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="unit_code" class="col-sm-2 col-form-label">Kode Unit</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('unit_code') is-invalid @enderror" id="unit_code" name="unit_code" value="{{ old('unit_code') }}">
                        <div class="form-text">Kode ini akan digunakan sebagai identifikasi singkat unit (opsional)</div>
                        @error('unit_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="unit_type" class="col-sm-2 col-form-label">Tipe Unit <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select @error('unit_type') is-invalid @enderror" id="unit_type" name="unit_type" required>
                            <option value="">-- Pilih Tipe Unit --</option>
                            <option value="medical" {{ old('unit_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                            <option value="non-medical" {{ old('unit_type') == 'non-medical' ? 'selected' : '' }}>Non-Medical</option>
                            <option value="supporting" {{ old('unit_type') == 'supporting' ? 'selected' : '' }}>Supporting</option>
                        </select>
                        @error('unit_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="head_of_unit_id" class="col-sm-2 col-form-label">Kepala Unit <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select @error('head_of_unit_id') is-invalid @enderror" id="head_of_unit_id" name="head_of_unit_id" required>
                            <option value="">-- Pilih Kepala Unit --</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('head_of_unit_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('head_of_unit_id')
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
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->unit_name }} {{ $parent->unit_code ? '('.$parent->unit_code.')' : '' }}
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
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
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
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order') }}">
                        <div class="form-text">Urutan penampilan (opsional, akan otomatis diisi jika tidak diisi)</div>
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
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-end">
                        <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection 