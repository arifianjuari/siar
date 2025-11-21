@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Standar Prosedur Operasional (SPO)')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Standar Prosedur Operasional (SPO)</h1>
    </div>
    
    <!-- Filter dan Pencarian -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter dan Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('spo.index') }}" method="GET" class="mb-0">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="work_unit_id" class="form-label">Unit Kerja</label>
                        <select class="form-select" id="work_unit_id" name="work_unit_id">
                            <option value="">Semua Unit Kerja</option>
                            @foreach($workUnits as $unit)
                                <option value="{{ $unit->id }}" {{ request('work_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="document_type" class="form-label">Jenis Dokumen</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="">Semua Jenis</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}" {{ request('document_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            @foreach($statusValidasi as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari judul atau nomor dokumen" value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <a href="{{ route('spo.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Daftar SPO -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Standar Prosedur Operasional</h6>
            
            <a href="{{ route('spo.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i> Tambah SPO Baru
            </a>
        </div>
        <div class="card-body">
            @if($spos->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-0">Belum ada dokumen SPO yang tersedia.</p>
                    
                    <a href="{{ route('spo.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Tambah SPO Baru
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="15%">Nomor SPO</th>
                                <th width="20%">Judul Dokumen</th>
                                <th width="7%">Versi</th>
                                <th width="12%">Tgl. Berlaku</th>
                                <th width="10%">Status</th>
                                <th width="10%">Tag</th>
                                <th width="11%">Tgl. Perubahan</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spos as $index => $spo)
                                <tr>
                                    <td>{{ $spos->firstItem() + $index }}</td>
                                    <td>{{ $spo->document_number }}</td>
                                    <td>{{ $spo->document_title }}</td>
                                    <td>{{ $spo->document_version }}</td>
                                    <td>{{ $spo->document_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $spo->status_validasi == 'Draft' ? 'bg-secondary' : 
                                                          ($spo->status_validasi == 'Disetujui' ? 'bg-success' : 
                                                          ($spo->status_validasi == 'Kadaluarsa' ? 'bg-danger' : 'bg-warning')) }}">
                                            {{ $spo->status_validasi }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 2px;">
                                            @forelse($spo->tags as $tag)
                                                <div class="badge bg-info text-white tag-badge" 
                                                     id="tag-item-{{ $tag->id }}-{{ $spo->id }}"> 
                                                    <span>{{ $tag->name }}</span>
                                                    <button 
                                                        type="button" 
                                                        class="btn-close btn-close-white"
                                                        onclick="hapusTagLangsung({{ $tag->id }}, '{{ $spo->id }}', 'App\\Models\\SPO')"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                            @empty
                                                <small class="text-muted">Tidak ada tag</small>
                                            @endforelse
                                        </div>
                                        
                                        <form class="d-flex mt-1" style="gap: 2px;" data-spo-id="{{ $spo->id }}">
                                            <input type="text" class="form-control tag-input w-100" placeholder="Tambah tag" required>
                                        </form>
                                    </td>
                                    <td>{{ $spo->updated_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('spo.show', $spo) }}" class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($spo->file_path)
                                        <a href="{{ $spo->file_path }}" class="btn btn-sm btn-secondary mb-1" target="_blank" title="Lihat Dokumen">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        @endif
                                        
                                        {{-- Debug info --}}
                                        @php
                                            $canUpdate = auth()->user()->can('update', $spo);
                                            $canDelete = auth()->user()->can('delete', $spo);
                                            $hasUpdatePermission = auth()->user()->hasPermission('work-units', 'can_edit');
                                            $hasDeletePermission = auth()->user()->hasPermission('work-units', 'can_delete');
                                        @endphp
                                        <div class="small d-none">
                                            canUpdate: {{ $canUpdate ? 'true' : 'false' }}<br>
                                            canDelete: {{ $canDelete ? 'true' : 'false' }}<br>
                                            hasUpdatePermission: {{ $hasUpdatePermission ? 'true' : 'false' }}<br>
                                            hasDeletePermission: {{ $hasDeletePermission ? 'true' : 'false' }}
                                        </div>
                                        
                                        {{-- Edit button --}}
                                        <a href="{{ route('spo.edit', $spo) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- Generate PDF button --}}
                                        <a href="{{ route('spo.generate-pdf', $spo) }}" class="btn btn-sm btn-primary mb-1" title="Generate PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        
                                        {{-- Delete button --}}
                                        <form action="{{ route('spo.destroy', $spo) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $spos->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dapatkan semua form tag
        const tagForms = document.querySelectorAll('form[data-spo-id]');
        
        // Fungsi untuk menghapus tag
        window.hapusTagLangsung = function(tagId, spoId, documentType) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Hapus elemen tag dari DOM terlebih dahulu (optimistic update)
            const tagElement = document.getElementById(`tag-item-${tagId}-${spoId}`);
            if (tagElement) {
                tagElement.style.opacity = '0.5'; // Visual feedback bahwa sedang diproses
                
                // Tambahkan placeholder "Tidak ada tag" jika semua tag sudah dihapus
                const tagsContainer = document.querySelector(`.d-flex.flex-wrap[style="gap: 2px;"]`);
                if (tagsContainer && !tagsContainer.querySelector('.tag-badge')) {
                    const emptyMsg = document.createElement('small');
                    emptyMsg.className = 'text-muted';
                    emptyMsg.textContent = 'Tidak ada tag';
                    tagsContainer.appendChild(emptyMsg);
                }
            }
            
            // Buat form data
            const formData = new FormData();
            formData.append('tag_id', tagId);
            formData.append('document_id', spoId);
            formData.append('document_type', documentType);
            formData.append('_token', csrfToken);
            
            // Kirim request dengan fetch API 
            fetch('{{ route('tenant.tags.delete-tag') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    if (tagElement) {
                        tagElement.remove();
                    }
                } else {
                    return response.text().then(text => {
                        throw new Error(`Server error: ${response.status} ${response.statusText}. ${text}`);
                    });
                }
            })
            .catch(error => {
                console.error('Error saat menghapus tag:', error);
                if (tagElement) {
                    tagElement.style.opacity = '1'; // Kembalikan tampilan jika gagal
                }
                alert('Terjadi kesalahan saat menghapus tag. Refresh halaman untuk melihat status terbaru.');
            });
        };
        
        // Handle submit form untuk setiap input tag
        tagForms.forEach(function(form) {
            let isSubmitting = false; // Flag untuk mencegah submit ganda
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    return; // Cegah submit ganda
                }
                isSubmitting = true; // Set flag
                
                // Dapatkan CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Dapatkan data dari form
                const spoId = form.dataset.spoId;
                const inputElement = form.querySelector('input[type="text"]');
                const tagName = inputElement.value.trim();
                
                if (!tagName) {
                    isSubmitting = false; // Reset flag jika input kosong
                    return; 
                }
                
                // Buat form data
                const formData = new FormData();
                formData.append('tag_name', tagName);
                formData.append('document_id', spoId);
                formData.append('document_type', 'App\\Models\\SPO');
                formData.append('_token', csrfToken);

                const tagsContainer = form.previousElementSibling; // Target kontainer tag
                
                // Tampilkan indikator visual bahwa tag sedang ditambahkan
                inputElement.disabled = true;
                inputElement.style.opacity = '0.5';
                
                // Kirim request dengan fetch API
                fetch('{{ route('tenant.tags.create-and-attach') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Cek jenis response
                    if (data.success) {
                        // Cek apakah tag sudah ada di DOM untuk mencegah duplikasi
                        const existingTag = tagsContainer.querySelector(`div[id="tag-item-${data.tag.id}-${spoId}"]`);
                        if (!existingTag) {
                            // Buat badge tag baru
                            const newTag = document.createElement('div');
                            newTag.className = 'badge bg-info text-white tag-badge';
                            newTag.id = `tag-item-${data.tag.id}-${spoId}`;
                            
                            newTag.innerHTML = `
                                <span>${data.tag.name}</span>
                                <button 
                                    type="button" 
                                    class="btn-close btn-close-white"
                                    onclick="hapusTagLangsung(${data.tag.id}, '${spoId}', 'App\\\\Models\\\\SPO')"
                                    aria-label="Close">
                                </button>
                            `;
                            
                            // Jika ada placeholder "Tidak ada tag", hapus itu terlebih dahulu
                            const emptyMsg = tagsContainer.querySelector('small.text-muted');
                            if (emptyMsg) {
                                emptyMsg.remove();
                            }
                            
                            // Tambahkan tag baru ke kontainer
                            tagsContainer.appendChild(newTag);
                        } else {
                            console.log('Tag sudah ada di DOM, tidak perlu ditambahkan lagi');
                        }
                        
                        // Reset input
                        inputElement.value = '';
                    } 
                    // Jika ini kesalahan duplicate entry, jangan tampilkan alert
                    else if (data.error && data.error.includes('Duplicate entry')) {
                        console.log('Tag sudah ada, tidak perlu menambahkan lagi');
                        inputElement.value = '';
                    }
                    // Jika kesalahan lain, tampilkan alert
                    else {
                        alert('Gagal menambahkan tag: ' + (data.error || 'Unknown error'));
                    }
                    
                    // Kembalikan input ke status normal
                    inputElement.disabled = false;
                    inputElement.style.opacity = '1';
                    isSubmitting = false; // Reset flag
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan tag');
                    // Kembalikan input ke status normal
                    inputElement.disabled = false;
                    inputElement.style.opacity = '1';
                    isSubmitting = false; // Reset flag
                });
            });
            
            // Handle enter key pada input dengan debounce
            const inputElement = form.querySelector('input[type="text"]');
            let debounceTimer;
            
            inputElement.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Pembatalan timer sebelumnya jika ada
                    clearTimeout(debounceTimer);
                    
                    // Setel timer baru
                    debounceTimer = setTimeout(() => {
                        form.dispatchEvent(new Event('submit'));
                    }, 300); // 300ms debounce
                }
            });
        });
    });
</script>
@endpush 