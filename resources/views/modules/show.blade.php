@extends('layouts.app')

@section('title', ' | Modul ' . $tenantModule->name)

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Modul: {{ $tenantModule->name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('modules.index') }}">Modul</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $tenantModule->name }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-1 mb-3">
                            <i class="fas {{ $tenantModule->icon ?? 'fa-cube' }} text-primary"></i>
                        </div>
                        <h3>{{ $tenantModule->name }}</h3>
                        <p class="text-muted">{{ $tenantModule->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Informasi Modul</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span>Status</span>
                                            <span class="badge bg-success">Aktif</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span>Kode</span>
                                            <span>{{ $tenantModule->code ?? 'Tidak tersedia' }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span>Slug</span>
                                            <span>{{ $tenantModule->slug ?? 'Tidak tersedia' }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span>Diaktifkan</span>
                                            <span>
                                                @if(isset($tenantModule->pivot) && isset($tenantModule->pivot->updated_at))
                                                    {{ $tenantModule->pivot->updated_at->format('d M Y H:i') }}
                                                @else
                                                    Tidak tersedia
                                                @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Akses Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($tenantModule->pivot->is_active)
                                            @php
                                                try {
                                                    if (!empty($tenantModule->slug)) {
                                                        if ($tenantModule->slug == 'user-management') {
                                                            $moduleUrl = route('modules.user-management.users.index');
                                                        } elseif ($tenantModule->slug == 'product-management') {
                                                            $moduleUrl = route('modules.product-management.products.index');
                                                        } elseif ($tenantModule->slug == 'risk-management') {
                                                            $moduleUrl = route('modules.risk-management.dashboard');
                                                        } elseif ($tenantModule->slug == 'document-management') {
                                                            $moduleUrl = route('modules.document-management.dashboard');
                                                        } elseif ($tenantModule->slug == 'dashboard') {
                                                            $moduleUrl = route('dashboard');
                                                        } else {
                                                            $moduleUrl = route('modules.show', $tenantModule->slug);
                                                        }
                                                    }
                                                } catch (\Exception $e) {
                                                    $moduleUrl = route('dashboard');
                                                }
                                            @endphp
                                            <a href="{{ $moduleUrl }}" class="btn btn-primary">
                                                <i class="fas fa-external-link-alt me-2"></i> Akses Modul
                                            </a>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-exclamation-circle me-2"></i> Modul Belum Tersedia
                                            </button>
                                        @endif
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 