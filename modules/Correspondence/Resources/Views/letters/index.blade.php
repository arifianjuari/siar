@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Daftar Surat')

@section('header')
<!-- Kosong untuk mencegah header otomatis -->
@endsection

@push('styles')
<style>
    .tag-input.form-control-sm {
        font-size: 0.75rem;
        padding: 0.15rem 0.4rem;
        height: calc(1.1 * 0.75rem + 0.15rem * 2 + 2px);
    }
    /* Perkecil tombol tambah tag */
    .tag-form .btn-sm {
        padding: 0.15rem 0.5rem;
        font-size: 0.75rem;
        height: calc(1.1 * 0.75rem + 0.15rem * 2 + 2px);
        line-height: 1.1;
    }
    .pagination {
        margin-bottom: 0;
    }
    /* Menghilangkan garis horizontal dan padding atas */
    .content-wrapper main {
        padding-top: 0 !important;
    }
    .content-wrapper main .border-bottom {
        border-bottom: none !important;
    }
    /* Styling untuk tombol buat surat */
    .btn-buat-surat {
        font-weight: 600;
        padding: 0.5rem 1.5rem;
    }
    .main-container {
        width: 100%;
        margin: 0.5rem auto;
        padding: 0 1rem;
    }
    .main-card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background: #fff;
        border: none;
        margin-bottom: 1rem;
        width: 100%;
    }
    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .header-title {
        font-weight: 600;
        margin: 0;
    }
    .filter-group {
        display: flex;
        flex: 1;
        margin: 0 1rem;
        max-width: 650px;
    }
    .search-input {
        border-radius: 6px 0 0 6px !important;
        border: 1px solid #ddd;
    }
    .card-content {
        padding: 1rem;
    }
    .table-container {
        margin-top: 0.5rem;
        width: 100%;
    }
    /* Perkecil ukuran teks pada tabel */
    .table {
        font-size: 0.85rem;
        width: 100%;
        table-layout: fixed;
    }
    /* Styling untuk kolom yang dapat di-resize */
    .table th {
        position: relative;
        overflow: hidden;
    }
    .table td {
        text-align: left;
        vertical-align: middle;
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .table td:last-child {
        text-align: center;
    }
    .table .resizer {
        position: absolute;
        top: 0;
        right: 0;
        width: 5px;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.1);
        cursor: col-resize;
        user-select: none;
        touch-action: none;
    }
    .table .resizer:hover,
    .table .resizing {
        background-color: #2c65e9;
    }
    /* Perkecil ukuran ikon pada tombol aksi */
    .table .btn i {
        font-size: 0.8rem;
    }
    /* Styling khusus tombol aksi */
    .action-btn {
        width: 28px;
        height: 28px;
        padding: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }
    /* Styling khusus untuk tag */
    .tag-badge {
        font-size: 0.7rem !important;
        padding: 2px 5px !important;
        margin-bottom: 2px;
        line-height: 1.2;
        border-radius: 3px;
        display: flex !important;
        align-items: center !important;
    }
    .tag-badge .btn-close {
        font-size: 0.45rem !important;
        margin-left: 2px !important;
        padding: 0 !important;
    }
    /* Input form tag yang lebih kecil */
    .tag-input {
        font-size: 0.7rem !important;
        height: 24px !important;
        padding: 2px 5px !important;
        width: 100px !important;
    }
    /* Perkecil ukuran badge tag */
    .table .badge {
        font-size: 0.75rem !important;
        padding: 3px 6px !important;
    }
    /* Perkecil form tambah tag */
    .table input.form-control {
        font-size: 0.8rem !important;
        height: 28px !important;
    }
    .table button.btn {
        height: 28px !important;
        font-size: 0.8rem !important;
    }
    @media (max-width: 991px) {
        .card-header-custom {
            flex-direction: column;
            align-items: stretch;
        }
        .header-title {
            margin-bottom: 0.75rem;
        }
        .filter-group {
            margin: 0.75rem 0;
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="main-container">
    <!-- Notification sudah ditangani oleh layout utama -->
    
    <!-- Kartu Utama yang Berisi Semua Elemen -->
    <div class="card main-card">
        <!-- Header dengan Judul, Filter, dan Tombol Buat dalam satu baris -->
        <div class="card-header-custom">
            <h4 class="header-title text-primary">Daftar Surat</h4>
            
            <!-- Filter dalam satu baris -->
            <form action="{{ route('modules.correspondence.letters.index') }}" method="GET" class="filter-group">
                <div class="input-group">
                    <input type="text" class="form-control search-input" id="search" name="search" placeholder="Judul, nomor, perihal..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level" style="border-radius: 0;">
                            <option value="">Semua Level</option>
                            <option value="Publik" {{ request('confidentiality_level') == 'Publik' ? 'selected' : '' }}>Publik</option>
                            <option value="Internal" {{ request('confidentiality_level') == 'Internal' ? 'selected' : '' }}>Internal</option>
                            <option value="Rahasia" {{ request('confidentiality_level') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('modules.correspondence.letters.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Tombol Buat Surat -->
            <a href="{{ route('modules.correspondence.letters.create') }}" class="btn btn-success btn-buat-surat">
                <i class="fas fa-plus mr-1"></i> Buat Surat
            </a>
        </div>

        <!-- Konten Daftar Surat -->
        <div class="card-content">
            @if($correspondences->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                    <p class="mb-2">Tidak ada surat yang ditemukan.</p>
                </div>
            @else
                <div class="table-container table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Tanggal Dibuat<div class="resizer"></div></th>
                                <th>No. Dokumen<div class="resizer"></div></th>
                                <th>Judul<div class="resizer"></div></th>
                                <th>Perihal<div class="resizer"></div></th>
                                <th>Pengirim<div class="resizer"></div></th>
                                <th>Penerima<div class="resizer"></div></th>
                                <th>Tag<div class="resizer"></div></th>
                                <th>Aksi<div class="resizer"></div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($correspondences as $letter)
                                <tr>
                                    <td>{{ $letter->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $letter->document_number ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('modules.correspondence.letters.show', $letter->id) }}" class="font-weight-bold text-dark">
                                            {{ $letter->document_title }}
                                        </a>
                                    </td>
                                    <td>{{ $letter->subject ?? '-' }}</td>
                                    <td>{{ $letter->sender_name }}</td>
                                    <td>{{ $letter->recipient_name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 2px;">
                                            @foreach($letter->tags as $tag)
                                                <div class="badge bg-dark text-white tag-badge" 
                                                     id="tag-item-{{ $tag->id }}-{{ $letter->id }}"> 
                                                    <span>{{ $tag->name }}</span>
                                                    <button 
                                                        type="button" 
                                                        class="btn-close btn-close-white"
                                                        onclick="hapusTagLangsung({{ $tag->id }}, {{ $letter->id }}, 'App\\Models\\Correspondence')"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <form class="d-flex mt-1" style="gap: 2px;" data-letter-id="{{ $letter->id }}">
                                            <input type="text" class="form-control tag-input w-100" placeholder="Tambah tag" required>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap justify-content-center" style="gap: 5px; min-width: 130px;">
                                            <a href="{{ route('modules.correspondence.letters.show', $letter->id) }}" class="btn btn-info action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('modules.correspondence.letters.edit', $letter->id) }}" class="btn btn-warning action-btn">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="{{ route('modules.correspondence.letters.export-pdf', $letter->id) }}" class="btn btn-primary action-btn">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            
                                            <form action="{{ route('modules.correspondence.letters.destroy', $letter->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $correspondences->firstItem() ?? 0 }} - {{ $correspondences->lastItem() ?? 0 }} dari {{ $correspondences->total() }} data
                    </div>
                    <div>
                        {{ $correspondences->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Implementasi resize kolom tabel
        const table = document.querySelector('.table');
        const resizers = document.querySelectorAll('.table .resizer');
        let currentResizer;
        
        // Simpan lebar kolom di local storage
        function saveColumnWidths() {
            const headers = document.querySelectorAll('.table th');
            const widths = {};
            
            headers.forEach((header, index) => {
                widths[index] = header.style.width;
            });
            
            localStorage.setItem('tableColumnWidths', JSON.stringify(widths));
        }
        
        // Muat lebar kolom dari local storage
        function loadColumnWidths() {
            const widths = JSON.parse(localStorage.getItem('tableColumnWidths'));
            if (widths) {
                const headers = document.querySelectorAll('.table th');
                
                headers.forEach((header, index) => {
                    if (widths[index]) {
                        header.style.width = widths[index];
                    }
                });
            }
        }
        
        // Inisialisasi lebar kolom
        function initColumnWidths() {
            // Coba muat dari local storage terlebih dahulu
            if (localStorage.getItem('tableColumnWidths')) {
                loadColumnWidths();
            } else {
                // Default widths jika tidak ada di local storage
                const headers = document.querySelectorAll('.table th');
                const defaultWidths = ['115px', '150px', '200px', '150px', '100px', '100px', '120px', '140px'];
                
                headers.forEach((header, index) => {
                    if (defaultWidths[index]) {
                        header.style.width = defaultWidths[index];
                    }
                });
                
                saveColumnWidths();
            }
        }
        
        // Inisialisasi resizer
        resizers.forEach(resizer => {
            resizer.addEventListener('mousedown', mouseDown);
            
            function mouseDown(e) {
                currentResizer = e.target;
                const th = currentResizer.parentElement;
                const table = th.closest('table');
                const startWidth = th.getBoundingClientRect().width;
                const startX = e.pageX;
                
                table.style.cursor = 'col-resize';
                currentResizer.classList.add('resizing');
                
                document.addEventListener('mousemove', mouseMove);
                document.addEventListener('mouseup', mouseUp);
                
                function mouseMove(e) {
                    const width = startWidth + (e.pageX - startX);
                    if (width > 50) { // Minimal width
                        th.style.width = `${width}px`;
                    }
                }
                
                function mouseUp() {
                    table.style.cursor = '';
                    currentResizer.classList.remove('resizing');
                    document.removeEventListener('mousemove', mouseMove);
                    document.removeEventListener('mouseup', mouseUp);
                    
                    saveColumnWidths();
                }
            }
        });
        
        // Inisialisasi lebar kolom
        initColumnWidths();
        
        // Tangani submit form tambah tag
        const tagForms = document.querySelectorAll('form[data-letter-id]');
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
                const letterId = form.dataset.letterId;
                const inputElement = form.querySelector('input[type="text"]');
                const tagName = inputElement.value.trim();
                
                if (!tagName) {
                    isSubmitting = false; // Reset flag jika input kosong
                    return; 
                }
                
                // Buat form data
                const formData = new FormData();
                formData.append('tag_name', tagName);
                formData.append('document_id', letterId);
                formData.append('document_type', 'App\\Models\\Correspondence');
                formData.append('_token', csrfToken);

                const tagsContainer = form.previousElementSibling; // Target kontainer tag
                
                // Kirim request dengan fetch API
                fetch('{{ route('tenant.tags.create-and-attach') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Server error: ${response.status} ${response.statusText}. ${text}`);
                        });
                    }
                    return response.json(); 
                })
                .then(data => {
                    if (data.success && data.tag) {
                        const newTag = data.tag; 
                        if (!tagsContainer.querySelector(`#tag-item-${newTag.id}-${letterId}`)) {
                            const newTagHtml = `
                                <div class="badge bg-dark text-white tag-badge" 
                                     id="tag-item-${newTag.id}-${letterId}"> 
                                    <span>${newTag.name}</span>
                                    <button 
                                        type="button" 
                                        class="btn-close btn-close-white"
                                        onclick="hapusTagLangsung(${newTag.id}, ${letterId}, 'App\\\\Models\\\\Correspondence')"
                                        aria-label="Close">
                                    </button>
                                </div>
                            `;
                            tagsContainer.insertAdjacentHTML('beforeend', newTagHtml);
                        }
                        inputElement.value = ''; // Reset input
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    isSubmitting = false;
                });
            });
        });
    });

    // Fungsi untuk menghapus tag langsung
    function hapusTagLangsung(tagId, documentId, documentType) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const tagElement = document.getElementById(`tag-item-${tagId}-${documentId}`);
        if (tagElement) {
            tagElement.style.opacity = '0.5'; // Visual feedback
        }
        
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        formData.append('_token', csrfToken);
        
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
        });
    }
</script>
@endpush 