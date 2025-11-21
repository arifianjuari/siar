@extends('layouts.app')

@section('title', ' | Profil Saya')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Profil Saya</h2>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    @php $photo = $user->profile_photo ? asset('storage/'.$user->profile_photo) : null; @endphp
                    <img src="{{ $photo ?: asset('images/patterns/avatar-placeholder.png') }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" alt="Avatar">
                </div>
                <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
                    @csrf
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png" class="form-control">
                    <button type="submit" class="btn btn-primary">Update Foto</button>
                </form>
                @if($user->profile_photo)
                <div class="mt-2">
                    <a href="{{ route('profile.remove-photo') }}" class="btn btn-outline-danger btn-sm">Hapus Foto</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $user->position) }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pangkat</label>
                            <input type="text" name="rank" class="form-control @error('rank') is-invalid @enderror" value="{{ old('rank', $user->rank) }}">
                            @error('rank')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NRP/NIK</label>
                            <input type="text" name="nrp" class="form-control @error('nrp') is-invalid @enderror" value="{{ old('nrp', $user->nrp) }}">
                            @error('nrp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit Kerja</label>
                            <select name="work_unit_id" class="form-select @error('work_unit_id') is-invalid @enderror">
                                <option value="">- Pilih -</option>
                                @foreach(($workUnits ?? []) as $wu)
                                    <option value="{{ $wu->id }}" {{ old('work_unit_id', $user->work_unit_id) == $wu->id ? 'selected' : '' }}>{{ $wu->unit_name }}</option>
                                @endforeach
                            </select>
                            @error('work_unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Ubah Password (opsional)</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
