@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Edit Indikator Kinerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Edit Indikator Kinerja</h5>
                        <a href="{{ route('performance-management.indicators.show', $indicator->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('performance-management.indicators.update', $indicator->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Kode Indikator <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $indicator->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Contoh: IND-001, KPI-HR-001</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Kinerja Utama" {{ old('category', $indicator->category) == 'Kinerja Utama' ? 'selected' : '' }}>Kinerja Utama</option>
                                        <option value="Kinerja Pendukung" {{ old('category', $indicator->category) == 'Kinerja Pendukung' ? 'selected' : '' }}>Kinerja Pendukung</option>
                                        <option value="Kinerja Individu" {{ old('category', $indicator->category) == 'Kinerja Individu' ? 'selected' : '' }}>Kinerja Individu</option>
                                        <option value="Kinerja Tim" {{ old('category', $indicator->category) == 'Kinerja Tim' ? 'selected' : '' }}>Kinerja Tim</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Indikator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $indicator->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="measurement_type" class="form-label">Jenis Pengukuran <span class="text-danger">*</span></label>
                                    <select class="form-select @error('measurement_type') is-invalid @enderror" id="measurement_type" name="measurement_type" required>
                                        <option value="">Pilih Jenis Pengukuran</option>
                                        <option value="Persentase" {{ old('measurement_type', $indicator->measurement_type) == 'Persentase' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="Angka" {{ old('measurement_type', $indicator->measurement_type) == 'Angka' ? 'selected' : '' }}>Angka</option>
                                        <option value="Rasio" {{ old('measurement_type', $indicator->measurement_type) == 'Rasio' ? 'selected' : '' }}>Rasio</option>
                                        <option value="Waktu" {{ old('measurement_type', $indicator->measurement_type) == 'Waktu' ? 'selected' : '' }}>Waktu</option>
                                        <option value="Custom" {{ old('measurement_type', $indicator->measurement_type) == 'Custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('measurement_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Satuan</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $indicator->unit) }}" placeholder="Contoh: %, hari, unit">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="custom_formula_container" style="display: {{ old('measurement_type', $indicator->measurement_type) == 'Custom' ? 'block' : 'none' }};">
                            <label for="custom_formula" class="form-label">Formula Custom</label>
                            <textarea class="form-control @error('custom_formula') is-invalid @enderror" id="custom_formula" name="custom_formula" rows="3">{{ old('custom_formula', $indicator->custom_formula) }}</textarea>
                            @error('custom_formula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan formula perhitungan jika menggunakan jenis pengukuran Custom</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_shared" name="is_shared" value="1" {{ old('is_shared', $indicator->is_shared) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_shared">
                                    Bagikan indikator ini ke tenant lain
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('performance-management.indicators.show', $indicator->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const measurementType = document.getElementById('measurement_type');
        const customFormulaContainer = document.getElementById('custom_formula_container');

        measurementType.addEventListener('change', function() {
            if (this.value === 'Custom') {
                customFormulaContainer.style.display = 'block';
            } else {
                customFormulaContainer.style.display = 'none';
            }
        });

        // Check on page load
        if (measurementType.value === 'Custom') {
            customFormulaContainer.style.display = 'block';
        }
    });
</script>
@endpush
@endsection
