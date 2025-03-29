@extends('layouts.app')

@section('title', ' | Konfigurasi Akses Analisis Risiko')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Konfigurasi Akses Analisis Risiko</h2>
            <p class="text-muted">Atur peran mana yang dapat mengakses fitur analisis risiko</p>
        </div>
        <div>
            <a href="{{ route('modules.risk-management.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Konfigurasi Akses Fitur Analisis Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Tentang Konfigurasi Akses</h5>
                                <p class="mb-0">Pada halaman ini Anda dapat mengatur peran mana saja yang memiliki akses ke fitur analisis risiko. Pengguna dengan peran yang tidak dipilih tidak akan dapat membuat, melihat, atau mengedit analisis risiko.</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('modules.risk-management.save-analysis-config') }}">
                        @csrf
                        <input type="hidden" name="module" value="risk_management">
                        <input type="hidden" name="feature" value="risk_analysis">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Role yang Dapat Mengakses Fitur Analisis:</label>
                            
                            @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="allowed_roles[]" value="{{ $role->id }}" 
                                    class="form-check-input" id="role_{{ $role->id }}"
                                    {{ in_array($role->id, $currentConfig->allowed_roles ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                    @if($role->slug == 'tenant-admin')
                                    <span class="badge bg-info ms-1">Admin</span>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">Pilih Semua / Batalkan Semua</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Role Default</h6>
                    <p>Jika tidak ada konfigurasi khusus, role berikut akan memiliki akses ke fitur analisis risiko:</p>
                    <ul class="mb-4">
                        <li>Tenant Admin</li>
                        <li>Risk Manager</li>
                        <li>Quality Manager</li>
                    </ul>
                    
                    <h6 class="fw-bold">Catatan Penting</h6>
                    <ul class="mb-0">
                        <li>Role Tenant Admin akan selalu memiliki akses meskipun tidak dipilih</li>
                        <li>Perubahan akses akan langsung berlaku setelah disimpan</li>
                        <li>Pengguna yang sedang melihat analisis risiko mungkin perlu me-refresh halaman</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const roleCheckboxes = document.querySelectorAll('input[name="allowed_roles[]"]');
        
        // Fungsi untuk periksa apakah semua checkbox dipilih
        function updateSelectAllCheckbox() {
            let allChecked = true;
            roleCheckboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });
            selectAllCheckbox.checked = allChecked;
        }
        
        // Event listener untuk "Pilih Semua" checkbox
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            roleCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
        
        // Event listeners untuk role checkboxes
        roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Inisialisasi status "Pilih Semua" checkbox
        updateSelectAllCheckbox();
    });
</script>
@endpush 