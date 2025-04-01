<?php
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
?>


<?php $__env->startSection('title', 'Buat Surat Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.index')); ?>">Korespondensi</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.letters.index')); ?>">Daftar Surat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Surat Baru</li>
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

    <form action="<?php echo e(route('modules.correspondence.letters.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Surat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_title" class="form-label">Judul Surat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_title" name="document_title" value="<?php echo e(old('document_title')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document_number" class="form-label">Nomor Surat</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="<?php echo e(old('document_number', $defaultDocumentNumber)); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Regulasi" <?php echo e(old('document_type', 'Bukti') == 'Regulasi' ? 'selected' : ''); ?>>Regulasi</option>
                            <option value="Bukti" <?php echo e(old('document_type', 'Bukti') == 'Bukti' ? 'selected' : ''); ?>>Bukti</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="document_version" class="form-label">Versi Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_version" name="document_version" value="<?php echo e(old('document_version', '1.0')); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="Publik" <?php echo e(old('confidentiality_level', 'Internal') == 'Publik' ? 'selected' : ''); ?>>Publik</option>
                            <option value="Internal" <?php echo e(old('confidentiality_level', 'Internal') == 'Internal' ? 'selected' : ''); ?>>Internal</option>
                            <option value="Rahasia" <?php echo e(old('confidentiality_level', 'Internal') == 'Rahasia' ? 'selected' : ''); ?>>Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_date" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="document_date" name="document_date" value="<?php echo e(old('document_date', date('Y-m-d'))); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subject" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo e(old('subject')); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="body" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="body" name="body" rows="6" required><?php echo e(old('body', $defaultBody)); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_name" class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?php echo e(old('sender_name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sender_position" class="form-label">Jabatan Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_position" name="sender_position" value="<?php echo e(old('sender_position')); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="<?php echo e(old('recipient_name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="recipient_position" class="form-label">Jabatan Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_position" name="recipient_position" value="<?php echo e(old('recipient_position')); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="cc_list" class="form-label">Tembusan</label>
                        <textarea class="form-control" id="cc_list" name="cc_list" rows="2"><?php echo e(old('cc_list')); ?></textarea>
                        <small class="text-muted">Pisahkan dengan baris baru untuk setiap penerima tembusan.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="reference_to" class="form-label">Referensi</label>
                        <input type="text" class="form-control" id="reference_to" name="reference_to" value="<?php echo e(old('reference_to')); ?>">
                        <small class="text-muted">Contoh: Surat No. XXX atau referensi lainnya.</small>
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
                        <input type="text" class="form-control" id="signed_at_location" name="signed_at_location" value="<?php echo e(old('signed_at_location')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signed_at_date" class="form-label">Tanggal Penandatanganan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="signed_at_date" name="signed_at_date" value="<?php echo e(old('signed_at_date', date('Y-m-d'))); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_name" class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_name" name="signatory_name" value="<?php echo e(old('signatory_name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_position" class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_position" name="signatory_position" value="<?php echo e(old('signatory_position')); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_rank" class="form-label">Pangkat Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_rank" name="signatory_rank" value="<?php echo e(old('signatory_rank')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_nrp" class="form-label">NRP/NIP Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_nrp" name="signatory_nrp" value="<?php echo e(old('signatory_nrp')); ?>">
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
                        <input type="text" class="form-control" id="document_link" name="document_link" placeholder="Masukkan link menuju file yang tersimpan di cloud (Google Drive, OneDrive, dll)" value="<?php echo e(old('document_link')); ?>">
                        <small class="text-muted">Pastikan link dapat diakses oleh penerima surat.</small>
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
                            
                        </div>
                        <div id="hidden-tags-container">
                            <?php if(old('tags')): ?>
                                <?php $__currentLoopData = old('tags'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oldTag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="tags[]" value="<?php echo e($oldTag); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Anda bisa menambahkan tag baru atau menggunakan tag yang sudah ada.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between">
                <a href="<?php echo e(route('modules.correspondence.letters.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan Surat
                </button>
            </div>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded from Inline Script.');
        
        // === Data User ===
        const userPosition = "<?php echo e(auth()->user()->position ?? ''); ?>";
        const userName = "<?php echo e(auth()->user()->name ?? ''); ?>";
        const userRank = "<?php echo e(auth()->user()->rank ?? ''); ?>";
        const userNrp = "<?php echo e(auth()->user()->nrp ?? ''); ?>";
        
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
        
        // === Logika Tag Input Baru ===
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

        function addTagFromValue(tagName, existingInput) {
            tagName = tagName.trim();
            if (!tagName) return;
    
            const badgeId = `tag-badge-${Date.now()}${Math.random()}`; // ID unik
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
            console.log('Adding tags from old input...', existingHiddenTags);
            existingHiddenTags.forEach(input => {
                addTagFromValue(input.value, input);
            });
        }
        // === Akhir Logika Tag Input Baru ===
    });
</script>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/letters/create.blade.php ENDPATH**/ ?>