@extends('layouts.app')

@section('title', 'Detail Evaluasi Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Detail Evaluasi Clinical Pathway</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('kendali-mutu-biaya.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <a href="#" onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <!-- Clinical Pathway Info -->
                <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-blue-800">{{ $evaluation->clinicalPathway->name }}</h3>
                            <p class="text-sm text-blue-600">Kategori: {{ $evaluation->clinicalPathway->category }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-blue-600">Tanggal Evaluasi: {{ $evaluation->evaluation_date->format('d-m-Y') }}</p>
                            <p class="text-sm text-blue-600">Evaluator: {{ $evaluation->evaluator->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-md font-medium text-blue-800">Hasil Evaluasi</h4>
                            @php
                                $statusClass = 'bg-red-100 text-red-800';
                                if ($evaluation->evaluation_status == 'Hijau') {
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($evaluation->evaluation_status == 'Kuning') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                }
                            @endphp
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $evaluation->evaluation_status }}
                            </span>
                            <p class="text-sm text-blue-600 mt-1">Kepatuhan: {{ number_format($evaluation->compliance_percentage, 1) }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Steps -->
                <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Evaluasi Langkah-langkah</h3>
                    
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Biaya
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($evaluation->evaluationSteps as $evalStep)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $evalStep->step->step_name }}</div>
                                        @if($evalStep->step->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ $evalStep->step->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $evalStep->step->step_category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp {{ number_format($evalStep->step->unit_cost, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($evalStep->is_done)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i> Dilakukan
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i> Tidak Dilakukan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Additional Steps -->
                @if(count($evaluation->additionalSteps) > 0)
                <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Langkah Tambahan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Langkah
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Biaya
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Justifikasi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($evaluation->additionalSteps as $additionalStep)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $additionalStep->additional_step_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp {{ number_format($additionalStep->additional_step_cost, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($additionalStep->justification_status == 'Justified')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $additionalStep->justification_status }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ $additionalStep->justification_status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-6 py-4 text-right font-bold" colspan="1">Total Biaya Tambahan:</td>
                                    <td class="px-6 py-4 font-bold" colspan="2">Rp {{ number_format($evaluation->total_additional_cost, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Summary -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Evaluasi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Persentase Kepatuhan</h4>
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($evaluation->compliance_percentage, 1) }}%</div>
                                    <div class="ml-4">
                                        @php
                                            $complianceColor = 'bg-red-500';
                                            if ($evaluation->compliance_percentage >= 90) {
                                                $complianceColor = 'bg-green-500';
                                            } elseif ($evaluation->compliance_percentage >= 70) {
                                                $complianceColor = 'bg-yellow-500';
                                            }
                                        @endphp
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="{{ $complianceColor }} h-2.5 rounded-full" style="width: {{ $evaluation->compliance_percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Status Evaluasi</h4>
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full {{ $statusClass }} flex items-center justify-center mr-3">
                                        @if($evaluation->evaluation_status == 'Hijau')
                                            <i class="fas fa-check text-green-800"></i>
                                        @elseif($evaluation->evaluation_status == 'Kuning')
                                            <i class="fas fa-exclamation text-yellow-800"></i>
                                        @else
                                            <i class="fas fa-times text-red-800"></i>
                                        @endif
                                    </div>
                                    <div class="text-xl font-bold text-gray-900">{{ $evaluation->evaluation_status }}</div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    @if($evaluation->evaluation_status == 'Hijau')
                                        Tingkat kepatuhan sangat baik (≥ 90%)
                                    @elseif($evaluation->evaluation_status == 'Kuning')
                                        Tingkat kepatuhan cukup baik (70% - 89%)
                                    @else
                                        Tingkat kepatuhan perlu ditingkatkan (< 70%)
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Total Biaya Tambahan</h4>
                        <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($evaluation->total_additional_cost, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body {
        background-color: white;
        font-size: 12pt;
    }
    
    nav, button, .flex.justify-between, .bg-gray-50.rounded-lg {
        display: none !important;
    }
    
    .shadow-sm, .shadow-md {
        box-shadow: none !important;
    }
    
    .rounded-lg, .rounded {
        border-radius: 0 !important;
    }
    
    .border-gray-200 {
        border-color: #eee !important;
    }
    
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    thead {
        display: table-header-group;
    }
    
    tfoot {
        display: table-footer-group;
    }
}
</style>
@endsection 