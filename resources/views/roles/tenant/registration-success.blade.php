@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Pendaftaran Berhasil') }}</div>

                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Selamat!</h4>
                        <p>Tenant baru Anda telah berhasil dibuat. Berikut adalah informasi tenant Anda:</p>
                    </div>

                    <div class="mt-4">
                        <table class="table">
                            <tr>
                                <th>Nama Institusi:</th>
                                <td>{{ session('tenant_info.name') }}</td>
                            </tr>
                            <tr>
                                <th>Domain:</th>
                                <td>{{ session('tenant_info.domain') }}.{{ config('app.url_base', 'localhost') }}</td>
                            </tr>
                            <tr>
                                <th>Admin Email:</th>
                                <td>{{ session('tenant_info.admin_email') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-4">
                        <p>Anda dapat mengakses tenant Anda melalui URL berikut:</p>
                        <div class="mb-3">
                            <a href="{{ session('tenant_info.tenant_url') }}" class="btn btn-primary" target="_blank">
                                Akses Tenant
                            </a>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <small>
                                <i class="fa fa-info-circle"></i> Gunakan email dan password admin yang Anda daftarkan untuk login ke sistem.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 