@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', $spo->document_title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Dokumen SPO</h1>
        
        <div>
            <a href="{{ route('spo.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            
            @can('update', $spo)
            <a href="{{ route('spo.edit', $spo) }}" class="btn btn-primary ms-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            @endcan
            
            @if ($spo->file_path)
            <a href="{{ $spo->file_path }}" class="btn btn-info ms-2" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Dokumen
            </a>
            @endif
        </div>
    </div>
    
    <!-- Informasi Dokumen -->
    <div class="row">
        <div class="col-md-8">
            <!-- Informasi Dasar -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Dokumen</h6>
                    <span class="badge {{ $spo->status_validasi == 'Draft' ? 'bg-secondary' : 
                                        ($spo->status_validasi == 'Disetujui' ? 'bg-success' : 
                                        ($spo->status_validasi == 'Kadaluarsa' ? 'bg-danger' : 'bg-warning')) }} 
                            px-3 py-2 rounded-pill">
                        {{ $spo->status_validasi }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="h4 mb-3">{{ $spo->document_title }}</h3>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Jenis Dokumen</th>
                                        <td>: {{ $spo->document_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Dokumen</th>
                                        <td>: {{ $spo->document_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Versi</th>
                                        <td>: {{ $spo->document_version }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Berlaku</th>
                                        <td>: {{ $spo->document_date->format('d/m/Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Unit Kerja</th>
                                        <td>: {{ $spo->workUnit->unit_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tingkat Kerahasiaan</th>
                                        <td>: {{ $spo->confidentiality_level }}</td>
                                    </tr>
                                    <tr>
                                        <th>Review Berikutnya</th>
                                        <td>: {{ $spo->next_review ? $spo->next_review->format('d/m/Y') : 'Belum ditentukan' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Siklus Review</th>
                                        <td>: {{ $spo->review_cycle_months }} bulan</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab untuk konten detail -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="definition-tab" data-bs-toggle="tab" data-bs-target="#definition" type="button" role="tab" aria-controls="definition" aria-selected="true">Pengertian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="purpose-tab" data-bs-toggle="tab" data-bs-target="#purpose" type="button" role="tab" aria-controls="purpose" aria-selected="false">Tujuan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy" type="button" role="tab" aria-controls="policy" aria-selected="false">Kebijakan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="procedure-tab" data-bs-toggle="tab" data-bs-target="#procedure" type="button" role="tab" aria-controls="procedure" aria-selected="false">Prosedur</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reference-tab" data-bs-toggle="tab" data-bs-target="#reference" type="button" role="tab" aria-controls="reference" aria-selected="false">Referensi</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="spoDetailTabContent">
                        <div class="tab-pane fade show active" id="definition" role="tabpanel" aria-labelledby="definition-tab">
                            <div class="py-3">
                                @if($spo->definition)
                                    {!! nl2br(e($spo->definition)) !!}
                                @else
                                    <p class="text-muted">Tidak ada informasi pengertian.</p>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="purpose" role="tabpanel" aria-labelledby="purpose-tab">
                            <div class="py-3">
                                @if($spo->purpose)
                                    {!! nl2br(e($spo->purpose)) !!}
                                @else
                                    <p class="text-muted">Tidak ada informasi tujuan.</p>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
                            <div class="py-3">
                                @if($spo->policy)
                                    {!! nl2br(e($spo->policy)) !!}
                                @else
                                    <p class="text-muted">Tidak ada informasi kebijakan.</p>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="procedure" role="tabpanel" aria-labelledby="procedure-tab">
                            <div class="py-3">
                                @if($spo->procedure)
                                    {!! nl2br(e($spo->procedure)) !!}
                                @else
                                    <p class="text-muted">Tidak ada informasi prosedur.</p>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reference" role="tabpanel" aria-labelledby="reference-tab">
                            <div class="py-3">
                                @if($spo->reference)
                                    {!! nl2br(e($spo->reference)) !!}
                                @else
                                    <p class="text-muted">Tidak ada informasi referensi.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Informasi Tambahan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Unit Kerja Terkait</h6>
                    @if($linkedUnits->isEmpty())
                        <p class="text-muted">Tidak ada unit kerja terkait</p>
                    @else
                        <ul class="list-group mb-4">
                            @foreach($linkedUnits as $unit)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $unit->unit_name }}
                                    <span class="badge bg-primary rounded-pill">{{ $unit->unit_code }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    <hr>
                    
                    <h6 class="mb-3">Tag</h6>
                    @if($spo->tags->isEmpty())
                        <p class="text-muted">Tidak ada tag</p>
                    @else
                        <div class="d-flex flex-wrap mb-4">
                            @foreach($spo->tags as $tag)
                                <span class="badge bg-info me-2 mb-2 py-2 px-3">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    <hr>
                    
                    <h6 class="mb-3">Persetujuan Dokumen</h6>
                    @if($spo->status_validasi == 'Disetujui' && $spo->approved_by && $spo->approved_at)
                        <div class="list-group-item">
                            <div>Disetujui oleh:</div>
                            <div class="fw-bold">{{ $spo->approver->name ?? 'N/A' }}</div>
                            <div class="small text-muted">
                                Pada: {{ $spo->approved_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Dokumen belum disetujui</p>
                    @endif
                    
                    <hr>
                    
                    <h6 class="mb-3">Informasi Pembuatan</h6>
                    <div class="list-group-item mb-2">
                        <div>Dibuat oleh:</div>
                        <div class="fw-bold">{{ $spo->creator->name ?? 'N/A' }}</div>
                        <div class="small text-muted">
                            Pada: {{ $spo->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    
                    <div class="list-group-item">
                        <div>Terakhir diubah:</div>
                        <div class="small text-muted">
                            {{ $spo->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 