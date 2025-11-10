@extends('layouts.app')

@section('title', 'Edit Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Clinical Pathway</h2>
                    <a href="{{ route('kendali-mutu-biaya.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <p class="font-bold">Terjadi kesalahan:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('kendali-mutu-biaya.update', $clinicalPathway->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Info Card -->
                    <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Clinical Pathway <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $clinicalPathway->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <select name="category" id="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Medis" {{ old('category', $clinicalPathway->category) == 'Medis' ? 'selected' : '' }}>Medis</option>
                                    <option value="Bedah" {{ old('category', $clinicalPathway->category) == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                                    <option value="Obstetri" {{ old('category', $clinicalPathway->category) == 'Obstetri' ? 'selected' : '' }}>Obstetri</option>
                                    <option value="Anak" {{ old('category', $clinicalPathway->category) == 'Anak' ? 'selected' : '' }}>Anak</option>
                                    <option value="Lainnya" {{ old('category', $clinicalPathway->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $clinicalPathway->start_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="is_active" class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $clinicalPathway->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $clinicalPathway->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Card -->
                    <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Langkah-langkah Clinical Pathway</h3>
                            <button type="button" id="addStepBtn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Tambah Langkah
                            </button>
                        </div>

                        <div id="stepsContainer" class="space-y-4">
                            <!-- Template for a step -->
                            <div class="step-template hidden border border-gray-300 p-4 rounded-md relative">
                                <button type="button" class="delete-step absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <input type="hidden" name="steps[INDEX][id]" value="">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Langkah <span class="text-red-500">*</span></label>
                                        <input type="text" name="steps[INDEX][step_name]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                        <select name="steps[INDEX][step_category]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Urutan <span class="text-red-500">*</span></label>
                                        <input type="number" name="steps[INDEX][step_order]" min="1" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
                                        <input type="number" name="steps[INDEX][unit_cost]" min="0" step="1000" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Steps -->
                            @foreach($clinicalPathway->steps as $index => $step)
                            <div class="step-item border border-gray-300 p-4 rounded-md relative">
                                <button type="button" class="delete-step absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <input type="hidden" name="steps[{{ $index }}][id]" value="{{ $step->id }}">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Langkah <span class="text-red-500">*</span></label>
                                        <input type="text" name="steps[{{ $index }}][step_name]" value="{{ old('steps.'.$index.'.step_name', $step->step_name) }}" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                        <select name="steps[{{ $index }}][step_category]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Pilih Kategori</option>
                                            <option value="Pemeriksaan" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                                            <option value="Laboratorium" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                            <option value="Radiologi" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
                                            <option value="Medikasi" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Medikasi' ? 'selected' : '' }}>Medikasi</option>
                                            <option value="Prosedur" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Prosedur' ? 'selected' : '' }}>Prosedur</option>
                                            <option value="Konsultasi" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Konsultasi' ? 'selected' : '' }}>Konsultasi</option>
                                            <option value="Nutrisi" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Nutrisi' ? 'selected' : '' }}>Nutrisi</option>
                                            <option value="Edukasi" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Edukasi' ? 'selected' : '' }}>Edukasi</option>
                                            <option value="Lainnya" {{ old('steps.'.$index.'.step_category', $step->step_category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Urutan <span class="text-red-500">*</span></label>
                                        <input type="number" name="steps[{{ $index }}][step_order]" value="{{ old('steps.'.$index.'.step_order', $step->step_order) }}" min="1" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
                                        <input type="number" name="steps[{{ $index }}][unit_cost]" value="{{ old('steps.'.$index.'.unit_cost', $step->unit_cost) }}" min="0" step="1000" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-2 text-right">
                            <p class="text-sm text-gray-500">Minimal 1 langkah harus ditambahkan</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-1"></i> Perbarui Clinical Pathway
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
        let stepCount = {{ count($clinicalPathway->steps) }};

        // Add step button click handler
        addStepBtn.addEventListener('click', function() {
            addStep();
        });

        // Add delete button handlers to existing steps
        document.querySelectorAll('.step-item .delete-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.step-item').length > 1) {
                    btn.closest('.step-item').remove();
                    reorderSteps();
                } else {
                    alert('Minimal satu langkah harus ada!');
                }
            });
        });

        // Function to add new step
        function addStep() {
            const newStep = stepTemplate.cloneNode(true);
            newStep.classList.remove('hidden', 'step-template');
            newStep.classList.add('step-item');
            
            // Replace INDEX placeholder with actual index
            const inputElements = newStep.querySelectorAll('input, select, textarea');
            inputElements.forEach(input => {
                input.name = input.name.replace('INDEX', stepCount);
                
                // Set default order number
                if (input.name.includes('step_order')) {
                    input.value = stepCount + 1;
                }
            });

            // Add delete button handler
            const deleteBtn = newStep.querySelector('.delete-step');
            deleteBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.step-item').length > 1) {
                    newStep.remove();
                    reorderSteps();
                } else {
                    alert('Minimal satu langkah harus ada!');
                }
            });

            stepsContainer.appendChild(newStep);
            stepCount++;
        }

        // Function to reorder steps when one is deleted
        function reorderSteps() {
            const stepItems = document.querySelectorAll('.step-item');
            let newIndex = 0;
            
            stepItems.forEach(item => {
                const inputs = item.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    // Skip the hidden id field
                    if (!input.name.includes('[id]')) {
                        const nameParts = input.name.split('[');
                        const suffix = nameParts[1].split(']')[1];
                        input.name = `steps[${newIndex}]${suffix}`;
                    }
                });
                
                newIndex++;
            });
        }
    });
</script>
@endpush
@endsection 