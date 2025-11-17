@extends('layouts.app')

@php($hideDefaultHeader = true)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Tenant Management') }}</h5>
                    <div>
                        <a href="{{ route('superadmin.tenants.monitor') }}" class="btn btn-info me-2">
                            <i class="fas fa-chart-bar"></i> {{ __('Monitoring') }}
                        </a>
                        <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Tambah Tenant') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="tenants-table">
                        @include('roles.superadmin.tenants._table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/ajax-utils.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle pagination clicks
    document.addEventListener('click', async function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            try {
                await AjaxUtils.refreshTable('tenants-table', e.target.href);
            } catch (error) {
                AjaxUtils.showNotification(error.message, 'danger');
            }
        }
    });
});
</script>
@endpush 