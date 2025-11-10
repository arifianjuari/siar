@extends('layouts.app')

@section('title', 'Rekap Evaluasi Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Rekap Evaluasi Clinical Pathway</h2>
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

                <!-- Filter Section -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form action="{{ route('kendali-mutu-biaya.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="Hijau" {{ $status == 'Hijau' ? 'selected' : '' }}>Hijau</option>
                                <option value="Kuning" {{ $status == 'Kuning' ? 'selected' : '' }}>Kuning</option>
                                <option value="Merah" {{ $status == 'Merah' ? 'selected' : '' }}>Merah</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="clinical_pathway_id" class="block text-sm font-medium text-gray-700">Clinical Pathway</label>
                            <select name="clinical_pathway_id" id="clinical_pathway_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua CP</option>
                                @foreach($clinicalPathways as $cp)
                                <option value="{{ $cp->id }}" {{ $cpId == $cp->id ? 'selected' : '' }}>{{ $cp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="lg:col-span-4 flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Green Status Summary -->
                    <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-green-800">Hijau</h3>
                                <p class="text-sm text-green-600">
                                    {{ $evaluations->where('evaluation_status', 'Hijau')->count() }} Evaluasi
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Yellow Status Summary -->
                    <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-exclamation text-yellow-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-yellow-800">Kuning</h3>
                                <p class="text-sm text-yellow-600">
                                    {{ $evaluations->where('evaluation_status', 'Kuning')->count() }} Evaluasi
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Red Status Summary -->
                    <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-times text-red-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-red-800">Merah</h3>
                                <p class="text-sm text-red-600">
                                    {{ $evaluations->where('evaluation_status', 'Merah')->count() }} Evaluasi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluations Table -->
                <div class="bg-white overflow-x-auto shadow-md rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Clinical Pathway
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Evaluator
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kepatuhan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Biaya Tambahan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($evaluations as $evaluation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $evaluation->evaluation_date->format('d-m-Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $evaluation->clinicalPathway->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $evaluation->clinicalPathway->category }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $evaluation->evaluator->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($evaluation->compliance_percentage, 1) }}%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-red-100 text-red-800';
                                        if ($evaluation->evaluation_status == 'Hijau') {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif ($evaluation->evaluation_status == 'Kuning') {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $evaluation->evaluation_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Rp {{ number_format($evaluation->total_additional_cost, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('kendali-mutu-biaya.show-evaluation', $evaluation->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Tidak ada data evaluasi yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $evaluations->links() }}
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
    
    .mb-6.bg-gray-50.p-4.rounded-lg, nav, button, .flex.justify-between {
        display: none !important;
    }
    
    .shadow-sm, .shadow-md {
        box-shadow: none !important;
    }
    
    .rounded-lg, .rounded {
        border-radius: 0 !important;
    }
    
    .mt-4 {
        display: none !important;
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