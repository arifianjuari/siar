@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Edit Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Edit Kegiatan</h5>
                        <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('activity-management.activities.update', $activity->uuid) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Informasi Dasar Kegiatan -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Informasi Dasar</h5>
                            </div>
                            
                            <!-- Judul Kegiatan -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $activity->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Kategori -->
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $activity->category) }}" required list="category-list">
                                <datalist id="category-list">
                                    <option value="Proyek">
                                    <option value="Rapat">
                                    <option value="Penelitian">
                                    <option value="Pengembangan">
                                    <option value="Pemeliharaan">
                                    <option value="Pelatihan">
                                    <option value="Audit">
                                    <option value="Evaluasi">
                                </datalist>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Deskripsi -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $activity->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Jadwal dan Prioritas -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Jadwal dan Prioritas</h5>
                            </div>
                            
                            <!-- Tanggal Mulai -->
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $activity->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tanggal Selesai -->
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $activity->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tenggat Waktu -->
                            <div class="col-md-4 mb-3">
                                <label for="due_date" class="form-label">Tenggat Waktu</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $activity->due_date ? $activity->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Kosongkan jika sama dengan tanggal selesai</small>
                            </div>
                            
                            <!-- Prioritas -->
                            <div class="col-md-4 mb-3">
                                <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="low" {{ old('priority', $activity->priority) == 'low' ? 'selected' : '' }}>Rendah</option>
                                    <option value="medium" {{ old('priority', $activity->priority) == 'medium' ? 'selected' : '' }}>Sedang</option>
                                    <option value="high" {{ old('priority', $activity->priority) == 'high' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="critical" {{ old('priority', $activity->priority) == 'critical' ? 'selected' : '' }}>Kritis</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Progress -->
                            <div class="col-md-4 mb-3">
                                <label for="progress_percentage" class="form-label">Progres Penyelesaian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('progress_percentage') is-invalid @enderror" id="progress_percentage" name="progress_percentage" value="{{ old('progress_percentage', $activity->progress_percentage) }}" min="0" max="100" step="1" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('progress_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Unit Kerja dan Relasi -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Unit Kerja dan Relasi</h5>
                            </div>
                            
                            <!-- Unit Kerja -->
                            <div class="col-md-6 mb-3">
                                <label for="work_unit_id" class="form-label">Unit Kerja</label>
                                <select class="form-select @error('work_unit_id') is-invalid @enderror" id="work_unit_id" name="work_unit_id">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($workUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('work_unit_id', $activity->work_unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('work_unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Kegiatan Induk -->
                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">Kegiatan Induk</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach($parentActivities as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $activity->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Pilih jika kegiatan ini adalah sub-kegiatan dari kegiatan lain</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 jika ada
        if ($.fn.select2) {
            $('#work_unit_id, #parent_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
        
        // Validasi tanggal
        $('#start_date, #end_date, #due_date').on('change', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const dueDate = $('#due_date').val();
            
            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                $('#end_date').val('');
            }
            
            if (startDate && dueDate && new Date(dueDate) < new Date(startDate)) {
                alert('Tenggat waktu tidak boleh lebih awal dari tanggal mulai');
                $('#due_date').val('');
            }
        });
    });
</script>
@endpush
