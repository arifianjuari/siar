@extends('layouts.app')

@section('title', ' | Buat Laporan Risiko dan Insiden')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">Buat Laporan Risiko dan Insiden</h1>
    <span class="badge bg-primary px-3 py-2" style="font-size: 1rem;">Form RIR-{{ date('Ymd') }}-{{ \App\Models\RiskReport::where('tenant_id', auth()->user()->tenant_id)->whereYear('created_at', date('Y'))->count() + 1 }}</span>
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
        <form method="POST" action="{{ route('modules.risk-management.risk-reports.store') }}" class="needs-validation" id="riskReportForm" novalidate>
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
                            <div class="mb-3">
                                <label for="risk_title" class="form-label required-field">Judul Insiden</label>
                                <input type="text" name="risk_title" id="risk_title" class="form-control form-control-lg @error('risk_title') is-invalid @enderror" value="{{ old('risk_title') }}" required placeholder="Masukkan judul insiden secara singkat dan jelas...">
                                @error('risk_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="reporter_unit" class="form-label required-field">Unit Pelapor</label>
                                    <select name="reporter_unit" id="reporter_unit" class="form-select @error('reporter_unit') is-invalid @enderror" required>
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        @foreach($workUnits as $unit)
                                            <option value="{{ $unit->name }}" {{ old('reporter_unit') == $unit->name ? 'selected' : '' }}>
                                                {{ $unit->name }} {{ $unit->code ? '('.$unit->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reporter_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="occurred_at" class="form-label required-field">Tanggal & Waktu</label>
                                    <input type="datetime-local" name="occurred_at" id="occurred_at" class="form-control @error('occurred_at') is-invalid @enderror" value="{{ old('occurred_at') }}" required>
                                    @error('occurred_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="chronology" class="form-label required-field">Kronologi Kejadian</label>
                                <textarea name="chronology" id="chronology" class="form-control @error('chronology') is-invalid @enderror" rows="4" required placeholder="Jelaskan kronologi kejadian secara detail...">{{ old('chronology') }}</textarea>
                                @error('chronology')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Rekomendasi -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i> Rekomendasi & Tindak Lanjut</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="recommendation" class="form-label required-field">Rekomendasi Tindakan</label>
                                <textarea name="recommendation" id="recommendation" class="form-control @error('recommendation') is-invalid @enderror" rows="4" required placeholder="Masukkan rekomendasi atau saran untuk perbaikan...">{{ old('recommendation') }}</textarea>
                                @error('recommendation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmation" required>
                                    <label class="form-check-label" for="confirmation">
                                        Saya menyatakan bahwa informasi yang saya berikan dalam laporan ini adalah benar dan akurat.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan -->
                <div class="col-md-6 ps-md-2">
                    <!-- Section 2: Kategorisasi & Penilaian Risiko -->
                    <div class="card form-section">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Kategorisasi & Penilaian Risiko</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="risk_type" class="form-label required-field">Tipe Risiko</label>
                                    <select name="risk_type" id="risk_type" class="form-select @error('risk_type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tipe Risiko --</option>
                                        <option value="KTD" {{ old('risk_type') == 'KTD' ? 'selected' : '' }}>KTD (Kejadian Tidak Diharapkan)</option>
                                        <option value="KNC" {{ old('risk_type') == 'KNC' ? 'selected' : '' }}>KNC (Kejadian Nyaris Cedera)</option>
                                        <option value="KTC" {{ old('risk_type') == 'KTC' ? 'selected' : '' }}>KTC (Kejadian Tidak Cedera)</option>
                                        <option value="KPC" {{ old('risk_type') == 'KPC' ? 'selected' : '' }}>KPC (Kondisi Potensial Cedera)</option>
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
                                        <option value="Keselamatan" {{ old('risk_category') == 'Keselamatan' ? 'selected' : '' }}>Keselamatan</option>
                                        <option value="Fasilitas" {{ old('risk_category') == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                                        <option value="Operasional" {{ old('risk_category') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                        <option value="Lainnya" {{ old('risk_category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('risk_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="impact" class="form-label required-field">Dampak</label>
                                    <select name="impact" id="impact" class="form-select risk-param @error('impact') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="ringan" {{ old('impact') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                                        <option value="sedang" {{ old('impact') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="berat" {{ old('impact') == 'berat' ? 'selected' : '' }}>Berat</option>
                                    </select>
                                    @error('impact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="probability" class="form-label required-field">Probabilitas</label>
                                    <select name="probability" id="probability" class="form-select risk-param @error('probability') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="jarang" {{ old('probability') == 'jarang' ? 'selected' : '' }}>Jarang</option>
                                        <option value="kadang" {{ old('probability') == 'kadang' ? 'selected' : '' }}>Kadang</option>
                                        <option value="sering" {{ old('probability') == 'sering' ? 'selected' : '' }}>Sering</option>
                                    </select>
                                    @error('probability')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="risk_level" class="form-label required-field">Tingkat Risiko</label>
                                    <select name="risk_level" id="risk_level" class="form-select @error('risk_level') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="rendah" {{ old('risk_level') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                        <option value="sedang" {{ old('risk_level') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="tinggi" {{ old('risk_level') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    </select>
                                    @error('risk_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Risk Matrix Visualization -->
                            <div class="risk-matrix">
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
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('modules.risk-management.risk-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
                <div>
                    <button type="reset" class="btn btn-light me-2">
                        <i class="fas fa-redo me-2"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('riskReportForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
        
        // Update risk level visualization based on selection
        function updateRiskMarker() {
            const riskLevel = document.getElementById('risk_level').value;
            const marker = document.getElementById('riskMarker');
            
            if (riskLevel === 'rendah') {
                marker.style.left = '15%';
            } else if (riskLevel === 'sedang') {
                marker.style.left = '50%';
            } else if (riskLevel === 'tinggi') {
                marker.style.left = '95%';
            } else {
                marker.style.left = '0%';
            }
        }
        
        // Auto calculate risk level based on impact and probability
        function calculateRiskLevel() {
            const impact = document.getElementById('impact').value;
            const probability = document.getElementById('probability').value;
            const riskLevelSelect = document.getElementById('risk_level');
            
            if (!impact || !probability) {
                return;
            }
            
            let riskLevel = 'rendah';
            
            if (impact === 'berat' && probability === 'sering') {
                riskLevel = 'tinggi';
            } else if ((impact === 'berat' && probability === 'kadang') || 
                      (impact === 'sedang' && probability === 'sering')) {
                riskLevel = 'tinggi';
            } else if ((impact === 'berat' && probability === 'jarang') || 
                      (impact === 'sedang' && probability === 'kadang') ||
                      (impact === 'ringan' && probability === 'sering')) {
                riskLevel = 'sedang';
            }
            
            riskLevelSelect.value = riskLevel;
            updateRiskMarker();
        }
        
        // Initial risk marker position
        updateRiskMarker();
        
        // Event listeners for risk parameters
        document.querySelectorAll('.risk-param').forEach(function(select) {
            select.addEventListener('change', calculateRiskLevel);
        });
        
        document.getElementById('risk_level').addEventListener('change', updateRiskMarker);
    });
</script>
@endpush 