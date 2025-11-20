@extends('layouts.app')

@section('title', ' | Buat Laporan Risiko dan Insiden')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">Buat Laporan Risiko dan Insiden</h1>
    <span class="badge bg-primary px-3 py-2" style="font-size: 1rem;">Form RIR-{{ date('Ymd') }}-{{ str_pad(\App\Models\RiskReport::where('tenant_id', auth()->user()->tenant_id)->whereYear('created_at', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT) }}</span>
</div>
@endsection

@push('styles')
<style>
    .form-section {
        box-shadow: 0 4px 8px rgba(0,0,0,0.03);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        border: none;
    }
    
    .form-section:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .form-section .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4F46E5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-primary {
        background-color: #4F46E5;
        border-color: #4F46E5;
    }
    
    .btn-primary:hover {
        background-color: #4338CA;
        border-color: #4338CA;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .required-field::after {
        content: "*";
        color: #EF4444;
        margin-left: 4px;
    }
    
    /* Progress Bar/Stepper Styles */
    .form-stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        position: relative;
        z-index: 2;
        transition: all 0.3s;
        font-weight: 600;
        color: #6c757d;
    }
    
    .step-title {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .step.active .step-circle {
        background-color: #4F46E5;
        border-color: #4F46E5;
        color: white;
    }
    
    .step.active .step-title {
        color: #4F46E5;
        font-weight: 600;
    }
    
    .step.completed .step-circle {
        background-color: #10B981;
        border-color: #10B981;
        color: white;
    }
    
    .step.completed .step-title {
        color: #10B981;
    }
    
    .step-connector {
        position: absolute;
        top: 20px;
        left: calc(50% + 20px);
        right: calc(50% - 20px);
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }
    
    .step:last-child .step-connector {
        display: none;
    }
    
    .step.completed .step-connector,
    .step.active .step-connector {
        background-color: #4F46E5;
    }
    
    /* Tingkat Risiko Visualization */
    .risk-matrix {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .risk-level-indicator {
        width: 100%;
        height: 10px;
        background: linear-gradient(to right, #10B981, #FBBF24, #EF4444);
        border-radius: 5px;
        margin-bottom: 5px;
        position: relative;
    }
    
    .risk-level-marker {
        width: 20px;
        height: 20px;
        background-color: #4F46E5;
        border-radius: 50%;
        position: absolute;
        top: -5px;
        transform: translateX(-50%);
        left: 50%;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
        border: 2px solid white;
    }
    
    /* Floating Labels (Optional Enhancement) */
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
    }
    
    .form-floating > label {
        padding: 1rem 0.75rem;
    }
    
    .risk-level-display-rendah {
        background-color: rgba(16, 185, 129, 0.1) !important;
        color: #10B981 !important;
        font-weight: 600;
        border-color: #10B981;
    }
    
    .risk-level-display-sedang {
        background-color: rgba(245, 158, 11, 0.1) !important;
        color: #F59E0B !important;
        font-weight: 600;
        border-color: #F59E0B;
    }
    
    .risk-level-display-tinggi {
        background-color: rgba(239, 68, 68, 0.1) !important;
        color: #EF4444 !important;
        font-weight: 600;
        border-color: #EF4444;
    }
    
    .risk-level-display-ekstrem {
        background-color: rgba(220, 38, 38, 0.1) !important;
        color: #DC2626 !important;
        font-weight: 700;
        border-color: #DC2626;
    }
    
    /* Matriks risiko styling */
    .risk-matrix-table th, .risk-matrix-table td {
        transition: all 0.3s ease;
    }
    
    .risk-matrix-table .active-cell {
        position: relative;
        box-shadow: 0 0 0 2px #4F46E5;
        z-index: 1;
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid pt-0 py-2">
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading">Terdapat kesalahan dalam pengisian formulir:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Form Card -->
        <form method="POST" action="{{ route('modules.risk-management.risk-reports.store') }}" class="needs-validation" id="riskReportForm" enctype="multipart/form-data" novalidate>
            @csrf
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6 pe-md-2">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i> Informasi Dasar Insiden</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="document_title" class="form-label required-field">Judul Insiden</label>
                                <input type="text" name="document_title" id="document_title" class="form-control form-control-lg @error('document_title') is-invalid @enderror" value="{{ old('document_title') }}" required placeholder="Masukkan judul insiden secara singkat dan jelas...">
                                @error('document_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="reporter_unit" class="form-label required-field">Unit Pelapor</label>
                                    <select name="work_unit_id" id="work_unit_id" class="form-select @error('work_unit_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        @foreach($workUnits as $unit)
                                            <option value="{{ $unit->id }}" 
                                                    data-unit-name="{{ $unit->unit_name }}"
                                                    {{ old('work_unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }} {{ $unit->unit_code ? '('.$unit->unit_code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="reporter_unit" id="reporter_unit_name" value="{{ old('reporter_unit') }}">
                                    @error('work_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="occurred_at" class="form-label required-field">Tanggal Kejadian</label>
                                    <input type="date" name="occurred_at" id="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at') }}" required>
                                    @error('occurred_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="risk_type" class="form-label required-field">Tipe Risiko</label>
                                    <select name="risk_type" id="risk_type" class="form-select @error('risk_type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="KTC" {{ old('risk_type') == 'KTC' ? 'selected' : '' }}>Kejadian Tidak Cedera (KTC)</option>
                                        <option value="KPC" {{ old('risk_type') == 'KPC' ? 'selected' : '' }}>Kejadian Potensial Cedera (KPC)</option>
                                        <option value="KNC" {{ old('risk_type') == 'KNC' ? 'selected' : '' }}>Kejadian Nyaris Cedera (KNC)</option>
                                        <option value="KTD" {{ old('risk_type') == 'KTD' ? 'selected' : '' }}>Kejadian Tidak Diharapkan (KTD)</option>
                                        <option value="Sentinel" {{ old('risk_type') == 'Sentinel' ? 'selected' : '' }}>Kejadian Sentinel</option>
                                    </select>
                                    @error('risk_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="risk_category" class="form-label required-field">Kategori Risiko</label>
                                    <select name="risk_category" id="risk_category" class="form-select @error('risk_category') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Medis" {{ old('risk_category') == 'Medis' ? 'selected' : '' }}>Medis</option>
                                        <option value="Non-medis" {{ old('risk_category') == 'Non-medis' ? 'selected' : '' }}>Non-medis</option>
                                        <option value="Pasien" {{ old('risk_category') == 'Pasien' ? 'selected' : '' }}>Pasien</option>
                                        <option value="Pengunjung" {{ old('risk_category') == 'Pengunjung' ? 'selected' : '' }}>Pengunjung</option>
                                        <option value="Fasilitas" {{ old('risk_category') == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                                        <option value="Karyawan" {{ old('risk_category') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                                    </select>
                                    @error('risk_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Deskripsi & Tindakan -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-align-left me-2"></i> Deskripsi Insiden</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="chronology" class="form-label required-field">Kronologi Singkat</label>
                                <textarea name="chronology" id="chronology" rows="3" class="form-control @error('chronology') is-invalid @enderror" required placeholder="Jelaskan secara singkat gambaran kejadian...">{{ old('chronology') }}</textarea>
                                @error('chronology')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label required-field">Detil Kejadian</label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required placeholder="Jelaskan secara detail kronologi kejadian, lokasi, dan pihak yang terlibat...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="immediate_action" class="form-label required-field">Tindakan Segera yang Dilakukan</label>
                                <textarea name="immediate_action" id="immediate_action" rows="3" class="form-control @error('immediate_action') is-invalid @enderror" required placeholder="Jelaskan tindakan apa yang segera diambil saat kejadian berlangsung...">{{ old('immediate_action') }}</textarea>
                                @error('immediate_action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="recommendation" class="form-label">Rekomendasi</label>
                                <textarea name="recommendation" id="recommendation" rows="3" class="form-control @error('recommendation') is-invalid @enderror" placeholder="Berikan rekomendasi untuk mencegah kejadian serupa...">{{ old('recommendation') }}</textarea>
                                @error('recommendation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan -->
                <div class="col-md-6 ps-md-2">
                    <!-- Section 2: Penilaian Risiko -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Penilaian Risiko</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="impact" class="form-label required-field">Dampak</label>
                                    <select name="impact" id="impact" class="form-select @error('impact') is-invalid @enderror" required>
                                        <option value="">-- Pilih Dampak --</option>
                                        <option value="ringan" {{ old('impact') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                        <option value="sedang" {{ old('impact') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="berat" {{ old('impact') == 'berat' ? 'selected' : '' }}>Berat</option>
                                    </select>
                                    @error('impact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="probability" class="form-label required-field">Probabilitas</label>
                                    <select name="probability" id="probability" class="form-select @error('probability') is-invalid @enderror" required>
                                        <option value="">-- Pilih Probabilitas --</option>
                                        <option value="jarang" {{ old('probability') == 'jarang' ? 'selected' : '' }}>Jarang</option>
                                        <option value="kadang" {{ old('probability') == 'kadang' ? 'selected' : '' }}>Kadang</option>
                                        <option value="sering" {{ old('probability') == 'sering' ? 'selected' : '' }}>Sering</option>
                                    </select>
                                    @error('probability')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="risk_level_display" class="form-label required-field">Tingkat Risiko</label>
                                
                                <!-- Hidden select for form submission -->
                                <select name="risk_level" id="risk_level" class="form-select d-none @error('risk_level') is-invalid @enderror" required>
                                    <option value="">-- Tingkat Risiko Otomatis --</option>
                                    <option value="rendah" {{ old('risk_level') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="sedang" {{ old('risk_level') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="tinggi" {{ old('risk_level') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="ekstrem" {{ old('risk_level') == 'ekstrem' ? 'selected' : '' }}>Ekstrem</option>
                                </select>
                                
                                <!-- Read-only display of risk level -->
                                <input type="text" id="risk_level_display" class="form-control" readonly value="{{ old('risk_level') ? ucfirst(old('risk_level')) : 'Otomatis dihitung berdasarkan Dampak dan Probabilitas' }}">
                                
                                @error('risk_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="risk-matrix mt-3">
                                    <div class="risk-level-indicator">
                                        <div class="risk-level-marker" id="riskLevelMarker" style="left: 25%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small text-success">Rendah</span>
                                        <span class="small text-warning">Sedang</span>
                                        <span class="small text-danger">Tinggi</span>
                                        <span class="small text-danger fw-bold">Ekstrem</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 4: Informasi Dokumen -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Informasi Dokumen</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="document_type" class="form-label">Tipe Dokumen</label>
                                    <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror">
                                        <option value="">-- Pilih Tipe Dokumen --</option>
                                        <option value="Regulasi" {{ old('document_type') == 'Regulasi' ? 'selected' : '' }}>Regulasi</option>
                                        <option value="Bukti" {{ old('document_type') == 'Bukti' ? 'selected' : '' }}>Bukti</option>
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Jenis dokumen akan menentukan bagaimana laporan ini dikategorikan di modul Manajemen Dokumen.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="document_version" class="form-label">Versi Dokumen</label>
                                    <input type="text" name="document_version" id="document_version" class="form-control @error('document_version') is-invalid @enderror" value="{{ old('document_version') }}">
                                    @error('document_version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Misalnya: 1.0, 2.1, dst.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan</label>
                                    <select name="confidentiality_level" id="confidentiality_level" class="form-select @error('confidentiality_level') is-invalid @enderror">
                                        <option value="">-- Pilih Tingkat Kerahasiaan --</option>
                                        <option value="Publik" {{ old('confidentiality_level') == 'Publik' ? 'selected' : '' }}>Publik</option>
                                        <option value="Internal" {{ old('confidentiality_level', 'Internal') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="Rahasia" {{ old('confidentiality_level') == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                    </select>
                                    @error('confidentiality_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Menentukan siapa yang boleh mengakses laporan ini.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="document_file" class="form-label">File Dokumen</label>
                                    <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror">
                                    @error('document_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX. Maks. 10MB.</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="next_review" class="form-label">Tanggal Tinjauan Berikutnya</label>
                                    <input type="date" name="next_review" id="next_review" class="form-control @error('next_review') is-invalid @enderror" value="{{ old('next_review') }}">
                                    @error('next_review')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kapan dokumen ini perlu ditinjau kembali.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="review_cycle_months" class="form-label">Siklus Tinjauan (bulan)</label>
                                    <input type="number" name="review_cycle_months" id="review_cycle_months" class="form-control @error('review_cycle_months') is-invalid @enderror" value="{{ old('review_cycle_months') }}" min="0" max="60">
                                    @error('review_cycle_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Berapa bulan sekali dokumen ini perlu ditinjau ulang.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Tambahan: Tags -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Tags Laporan</h5>
                        </div>
                        <div class="card-body">
                            <label for="tag-input" class="form-label">Tambah Tag</label>
                            <div class="input-group mb-2">
                                <input type="text" id="tag-input" class="form-control form-control-sm" placeholder="Ketik tag lalu tekan Enter...">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="add-tag-button">Tambah</button>
                            </div>
                            <div id="tags-container" class="d-flex flex-wrap gap-1 mb-1">
                                {{-- Tag badges akan ditambahkan di sini oleh JS --}}
                            </div>
                            <div class="form-text">Pisahkan beberapa tag dengan menekan Enter atau tombol Tambah setelah mengetik.</div>
                            
                            {{-- Hidden inputs untuk menyimpan tag yang akan dikirim --}}
                            <div id="hidden-tags-container">
                                @if(old('tags'))
                                    @foreach(old('tags') as $tagName)
                                        <input type="hidden" name="tags[]" value="{{ $tagName }}">
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 5: Submit -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i> Pengiriman Laporan</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Perhatian Sebelum Mengirim</h5>
                                        <p class="mb-0">Pastikan semua informasi yang Anda masukkan sudah benar dan lengkap. Laporan yang dikirimkan akan langsung ditinjau oleh tim terkait.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i> Simpan Laporan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set reporter_unit_name saat halaman dimuat
        const workUnitSelect = document.getElementById('work_unit_id');
        const reporterUnitNameInput = document.getElementById('reporter_unit_name');
        
        if (workUnitSelect && reporterUnitNameInput) {
            // Set nilai awal
            if (workUnitSelect.selectedIndex > 0) {
                const selectedOption = workUnitSelect.options[workUnitSelect.selectedIndex];
                reporterUnitNameInput.value = selectedOption.dataset.unitName;
            }
            
            // Listener untuk perubahan dropdown
            workUnitSelect.addEventListener('change', function() {
                if (this.selectedIndex > 0) {
                    const selectedOption = this.options[this.selectedIndex];
                    reporterUnitNameInput.value = selectedOption.dataset.unitName;
                } else {
                    reporterUnitNameInput.value = '';
                }
            });
        }
        
        // Risk Level Logic
        const impactSelect = document.getElementById('impact');
        const probabilitySelect = document.getElementById('probability');
        const riskLevelSelect = document.getElementById('risk_level');
        const riskLevelMarker = document.getElementById('riskLevelMarker');
        const riskLevelDisplay = document.getElementById('risk_level_display');
        
        function updateRiskMarker() {
            const level = riskLevelSelect.value;
            
            switch(level) {
                case 'rendah':
                    riskLevelMarker.style.left = '12.5%';
                    break;
                case 'sedang':
                    riskLevelMarker.style.left = '37.5%';
                    break;
                case 'tinggi':
                    riskLevelMarker.style.left = '62.5%';
                    break;
                case 'ekstrem':
                    riskLevelMarker.style.left = '87.5%';
                    break;
                default:
                    riskLevelMarker.style.left = '25%';
            }
        }
        
        function calculateRiskLevel() {
            const impact = impactSelect.value;
            const probability = probabilitySelect.value;
            
            // Hapus semua kelas warna sebelumnya
            riskLevelDisplay.classList.remove('risk-level-display-rendah', 'risk-level-display-sedang', 'risk-level-display-tinggi', 'risk-level-display-ekstrem');
            
            // Reset semua highlight pada matriks
            document.querySelectorAll('.risk-matrix-table td').forEach(cell => {
                cell.classList.remove('active-cell');
            });
            
            if (!impact || !probability) {
                riskLevelDisplay.value = 'Otomatis dihitung berdasarkan Dampak dan Probabilitas';
                return;
            }
            
            let riskLevel = '';
            
            // Matriks risiko
            if (impact === 'ringan' && probability === 'jarang') {
                riskLevel = 'rendah';
                highlightMatrixCell(0, 0);
            } else if (impact === 'ringan' && probability === 'kadang') {
                riskLevel = 'rendah';
                highlightMatrixCell(0, 1);
            } else if (impact === 'ringan' && probability === 'sering') {
                riskLevel = 'sedang';
                highlightMatrixCell(0, 2);
            } else if (impact === 'sedang' && probability === 'jarang') {
                riskLevel = 'rendah';
                highlightMatrixCell(1, 0);
            } else if (impact === 'sedang' && probability === 'kadang') {
                riskLevel = 'sedang';
                highlightMatrixCell(1, 1);
            } else if (impact === 'sedang' && probability === 'sering') {
                riskLevel = 'tinggi';
                highlightMatrixCell(1, 2);
            } else if (impact === 'berat' && probability === 'jarang') {
                riskLevel = 'sedang';
                highlightMatrixCell(2, 0);
            } else if (impact === 'berat' && probability === 'kadang') {
                riskLevel = 'tinggi';
                highlightMatrixCell(2, 1);
            } else if (impact === 'berat' && probability === 'sering') {
                riskLevel = 'ekstrem';
                highlightMatrixCell(2, 2);
            }
            
            // Set the risk level select value
            riskLevelSelect.value = riskLevel;
            
            // Update visual display
            riskLevelDisplay.value = riskLevel.charAt(0).toUpperCase() + riskLevel.slice(1);
            
            // Tambahkan kelas CSS sesuai tingkat risiko
            riskLevelDisplay.classList.add('risk-level-display-' + riskLevel);
            
            // Update the marker
            updateRiskMarker();
        }
        
        function highlightMatrixCell(row, col) {
            // Matriks sudah dihapus, jadi fungsi ini tidak melakukan apa-apa
            return;
        }
        
        // Initialize marker position
        updateRiskMarker();
        
        // Update risk level when impact or probability changes
        impactSelect.addEventListener('change', calculateRiskLevel);
        probabilitySelect.addEventListener('change', calculateRiskLevel);
        
        // Calculate initial risk level if impact and probability have values (for old input)
        if (impactSelect.value && probabilitySelect.value) {
            calculateRiskLevel();
        }
        
        // Form validation
        const form = document.getElementById('riskReportForm');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });

    // --- Tag Input Logic --- 
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
    tagInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Cegah submit form
            addTag(tagInput.value);
        }
    });

    addTagButton.addEventListener('click', function() {
        addTag(tagInput.value);
    });

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

</script>
@endpush 