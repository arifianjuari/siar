@extends('layouts.app')

@section('title', ' | Profil Tenant')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Profil Tenant</h2>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Tenant</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($tenant->logo)
                        @php
                            // Extract filename from tenant logo path
                            $logoFilename = basename($tenant->logo);
                            // Try public/images path first (for Laravel Cloud compatibility), fallback to storage
                            $publicImagePath = 'images/' . $logoFilename;
                            $storageImagePath = 'storage/' . $tenant->logo;
                        @endphp
                        <img src="{{ asset($publicImagePath) }}?v={{ $tenant->updated_at->timestamp }}" 
                             alt="{{ $tenant->name }}" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 150px; object-fit: contain;" 
                             onerror="this.onerror=null; this.src='{{ asset($storageImagePath) }}?v=' + new Date().getTime();">
                        <script>
                            // Pre-load logo untuk mencegah masalah loading pertama kali
                            (function() {
                                var publicPath = '{{ asset($publicImagePath) }}?force_refresh=1&t={{ time() }}';
                                var storagePath = '{{ asset($storageImagePath) }}?force_refresh=1&t={{ time() }}';
                                
                                // Try public path first
                                fetch(publicPath, {
                                    method: 'GET',
                                    cache: 'no-store'
                                }).then(function(response) {
                                    if (response.ok) {
                                        console.log('Logo pre-fetched successfully from public path');
                                        var img = document.querySelector('img[alt="{{ $tenant->name }}"]');
                                        if (img) {
                                            img.src = '{{ asset($publicImagePath) }}?v=' + new Date().getTime();
                                        }
                                    } else {
                                        throw new Error('Public path failed');
                                    }
                                }).catch(function(err) {
                                    console.log('Trying storage path as fallback');
                                    // Fallback to storage path
                                    fetch(storagePath, {
                                        method: 'GET',
                                        cache: 'no-store'
                                    }).then(function() {
                                        console.log('Logo pre-fetched successfully from storage path');
                                        var img = document.querySelector('img[alt="{{ $tenant->name }}"]');
                                        if (img) {
                                            img.src = '{{ asset($storageImagePath) }}?v=' + new Date().getTime();
                                        }
                                    }).catch(function(err) {
                                        console.error('Error pre-fetching logo from both paths:', err);
                                    });
                                });
                            })();
                        </script>
                    @endif
                    <h4>{{ $tenant->name }}</h4>
                    @if($tenant->description)
                        <p class="text-muted">{{ $tenant->description }}</p>
                    @endif
                </div>
                
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row" style="width: 120px;">Alamat</th>
                            <td>{{ $tenant->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Kota</th>
                            <td>{{ $tenant->city ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pimpinan RS</th>
                            <td>{{ $tenant->ceo ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pangkat</th>
                            <td>{{ $tenant->ceo_rank ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">NRP/NIK</th>
                            <td>{{ $tenant->ceo_nrp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $tenant->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Telepon</th>
                            <td>{{ $tenant->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Dibuat Pada</th>
                            <td>{{ $tenant->created_at->format('d M Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Profil Tenant</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Tenant <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $tenant->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $tenant->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $tenant->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ceo" class="form-label">Pimpinan RS</label>
                        <input type="text" class="form-control @error('ceo') is-invalid @enderror" id="ceo" name="ceo" value="{{ old('ceo', $tenant->ceo) }}">
                        <div class="form-text">Nama lengkap Direktur / Pimpinan utama rumah sakit.</div>
                        @error('ceo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ceo_rank" class="form-label">Pangkat</label>
                                <input type="text" class="form-control @error('ceo_rank') is-invalid @enderror" id="ceo_rank" name="ceo_rank" value="{{ old('ceo_rank', $tenant->ceo_rank) }}">
                                <div class="form-text">Pangkat Direktur / Pimpinan RS.</div>
                                @error('ceo_rank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ceo_nrp" class="form-label">NRP/NIK</label>
                                <input type="text" class="form-control @error('ceo_nrp') is-invalid @enderror" id="ceo_nrp" name="ceo_nrp" value="{{ old('ceo_nrp', $tenant->ceo_nrp) }}">
                                <div class="form-text">NRP/NIK Direktur / Pimpinan RS.</div>
                                @error('ceo_nrp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="city" class="form-label">Kota</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $tenant->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="logo" class="form-label">Logo</label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/jpeg,image/png,image/gif">
                            @if($tenant->logo)
                                @php
                                    $logoFilename = basename($tenant->logo);
                                    $publicImagePath = 'images/' . $logoFilename;
                                @endphp
                                <a href="{{ asset($publicImagePath) }}?v={{ $tenant->updated_at->timestamp }}" target="_blank" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Lihat Logo
                                </a>
                            @endif
                        </div>
                        <div class="form-text">Format yang diizinkan: JPG, PNG, GIF. Ukuran maksimal: 2MB. Ukuran optimal: 400px lebar.</div>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="logo-preview" class="mt-2 d-none">
                            <img src="#" class="img-thumbnail" style="max-height: 150px; max-width: 200px;">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="letter_head" class="form-label">Kop Surat</label>
                        <textarea class="form-control @error('letter_head') is-invalid @enderror" id="letter_head" name="letter_head" rows="3">{{ old('letter_head', $tenant->letter_head) }}</textarea>
                        <div class="form-text">Masukkan teks untuk kop surat yang akan ditampilkan pada dokumen resmi.</div>
                        @error('letter_head')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview logo saat dipilih
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logo-preview');
        const logoPreviewImg = logoPreview.querySelector('img');
        
        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Cek ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    logoPreview.classList.add('d-none');
                    return;
                }
                
                // Cek tipe file
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    logoPreview.classList.add('d-none');
                    return;
                }
                
                // Tampilkan preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreviewImg.src = e.target.result;
                    logoPreview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                logoPreview.classList.add('d-none');
            }
        });
    });
</script>
@endsection 