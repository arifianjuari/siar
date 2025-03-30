@extends('layouts.app')

@section('title', ' | Detail Laporan Risiko')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem 1.25rem;
        font-weight: 600;
    }
    
    .section-heading {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #495057;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .info-label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        margin-bottom: 1rem;
    }
    
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .status-open {
        background-color: #dc3545;
        color: white;
    }
    
    .status-in-review {
        background-color: #ffc107;
        color: #000;
    }
    
    .status-resolved {
        background-color: #198754;
        color: white;
    }
    
    .risk-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .risk-high {
        background-color: #dc3545;
        color: white;
    }
    
    .risk-medium {
        background-color: #ffc107;
        color: #000;
    }
    
    .risk-low {
        background-color: #198754;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('modules.risk-management.dashboard') }}" class="text-decoration-none">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('modules.risk-management.risk-reports.index') }}" class="text-decoration-none">Daftar Laporan</a></li>
                    <li class="breadcrumb-item active">Detail Laporan</li>
                </ol>
            </nav>
        </div>
        <div>
            @php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant-admin' || 
                                  strtolower($userRole) === 'tenant-admin';
            @endphp
            
            @if(auth()->user()->role && $isTenantAdmin)
                <a href="{{ route('modules.risk-management.analysis-config') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
                </a>
            @endif
            
            @can('create', [App\Models\RiskAnalysis::class, $riskReport])
                @if(!$riskReport->analysis)
                    <a href="{{ route('modules.risk-management.risk-analysis.create', $riskReport->id) }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-1"></i> Buat Analisis
                    </a>
                @endif
            @else
                @if(!$riskReport->analysis && $isTenantAdmin)
                    <div class="alert alert-warning d-inline-block py-1 px-3 mb-0 ms-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> Akses analisis perlu dikonfigurasi
                    </div>
                @endif
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informasi Utama -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $riskReport->document_title }}</h5>
                    <div>
                        <span class="status-badge status-{{ $riskReport->status }}">
                            {{ ucfirst($riskReport->status) }}
                        </span>
                        <span class="risk-badge risk-{{ strtolower($riskReport->risk_level) }} ms-2">
                            Risiko {{ ucfirst($riskReport->risk_level) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label">Unit Pelapor</div>
                            <div class="info-value">{{ $riskReport->reporter_unit }}</div>
                            
                            <div class="info-label">Tipe Risiko</div>
                            <div class="info-value">{{ $riskReport->risk_type }}</div>
                            
                            <div class="info-label">Kategori Risiko</div>
                            <div class="info-value">{{ $riskReport->risk_category }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Tanggal Kejadian</div>
                            <div class="info-value">{{ $riskReport->occurred_at->format('d/m/Y') }}</div>
                            
                            <div class="info-label">Dampak</div>
                            <div class="info-value">{{ $riskReport->impact }}</div>
                            
                            <div class="info-label">Probabilitas</div>
                            <div class="info-value">{{ $riskReport->probability }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Kronologi Singkat</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->chronology }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Detil Kejadian</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->description }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Tindakan Segera</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->immediate_action }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi</div>
                        <div class="info-value mb-0" style="white-space: pre-line">{{ $riskReport->recommendation }}</div>
                    </div>
                </div>
            </div>

            <!-- Analisis Risiko -->
            @if($riskReport->analysis)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Analisis Risiko</h5>
                    <div>
                        @can('view', [$riskReport->analysis])
                        <a href="{{ route('modules.risk-management.risk-analysis.show', ['reportId' => $riskReport->id, 'id' => $riskReport->analysis->id]) }}" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Detail
                        </a>
                        @endcan
                        @can('update', [$riskReport->analysis])
                        <a href="{{ route('modules.risk-management.risk-analysis.edit', ['reportId' => $riskReport->id, 'id' => $riskReport->analysis->id]) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Analisis
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Status Analisis:</strong> {{ $riskReport->analysis->status }}
                                <br>
                                <small>Dianalisis oleh {{ $riskReport->analysis->analyst->name }} pada {{ $riskReport->analysis->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Penyebab Langsung</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->analysis->direct_cause }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Akar Masalah</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->analysis->root_cause }}</div>
                    </div>

                    <h6 class="section-heading mb-3">Faktor Kontributor</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label">Faktor Manusia</div>
                            <div class="info-value">
                                @if(isset($riskReport->analysis->contributor_factors['human_factors']))
                                    @php
                                        $humanFactors = [
                                            'knowledge' => 'Pengetahuan',
                                            'fatigue' => 'Kelelahan',
                                            'stress' => 'Stres',
                                            'communication' => 'Komunikasi',
                                            'teamwork' => 'Kerja Tim',
                                            'supervision' => 'Pengawasan',
                                            'experience' => 'Pengalaman',
                                            'attitude' => 'Sikap'
                                        ];
                                    @endphp
                                    @if(is_array($riskReport->analysis->contributor_factors['human_factors']))
                                        {{ implode(', ', array_map(function($item) use ($humanFactors) { 
                                            return $humanFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['human_factors'])) }}
                                    @else
                                        {{ $humanFactors[$riskReport->analysis->contributor_factors['human_factors']] ?? $riskReport->analysis->contributor_factors['human_factors'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </div>
                            
                            <div class="info-label">Faktor Lingkungan</div>
                            <div class="info-value">
                                @if(isset($riskReport->analysis->contributor_factors['environmental']))
                                    @php
                                        $environmentalFactors = [
                                            'temperature' => 'Suhu',
                                            'lighting' => 'Pencahayaan',
                                            'noise' => 'Kebisingan',
                                            'space_constraints' => 'Keterbatasan Ruang',
                                            'cleanliness' => 'Kebersihan',
                                            'ventilation' => 'Ventilasi',
                                            'workplace_layout' => 'Tata Letak Tempat Kerja'
                                        ];
                                    @endphp
                                    @if(is_array($riskReport->analysis->contributor_factors['environmental']))
                                        {{ implode(', ', array_map(function($item) use ($environmentalFactors) { 
                                            return $environmentalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['environmental'])) }}
                                    @else
                                        {{ $environmentalFactors[$riskReport->analysis->contributor_factors['environmental']] ?? $riskReport->analysis->contributor_factors['environmental'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Faktor Teknis</div>
                            <div class="info-value">
                                @if(isset($riskReport->analysis->contributor_factors['technical']))
                                    @php
                                        $technicalFactors = [
                                            'equipment_failure' => 'Kegagalan Peralatan',
                                            'software_issues' => 'Masalah Perangkat Lunak',
                                            'maintenance' => 'Pemeliharaan',
                                            'design_issues' => 'Masalah Desain',
                                            'technical_documentation' => 'Dokumentasi Teknis',
                                            'calibration' => 'Kalibrasi',
                                            'compatibility' => 'Kompatibilitas'
                                        ];
                                    @endphp
                                    @if(is_array($riskReport->analysis->contributor_factors['technical']))
                                        {{ implode(', ', array_map(function($item) use ($technicalFactors) { 
                                            return $technicalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['technical'])) }}
                                    @else
                                        {{ $technicalFactors[$riskReport->analysis->contributor_factors['technical']] ?? $riskReport->analysis->contributor_factors['technical'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </div>

                            <div class="info-label">Faktor Organisasi</div>
                            <div class="info-value">
                                @if(isset($riskReport->analysis->contributor_factors['organizational']))
                                    @php
                                        $organizationalFactors = [
                                            'policies_procedures' => 'Kebijakan & Prosedur',
                                            'staffing' => 'Kepegawaian',
                                            'training' => 'Pelatihan',
                                            'leadership' => 'Kepemimpinan',
                                            'resource_allocation' => 'Alokasi Sumber Daya',
                                            'organizational_culture' => 'Budaya Organisasi',
                                            'communication_systems' => 'Sistem Komunikasi'
                                        ];
                                    @endphp
                                    @if(is_array($riskReport->analysis->contributor_factors['organizational']))
                                        {{ implode(', ', array_map(function($item) use ($organizationalFactors) { 
                                            return $organizationalFactors[$item] ?? $item; 
                                        }, $riskReport->analysis->contributor_factors['organizational'])) }}
                                    @else
                                        {{ $organizationalFactors[$riskReport->analysis->contributor_factors['organizational']] ?? $riskReport->analysis->contributor_factors['organizational'] }}
                                    @endif
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>

                    <h6 class="section-heading mb-3">Rekomendasi</h6>
                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Pendek (0-3 bulan)</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->analysis->recommendation_short }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Menengah (3-6 bulan)</div>
                        <div class="info-value" style="white-space: pre-line">{{ $riskReport->analysis->recommendation_medium ?: '-' }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Rekomendasi Jangka Panjang (6+ bulan)</div>
                        <div class="info-value mb-0" style="white-space: pre-line">{{ $riskReport->analysis->recommendation_long ?: '-' }}</div>
                    </div>
                </div>
            </div>
            @else
                @can('create', [App\Models\RiskAnalysis::class, $riskReport])
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-chart-line fa-4x mb-3"></i>
                            <h5>Analisis Risiko Belum Dibuat</h5>
                            <p>Lakukan analisis mendalam untuk identifikasi akar masalah dan rekomendasi penyelesaian.</p>
                        </div>
                        <a href="{{ route('modules.risk-management.risk-analysis.create', $riskReport->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Buat Analisis Baru
                        </a>
                    </div>
                </div>
                @endcan
            @endif
        </div>

        <div class="col-md-4">
            <!-- Informasi Pelapor -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pelapor</h5>
                </div>
                <div class="card-body">
                    <div class="info-label">Dilaporkan oleh</div>
                    <div class="info-value">{{ $riskReport->creator->name }}</div>

                    <div class="info-label">Tanggal Laporan</div>
                    <div class="info-value">{{ $riskReport->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- Tanda Tangan Digital -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tanda Tangan Digital</h5>
                </div>
                <div class="card-body text-center">
                    @if($riskReport->status === 'resolved')
                        <img src="{{ route('modules.risk-management.risk-reports.qr-code', $riskReport->id) }}" 
                             alt="QR Code" class="img-fluid mb-2" style="max-width: 200px;">
                        <p class="text-muted small mb-0">Scan QR code untuk verifikasi laporan</p>
                    @elseif($riskReport->analysis && in_array($riskReport->analysis->analysis_status, ['completed', 'in_progress'])) 
                        <img src="{{ route('modules.risk-management.risk-analysis.qr-code', [$riskReport->id, $riskReport->analysis->id]) }}" 
                             alt="QR Code Analisis" class="img-fluid mb-2" style="max-width: 200px;">
                        <p class="text-muted small">Scan QR code untuk verifikasi analisis</p>
                        <p class="text-muted small mb-0">Status: 
                            @if($riskReport->analysis->analysis_status === 'completed')
                                <span class="badge bg-success">Analisis Selesai</span>
                            @else
                                <span class="badge bg-warning text-dark">Analisis Dalam Proses</span> 
                            @endif
                        </p>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            QR code akan tersedia setelah laporan disetujui atau analisis selesai
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('modules.risk-management.risk-reports.export-awal', $riskReport->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-word me-1"></i> Export Laporan Awal
                        </a>
                        
                        @if($riskReport->status === 'resolved' || ($riskReport->analysis && $riskReport->analysis->analysis_status === 'completed'))
                        <a href="{{ route('modules.risk-management.risk-reports.export-akhir', $riskReport->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF Laporan Final
                        </a>
                        @endif

                        <a href="{{ route('modules.risk-management.risk-reports.qr-code', $riskReport->id) }}" 
                           class="btn btn-dark" target="_blank">
                            <i class="fas fa-qrcode me-1"></i> Generate QR Code Tanda Tangan
                        </a>

                        <!-- Dokumen Terkait -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Dokumen Terkait</h5>
                            </div>
                            <div class="card-body">
                                @if($riskReport->documents->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($riskReport->documents as $doc)
                                    <li class="list-group-item border-0 p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $doc->document_title }}</strong><br>
                                                <small class="text-muted">No: {{ $doc->document_number }}</small>
                                                @if($doc->file_path)
                                                <div>
                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" class="text-primary" target="_blank">
                                                        <i class="fas fa-paperclip me-1"></i> Lihat File
                                                    </a>
                                                </div>
                                                @else
                                                <div>
                                                    <span class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> Tanpa file</span>
                                                </div>
                                                @endif
                                            </div>
                                            <a href="{{ route('modules.document-management.documents.show', $doc->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail Dokumen">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted fst-italic mb-0">Belum ada dokumen yang terhubung.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tag Management Section -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Tag</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($riskReport->tags as $tag)
                                    <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-{{ $tag->id }}">
                                        <a href="{{ route('tenant.tags.documents', $tag->slug) }}" class="text-decoration-none text-white">
                                            {{ $tag->name }}
                                        </a>
                                        <button 
                                            type="button" 
                                            class="btn-close btn-close-white ms-2" 
                                            style="font-size: 0.7rem;" 
                                            onclick="hapusTagLangsung({{ $tag->id }}, {{ $riskReport->id }}, 'App\\Models\\RiskReport')"
                                            aria-label="Close">
                                        </button>
                                    </div>
                                    @endforeach
                                </div>

                                <form id="formTambahTag" action="{{ route('tenant.tags.attach-document') }}" method="POST" class="d-flex gap-2 mt-2">
                                    @csrf
                                    <input type="hidden" name="document_id" value="{{ $riskReport->id }}">
                                    <input type="hidden" name="document_type" value="App\Models\RiskReport">
                                    <select name="tag_id" id="selectTag" class="form-select form-select-sm" required>
                                        <option value="">Pilih Tag</option>
                                        @foreach(App\Models\Tag::forTenant(session('tenant_id'))->orderBy('name')->get() as $tag)
                                            <option value="{{ $tag->id }}" data-slug="{{ $tag->slug }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Tambah Tag</button>
                                </form>
                            </div>
                        </div>

                        @can('edit', $riskReport)
                        <a href="{{ route('modules.risk-management.risk-reports.edit', $riskReport->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Laporan
                        </a>
                        @endcan

                        @can('delete', $riskReport)
                        <form method="POST" action="{{ route('modules.risk-management.risk-reports.destroy', $riskReport->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                <i class="fas fa-trash me-1"></i> Hapus Laporan
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menghapus tag langsung tanpa konfirmasi
    function hapusTagLangsung(tagId, documentId, documentType) {
        // Dapatkan CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Hapus tag dari DOM sebelum request selesai (untuk UX yang lebih cepat)
        const tagElement = document.getElementById('tag-item-' + tagId);
        if (tagElement) {
            tagElement.style.opacity = '0.5'; // Visual feedback saat proses penghapusan
        }
        
        // Buat form data untuk endpoint baru
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        
        // Kirim request dengan fetch API ke endpoint baru
        fetch('/tenant/tags/delete-tag', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Hapus tag dari DOM jika belum dihapus
                if (tagElement) {
                    tagElement.remove();
                }
            } else {
                console.error('Gagal menghapus tag:', response.statusText);
                // Kembalikan tampilan tag jika terjadi error
                if (tagElement) {
                    tagElement.style.opacity = '1';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Kembalikan tampilan tag jika terjadi error
            if (tagElement) {
                tagElement.style.opacity = '1';
            }
        });
    }
    
    // Tangani submit form tambah tag
    document.getElementById('formTambahTag').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Dapatkan CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Dapatkan data dari form
        const selectElement = document.getElementById('selectTag');
        const tagId = selectElement.value;
        
        if (!tagId) return;
        
        const tagName = selectElement.options[selectElement.selectedIndex].text;
        const tagSlug = selectElement.options[selectElement.selectedIndex].dataset.slug;
        const documentId = document.querySelector('input[name="document_id"]').value;
        const documentType = document.querySelector('input[name="document_type"]').value;
        
        // Buat form data
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        formData.append('_token', csrfToken);
        
        // Kirim request dengan fetch API
        fetch('{{ route('tenant.tags.attach-document') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Tambahkan tag baru ke DOM
                const tagsContainer = document.querySelector('.d-flex.flex-wrap.gap-2.mb-2');
                const newTagHtml = `
                    <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-${tagId}">
                        <a href="/tenant/tags/${tagSlug}/documents" class="text-decoration-none text-white">
                            ${tagName}
                        </a>
                        <button 
                            type="button" 
                            class="btn-close btn-close-white ms-2" 
                            style="font-size: 0.7rem;" 
                            onclick="hapusTagLangsung(${tagId}, ${documentId}, '${documentType}')"
                            aria-label="Close">
                        </button>
                    </div>
                `;
                tagsContainer.insertAdjacentHTML('beforeend', newTagHtml);
                
                // Reset pilihan dropdown
                selectElement.value = '';
            } else {
                console.error('Gagal menambahkan tag:', response.statusText);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
@endsection 