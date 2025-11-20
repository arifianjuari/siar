@extends('roles.superadmin.layout')

@php $hideDefaultHeader = true; @endphp

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Tambah Tenant Baru</h2>
                    <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
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
                    <form id="tenant-form" action="{{ route('superadmin.tenants.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Informasi Tenant</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Tenant <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain') }}" required>
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Domain harus unik dan digunakan untuk akses tenant (contoh: company1)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="database" class="form-label">Database <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('database') is-invalid @enderror" id="database" name="database" value="{{ old('database') }}" required>
                                    @error('database')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nama database untuk tenant ini (contoh: tenant1_db)</div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
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
                                                    {{ (is_array(old('modules')) && in_array($module->id, old('modules'))) ? 'checked' : '' }}
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

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2 mb-3">Admin Tenant</h4>
                                <p class="text-muted mb-3">Buat akun administrator untuk tenant ini:</p>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Nama Admin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('admin_name') is-invalid @enderror" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                                    @error('admin_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Email Admin <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Password Admin <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" required>
                                    @error('admin_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Password minimal 8 karakter</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">Perhatian!</h5>
                                        <p class="mb-0">Dengan membuat tenant baru, Anda akan membuat database dan struktur data terpisah. Pastikan informasi yang dimasukkan sudah benar.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Buat Tenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let isSubmitting = false;

document.getElementById('tenant-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Prevent double submission
    if (isSubmitting) {
        return false;
    }
    
    isSubmitting = true;
    
    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (response) => {
        const contentType = response.headers.get('content-type') || '';
        let data = null;

        if (contentType.includes('application/json')) {
            try { data = await response.json(); } catch (_) { /* ignore parse error */ }
        }

        if (response.ok) {
            if (data && data.success) {
                alert('Tenant berhasil dibuat!');
                window.location.href = '{{ route("superadmin.tenants.index") }}';
                return;
            }

            // Jika server mengembalikan HTML (redirect/halaman), anggap sukses
            if (contentType.includes('text/html')) {
                window.location.href = '{{ route("superadmin.tenants.index") }}';
                return;
            }

            alert('Terjadi kesalahan: ' + ((data && data.message) ? data.message : 'Respon tidak valid'));
            // Re-enable button on error
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            return;
        }

        // Tidak OK (4xx/5xx)
        let message = (data && data.message) ? data.message : '';
        if (!message) {
            try { message = await response.text(); } catch (_) { message = 'Unknown error'; }
        }
        alert('Terjadi kesalahan: ' + message);
        
        // Re-enable button on error
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + (error && error.message ? error.message : error));
        
        // Re-enable button on error
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});
</script>
@endpush 