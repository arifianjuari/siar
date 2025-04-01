@php
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
@endphp

@extends('layouts.app')

@section('title', 'Edit Surat: ' . $correspondence->document_title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.index') }}">Korespondensi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.letters.index') }}">Daftar Surat</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.letters.show', $correspondence->id) }}">{{ $correspondence->document_title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Surat</li>
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

    {{-- Ganti action ke route update dan tambahkan method PUT --}}
    <form action="{{ route('modules.correspondence.letters.update', $correspondence->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Method spoofing untuk PUT request --}}

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Surat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_title" class="form-label">Judul Surat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_title" name="document_title" value="{{ $oldDocumentTitle }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document_number" class="form-label">Nomor Surat</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $oldDocumentNumber }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Regulasi" {{ $oldDocumentType == 'Regulasi' ? 'selected' : '' }}>Regulasi</option>
                            <option value="Bukti" {{ $oldDocumentType == 'Bukti' ? 'selected' : '' }}>Bukti</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="document_version" class="form-label">Versi Dokumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_version" name="document_version" value="{{ $oldDocumentVersion }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="Publik" {{ $oldConfidentialityLevel == 'Publik' ? 'selected' : '' }}>Publik</option>
                            <option value="Internal" {{ $oldConfidentialityLevel == 'Internal' ? 'selected' : '' }}>Internal</option>
                            <option value="Rahasia" {{ $oldConfidentialityLevel == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="document_date" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="document_date" name="document_date" value="{{ $oldDocumentDate }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subject" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" value="{{ $oldSubject }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="body" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="body" name="body" rows="6" required>{{ $oldBody }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_name" class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="{{ $oldSenderName }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sender_position" class="form-label">Jabatan Pengirim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_position" name="sender_position" value="{{ $oldSenderPosition }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ $oldRecipientName }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="recipient_position" class="form-label">Jabatan Penerima <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_position" name="recipient_position" value="{{ $oldRecipientPosition }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="cc_list" class="form-label">Tembusan</label>
                        <textarea class="form-control" id="cc_list" name="cc_list" rows="2">{{ $oldCcList }}</textarea>
                        <small class="text-muted">Pisahkan dengan baris baru untuk setiap penerima tembusan.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="reference_to" class="form-label">Referensi</label>
                        <input type="text" class="form-control" id="reference_to" name="reference_to" value="{{ $oldReferenceTo }}">
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
                        <input type="text" class="form-control" id="signed_at_location" name="signed_at_location" value="{{ $oldSignedAtLocation }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signed_at_date" class="form-label">Tanggal Penandatanganan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="signed_at_date" name="signed_at_date" value="{{ $oldSignedAtDate }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_name" class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_name" name="signatory_name" value="{{ $oldSignatoryName }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_position" class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatory_position" name="signatory_position" value="{{ $oldSignatoryPosition }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="signatory_rank" class="form-label">Pangkat Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_rank" name="signatory_rank" value="{{ $oldSignatoryRank }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signatory_nrp" class="form-label">NRP/NIP Penandatangan</label>
                        <input type="text" class="form-control" id="signatory_nrp" name="signatory_nrp" value="{{ $oldSignatoryNrp }}">
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
                    <div class="col-md-6 mb-3">
                        <label for="document_file" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="document_file" name="document_file">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Ukuran maksimal: 10MB.</small>
                        @if($correspondence->file_path)
                            <small>File saat ini: <a href="{{ asset('storage/' . $correspondence->file_path) }}" target="_blank">Lihat</a>. Upload file baru akan menggantikan file ini.</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signature_file" class="form-label">File Tanda Tangan</label>
                        <input type="file" class="form-control" id="signature_file" name="signature_file">
                        <small class="text-muted">Format: PNG, JPG, JPEG. Ukuran maksimal: 2MB.</small>
                        @if($correspondence->signature_file)
                            <small>Tanda tangan saat ini:</small>
                            <img src="{{ asset('storage/' . $correspondence->signature_file) }}" alt="Tanda Tangan" class="img-thumbnail mt-1" style="max-height: 50px;">
                            <small>Upload file baru akan menggantikan file ini.</small>
                        @endif
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
                            {{-- Tampilkan tag yang sudah ada --}}
                            @foreach($oldTags as $tagName)
                                <div class="badge bg-primary text-white me-2 mb-2 p-2 d-flex align-items-center">
                                    <span>{{ $tagName }}</span>
                                    <button type="button" class="btn-close btn-close-white ms-2 remove-tag-button" style="font-size: 0.6rem;" aria-label="Close"></button>
                                </div>
                            @endforeach
                        </div>
                        <div id="hidden-tags-container">
                            {{-- Input hidden untuk tags --}}
                            @foreach($oldTags as $tagName)
                                <input type="hidden" name="tags[]" value="{{ $tagName }}">
                            @endforeach
                        </div>
                        <small class="text-muted">Anda bisa menambahkan tag baru atau menggunakan tag yang sudah ada.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('modules.correspondence.letters.show', $correspondence->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Script dipindah ke sini karena @section('scripts') tidak berjalan --}}
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
    });
</script>
@endsection 