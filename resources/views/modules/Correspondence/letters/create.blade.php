@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">Buat Surat Baru</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.correspondence.index') }}">Korespondensi</a></li>
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
                        <input type="text" class="form-control" id="document_number" name="document_number" value="{{ old('document_number') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Regulasi" {{ old('document_type') == 'Regulasi' ? 'selected' : '' }}>Regulasi</option>
                            <option value="Bukti" {{ old('document_type') == 'Bukti' ? 'selected' : '' }}>Bukti</option>
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
                            <option value="Publik" {{ old('confidentiality_level') == 'Publik' ? 'selected' : '' }}>Publik</option>
                            <option value="Internal" {{ old('confidentiality_level') == 'Internal' ? 'selected' : '' }}>Internal</option>
                            <option value="Rahasia" {{ old('confidentiality_level') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
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
                        <textarea class="form-control" id="body" name="body" rows="6" required>{{ old('body') }}</textarea>
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
                        <input type="text" class="form-control" id="reference_to" name="reference_to" value="{{ old('reference_to') }}">
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
                    <div class="col-md-6 mb-3">
                        <label for="document_file" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="document_file" name="document_file">
                        <small class="text-muted">Format: PDF, DOC, DOCX. Ukuran maksimal: 10MB.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="signature_file" class="form-label">File Tanda Tangan</label>
                        <input type="file" class="form-control" id="signature_file" name="signature_file">
                        <small class="text-muted">Format: PNG, JPG, JPEG. Ukuran maksimal: 2MB.</small>
                    </div>
                </div>

                @if(count($tags) > 0)
                <div class="row mt-2">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row">
                            @foreach($tags as $tag)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->slug }}" id="tag_{{ $tag->id }}" 
                                        {{ in_array($tag->slug, old('tags', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tag_{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mengisi data pengirim dan penandatangan secara otomatis jika diperlukan
        const userPosition = "{{ auth()->user()->position ?? '' }}";
        const userName = "{{ auth()->user()->name ?? '' }}";
        
        if (userPosition && document.getElementById('sender_position').value === '') {
            document.getElementById('sender_position').value = userPosition;
        }
        
        if (userName && document.getElementById('sender_name').value === '') {
            document.getElementById('sender_name').value = userName;
        }
        
        // Formatir textarea untuk WYSIWYG jika tersedia
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#body'))
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>
@endsection 