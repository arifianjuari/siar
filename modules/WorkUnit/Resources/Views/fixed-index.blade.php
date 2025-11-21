@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-blue-500 text-white p-4 mb-4 rounded-lg" style="background-color: #3b82f6 !important;">
        <h1 class="text-2xl font-bold">TAMPILAN PERBAIKAN UNIT KERJA</h1>
        <p class="text-sm">Halaman ini adalah versi perbaikan dari daftar unit kerja</p>
        @if(isset($debug_message))
            <div class="mt-2 p-2 bg-blue-700 rounded" style="background-color: #1d4ed8 !important;">
                <p>Debug: {{ $debug_message }}</p>
            </div>
        @endif
    </div>

    <div class="mb-4 flex justify-between">
        <h2 class="text-xl font-semibold">Daftar Unit Kerja (Total: {{ count($flattenedWorkUnits) }})</h2>
        <a href="{{ url('/') }}" class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded" style="background-color: #22c55e !important;">
            <i class="fas fa-home mr-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white" style="background-color: #1f2937 !important;">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Kode Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Nama Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Jenis Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Kepala Unit
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($flattenedWorkUnits as $index => $unit)
                <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span style="padding-left: {{ $unit->depth * 20 }}px;">
                            <span class="font-mono bg-gray-100 px-2 py-1 rounded" style="background-color: #f3f4f6 !important;">{{ $unit->unit_code }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">
                        {{ $unit->unit_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($unit->unit_type == 'medical')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" style="background-color: #dcfce7 !important;">
                                <i class="fas fa-hospital mr-1"></i> Medical
                            </span>
                        @elseif($unit->unit_type == 'non-medical')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800" style="background-color: #dbeafe !important;">
                                <i class="fas fa-building mr-1"></i> Non-Medical
                            </span>
                        @elseif($unit->unit_type == 'supporting')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-cogs mr-1"></i> Supporting
                            </span>
                        @else
                            {{ ucfirst($unit->unit_type) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-700">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $unit->headOfUnit->name ?? 'Tidak ada' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('work-units.fix.dashboard', $unit->id) }}" class="bg-green-500 hover:bg-green-700 text-white py-2 px-3 rounded-lg inline-flex items-center">
                                <i class="fas fa-chart-bar mr-1"></i> Dashboard
                            </a>
                            <a href="#" class="bg-indigo-500 hover:bg-indigo-700 text-white py-2 px-3 rounded-lg inline-flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white py-2 px-3 rounded-lg inline-flex items-center">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-5xl mb-4 text-gray-300"></i>
                                <p class="text-lg">Tidak ada data unit kerja.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 