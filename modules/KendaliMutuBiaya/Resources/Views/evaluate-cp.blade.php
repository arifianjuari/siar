@extends('layouts.app')

@section('title', 'Evaluasi Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Evaluasi Clinical Pathway</h2>
                    <a href="{{ route('kendali-mutu-biaya.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                <div class="bg-blue-50 p-4 mb-6 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-800">{{ $clinicalPathway->name }}</h3>
                    <p class="text-sm text-blue-600">Kategori: {{ $clinicalPathway->category }}</p>
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

                <form action="{{ route('kendali-mutu-biaya.store-evaluation', $clinicalPathway->id) }}" method="POST">
                    @csrf
                    
                    <!-- Basic Evaluation Info -->
                    <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Evaluasi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="evaluation_date" class="block text-sm font-medium text-gray-700">Tanggal Evaluasi <span class="text-red-500">*</span></label>
                                <input type="date" name="evaluation_date" id="evaluation_date" value="{{ old('evaluation_date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Steps Checklist -->
                    <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Checklist Langkah-langkah</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Langkah
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($clinicalPathway->steps as $index => $step)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $step->step_name }}</div>
                                            @if($step->description)
                                                <div class="text-xs text-gray-500 mt-1">{{ $step->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $step->step_category }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="step_status[{{ $index }}]" value="1" 
                                                    {{ old('step_status.'.$index) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            </label>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Additional Steps -->
                    <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Langkah Tambahan</h3>
                            <button type="button" id="addStepBtn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Tambah Langkah
                            </button>
                        </div>
                        
                        <div id="additionalStepsContainer" class="space-y-4">
                            <!-- Template for additional step -->
                            <div class="additional-step-template hidden border border-gray-300 p-4 rounded-md relative">
                                <button type="button" class="delete-step absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Langkah <span class="text-red-500">*</span></label>
                                        <input type="text" name="additional_steps[INDEX][additional_step_name]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Biaya <span class="text-red-500">*</span></label>
                                        <input type="number" name="additional_steps[INDEX][additional_step_cost]" min="0" step="1000" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status Justifikasi <span class="text-red-500">*</span></label>
                                        <select name="additional_steps[INDEX][justification_status]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Justified">Justified</option>
                                            <option value="Tidak Justified">Tidak Justified</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 text-center text-sm text-gray-500">
                            Tambahkan langkah di luar Clinical Pathway yang telah dilakukan
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-1"></i> Simpan Evaluasi
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
        const additionalStepsContainer = document.getElementById('additionalStepsContainer');
        const addStepBtn = document.getElementById('addStepBtn');
        const stepTemplate = document.querySelector('.additional-step-template');
        let stepCount = 0;

        // Add step button click handler
        addStepBtn.addEventListener('click', function() {
            addStep();
        });

        // Function to add new additional step
        function addStep() {
            const newStep = stepTemplate.cloneNode(true);
            newStep.classList.remove('hidden', 'additional-step-template');
            newStep.classList.add('additional-step-item');
            
            // Replace INDEX placeholder with actual index
            const inputElements = newStep.querySelectorAll('input, select, textarea');
            inputElements.forEach(input => {
                input.name = input.name.replace('INDEX', stepCount);
            });

            // Add delete button handler
            const deleteBtn = newStep.querySelector('.delete-step');
            deleteBtn.addEventListener('click', function() {
                newStep.remove();
                reorderSteps();
            });

            additionalStepsContainer.appendChild(newStep);
            stepCount++;
        }

        // Function to reorder steps when one is deleted
        function reorderSteps() {
            const stepItems = document.querySelectorAll('.additional-step-item');
            let newIndex = 0;
            
            stepItems.forEach(item => {
                const inputs = item.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    const nameParts = input.name.split('[');
                    const endPart = nameParts[2];
                    input.name = `additional_steps[${newIndex}][${endPart}`;
                });
                
                newIndex++;
            });
        }
    });
</script>
@endpush
@endsection 