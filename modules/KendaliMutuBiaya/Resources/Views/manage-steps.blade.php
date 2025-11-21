@extends('layouts.app')

@section('title', 'Kelola Langkah Clinical Pathway')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Kelola Langkah Clinical Pathway</h2>
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

                <!-- Form Tambah Langkah -->
                <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Langkah Baru</h3>
                    <form action="{{ route('kendali-mutu-biaya.store-step', $clinicalPathway->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Langkah <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <select name="category" id="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Diagnosis" {{ old('category') == 'Diagnosis' ? 'selected' : '' }}>Diagnosis</option>
                                    <option value="Terapi" {{ old('category') == 'Terapi' ? 'selected' : '' }}>Terapi</option>
                                    <option value="Pemeriksaan" {{ old('category') == 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                                    <option value="Perawatan" {{ old('category') == 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
                                    <option value="Konsultasi" {{ old('category') == 'Konsultasi' ? 'selected' : '' }}>Konsultasi</option>
                                </select>
                            </div>

                            <div>
                                <label for="day" class="block text-sm font-medium text-gray-700">Hari Ke <span class="text-red-500">*</span></label>
                                <input type="number" name="day" id="day" value="{{ old('day') }}" min="1" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700">Unit Terkait <span class="text-red-500">*</span></label>
                                <select name="unit" id="unit" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Unit --</option>
                                    <option value="Rawat Inap" {{ old('unit') == 'Rawat Inap' ? 'selected' : '' }}>Rawat Inap</option>
                                    <option value="Laboratorium" {{ old('unit') == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                    <option value="Radiologi" {{ old('unit') == 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
                                    <option value="Farmasi" {{ old('unit') == 'Farmasi' ? 'selected' : '' }}>Farmasi</option>
                                    <option value="Fisioterapi" {{ old('unit') == 'Fisioterapi' ? 'selected' : '' }}>Fisioterapi</option>
                                    <option value="Konsultan" {{ old('unit') == 'Konsultan' ? 'selected' : '' }}>Konsultan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Tambah Langkah
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Daftar Langkah -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Langkah</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hari Ke
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Langkah
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($steps as $step)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Hari {{ $step->day }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $step->name }}</div>
                                        @if($step->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ $step->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $step->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $step->unit }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="#" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus langkah ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Belum ada langkah yang ditambahkan.
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