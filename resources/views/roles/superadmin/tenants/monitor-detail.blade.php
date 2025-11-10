@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Detail Monitoring Tenant:') }} {{ $tenant->name }}</h5>
                    <div>
                        <a href="{{ route('superadmin.tenants.monitor') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Kembali') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Informasi Tenant dan Statistik -->
                    <div class="row">
                        <!-- Informasi Tenant -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Informasi Tenant') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th>{{ __('Nama') }}</th>
                                            <td>{{ $tenant->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Domain') }}</th>
                                            <td>{{ $tenant->domain }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Database') }}</th>
                                            <td>{{ $tenant->database }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}</th>
                                            <td>
                                                @if($tenant->is_active)
                                                    <span class="badge bg-success">{{ __('Aktif') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Non-Aktif') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Dibuat') }}</th>
                                            <td>{{ $tenant->created_at->format('d M Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Admin Tenant -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Admin Tenant') }}</h6>
                                </div>
                                <div class="card-body">
                                    @forelse($adminUsers as $admin)
                                        <div class="mb-3 p-2 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold">{{ $admin->name }}</div>
                                                    <div class="text-muted">{{ $admin->email }}</div>
                                                </div>
                                                <div>
                                                    @if($admin->last_login_at)
                                                        <span class="badge bg-info" title="{{ $admin->last_login_at->format('d M Y H:i') }}">
                                                            {{ $admin->last_login_at->diffForHumans() }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Belum login') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($admin->last_login_ip)
                                                <div class="small text-muted mt-1">
                                                    {{ __('IP:') }} {{ $admin->last_login_ip }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center text-muted">
                                            {{ __('Tidak ada admin tenant') }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Statistik User dan Modul -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Statistik User -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ __('Statistik User') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center align-items-center mb-3">
                                                <div class="text-center">
                                                    <h3 class="mb-0">{{ $userCount }}</h3>
                                                    <div class="text-muted">{{ __('Total User') }}</div>
                                                </div>
                                            </div>
                                            
                                            <h6 class="border-bottom pb-2">{{ __('User per Role') }}</h6>
                                            <ul class="list-group">
                                                @forelse($usersByRole as $role => $count)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $role }}
                                                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center text-muted">
                                                        {{ __('Tidak ada data') }}
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modul Aktif -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ __('Modul Aktif') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center align-items-center mb-3">
                                                <div class="text-center">
                                                    <h3 class="mb-0">{{ $tenant->modules->count() }}</h3>
                                                    <div class="text-muted">{{ __('Total Modul Aktif') }}</div>
                                                </div>
                                            </div>

                                            <h6 class="border-bottom pb-2">{{ __('Jumlah Data per Modul') }}</h6>
                                            <ul class="list-group">
                                                @forelse($moduleDataCounts as $moduleName => $count)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $moduleName }}
                                                        @if($count !== 'N/A' && $count !== 'Error')
                                                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                                        @else
                                                            <span class="badge bg-secondary rounded-pill">{{ $count }}</span>
                                                        @endif
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center text-muted">
                                                        {{ __('Tidak ada data') }}
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grafik Login -->
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ __('Aktivitas Login (30 hari terakhir)') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="loginChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas Terbaru -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Aktivitas Terbaru') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Waktu') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Aktivitas') }}</th>
                                                    <th>{{ __('IP Address') }}</th>
                                                    <th>{{ __('User Agent') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentActivities as $activity)
                                                    <tr>
                                                        <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @if($activity->causer)
                                                                {{ $activity->causer->name }}
                                                            @else
                                                                {{ __('System') }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $activity->description }}</td>
                                                        <td>{{ $activity->properties['ip_address'] ?? '-' }}</td>
                                                        <td>
                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $activity->properties['user_agent'] ?? '' }}">
                                                                {{ substr($activity->properties['user_agent'] ?? '-', 0, 50) }}{{ strlen($activity->properties['user_agent'] ?? '') > 50 ? '...' : '' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">{{ __('Tidak ada aktivitas terbaru') }}</td>
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
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Siapkan data dari controller ke format yang bisa digunakan Chart.js
        const loginData = @json($dailyLogins);
        
        // Ekstrak tanggal dan jumlah login
        const dates = Object.keys(loginData).sort();
        const counts = dates.map(date => loginData[date]);
        
        // Siapkan gradasi warna
        const gradient = document.getElementById('loginChart').getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(75, 192, 192, 0.7)');
        gradient.addColorStop(1, 'rgba(75, 192, 192, 0.1)');
        
        // Buat chart
        new Chart(document.getElementById('loginChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Jumlah Login',
                    data: counts,
                    backgroundColor: gradient,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 4,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                const date = new Date(tooltipItems[0].label);
                                return date.toLocaleDateString('id-ID', { 
                                    weekday: 'long', 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush 