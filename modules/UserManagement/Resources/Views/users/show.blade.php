@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Pengguna</h5>
                        <div>
                            <a href="{{ route('modules.user-management.users.edit', $user->id) }}" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-pencil-alt me-1"></i> Edit
                            </a>
                            <a href="{{ route('modules.user-management.users.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>{{ $user->role->name ?? 'Tidak ada role' }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Kerja</th>
                                    <td>{{ $user->workUnit->unit_name ?? '-' }}</td>
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
                                    <th>Terakhir Login</th>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->format('d-m-Y H:i:s') : 'Belum pernah login' }}</td>
                                </tr>
                                <tr>
                                    <th>IP Terakhir Login</th>
                                    <td>{{ $user->last_login_ip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ $user->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui Pada</th>
                                    <td>{{ $user->updated_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                @if($user->creator)
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $user->creator->name }}</td>
                                </tr>
                                @endif
                                @if($user->updater)
                                <tr>
                                    <th>Diperbarui Oleh</th>
                                    <td>{{ $user->updater->name }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 