@extends('layouts.app')

@section('title', 'Edit Modul')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="mb-0">Edit Modul: {{ $module->name }}</h2>
    <a href="{{ route('superadmin.modules.show', $module) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('superadmin.modules.update', $module) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Modul</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $module->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" value="{{ $module->slug }}" disabled readonly>
                            <div class="form-text text-warning">Slug tidak dapat diubah untuk menjaga konsistensi routing.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $module->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="icon" class="form-label">Ikon (Font Awesome)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas {{ $module->icon ?? 'fa-cube' }}"></i></span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $module->icon) }}" placeholder="fa-cube">
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Masukkan kode ikon Font Awesome. Misalnya: 'fa-users', 'fa-cog', dll.
                                <a href="https://fontawesome.com/icons" target="_blank">Lihat daftar ikon</a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 