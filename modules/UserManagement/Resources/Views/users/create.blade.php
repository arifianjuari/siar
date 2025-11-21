@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Pengguna Baru</h5>
                        <a href="{{ route('modules.user-management.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('modules.user-management.users.store') }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="tenant_id" value="{{ session('tenant_id') }}">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="work_unit_id" class="form-label">Unit Kerja</label>
                            <select class="form-select @error('work_unit_id') is-invalid @enderror" id="work_unit_id" name="work_unit_id">
                                <option value="">Pilih Unit Kerja</option>
                                @foreach($workUnits as $workUnit)
                                    <option value="{{ $workUnit->id }}" {{ old('work_unit_id') == $workUnit->id ? 'selected' : '' }}>{{ $workUnit->unit_name }}</option>
                                @endforeach
                            </select>
                            @error('work_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Jabatan</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rank" class="form-label">Pangkat</label>
                            <input type="text" class="form-control @error('rank') is-invalid @enderror" id="rank" name="rank" value="{{ old('rank') }}">
                            @error('rank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nrp" class="form-label">NRP</label>
                            <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp" name="nrp" value="{{ old('nrp') }}">
                            @error('nrp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="supervisor_id" class="form-label">Atasan Langsung</label>
                            <select class="form-select @error('supervisor_id') is-invalid @enderror" id="supervisor_id" name="supervisor_id">
                                <option value="">Pilih Atasan</option>
                                @php
                                    $users = \App\Models\User::where('tenant_id', session('tenant_id'))
                                        ->where('is_active', true)
                                        ->orderBy('name')
                                        ->get();
                                @endphp
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('supervisor_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->position ?? 'Tidak ada jabatan' }})</option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="employment_status" class="form-label">Status Kepegawaian</label>
                            <select class="form-select @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status">
                                <option value="aktif" {{ old('employment_status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="resign" {{ old('employment_status') == 'resign' ? 'selected' : '' }}>Resign</option>
                                <option value="cuti" {{ old('employment_status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="magang" {{ old('employment_status') == 'magang' ? 'selected' : '' }}>Magang</option>
                            </select>
                            @error('employment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 