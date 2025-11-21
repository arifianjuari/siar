@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="mb-0">Detail Pengguna</h2>
    <div>
        <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Informasi Pengguna -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Informasi Pengguna</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                <span class="badge bg-info">
                                    {{ $user->role->name ?? 'Tidak ada role' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tenant</th>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $user->tenant->name ?? 'Tidak ada tenant' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Terdaftar Pada</th>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Log Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada aktivitas yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .avatar-lg {
        height: 5rem;
        width: 5rem;
    }
    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 500;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
</style> 