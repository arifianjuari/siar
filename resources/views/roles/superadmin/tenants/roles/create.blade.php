@extends('roles.superadmin.layout')

@section('title', 'Tambah Role Baru')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Tambah Role Baru untuk {{ $tenant->name }}</h2>
                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Tambah Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.tenants.roles.store', $tenant) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            <div class="form-text">Masukkan nama role sesuai dengan kebutuhan tenant (contoh: Direktur, Manager, dll)</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role_slug" class="form-label">Tipe Role <span class="text-danger">*</span></label>
                            @php $hasTenantAdmin = $tenant->roles()->where('slug', 'tenant-admin')->exists(); @endphp
                            <select class="form-select @error('role_slug') is-invalid @enderror" id="role_slug" name="role_slug" required>
                                <option value="">Pilih Tipe Role</option>
                                <option value="tenant-admin" {{ old('role_slug') == 'tenant-admin' ? 'selected' : '' }} {{ $hasTenantAdmin ? 'disabled' : '' }}>Tenant Admin {{ $hasTenantAdmin ? '(sudah ada)' : '' }}</option>
                                <option value="manajemen-strategis" {{ old('role_slug') == 'manajemen-strategis' ? 'selected' : '' }}>Manajemen Strategis</option>
                                <option value="manajemen-eksekutif" {{ old('role_slug') == 'manajemen-eksekutif' ? 'selected' : '' }}>Manajemen Eksekutif</option>
                                <option value="manajemen-operasional" {{ old('role_slug') == 'manajemen-operasional' ? 'selected' : '' }}>Manajemen Operasional</option>
                                <option value="staf" {{ old('role_slug') == 'staf' ? 'selected' : '' }}>Staf</option>
                            </select>
                            <div class="form-text">Pilih tipe role yang menentukan hak akses pengguna.</div>
                            @error('role_slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <div class="form-text">Jelaskan fungsi dan tanggung jawab role ini.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Role Aktif
                                </label>
                                <div class="form-text">Role yang tidak aktif tidak dapat dipilih saat menambahkan pengguna baru.</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 