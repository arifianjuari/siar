@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Tambah SPO Baru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Dokumen SPO Baru</h1>
        
        <a href="{{ route('work-units.spo.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <!-- Form SPO -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form SPO Baru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('work-units.spo.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Informasi Dokumen</h5>
                        
                        <div class="mb-3">
                            <label for="document_title" class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('document_title') is-invalid @enderror" 
                                id="document_title" name="document_title" value="{{ old('document_title') }}" required>
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
                                        <option value="{{ $value }}" {{ old('document_type', $defaultValues['document_type']) == $value ? 'selected' : '' }}>
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
                                    id="document_version" name="document_version" value="{{ old('document_version', $defaultValues['document_version']) }}" required>
                                @error('document_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="document_number" class="form-label">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('document_number') is-invalid @enderror" 
                                id="document_number" name="document_number" value="{{ old('document_number', $defaultValues['document_number']) }}" required>
                            <div class="form-text">Format: [nomor urut]/[bulan romawi]/[tahun]/SPO</div>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="document_date" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('document_date') is-invalid @enderror" 
                                    id="document_date" name="document_date" value="{{ old('document_date', $defaultValues['document_date']) }}" required>
                                @error('document_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                                <select class="form-select @error('confidentiality_level') is-invalid @enderror" 
                                    id="confidentiality_level" name="confidentiality_level" required>
                                    @foreach($confidentialityLevels as $value => $label)
                                        <option value="{{ $value }}" {{ old('confidentiality_level', $defaultValues['confidentiality_level']) == $value ? 'selected' : '' }}>
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
                                    <option value="{{ $workUnit->id }}" {{ old('work_unit_id') == $workUnit->id ? 'selected' : '' }}>
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
                                    <option value="{{ $unit->id }}" {{ (is_array(old('linked_unit')) && in_array($unit->id, old('linked_unit'))) ? 'selected' : '' }}>
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
                            <label for="file_url" class="form-label">Link Dokumen</label>
                            <input type="url" class="form-control @error('file_url') is-invalid @enderror" 
                                id="file_url" name="file_url" value="{{ old('file_url') }}" 
                                placeholder="https://drive.google.com/file/d/xxx/view">
                            <div class="form-text">Masukkan link Google Drive atau link lainnya ke dokumen SPO (opsional)</div>
                            @error('file_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tag-input" class="form-label">Tag</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tag-input" placeholder="Ketik tag lalu tekan Enter atau tombol Tambah">
                                <button class="btn btn-outline-secondary" type="button" id="add-tag-button">Tambah</button>
                            </div>
                            <div id="tags-container" class="mt-2 d-flex flex-wrap">
                                {{-- Badge tag akan muncul di sini --}}
                            </div>
                            <div id="hidden-tags-container">
                                @if(old('tags'))
                                    @foreach(old('tags') as $oldTag)
                                        <input type="hidden" name="tags[]" value="{{ $oldTag }}">
                                    @endforeach
                                @endif
                            </div>
                            <small class="text-muted">Anda bisa menambahkan tag untuk memudahkan pencarian dan pengkategorian.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="review_cycle_months" class="form-label">Siklus Review (bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('review_cycle_months') is-invalid @enderror" 
                                id="review_cycle_months" name="review_cycle_months" value="{{ old('review_cycle_months', $defaultValues['review_cycle_months']) }}" min="1" max="60" required>
                            @error('review_cycle_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Isi Dokumen</h5>
                        
                        <div class="mb-3">
                            <label for="definition" class="form-label">Pengertian</label>
                            <textarea class="form-control @error('definition') is-invalid @enderror" 
                                id="definition" name="definition" rows="3">{{ old('definition') }}</textarea>
                            @error('definition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Tujuan</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                id="purpose" name="purpose" rows="3">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="policy" class="form-label">Kebijakan</label>
                            <textarea class="form-control @error('policy') is-invalid @enderror" 
                                id="policy" name="policy" rows="3">{{ old('policy') }}</textarea>
                            @error('policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="procedure" class="form-label">Prosedur</label>
                            <textarea class="form-control @error('procedure') is-invalid @enderror" 
                                id="procedure" name="procedure" rows="8">{{ old('procedure') }}</textarea>
                            @error('procedure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reference" class="form-label">Referensi</label>
                            <textarea class="form-control @error('reference') is-invalid @enderror" 
                                id="reference" name="reference" rows="3">{{ old('reference') }}</textarea>
                            <div class="form-text">Masukkan referensi dokumen atau sumber yang digunakan dalam pembuatan SPO ini.</div>
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('work-units.spo.index') }}" class="btn btn-secondary ms-2">
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

        // === Logika Tag Input ===
        const tagInput = document.getElementById('tag-input');
        const addTagButton = document.getElementById('add-tag-button');
        const tagsContainer = document.getElementById('tags-container');
        const hiddenTagsContainer = document.getElementById('hidden-tags-container');
    
        function addTag(tagName) {
            tagName = tagName.trim();
            if (!tagName) return; // Jangan tambahkan jika kosong
    
            // Cek duplikasi visual
            const existingBadges = tagsContainer.querySelectorAll('.tag-badge');
            for (let badge of existingBadges) {
                if (badge.dataset.tagName.toLowerCase() === tagName.toLowerCase()) {
                    tagInput.value = ''; // Kosongkan input saja
                    return;
                }
            }
    
            // Buat badge visual
            const badgeId = `tag-badge-${Date.now()}${Math.random()}`; // ID unik
            const badge = document.createElement('div');
            badge.classList.add('d-flex', 'align-items-center', 'badge', 'bg-info', 'text-white', 'me-1', 'mb-1', 'p-1', 'tag-badge');
            badge.style.fontSize = '0.75rem';
            badge.dataset.tagName = tagName;
            badge.id = badgeId;
    
            const badgeText = document.createElement('span');
            badgeText.textContent = tagName;
            badge.appendChild(badgeText);
    
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.classList.add('btn-close', 'btn-close-white', 'ms-2');
            closeButton.style.fontSize = '0.6rem';
            closeButton.ariaLabel = 'Close';
            closeButton.onclick = function() { removeTag(badgeId, tagName); };
            badge.appendChild(closeButton);
    
            tagsContainer.appendChild(badge);
    
            // Tambahkan ke hidden input
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';
            hiddenInput.value = tagName;
            hiddenInput.id = `hidden-${badgeId}`;
            hiddenTagsContainer.appendChild(hiddenInput);
    
            // Kosongkan input field
            tagInput.value = '';
            tagInput.focus(); // Fokus kembali ke input
        }
    
        function removeTag(badgeId, tagName) {
            const badgeElement = document.getElementById(badgeId);
            const hiddenInputElement = document.getElementById(`hidden-${badgeId}`);
    
            if (badgeElement) {
                badgeElement.remove();
            }
            if (hiddenInputElement) {
                hiddenInputElement.remove();
            }
        }

        // Tambahkan tag dari old input jika ada (saat validasi error)
        function addTagFromValue(tagName, existingInput) {
            tagName = tagName.trim();
            if (!tagName) return;
    
            const badgeId = `tag-badge-${Date.now()}${Math.random()}`; // ID unik
            const badge = document.createElement('div');
            badge.classList.add('d-flex', 'align-items-center', 'badge', 'bg-info', 'text-white', 'me-1', 'mb-1', 'p-1', 'tag-badge');
            badge.style.fontSize = '0.75rem';
            badge.dataset.tagName = tagName;
            badge.id = badgeId;
    
            const badgeText = document.createElement('span');
            badgeText.textContent = tagName;
            badge.appendChild(badgeText);
    
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.classList.add('btn-close', 'btn-close-white', 'ms-2');
            closeButton.style.fontSize = '0.6rem';
            closeButton.ariaLabel = 'Close';
            // Pastikan fungsi removeTag juga menghapus input hidden yang sudah ada
            closeButton.onclick = function() { 
                const badgeElement = document.getElementById(badgeId);
                if (badgeElement) badgeElement.remove();
                if (existingInput) existingInput.remove(); 
            };
            badge.appendChild(closeButton);
    
            tagsContainer.appendChild(badge);
            // Set ID pada input hidden yang sudah ada agar bisa dihapus
            if(existingInput) existingInput.id = `hidden-${badgeId}`; 
        }
    
        // Event listeners untuk input tag
        if(tagInput) {
            tagInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Cegah submit form
                    addTag(tagInput.value);
                }
            });
        }
        
        if(addTagButton) {
            addTagButton.addEventListener('click', function() {
                addTag(tagInput.value);
            });
        }
    
        // Tambahkan tag dari old input jika ada (saat validasi error)
        const existingHiddenTags = hiddenTagsContainer.querySelectorAll('input[name="tags[]"]');
        if(existingHiddenTags.length > 0) {
            existingHiddenTags.forEach(input => {
                addTagFromValue(input.value, input);
            });
        }
        // === Akhir Logika Tag Input ===
    });
</script>
@endpush
@endsection 