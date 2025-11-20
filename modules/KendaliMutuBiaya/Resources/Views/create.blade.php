@extends('layouts.app')

@section('title', 'Buat Clinical Pathway Baru')

@push('styles')
<style>
    .form-submitting {
        position: relative;
    }

    .form-submitting::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 10;
        pointer-events: all;
    }
</style>
@endpush

@section('content')
<div class="py-4">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fs-2 fw-semibold mb-0">Buat Clinical Pathway Baru</h2>
                    <a href="{{ route('kendali-mutu-biaya.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <h5 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan:</h5>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('kendali-mutu-biaya.store') }}" method="POST" id="clinical-pathway-form">
                    @csrf
                    <input type="hidden" name="has_steps" id="has_steps" value="0">
                    <input type="hidden" name="structured_data" id="structured_data" value="">

                    <!-- Basic Info Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title fw-semibold mb-0">Informasi Dasar</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama Clinical Pathway <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="form-control">
                                    <div class="invalid-feedback">
                                        Nama Clinical Pathway harus diisi.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code', 'CP-' . time()) }}" required
                                        class="form-control">
                                    <div class="invalid-feedback">
                                        Kode harus diisi.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="effective_date" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                                    <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}" required
                                        class="form-control">
                                    <div class="invalid-feedback">
                                        Tanggal berlaku harus diisi.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="expiry_date" class="form-label">Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}" required
                                        class="form-control">
                                    <div class="invalid-feedback">
                                        Tanggal kadaluarsa harus diisi.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="diagnosis_code" class="form-label">Kode Diagnosis</label>
                                    <input type="text" name="diagnosis_code" id="diagnosis_code" value="{{ old('diagnosis_code') }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="diagnosis_name" class="form-label">Nama Diagnosis</label>
                                    <input type="text" name="diagnosis_name" id="diagnosis_name" value="{{ old('diagnosis_name') }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="procedure_code" class="form-label">Kode Prosedur</label>
                                    <input type="text" name="procedure_code" id="procedure_code" value="{{ old('procedure_code') }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="procedure_name" class="form-label">Nama Prosedur</label>
                                    <input type="text" name="procedure_name" id="procedure_name" value="{{ old('procedure_name') }}"
                                        class="form-control">
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold mb-0">Langkah-langkah Clinical Pathway</h5>
                            <button type="button" id="addStepBtn" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i> Tambah Langkah
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="stepsContainer" class="mb-3">
                                <!-- Template for a step -->
                                <div class="step-template d-none border rounded p-3 mb-3 position-relative">
                                    <button type="button" class="delete-step btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Nama Langkah <span class="text-danger">*</span></label>
                                            <input type="text" name="step_name" data-step-field="step_name" required class="form-control">
                                            <div class="invalid-feedback">
                                                Nama langkah harus diisi.
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select name="step_category" data-step-field="step_category" required class="form-select">
                                                <option value="">Pilih Kategori</option>
                                                <option value="Pemeriksaan">Pemeriksaan</option>
                                                <option value="Laboratorium">Laboratorium</option>
                                                <option value="Radiologi">Radiologi</option>
                                                <option value="Medikasi">Medikasi</option>
                                                <option value="Prosedur">Prosedur</option>
                                                <option value="Konsultasi">Konsultasi</option>
                                                <option value="Nutrisi">Nutrisi</option>
                                                <option value="Edukasi">Edukasi</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Kategori langkah harus dipilih.
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                            <input type="number" name="step_order" data-step-field="step_order" min="1" required class="form-control">
                                            <div class="invalid-feedback">
                                                Urutan langkah harus diisi.
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Biaya <span class="text-danger">*</span></label>
                                            <input type="number" name="unit_cost" data-step-field="unit_cost" min="0" step="1000" required class="form-control">
                                            <div class="invalid-feedback">
                                                Biaya langkah harus diisi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-muted text-end small">
                                <p>Minimal 1 langkah harus ditambahkan</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" id="submit-btn" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> <span id="submit-text">Simpan Clinical Pathway</span>
                            <span id="loading-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stepsContainer = document.getElementById('stepsContainer');
        const addStepBtn = document.getElementById('addStepBtn');
        const stepTemplate = document.querySelector('.step-template');
        const form = document.getElementById('clinical-pathway-form');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const loadingSpinner = document.getElementById('loading-spinner');
        let stepCount = 0;
        let isSubmitting = false;

        // Add the first step immediately on page load
        addStep();

        // Add step button handler
        addStepBtn.addEventListener('click', function() {
            addStep();
        });

        // Add name attributes to step fields for proper form submission
        function addNameAttributes(stepElem, stepIndex) {
            const inputElements = stepElem.querySelectorAll('[data-step-field]');
            inputElements.forEach(input => {
                const fieldName = input.getAttribute('data-step-field');
                // Using direct field names instead of array notation
                input.setAttribute('name', fieldName);
            });
        }

        // Function to add new step
        function addStep() {
            try {
                // Clone the template
                const newStep = stepTemplate.cloneNode(true);

                // Remove template classes and add step-item class
                newStep.classList.remove('d-none');
                newStep.classList.remove('step-template');
                newStep.classList.add('step-item');
                newStep.style.display = 'block';

                // Add name attributes to fields with proper index
                const stepIndex = document.querySelectorAll('.step-item').length;
                addNameAttributes(newStep, stepIndex);

                // Set default values
                const inputElements = newStep.querySelectorAll('input[data-step-field], select[data-step-field]');
                inputElements.forEach(input => {
                    // Set default order number
                    if (input.getAttribute('data-step-field') === 'step_order') {
                        input.value = stepCount + 1;
                    }
                    // Set default cost
                    if (input.getAttribute('data-step-field') === 'unit_cost') {
                        input.value = '0';
                    }
                });

                // Add delete button handler
                const deleteBtn = newStep.querySelector('.delete-step');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        const stepItems = document.querySelectorAll('.step-item');
                        if (stepItems.length > 1) {
                            newStep.remove();
                            // Renumber all steps after deletion
                            updateAllFormControls();
                        } else {
                            alert('Minimal satu langkah harus ada!');
                        }
                    });
                }

                // Add to container
                stepsContainer.appendChild(newStep);
                stepCount++;
            } catch (error) {
                console.error('Error adding step:', error);
            }
        }

        // Update all form controls to ensure they have proper names
        function updateAllFormControls() {
            const stepItems = document.querySelectorAll('.step-item');
            stepItems.forEach((item, index) => {
                const inputs = item.querySelectorAll('[data-step-field]');
                inputs.forEach(input => {
                    const fieldName = input.getAttribute('data-step-field');
                    input.setAttribute('name', fieldName);
                    
                    // Update order field to match position
                    if (fieldName === 'step_order') {
                        input.value = index + 1;
                    }
                });
            });
        }

        // On form submit: collect steps, serialize, save to hidden input
        form.addEventListener('submit', function(e) {
            // Original submit event
            e.preventDefault();
            
            // Update hidden field to track steps
            document.getElementById('has_steps').value = document.querySelectorAll('.step-item').length > 0 ? "1" : "0";

            // Check steps exist
            if (document.querySelectorAll('.step-item').length === 0) {
                alert('Minimal satu langkah harus ditambahkan!');
                return false;
            }

            try {
                // Collect step data for structured_data field
                const stepItems = document.querySelectorAll('.step-item');
                const stepsArr = [];
                
                stepItems.forEach((item, index) => {
                    const stepObj = {};
                    // Each field
                    const stepName = item.querySelector('[data-step-field="step_name"]');
                    const stepCategory = item.querySelector('[data-step-field="step_category"]');
                    const stepOrder = item.querySelector('[data-step-field="step_order"]');
                    const unitCost = item.querySelector('[data-step-field="unit_cost"]');
                    
                    stepObj.step_name = stepName ? stepName.value : '';
                    stepObj.step_category = stepCategory ? stepCategory.value : '';
                    stepObj.step_order = index + 1; // Use the index directly
                    stepObj.unit_cost = unitCost ? parseInt(unitCost.value, 10) : 0;
                    
                    stepsArr.push(stepObj);
                });
                
                // Set to hidden input as JSON string
                document.getElementById('structured_data').value = JSON.stringify(stepsArr);
                
                // Visual feedback
                submitBtn.disabled = true;
                submitText.textContent = 'Menyimpan...';
                loadingSpinner.classList.remove('d-none');
                
                // Remove form preventDefault and submit the form normally
                setTimeout(() => {
                    form.removeEventListener('submit', arguments.callee);
                    form.submit();
                }, 10);
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Terjadi kesalahan saat menyimpan. Silakan coba lagi.');
                submitBtn.disabled = false;
                submitText.textContent = 'Simpan Clinical Pathway';
                loadingSpinner.classList.add('d-none');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
