@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-700 mb-6">{{ isset($workUnit) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja' }}</h1>

    <form action="{{ isset($workUnit) ? route('work-units.update', $workUnit->id) : route('work-units.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @if(isset($workUnit))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit_code">
                Kode Unit
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unit_code') border-red-500 @enderror" id="unit_code" name="unit_code" type="text" placeholder="Kode Unit" value="{{ old('unit_code', $workUnit->unit_code ?? '') }}" required>
            @error('unit_code')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit_name">
                Nama Unit
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unit_name') border-red-500 @enderror" id="unit_name" name="unit_name" type="text" placeholder="Nama Unit" value="{{ old('unit_name', $workUnit->unit_name ?? '') }}" required>
            @error('unit_name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit_type">
                Jenis Unit
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unit_type') border-red-500 @enderror" id="unit_type" name="unit_type" required>
                <option value="">Pilih Jenis Unit</option>
                <option value="medical" {{ old('unit_type', $workUnit->unit_type ?? '') == 'medical' ? 'selected' : '' }}>Medis</option>
                <option value="non-medical" {{ old('unit_type', $workUnit->unit_type ?? '') == 'non-medical' ? 'selected' : '' }}>Non-Medis</option>
                <option value="supporting" {{ old('unit_type', $workUnit->unit_type ?? '') == 'supporting' ? 'selected' : '' }}>Penunjang</option>
            </select>
            @error('unit_type')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="head_of_unit_id">
                Kepala Unit
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('head_of_unit_id') border-red-500 @enderror" id="head_of_unit_id" name="head_of_unit_id" required>
                <option value="">Pilih Kepala Unit</option>
                {{-- Data user akan diisi dari controller --}}
                {{-- Contoh loop:
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('head_of_unit_id', $workUnit->head_of_unit_id ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
                --}}
            </select>
            @error('head_of_unit_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Deskripsi
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror" id="description" name="description" placeholder="Deskripsi (opsional)">{{ old('description', $workUnit->description ?? '') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                {{ isset($workUnit) ? 'Update Unit Kerja' : 'Simpan Unit Kerja' }}
            </button>
            <a href="{{ route('work-units.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection 