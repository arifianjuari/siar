@extends('layouts.app')

@section('title', ' | Pengaturan Tenant')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Pengaturan Tenant</h2>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pengaturan Umum</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Zona Waktu</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                    <option value="Asia/Jakarta" {{ old('timezone', $tenant->settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ old('timezone', $tenant->settings['timezone'] ?? '') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ old('timezone', $tenant->settings['timezone'] ?? '') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="language" class="form-label">Bahasa</label>
                                <select class="form-select @error('language') is-invalid @enderror" id="language" name="language">
                                    <option value="id" {{ old('language', $tenant->settings['language'] ?? 'id') == 'id' ? 'selected' : '' }}>Indonesia</option>
                                    <option value="en" {{ old('language', $tenant->settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Format Tanggal</label>
                                <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format">
                                    <option value="d/m/Y" {{ old('date_format', $tenant->settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2023)</option>
                                    <option value="m/d/Y" {{ old('date_format', $tenant->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2023)</option>
                                    <option value="Y-m-d" {{ old('date_format', $tenant->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2023-12-31)</option>
                                    <option value="d M Y" {{ old('date_format', $tenant->settings['date_format'] ?? '') == 'd M Y' ? 'selected' : '' }}>DD MMM YYYY (31 Dec 2023)</option>
                                </select>
                                @error('date_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="time_format" class="form-label">Format Waktu</label>
                                <select class="form-select @error('time_format') is-invalid @enderror" id="time_format" name="time_format">
                                    <option value="H:i" {{ old('time_format', $tenant->settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24 Jam (14:30)</option>
                                    <option value="h:i A" {{ old('time_format', $tenant->settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12 Jam (02:30 PM)</option>
                                </select>
                                @error('time_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Mata Uang</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                                    <option value="IDR" {{ old('currency', $tenant->settings['currency'] ?? 'IDR') == 'IDR' ? 'selected' : '' }}>Rupiah (IDR)</option>
                                    <option value="USD" {{ old('currency', $tenant->settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="EUR" {{ old('currency', $tenant->settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 