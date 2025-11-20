@extends('layouts.app')

@section('title', ' | Tambah Role Baru')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Tambah Role Baru</h2>
        
        <a href="{{ route('modules.user-management.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('modules.user-management.roles.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('slug') is-invalid @enderror" id="slug" name="slug" required>
                                <option value="">Pilih Slug Role</option>
                                <option value="superadmin" {{ old('slug') == 'superadmin' ? 'selected' : '' }}>superadmin</option>
                                <option value="tenant-admin" {{ old('slug') == 'tenant-admin' ? 'selected' : '' }}>tenant-admin</option>
                                <option value="manajemen-strategis" {{ old('slug') == 'manajemen-strategis' ? 'selected' : '' }}>manajemen-strategis</option>
                                <option value="manajemen-eksekutif" {{ old('slug') == 'manajemen-eksekutif' ? 'selected' : '' }}>manajemen-eksekutif</option>
                                <option value="manajemen-operasional" {{ old('slug') == 'manajemen-operasional' ? 'selected' : '' }}>manajemen-operasional</option>
                                <option value="staf" {{ old('slug') == 'staf' ? 'selected' : '' }}>staf</option>
                            </select>
                            <div class="form-text">Pilih slug yang paling mendekati dengan peran pengguna</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5>Pengaturan Hak Akses</h5>
                    <hr>
                    
                    @if($modules->isEmpty())
                        <div class="alert alert-warning">
                            Tidak ada modul aktif yang tersedia untuk tenant ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="15%">Modul</th>
                                        <th class="text-center" width="10%">Lihat</th>
                                        <th class="text-center" width="10%">Tambah</th>
                                        <th class="text-center" width="10%">Edit</th>
                                        <th class="text-center" width="10%">Hapus</th>
                                        <th class="text-center" width="10%">Ekspor</th>
                                        <th class="text-center" width="10%">Impor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="module_{{ $module->id }}">
                                                    <strong>{{ $module->name }}</strong>
                                                </label>
                                                <input type="hidden" name="permissions[{{ $module->id }}][module_id]" value="{{ $module->id }}">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_view_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_view]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_view") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_create_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_create]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_create") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_edit_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_edit]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_edit") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_delete_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_delete]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_delete") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_export_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_export]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_export") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="can_import_{{ $module->id }}" 
                                                           name="permissions[{{ $module->id }}][can_import]" 
                                                           value="1" 
                                                           {{ old("permissions.{$module->id}.can_import") ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="my-3">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">Batalkan Semua</button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tombol pilih semua permission
        document.getElementById('selectAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
        
        // Tombol batalkan semua permission
        document.getElementById('deselectAllPermissions').addEventListener('click', function() {
            document.querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    });
</script>
@endpush 