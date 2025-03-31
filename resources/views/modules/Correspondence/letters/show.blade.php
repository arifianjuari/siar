@extends('layouts.app')

@section('title', $correspondence->document_title)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Surat</h1>
        <div>
            <a href="{{ route('modules.correspondence.letters.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
            @can('export', $correspondence)
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download fa-sm"></i> Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('modules.correspondence.letters.export-pdf', $correspondence->id) }}">PDF</a>
                    <a class="dropdown-item" href="{{ route('modules.correspondence.letters.export-word', $correspondence->id) }}">Word</a>
                </div>
            </div>
            @endcan
            @can('update', $correspondence)
            <a href="{{ route('modules.correspondence.letters.edit', $correspondence->id) }}" class="btn btn-sm btn-warning ml-2">
                <i class="fas fa-edit fa-sm"></i> Edit
            </a>
            @endcan
            @can('delete', $correspondence)
            <form action="{{ route('modules.correspondence.letters.destroy', $correspondence->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                    <i class="fas fa-trash fa-sm"></i> Hapus
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="row">
        <!-- Detail Surat -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Surat</h6>
                    <div class="dropdown no-arrow">
                        <span class="badge {{ $correspondence->document_type == 'Regulasi' ? 'badge-primary' : 'badge-success' }}">
                            {{ $correspondence->document_type }}
                        </span>
                        <span class="badge 
                            {{ $correspondence->confidentiality_level == 'Publik' ? 'badge-info' : 
                              ($correspondence->confidentiality_level == 'Internal' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $correspondence->confidentiality_level }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Nomor Dokumen</h5>
                            <p>{{ $correspondence->document_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Tanggal Dokumen</h5>
                            <p>{{ $correspondence->document_date->format('d F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Judul Dokumen</h5>
                        <p class="font-weight-bold">{{ $correspondence->document_title }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Subjek</h5>
                        <p>{{ $correspondence->subject }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Pengirim</h5>
                            <p>{{ $correspondence->sender_name }}<br>
                            <small class="text-muted">{{ $correspondence->sender_position }}</small></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Penerima</h5>
                            <p>{{ $correspondence->recipient_name }}<br>
                            <small class="text-muted">{{ $correspondence->recipient_position }}</small></p>
                        </div>
                    </div>
                    
                    @if($correspondence->cc_list)
                    <div class="mb-4">
                        <h5>CC</h5>
                        <p>{{ $correspondence->cc_list }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <h5>Isi Surat</h5>
                        <div class="border p-3 bg-light rounded">
                            {!! nl2br(e($correspondence->body)) !!}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Lokasi Penandatanganan</h5>
                            <p>{{ $correspondence->signed_at_location }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Tanggal Penandatanganan</h5>
                            <p>{{ $correspondence->signed_at_date->format('d F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <h5>Penandatangan</h5>
                            <p>
                                {{ $correspondence->signatory_name }}<br>
                                <small class="text-muted">{{ $correspondence->signatory_position }}</small>
                                @if($correspondence->signatory_rank)
                                <br><small class="text-muted">{{ $correspondence->signatory_rank }}</small>
                                @endif
                                @if($correspondence->signatory_nrp)
                                <br><small class="text-muted">NRP: {{ $correspondence->signatory_nrp }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 mb-3 text-center">
                            @if($correspondence->signature_file)
                            <img src="{{ asset('storage/' . $correspondence->signature_file) }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px;">
                            @else
                            <p class="text-muted">Tidak ada file tanda tangan</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($correspondence->reference_to)
                    <div class="mb-4">
                        <h5>Referensi</h5>
                        <p>{{ $correspondence->reference_to }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Dokumen yang Terhubung -->
            @if($correspondence->documents->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Terhubung</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($correspondence->documents as $document)
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $document->document_title }}</h5>
                                <small>{{ $document->document_date->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($document->description, 100) }}</p>
                            <small class="text-muted">{{ $document->document_number }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Metadata -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Versi Dokumen</h5>
                        <p>{{ $correspondence->document_version }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Dibuat Oleh</h5>
                        <p>{{ $correspondence->creator->name ?? 'Unknown' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Tanggal Pembuatan</h5>
                        <p>{{ $correspondence->created_at->format('d F Y H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Tanggal Update Terakhir</h5>
                        <p>{{ $correspondence->updated_at->format('d F Y H:i') }}</p>
                    </div>
                    
                    @if($correspondence->next_review)
                    <div class="mb-3">
                        <h5>Jadwal Review Berikutnya</h5>
                        <p>{{ $correspondence->next_review->format('d F Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- File Lampiran -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Lampiran</h6>
                </div>
                <div class="card-body">
                    @if($correspondence->file_path)
                    <a href="{{ asset('storage/' . $correspondence->file_path) }}" class="btn btn-primary btn-block" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Download File
                    </a>
                    @else
                    <p class="text-center text-muted">Tidak ada file lampiran</p>
                    @endif
                </div>
            </div>
            
            <!-- QR Code -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ route('modules.correspondence.letters.qr-code', $correspondence->id) }}" alt="QR Code" class="img-fluid mb-2" style="max-width: 150px;">
                    <p class="small text-muted">Scan untuk melihat dokumen ini</p>
                </div>
            </div>
            
            <!-- Tag -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tag</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($correspondence->tags as $tag)
                        <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-{{ $tag->id }}">
                            <a href="{{ route('modules.correspondence.search', ['tag' => $tag->slug]) }}" class="text-decoration-none text-white">
                                {{ $tag->name }}
                            </a>
                            @can('update', $correspondence)
                            <button 
                                type="button" 
                                class="btn-close btn-close-white ms-2" 
                                style="font-size: 0.7rem;" 
                                onclick="hapusTagLangsung({{ $tag->id }}, {{ $correspondence->id }}, 'App\\Models\\Correspondence')"
                                aria-label="Close">
                            </button>
                            @endcan
                        </div>
                        @endforeach
                    </div>

                    @can('update', $correspondence)
                    <form id="formTambahTag" action="{{ route('tenant.tags.attach-document') }}" method="POST" class="d-flex gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="document_id" value="{{ $correspondence->id }}">
                        <input type="hidden" name="document_type" value="App\Models\Correspondence">
                        <select name="tag_id" id="selectTag" class="form-select form-select-sm" required>
                            <option value="">Pilih Tag</option>
                            @foreach(App\Models\Tag::forTenant(session('tenant_id'))->orderBy('name')->get() as $tag)
                                <option value="{{ $tag->id }}" data-slug="{{ $tag->slug }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Tambah Tag</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function hapusTagLangsung(tagId, documentId, documentType) {
        if (confirm('Apakah Anda yakin ingin menghapus tag ini?')) {
            fetch('/tenant/tags/detach-document', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tag_id: tagId,
                    document_id: documentId,
                    document_type: documentType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tag-item-' + tagId).remove();
                } else {
                    alert('Gagal menghapus tag: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus tag');
            });
        }
    }
</script>
@endsection 