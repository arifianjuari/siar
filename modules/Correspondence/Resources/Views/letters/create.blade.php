@php
    // --- Penambahan Mulai ---
    $currentYear = date('Y');
    $currentMonth = date('n');
    $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $romanMonth = $romanMonths[$currentMonth];
    // Placeholder untuk nomor urut surat. Ini harus di-pass dari Controller.
    $defaultDocumentNumber = "B/ND-" . ($nextLetterNumber ?? '...') . " /" . $romanMonth . "/" . $currentYear . "/...";

    // -- Perbaikan Isi Surat Mulai --
    $defaultBody = "Sehubungan dengan rujukan tersebut di atas, bersama ini kami merencanakan untuk melakukan kegiatan sebagai berikut :\n" .
                   "Nama Giat : \n" .
                   "Waktu        : \n" .
                   "Tempat      : \n" .
                   "Agenda      :";
    // -- Perbaikan Isi Surat Selesai --
    // --- Penambahan Selesai ---
@endphp
@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.dashboard') }}">Korespondensi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.letters.index') }}">Daftar Surat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Surat Baru</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('modules.correspondence.letters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Surat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_title" class="form-label">Judul Surat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_title" name="document_title" value="{{ old('document_title') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document_number" class="form-label">Nomor Surat</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="document_number" name="document_number" value="{{ old('document_number', $defaultDocumentNumber) }}">
                            <button class="btn btn-outline-secondary" type="button" id="select-letter-btn">Pilih dari Daftar</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Regulasi" {{ old('document_type', 'Bukti') == 'Regulasi' ? 'selected' : '' }}>Regulasi</option>
                            <option value="Bukti" {{ old('document_type', 'Bukti') == 'Bukti' ? 'selected' : '' }}>Bukti</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="document_version" class="form-label">Versi Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_version" name="document_version" value="{{ old('document_version', '1.0') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="Publik" {{ old('confidentiality_level', 'Internal') == 'Publik' ? 'selected' : '' }}>Publik</option>
                            <option value="Internal" {{ old('confidentiality_level', 'Internal') == 'Internal' ? 'selected' : '' }}>Internal</option>
                            <option value="Rahasia" {{ old('confidentiality_level', 'Internal') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_date" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="document_date" name="document_date" value="{{ old('document_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subject" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="body" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="body" name="body" rows="6" required>{{ old('body', $defaultBody) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_name" class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sender_position" class="form-label">Jabatan Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_position" name="sender_position" value="{{ old('sender_position') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="recipient_position" class="form-label">Jabatan Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_position" name="recipient_position" value="{{ old('recipient_position') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="cc_list" class="form-label">Tembusan</label>
                        <textarea class="form-control" id="cc_list" name="cc_list" rows="2">{{ old('cc_list') }}</textarea>
                        <small class="text-muted">Pisahkan dengan baris baru untuk setiap penerima tembusan.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="reference_to" class="form-label">Referensi</label>
                        <div class="input-group mb-2">
                            <textarea class="form-control" id="reference_to" name="reference_to" rows="3">{{ old('reference_to') }}</textarea>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="select-reference-btn">Pilih Referensi</button>
                                <button class="btn btn-outline-secondary" type="button" id="select-letter-for-ref-btn">Pilih Surat</button>
                            </div>
                        </div>
                        <small class="text-muted">Anda dapat menuliskan referensi secara bebas atau memilih dari daftar referensi/surat yang tersedia.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Penandatangan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signed_at_location" class="form-label">Lokasi Penandatanganan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signed_at_location" name="signed_at_location" value="{{ old('signed_at_location') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signed_at_date" class="form-label">Tanggal Penandatanganan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="signed_at_date" name="signed_at_date" value="{{ old('signed_at_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_name" class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_name" name="signatory_name" value="{{ old('signatory_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_position" class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_position" name="signatory_position" value="{{ old('signatory_position') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_rank" class="form-label">Pangkat Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_rank" name="signatory_rank" value="{{ old('signatory_rank') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_nrp" class="form-label">NRP/NIP Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_nrp" name="signatory_nrp" value="{{ old('signatory_nrp') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lampiran & Metadata</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="document_link" class="form-label">Link File Dokumen</label>
                        <input type="text" class="form-control" id="document_link" name="document_link" placeholder="Masukkan link menuju file yang tersimpan di cloud (Google Drive, OneDrive, dll)" value="{{ old('document_link') }}">
                        <small class="text-muted">Pastikan link dapat diakses oleh penerima surat.</small>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 mb-3">
                        <label for="tag-input" class="form-label">Tag</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="tag-input" placeholder="Ketik tag lalu tekan Enter atau tombol Tambah">
                            <button class="btn btn-outline-secondary" type="button" id="add-tag-button">Tambah</button>
                        </div>
                        <div id="tags-container" class="mt-2 d-flex flex-wrap gap-1 mb-1">
                            {{-- Tag badges akan ditambahkan di sini oleh JS --}}
                        </div>
                        <div id="hidden-tags-container">
                            @if(old('tags'))
                                @foreach(old('tags') as $tagName)
                                    <input type="hidden" name="tags[]" value="{{ $tagName }}">
                                @endforeach
                            @endif
                        </div>
                        <small class="text-muted">Anda bisa menambahkan tag baru atau menggunakan tag yang sudah ada.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('modules.correspondence.letters.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan Surat
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Script dipindah ke sini karena @section('scripts') tidak berjalan --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded from Inline Script.');
        
        // === Data User ===
        const userPosition = "{{ auth()->user()->position ?? '' }}";
        const userName = "{{ auth()->user()->name ?? '' }}";
        const userRank = "{{ auth()->user()->rank ?? '' }}";
        const userNrp = "{{ auth()->user()->nrp ?? '' }}";
        
        console.log('User Name from Blade:', userName);
        console.log('User Position from Blade:', userPosition);
        console.log('User Rank from Blade:', userRank);
        console.log('User NRP from Blade:', userNrp);
        
        // === Isi Data Pengirim ===
        const senderNameInput = document.getElementById('sender_name');
        const senderPositionInput = document.getElementById('sender_position');
        
        if (senderNameInput) {
            console.log('Initial Sender Name Input Value:', senderNameInput.value);
            if (userName && senderNameInput.value === '') {
                console.log('Condition met for Sender Name. Setting value...');
                senderNameInput.value = userName;
            } else {
                console.log('Condition NOT met for Sender Name. Reason:', { hasUserName: !!userName, isValueEmpty: senderNameInput.value === '' });
            }
        } else {
            console.error('Element with ID \'sender_name\' not found!');
        }

        if (senderPositionInput) {
            console.log('Initial Sender Position Input Value:', senderPositionInput.value);
            if (userPosition && senderPositionInput.value === '') {
                console.log('Condition met for Sender Position. Setting value...');
                senderPositionInput.value = userPosition;
            } else {
                console.log('Condition NOT met for Sender Position. Reason:', { hasUserPosition: !!userPosition, isValueEmpty: senderPositionInput.value === '' });
            }
        } else {
            console.error('Element with ID \'sender_position\' not found!');
        }
        
        // === Isi Data Penandatangan ===
        const signatoryNameInput = document.getElementById('signatory_name');
        const signatoryPositionInput = document.getElementById('signatory_position');
        const signatoryRankInput = document.getElementById('signatory_rank');
        const signatoryNrpInput = document.getElementById('signatory_nrp');

        if (signatoryNameInput) {
            console.log('Initial Signatory Name Input Value:', signatoryNameInput.value);
            if (userName && signatoryNameInput.value === '') {
                console.log('Condition met for Signatory Name. Setting value...');
                signatoryNameInput.value = userName;
            } else {
                console.log('Condition NOT met for Signatory Name. Reason:', { hasUserName: !!userName, isValueEmpty: signatoryNameInput.value === '' });
            }
        } else {
             console.error('Element with ID \'signatory_name\' not found!');
        }

        if (signatoryPositionInput) {
            console.log('Initial Signatory Position Input Value:', signatoryPositionInput.value);
            if (userPosition && signatoryPositionInput.value === '') {
                console.log('Condition met for Signatory Position. Setting value...');
                signatoryPositionInput.value = userPosition;
            } else {
                console.log('Condition NOT met for Signatory Position. Reason:', { hasUserPosition: !!userPosition, isValueEmpty: signatoryPositionInput.value === '' });
            }
        } else {
             console.error('Element with ID \'signatory_position\' not found!');
        }

        if (signatoryRankInput) {
            console.log('Initial Signatory Rank Input Value:', signatoryRankInput.value);
            if (userRank && signatoryRankInput.value === '') {
                console.log('Condition met for Signatory Rank. Setting value...');
                signatoryRankInput.value = userRank;
            } else {
                console.log('Condition NOT met for Signatory Rank. Reason:', { hasUserRank: !!userRank, isValueEmpty: signatoryRankInput.value === '' });
            }
        } else {
             console.error('Element with ID \'signatory_rank\' not found!');
        }

        if (signatoryNrpInput) {
            console.log('Initial Signatory NRP Input Value:', signatoryNrpInput.value);
            if (userNrp && signatoryNrpInput.value === '') {
                console.log('Condition met for Signatory NRP. Setting value...');
                signatoryNrpInput.value = userNrp;
            } else {
                console.log('Condition NOT met for Signatory NRP. Reason:', { hasUserNrp: !!userNrp, isValueEmpty: signatoryNrpInput.value === '' });
            }
        } else {
             console.error('Element with ID \'signatory_nrp\' not found!');
        }
        
        // === Formatir WYSIWYG ===
        if (typeof ClassicEditor !== 'undefined') {
            console.log('Attempting to initialize ClassicEditor for #body');
            ClassicEditor
                .create(document.querySelector('#body'))
                .then(editor => {
                    console.log('ClassicEditor initialized successfully.');
                })
                .catch(error => {
                    console.error('Error initializing ClassicEditor:', error);
                });
        } else {
            console.log('ClassicEditor is not defined.');
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
            const badgeId = `tag-badge-${Date.now()}`; // ID unik sementara
            const badge = document.createElement('div');
            badge.classList.add('d-flex', 'align-items-center', 'badge', 'bg-secondary', 'text-white', 'me-1', 'mb-1', 'p-1', 'tag-badge');
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
        existingHiddenTags.forEach(input => {
            addTagFromValue(input.value, input);
        });

        function addTagFromValue(tagName, existingInput) {
            tagName = tagName.trim();
            if (!tagName) return;

            const badgeId = `tag-badge-${Date.now()}${Math.random()}`; // ID unik sementara
            const badge = document.createElement('div');
            badge.classList.add('d-flex', 'align-items-center', 'badge', 'bg-secondary', 'text-white', 'me-1', 'mb-1', 'p-1', 'tag-badge');
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
        
        // === Logika Referensi Dokumen ===
        const selectReferenceBtn = document.getElementById('select-reference-btn');
        const referenceToInput = document.getElementById('reference_to');
        let currentCursorPosition = 0;
        
        // Simpan posisi kursor saat text area difokuskan atau diubah
        if (referenceToInput) {
            referenceToInput.addEventListener('focus', function() {
                currentCursorPosition = this.selectionStart;
            });
            
            referenceToInput.addEventListener('click', function() {
                currentCursorPosition = this.selectionStart;
            });
            
            referenceToInput.addEventListener('keyup', function() {
                currentCursorPosition = this.selectionStart;
            });
        }
        
        if (selectReferenceBtn) {
            selectReferenceBtn.addEventListener('click', function() {
                // Simpan posisi kursor saat ini
                currentCursorPosition = referenceToInput.selectionStart;
                
                // Tampilkan modal untuk memilih referensi
                const modal = new bootstrap.Modal(document.getElementById('reference-selector-modal'));
                modal.show();
                
                // Load data referensi saat modal dibuka
                loadReferences();
            });
        }
        
        function loadReferences(searchTerm = '') {
            const referenceList = document.getElementById('reference-list');
            referenceList.innerHTML = '<tr><td colspan="3" class="text-center">Memuat data...</td></tr>';
            
            fetch(`/api/document-references?search=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        referenceList.innerHTML = '<tr><td colspan="3" class="text-center">Tidak ada data yang sesuai.</td></tr>';
                        return;
                    }
                    
                    referenceList.innerHTML = '';
                    data.forEach(reference => {
                        const row = document.createElement('tr');
                        row.style.cursor = 'pointer';
                        row.addEventListener('click', function() {
                            selectReference(reference.reference_number, reference.title);
                        });
                        
                        const numberCell = document.createElement('td');
                        numberCell.textContent = reference.reference_number;
                        row.appendChild(numberCell);
                        
                        const titleCell = document.createElement('td');
                        titleCell.textContent = reference.title;
                        row.appendChild(titleCell);
                        
                        const actionCell = document.createElement('td');
                        const selectBtn = document.createElement('button');
                        selectBtn.className = 'btn btn-sm btn-primary';
                        selectBtn.textContent = 'Pilih';
                        selectBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectReference(reference.reference_number, reference.title);
                        });
                        actionCell.appendChild(selectBtn);
                        row.appendChild(actionCell);
                        
                        referenceList.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error loading references:', error);
                    referenceList.innerHTML = '<tr><td colspan="3" class="text-center">Terjadi kesalahan saat memuat data.</td></tr>';
                });
        }
        
        function selectReference(referenceNumber, title) {
            // Format teks yang akan disisipkan
            const referenceText = `${referenceNumber} - ${title}`;
            
            // Sisipkan teks pada posisi kursor
            const currentValue = referenceToInput.value;
            const beforeCursor = currentValue.substring(0, currentCursorPosition);
            const afterCursor = currentValue.substring(currentCursorPosition);
            
            // Tambahkan baris baru jika bukan di awal dan baris sebelumnya tidak kosong
            const insertText = (beforeCursor.length > 0 && !beforeCursor.endsWith('\n')) ? 
                               '\n' + referenceText : referenceText;
            
            // Set nilai baru
            referenceToInput.value = beforeCursor + insertText + afterCursor;
            
            // Perbarui posisi kursor
            const newPosition = currentCursorPosition + insertText.length;
            referenceToInput.setSelectionRange(newPosition, newPosition);
            
            // Fokus kembali ke textarea
            referenceToInput.focus();
            
            // Perbarui currentCursorPosition
            currentCursorPosition = newPosition;
            
            // Sembunyikan modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('reference-selector-modal'));
            modal.hide();
        }
        
        // Event listener untuk pencarian referensi
        const searchReferenceInput = document.getElementById('search-reference');
        if (searchReferenceInput) {
            searchReferenceInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    loadReferences(this.value);
                }
            });
            
            document.getElementById('search-reference-btn').addEventListener('click', function() {
                loadReferences(searchReferenceInput.value);
            });
        }

        // === Logika Pemilihan Surat untuk Referensi ===
        const selectLetterForRefBtn = document.getElementById('select-letter-for-ref-btn');
        
        if (selectLetterForRefBtn) {
            selectLetterForRefBtn.addEventListener('click', function() {
                // Simpan posisi kursor saat ini
                currentCursorPosition = referenceToInput.selectionStart;
                
                // Tampilkan modal untuk memilih surat
                const modal = new bootstrap.Modal(document.getElementById('letter-selector-modal'));
                modal.show();
                
                // Load data surat saat modal dibuka
                loadLettersForRef();
            });
        }
        
        function loadLettersForRef(searchTerm = '') {
            const letterList = document.getElementById('letter-list');
            letterList.innerHTML = '<tr><td colspan="4" class="text-center">Memuat data...</td></tr>';
            
            fetch(`/api/letters?search=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        letterList.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada data yang sesuai.</td></tr>';
                        return;
                    }
                    
                    letterList.innerHTML = '';
                    data.forEach(letter => {
                        const row = document.createElement('tr');
                        row.style.cursor = 'pointer';
                        row.addEventListener('click', function() {
                            selectLetterForRef(letter.document_number, letter.subject);
                        });
                        
                        const numberCell = document.createElement('td');
                        numberCell.textContent = letter.document_number;
                        row.appendChild(numberCell);
                        
                        const subjectCell = document.createElement('td');
                        subjectCell.textContent = letter.subject;
                        row.appendChild(subjectCell);
                        
                        const actionCell = document.createElement('td');
                        const selectBtn = document.createElement('button');
                        selectBtn.className = 'btn btn-sm btn-primary';
                        selectBtn.textContent = 'Pilih';
                        selectBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectLetterForRef(letter.document_number, letter.subject);
                        });
                        actionCell.appendChild(selectBtn);
                        row.appendChild(actionCell);
                        
                        letterList.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error loading letters:', error);
                    letterList.innerHTML = '<tr><td colspan="4" class="text-center">Terjadi kesalahan saat memuat data.</td></tr>';
                });
        }
        
        function selectLetterForRef(documentNumber, subject) {
            // Format teks yang akan disisipkan
            const referenceText = `${documentNumber} - ${subject}`;
            
            // Sisipkan teks pada posisi kursor
            const currentValue = referenceToInput.value;
            const beforeCursor = currentValue.substring(0, currentCursorPosition);
            const afterCursor = currentValue.substring(currentCursorPosition);
            
            // Tambahkan baris baru jika bukan di awal dan baris sebelumnya tidak kosong
            const insertText = (beforeCursor.length > 0 && !beforeCursor.endsWith('\n')) ? 
                               '\n' + referenceText : referenceText;
            
            // Set nilai baru
            referenceToInput.value = beforeCursor + insertText + afterCursor;
            
            // Perbarui posisi kursor
            const newPosition = currentCursorPosition + insertText.length;
            referenceToInput.setSelectionRange(newPosition, newPosition);
            
            // Fokus kembali ke textarea
            referenceToInput.focus();
            
            // Perbarui currentCursorPosition
            currentCursorPosition = newPosition;
            
            // Sembunyikan modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('letter-selector-modal'));
            modal.hide();
        }
        
        // Event listener untuk pencarian surat
        const searchLetterInput = document.getElementById('search-letter');
        if (searchLetterInput) {
            searchLetterInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    if (document.getElementById('letter-selector-modal').classList.contains('show')) {
                        loadLettersForRef(this.value);
                    }
                }
            });
            
            document.getElementById('search-letter-btn').addEventListener('click', function() {
                if (document.getElementById('letter-selector-modal').classList.contains('show')) {
                    loadLettersForRef(searchLetterInput.value);
                }
            });
        }
    });
</script>

<!-- Modal untuk Pemilihan Referensi -->
<div class="modal fade" id="reference-selector-modal" tabindex="-1" aria-labelledby="reference-selector-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reference-selector-label">Pilih Dokumen Referensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="search-reference" class="form-control" placeholder="Cari nomor atau judul dokumen...">
                        <button class="btn btn-outline-secondary" type="button" id="search-reference-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nomor Referensi</th>
                                <th>Judul</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="reference-list">
                            <tr>
                                <td colspan="3" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Pemilihan Surat -->
<div class="modal fade" id="letter-selector-modal" tabindex="-1" aria-labelledby="letter-selector-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="letter-selector-label">Pilih dari Daftar Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="search-letter" class="form-control" placeholder="Cari nomor surat atau perihal...">
                        <button class="btn btn-outline-secondary" type="button" id="search-letter-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nomor Surat</th>
                                <th>Perihal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="letter-list">
                            <tr>
                                <td colspan="4" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

    <!-- Summernote CSS & JS CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 250,
                tabsize: 2,
                toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'italic', 'underline', 'clear']],
                  ['fontname', ['fontname']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['table', ['table']],
                  ['insert', ['link', 'picture', 'video']],
                  ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
@endsection 