@extends('layouts.app')

@section('title', 'Kelola Indikator Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Kelola Indikator Clinical Pathway</h2>
                    <a href="{{ route('kendali-mutu-biaya.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                <div class="bg-blue-50 p-4 mb-6 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-800">{{ $clinicalPathway->name }}</h3>
                    <p class="text-sm text-blue-600">Kategori: {{ $clinicalPathway->category }}</p>
                </div>

                @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
                @endif

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

                <!-- Form Tambah Indikator -->
                <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Indikator Baru</h3>
                    <form action="{{ route('kendali-mutu-biaya.store-indicator', $clinicalPathway->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Indikator <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Jenis Indikator <span class="text-red-500">*</span></label>
                                <select name="type" id="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Proses" {{ old('type') == 'Proses' ? 'selected' : '' }}>Proses</option>
                                    <option value="Output" {{ old('type') == 'Output' ? 'selected' : '' }}>Output</option>
                                    <option value="Outcome" {{ old('type') == 'Outcome' ? 'selected' : '' }}>Outcome</option>
                                    <option value="Efisiensi" {{ old('type') == 'Efisiensi' ? 'selected' : '' }}>Efisiensi</option>
                                </select>
                            </div>

                            <div>
                                <label for="target" class="block text-sm font-medium text-gray-700">Target (%) <span class="text-red-500">*</span></label>
                                <input type="number" name="target" id="target" value="{{ old('target') }}" min="0" max="100" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="unit_responsible" class="block text-sm font-medium text-gray-700">Unit Penanggung Jawab <span class="text-red-500">*</span></label>
                                <select name="unit_responsible" id="unit_responsible" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Unit --</option>
                                    <option value="KMKP" {{ old('unit_responsible') == 'KMKP' ? 'selected' : '' }}>KMKP</option>
                                    <option value="Perawatan" {{ old('unit_responsible') == 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
                                    <option value="Dokter" {{ old('unit_responsible') == 'Dokter' ? 'selected' : '' }}>Dokter</option>
                                    <option value="Farmasi" {{ old('unit_responsible') == 'Farmasi' ? 'selected' : '' }}>Farmasi</option>
                                    <option value="Laboratorium" {{ old('unit_responsible') == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="formula" class="block text-sm font-medium text-gray-700">Formula Pengukuran</label>
                            <textarea name="formula" id="formula" rows="2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('formula') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Tambah Indikator
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Daftar Indikator -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Indikator</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Indikator
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Target
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit Penanggung Jawab
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($indicators as $indicator)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $indicator->name }}</div>
                                        @if($indicator->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ $indicator->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $indicator->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $indicator->target }}%</div>
                                        @if($indicator->formula)
                                            <div class="text-xs text-gray-500 mt-1">{{ $indicator->formula }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $indicator->unit_responsible }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="#" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus indikator ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Belum ada indikator yang ditambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 