<?php
    // Menggunakan data dari $correspondence jika ada
    $currentYear = $correspondence->created_at->format('Y');
    $currentMonth = $correspondence->created_at->format('n');
    $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $romanMonth = $romanMonths[$currentMonth];

    // Gunakan nomor dokumen yang sudah ada jika tidak kosong
    $defaultDocumentNumber = $correspondence->document_number ?? ("B/ND-" . ($nextLetterNumber ?? '...') . " /" . $romanMonth . "/" . $currentYear . "/Subbidyanmeddokpol");

    // Gunakan isi surat yang sudah ada
    $defaultBody = $correspondence->body ?? "Sehubungan dengan rujukan tersebut di atas, bersama ini kami merencanakan untuk melakukan kegiatan sebagai berikut :\n" .
                   "Nama Giat : \n" .
                   "Waktu        : \n" .
                   "Tempat      : \n" .
                   "Agenda      :";

    // Data untuk isian default form dari $correspondence
    $oldDocumentTitle = old('document_title', $correspondence->document_title);
    $oldDocumentNumber = old('document_number', $defaultDocumentNumber);
    $oldDocumentType = old('document_type', $correspondence->document_type);
    $oldDocumentVersion = old('document_version', $correspondence->document_version);
    $oldConfidentialityLevel = old('confidentiality_level', $correspondence->confidentiality_level);
    $oldDocumentDate = old('document_date', $correspondence->document_date ? $correspondence->document_date->format('Y-m-d') : date('Y-m-d'));
    $oldSubject = old('subject', $correspondence->subject);
    $oldBody = old('body', $defaultBody);
    $oldSenderName = old('sender_name', $correspondence->sender_name);
    $oldSenderPosition = old('sender_position', $correspondence->sender_position);
    $oldRecipientName = old('recipient_name', $correspondence->recipient_name);
    $oldRecipientPosition = old('recipient_position', $correspondence->recipient_position);
    $oldCcList = old('cc_list', $correspondence->cc_list);
    $oldReferenceTo = old('reference_to', $correspondence->reference_to);
    $oldSignedAtLocation = old('signed_at_location', $correspondence->signed_at_location);
    $oldSignedAtDate = old('signed_at_date', $correspondence->signed_at_date ? $correspondence->signed_at_date->format('Y-m-d') : date('Y-m-d'));
    $oldSignatoryName = old('signatory_name', $correspondence->signatory_name);
    $oldSignatoryPosition = old('signatory_position', $correspondence->signatory_position);
    $oldSignatoryRank = old('signatory_rank', $correspondence->signatory_rank);
    $oldSignatoryNrp = old('signatory_nrp', $correspondence->signatory_nrp);
    $oldTags = old('tags', $correspondence->tags->pluck('name')->toArray()); // Ambil nama tag
?>



<?php $__env->startSection('title', 'Edit Surat: ' . $correspondence->document_title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.index')); ?>">Korespondensi</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.letters.index')); ?>">Daftar Surat</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.letters.show', $correspondence->id)); ?>"><?php echo e($correspondence->document_title); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('modules.correspondence.letters.update', $correspondence->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?> 

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Surat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_title" class="form-label">Judul Surat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_title" name="document_title" value="<?php echo e($oldDocumentTitle); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document_number" class="form-label">Nomor Surat</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="document_number" name="document_number" value="<?php echo e(old('document_number', $correspondence->document_number)); ?>">
                            <button class="btn btn-outline-secondary" type="button" id="select-letter-btn">Pilih dari Daftar</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Regulasi" <?php echo e($oldDocumentType == 'Regulasi' ? 'selected' : ''); ?>>Regulasi</option>
                            <option value="Bukti" <?php echo e($oldDocumentType == 'Bukti' ? 'selected' : ''); ?>>Bukti</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="document_version" class="form-label">Versi Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_version" name="document_version" value="<?php echo e($oldDocumentVersion); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="Publik" <?php echo e($oldConfidentialityLevel == 'Publik' ? 'selected' : ''); ?>>Publik</option>
                            <option value="Internal" <?php echo e($oldConfidentialityLevel == 'Internal' ? 'selected' : ''); ?>>Internal</option>
                            <option value="Rahasia" <?php echo e($oldConfidentialityLevel == 'Rahasia' ? 'selected' : ''); ?>>Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_date" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="document_date" name="document_date" value="<?php echo e($oldDocumentDate); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subject" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo e(old('subject', $correspondence->subject)); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="body" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="body" name="body" rows="6" required><?php echo e($oldBody); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_name" class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?php echo e($oldSenderName); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sender_position" class="form-label">Jabatan Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_position" name="sender_position" value="<?php echo e($oldSenderPosition); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="<?php echo e($oldRecipientName); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="recipient_position" class="form-label">Jabatan Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_position" name="recipient_position" value="<?php echo e($oldRecipientPosition); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="cc_list" class="form-label">Tembusan</label>
                        <textarea class="form-control" id="cc_list" name="cc_list" rows="2"><?php echo e($oldCcList); ?></textarea>
                        <small class="text-muted">Pisahkan dengan baris baru untuk setiap penerima tembusan.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="reference_to" class="form-label">Referensi</label>
                        <div class="input-group mb-2">
                            <textarea class="form-control" id="reference_to" name="reference_to" rows="3"><?php echo e($oldReferenceTo); ?></textarea>
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
                        <input type="text" class="form-control" id="signed_at_location" name="signed_at_location" value="<?php echo e($oldSignedAtLocation); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signed_at_date" class="form-label">Tanggal Penandatanganan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="signed_at_date" name="signed_at_date" value="<?php echo e($oldSignedAtDate); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_name" class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_name" name="signatory_name" value="<?php echo e($oldSignatoryName); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_position" class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_position" name="signatory_position" value="<?php echo e($oldSignatoryPosition); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_rank" class="form-label">Pangkat Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_rank" name="signatory_rank" value="<?php echo e($oldSignatoryRank); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_nrp" class="form-label">NRP/NIP Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_nrp" name="signatory_nrp" value="<?php echo e($oldSignatoryNrp); ?>">
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
                        <input type="text" class="form-control" id="document_link" name="document_link" 
                               placeholder="Masukkan link menuju file yang tersimpan di cloud (Google Drive, OneDrive, dll)" 
                               value="<?php echo e(old('document_link', $correspondence->document_link)); ?>">
                        <small class="text-muted">Pastikan link dapat diakses oleh penerima surat.</small>
                        <?php if($correspondence->file_path): ?>
                            <small class="d-block mt-1">File saat ini: <a href="<?php echo e(asset('storage/' . $correspondence->file_path)); ?>" target="_blank">Lihat</a></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 mb-3">
                        <label for="tag-input" class="form-label">Tag</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="tag-input" placeholder="Ketik tag lalu tekan Enter atau tombol Tambah">
                            <button class="btn btn-outline-secondary" type="button" id="add-tag-button">Tambah</button>
                        </div>
                        <div id="tags-container" class="mt-2 d-flex flex-wrap">
                            
                            <?php $__currentLoopData = $oldTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tagName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="badge bg-primary text-white me-2 mb-2 p-2 d-flex align-items-center">
                                    <span><?php echo e($tagName); ?></span>
                                    <button type="button" class="btn-close btn-close-white ms-2 remove-tag-button" style="font-size: 0.6rem;" aria-label="Close"></button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div id="hidden-tags-container">
                            
                            <?php $__currentLoopData = $oldTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tagName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="tags[]" value="<?php echo e($tagName); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <small class="text-muted">Anda bisa menambahkan tag baru atau menggunakan tag yang sudah ada.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between">
                <a href="<?php echo e(route('modules.correspondence.letters.show', $correspondence->id)); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tagInput = document.getElementById('tag-input');
        const addTagButton = document.getElementById('add-tag-button');
        const tagsContainer = document.getElementById('tags-container');
        const hiddenTagsContainer = document.getElementById('hidden-tags-container');

        function addTag(tagName) {
            tagName = tagName.trim();
            if (tagName === '') return;

            // Cek duplikasi
            const existingTags = hiddenTagsContainer.querySelectorAll('input[name="tags[]"]');
            let isDuplicate = false;
            existingTags.forEach(input => {
                if (input.value.toLowerCase() === tagName.toLowerCase()) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                tagInput.value = ''; // Kosongkan input jika duplikat
                return; // Jangan tambahkan jika sudah ada
            }

            // Buat badge
            const badge = document.createElement('div');
            badge.className = 'badge bg-primary text-white me-2 mb-2 p-2 d-flex align-items-center';
            badge.innerHTML = `
                <span>${tagName}</span>
                <button type="button" class="btn-close btn-close-white ms-2 remove-tag-button" style="font-size: 0.6rem;" aria-label="Close"></button>
            `;
            tagsContainer.appendChild(badge);

            // Buat input hidden
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';
            hiddenInput.value = tagName;
            hiddenTagsContainer.appendChild(hiddenInput);

            // Kosongkan input
            tagInput.value = '';
        }

        function removeTag(badgeElement) {
            const tagName = badgeElement.querySelector('span').textContent;
            const hiddenInputs = hiddenTagsContainer.querySelectorAll(`input[value="${tagName}"]`);
            hiddenInputs.forEach(input => input.remove());
            badgeElement.remove();
        }

        addTagButton.addEventListener('click', () => {
            addTag(tagInput.value);
        });

        tagInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault(); // Mencegah form submit
                addTag(tagInput.value);
            }
        });

        // Event delegation untuk tombol remove pada tag
        tagsContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-tag-button')) {
                removeTag(event.target.closest('.badge'));
            }
        });

        // Inisialisasi tombol remove untuk tag yang sudah ada dari server
        tagsContainer.querySelectorAll('.remove-tag-button').forEach(button => {
            button.addEventListener('click', () => {
                removeTag(button.closest('.badge'));
            });
        });

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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/letters/edit.blade.php ENDPATH**/ ?>