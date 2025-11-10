@extends('layouts.app')

@section('title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <!-- Tombol-tombol navigasi dipindahkan ke header card -->
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">{{ $reference->title }}</h4>
            <div>
                <a href="{{ route('tenant.document-references.edit', $reference->id) }}" class="btn btn-sm btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="{{ route('tenant.document-references.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th width="25%">Jenis Dokumen</th>
                            <td width="75%">{{ $reference->reference_type }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Referensi</th>
                            <td>{{ $reference->reference_number }}</td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <td>{{ $reference->title }}</td>
                        </tr>
                        <tr>
                            <th>Diterbitkan Oleh</th>
                            <td>{{ $reference->issued_by }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dokumen</th>
                            <td>{{ $reference->issued_date->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Unit Terkait</th>
                            <td>{{ $reference->related_unit ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tag</th>
                            <td>
                                @if(is_array($reference->tags) && count($reference->tags) > 0)
                                    @foreach($reference->tags as $tag)
                                        <span class="badge bg-primary me-1">{{ $tag }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $reference->description ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Link File Dokumen</div>
                        <div class="card-body text-center">
                            @if($reference->file_url)
                                <div class="mb-3">
                                    <i class="fas fa-link fa-3x text-primary mb-3"></i>
                                </div>
                                <a href="{{ $reference->file_url }}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Link
                                </a>
                                <small class="d-block mt-2 text-muted text-break">{{ $reference->file_url }}</small>
                            @else
                                <p class="text-muted">Tidak ada link file yang disediakan</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-muted mt-3">
                        <small>
                            <div>Dibuat: {{ $reference->created_at->format('d M Y H:i') }}</div>
                            <div>Diperbarui: {{ $reference->updated_at->format('d M Y H:i') }}</div>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 