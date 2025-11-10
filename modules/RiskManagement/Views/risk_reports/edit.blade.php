@extends('layouts.app')

@section('title', ' | Edit Laporan Risiko')

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
</style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Laporan Risiko</h2>
            <div>
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <form method="POST" action="{{ route('modules.risk-management.risk-reports.update', $riskReport->id) }}" class="needs-validation" id="riskReportForm" novalidate>
            @csrf
            @method('PUT')
            
            <!-- Intro Section -->
            <div class="card form-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Informasi Laporan</h5>
                    <span class="badge bg-primary">ID: {{ $riskReport->id }}</span>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
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
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Status Laporan</h5>
                                <p class="mb-0">
                                    @if($riskReport->status == 'open')
                                        <span class="badge bg-danger">Open</span>
                                    @elseif($riskReport->status == 'in_review')
                                        <span class="badge bg-warning text-dark">In Review</span>
                                    @else
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                    &nbsp; Dibuat pada: {{ $riskReport->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 1: Informasi Dasar -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i> Informasi Dasar Insiden</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="risk_title" class="form-label required-field">Judul Insiden</label>
                            <input type="text" name="risk_title" id="risk_title" class="form-control form-control-lg @error('risk_title') is-invalid @enderror" value="{{ old('risk_title', $riskReport->risk_title) }}" required>
                            @error('risk_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reporter_unit" class="form-label required-field">Unit Pelapor</label>
                            <select name="reporter_unit" id="reporter_unit" class="form-select @error('reporter_unit') is-invalid @enderror" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($workUnits as $unit)
                                    <option value="{{ $unit->name }}" {{ old('reporter_unit', $riskReport->reporter_unit) == $unit->name ? 'selected' : '' }}>
                                        {{ $unit->name }} {{ $unit->code ? '('.$unit->code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reporter_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="occurred_at" class="form-label required-field">Tanggal Kejadian</label>
                            <input type="date" name="occurred_at" id="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at', $riskReport->occurred_at->format('Y-m-d')) }}" required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="chronology" class="form-label required-field">Kronologi Kejadian</label>
                            <textarea name="chronology" id="chronology" rows="5" class="form-control @error('chronology') is-invalid @enderror" required>{{ old('chronology', $riskReport->chronology) }}</textarea>
                            @error('chronology')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 2: Detail Risiko -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i> Detail Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="risk_type" class="form-label">Tipe Risiko</label>
                            <select name="risk_type" id="risk_type" class="form-select @error('risk_type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe Risiko --</option>
                                <option value="KTD" {{ old('risk_type', $riskReport->risk_type) == 'KTD' ? 'selected' : '' }}>KTD (Kejadian Tidak Diharapkan)</option>
                                <option value="KNC" {{ old('risk_type', $riskReport->risk_type) == 'KNC' ? 'selected' : '' }}>KNC (Kejadian Nyaris Cedera)</option>
                                <option value="KTC" {{ old('risk_type', $riskReport->risk_type) == 'KTC' ? 'selected' : '' }}>KTC (Kejadian Tidak Cedera)</option>
                                <option value="KPC" {{ old('risk_type', $riskReport->risk_type) == 'KPC' ? 'selected' : '' }}>KPC (Kondisi Potensial Cedera)</option>
                            </select>
                            @error('risk_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="risk_category" class="form-label required-field">Kategori Risiko</label>
                            <input type="text" name="risk_category" id="risk_category" class="form-control @error('risk_category') is-invalid @enderror" value="{{ old('risk_category', $riskReport->risk_category) }}" required>
                            @error('risk_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="impact" class="form-label required-field">Dampak</label>
                            <select name="impact" id="impact" class="form-select @error('impact') is-invalid @enderror" required>
                                <option value="">-- Pilih Dampak --</option>
                                <option value="ringan" {{ old('impact', strtolower($riskReport->impact)) == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ old('impact', strtolower($riskReport->impact)) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="berat" {{ old('impact', strtolower($riskReport->impact)) == 'berat' ? 'selected' : '' }}>Berat</option>
                            </select>
                            @error('impact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="probability" class="form-label required-field">Probabilitas</label>
                            <select name="probability" id="probability" class="form-select @error('probability') is-invalid @enderror" required>
                                <option value="">-- Pilih Probabilitas --</option>
                                <option value="jarang" {{ old('probability', strtolower($riskReport->probability)) == 'jarang' ? 'selected' : '' }}>Jarang</option>
                                <option value="kadang" {{ old('probability', strtolower($riskReport->probability)) == 'kadang' ? 'selected' : '' }}>Kadang</option>
                                <option value="sering" {{ old('probability', strtolower($riskReport->probability)) == 'sering' ? 'selected' : '' }}>Sering</option>
                            </select>
                            @error('probability')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="risk_level" class="form-label required-field">Tingkat Risiko</label>
                            <select name="risk_level" id="risk_level" class="form-select @error('risk_level') is-invalid @enderror" required>
                                <option value="">-- Pilih Tingkat Risiko --</option>
                                <option value="rendah" {{ old('risk_level', strtolower($riskReport->risk_level)) == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="sedang" {{ old('risk_level', strtolower($riskReport->risk_level)) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="tinggi" {{ old('risk_level', strtolower($riskReport->risk_level)) == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                            @error('risk_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Dihitung otomatis dari Dampak dan Probabilitas
                            </div>
                        </div>
                    </div>
                    
                    <div class="risk-matrix mt-3">
                        <p class="mb-2">Visualisasi Tingkat Risiko:</p>
                        <div class="risk-level-indicator mb-3" style="position: relative; height: 20px; width: 100%; background: linear-gradient(to right, #28a745, #ffc107, #dc3545); border-radius: 10px; margin-top: 10px;">
                            <div id="riskMarker" class="risk-level-marker" style="position: absolute; width: 28px; height: 28px; background-color: #fff; border-radius: 50%; border: 3px solid #343a40; box-shadow: 0 0 5px rgba(0,0,0,0.3); top: -4px; transform: translateX(-50%); left: 50%;"></div>
                            <div style="display: flex; justify-content: space-between; position: relative; top: 25px;">
                                <div>Rendah</div>
                                <div>Sedang</div>
                                <div>Tinggi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section 3: Rekomendasi -->
            <div class="card form-section">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i> Evaluasi & Rekomendasi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="recommendation" class="form-label">Rekomendasi Tindakan</label>
                            <textarea name="recommendation" id="recommendation" rows="5" class="form-control @error('recommendation') is-invalid @enderror">{{ old('recommendation', $riskReport->recommendation) }}</textarea>
                            @error('recommendation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Berikan rekomendasi tindakan yang harus dilakukan untuk mencegah atau menangani risiko serupa di masa mendatang.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Submit -->
            <div class="d-flex justify-content-between my-4">
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    
    @push('scripts')
    <script>
        // Script untuk memperbarui visualisasi tingkat risiko
        document.addEventListener('DOMContentLoaded', function() {
            const riskLevelSelect = document.getElementById('risk_level');
            const riskMarker = document.getElementById('riskMarker');
            const impactSelect = document.getElementById('impact');
            const probabilitySelect = document.getElementById('probability');
            
            function updateRiskMarker() {
                const riskLevel = riskLevelSelect.value.toLowerCase();
                
                if (riskLevel === 'rendah') {
                    riskMarker.style.left = '15%';
                } else if (riskLevel === 'sedang') {
                    riskMarker.style.left = '50%';
                } else if (riskLevel === 'tinggi') {
                    riskMarker.style.left = '95%';
                }
            }
            
            // Fungsi untuk menghitung tingkat risiko otomatis berdasarkan dampak dan probabilitas
            function calculateRiskLevel() {
                const impact = impactSelect.value.toLowerCase();
                const probability = probabilitySelect.value.toLowerCase();
                
                // Jika salah satu tidak dipilih, jangan lakukan perhitungan
                if (!impact || !probability) {
                    return;
                }
                
                let riskLevel = 'rendah'; // Default rendah
                
                // Matriks perhitungan risiko
                if (impact === 'berat' && probability === 'sering') {
                    riskLevel = 'tinggi';
                } else if (impact === 'berat' && probability === 'kadang') {
                    riskLevel = 'tinggi';
                } else if (impact === 'berat' && probability === 'jarang') {
                    riskLevel = 'sedang';
                } else if (impact === 'sedang' && probability === 'sering') {
                    riskLevel = 'tinggi';
                } else if (impact === 'sedang' && probability === 'kadang') {
                    riskLevel = 'sedang';
                } else if (impact === 'sedang' && probability === 'jarang') {
                    riskLevel = 'rendah';
                } else if (impact === 'ringan' && probability === 'sering') {
                    riskLevel = 'sedang';
                } else if (impact === 'ringan' && probability === 'kadang') {
                    riskLevel = 'rendah';
                } else if (impact === 'ringan' && probability === 'jarang') {
                    riskLevel = 'rendah';
                }
                
                // Update nilai dropdown tingkat risiko
                riskLevelSelect.value = riskLevel;
                
                // Update visualisasi
                updateRiskMarker();
            }
            
            // Initial update
            updateRiskMarker();
            
            // Listen for changes
            riskLevelSelect.addEventListener('change', updateRiskMarker);
            
            // Otomatis hitung tingkat risiko saat dampak atau probabilitas berubah
            impactSelect.addEventListener('change', calculateRiskLevel);
            probabilitySelect.addEventListener('change', calculateRiskLevel);
        });
    </script>
    @endpush
@endsection 