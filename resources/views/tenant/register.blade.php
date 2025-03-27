@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">{{ config('app.name', 'SIAR') }}</h2>
                <p class="text-muted">Sistem Informasi Audit & Risk Management</p>
            </div>
            
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0 text-center">{{ __('Pendaftaran Tenant Baru') }}</h4>
                </div>

                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tenant.register.submit') }}">
                        @csrf

                        <h5 class="mb-3 fw-bold"><i class="fas fa-building me-2"></i>{{ __('Informasi Institusi') }}</h5>
                        <hr class="mb-4">

                        <div class="mb-3">
                            <label for="institution_name" class="form-label fw-medium">{{ __('Nama Institusi') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-landmark"></i></span>
                                <input id="institution_name" type="text" class="form-control @error('institution_name') is-invalid @enderror" name="institution_name" value="{{ old('institution_name') }}" required autocomplete="institution_name" autofocus placeholder="masukkan nama institusi">
                            </div>
                            @error('institution_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="domain" class="form-label fw-medium">{{ __('Domain') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-globe"></i></span>
                                <input id="domain" type="text" class="form-control @error('domain') is-invalid @enderror" name="domain" value="{{ old('domain') }}" required autocomplete="domain" placeholder="domain-anda">
                                <span class="input-group-text">.{{ config('app.url_base', 'localhost') }}</span>
                            </div>
                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Domain akan digunakan sebagai subdomain untuk akses tenant Anda.</small>
                            @error('domain')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <h5 class="mb-3 fw-bold mt-4"><i class="fas fa-user-shield me-2"></i>{{ __('Informasi Admin') }}</h5>
                        <hr class="mb-4">

                        <div class="mb-3">
                            <label for="admin_name" class="form-label fw-medium">{{ __('Nama Admin') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input id="admin_name" type="text" class="form-control @error('admin_name') is-invalid @enderror" name="admin_name" value="{{ old('admin_name') }}" required autocomplete="name" placeholder="masukkan nama admin">
                            </div>
                            @error('admin_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_email" class="form-label fw-medium">{{ __('Email Admin') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                <input id="admin_email" type="email" class="form-control @error('admin_email') is-invalid @enderror" name="admin_email" value="{{ old('admin_email') }}" required autocomplete="email" placeholder="masukkan email admin">
                            </div>
                            @error('admin_email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_password" class="form-label fw-medium">{{ __('Password Admin') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                <input id="admin_password" type="password" class="form-control @error('admin_password') is-invalid @enderror" name="admin_password" required autocomplete="new-password" placeholder="minimal 8 karakter">
                            </div>
                            @error('admin_password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="admin_password_confirmation" class="form-label fw-medium">{{ __('Konfirmasi Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                <input id="admin_password_confirmation" type="password" class="form-control" name="admin_password_confirmation" required autocomplete="new-password" placeholder="ulangi password admin">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-medium">
                                <i class="fas fa-building me-2"></i>{{ __('Daftar Tenant') }}
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">{{ __('Sudah memiliki akun?') }} <a href="{{ route('login') }}" class="text-decoration-none">{{ __('Login') }}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 