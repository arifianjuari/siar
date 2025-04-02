@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Daftar Unit Kerja</h1>
        @can('create work-units')
            <a href="{{ route('work-units.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Unit Kerja
            </a>
        @endcan
    </div>

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kepala Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- Data akan diisi dari controller --}}
                {{-- Contoh baris: --}}
                {{-- @foreach ($workUnits as $unit)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->unit_code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->unit_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($unit->unit_type) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $unit->headOfUnit->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @can('edit work-units')
                            <a href="{{ route('work-units.edit', $unit->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        @endcan
                        @can('delete work-units')
                            <form action="{{ route('work-units.destroy', $unit->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @endcan
                    </td>
                </tr>
                @endforeach --}}
                {{-- Jika tidak ada data --}}
                {{-- @if ($workUnits->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada data unit kerja.</td>
                    </tr>
                @endif --}}
            </tbody>
        </table>
    </div>

    {{-- Pagination links --}}
    {{-- {{ $workUnits->links() }} --}}
</div>
@endsection 