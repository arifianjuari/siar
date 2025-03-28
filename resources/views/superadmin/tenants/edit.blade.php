@extends('superadmin.layout')

@section('title', 'Edit Tenant')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Tenant: {{ $tenant->name }}</h2>
                    <div>
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-eye me-2"></i> Lihat Detail
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('superadmin.tenants.update', $tenant) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Informasi Tenant</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Tenant <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain', $tenant->domain) }}" required>
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Domain harus unik dan digunakan untuk akses tenant (contoh: company1)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="database" class="form-label">Database <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('database') is-invalid @enderror" id="database" name="database" value="{{ old('database', $tenant->database) }}" required>
                                    @error('database')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nama database untuk tenant ini (contoh: tenant1_db)</div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Tenant Aktif</label>
                                    <div class="form-text">Tenant harus aktif agar pengguna dapat mengakses</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Modul</h4>
                                <p class="text-muted mb-3">Pilih modul yang akan diaktifkan untuk tenant ini:</p>
                                
                                <div class="row">
                                    @foreach($modules as $module)
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    name="modules[]" 
                                                    value="{{ $module->id }}" 
                                                    id="module-{{ $module->id }}"
                                                    {{ (is_array(old('modules')) && in_array($module->id, old('modules'))) || 
                                                        (old('modules') === null && in_array($module->id, $activeModuleIds)) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="module-{{ $module->id }}">
                                                    <i class="fas {{ $module->icon ?? 'fa-cube' }} me-2"></i>
                                                    {{ $module->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('modules')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">Info</h5>
                                        <p class="mb-0">Perubahan status modul akan mempengaruhi akses pengguna tenant ini. Pastikan untuk mengomunikasikan perubahan kepada admin tenant.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 