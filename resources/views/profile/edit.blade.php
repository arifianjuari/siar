@extends('layouts.app')

@section('title', ' | Edit Profil')

@section('header')
<h1 class="h3 mb-0">Edit Profil</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="position" class="form-label">Jabatan</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" 
                            id="position" name="position" value="{{ old('position', $user->position) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="rank" class="form-label">Pangkat</label>
                        <input type="text" class="form-control @error('rank') is-invalid @enderror" 
                            id="rank" name="rank" value="{{ old('rank', $user->rank) }}">
                        @error('rank')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nrp" class="form-label">NRP</label>
                        <input type="text" class="form-control @error('nrp') is-invalid @enderror" 
                            id="nrp" name="nrp" value="{{ old('nrp', $user->nrp) }}">
                        @error('nrp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    <h5 class="mb-3">Ubah Password</h5>
                    <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin mengubah password</p>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                            id="current_password" name="current_password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                            id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" 
                            id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Foto Profil</h5>
            </div>
            <div class="card-body text-center py-4">
                <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3" 
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->role->name ?? 'User' }}</p>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">
                        <i class="fas fa-envelope me-2"></i> {{ $user->email }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar me-2"></i> Bergabung: {{ $user->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Status Akun</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                @if($user->position)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Jabatan</span>
                    <span>{{ $user->position }}</span>
                </div>
                @endif
                @if($user->rank)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Pangkat</span>
                    <span>{{ $user->rank }}</span>
                </div>
                @endif
                @if($user->nrp)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>NRP</span>
                    <span>{{ $user->nrp }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Login Terakhir</span>
                    <span>{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 