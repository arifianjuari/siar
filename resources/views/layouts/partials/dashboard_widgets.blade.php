<!-- Dashboard Widgets & Charts -->
<div class="row" x-data="{ chartData: initializeChartData() }">
    <!-- Statistik Widget -->
    @if(hasModulePermission('user_management'))
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Total Pengguna</h6>
                        <h3 class="mb-0 fs-4 fw-bold">{{ \App\Models\User::where('tenant_id', auth()->user()->tenant_id)->count() ?? 0 }}</h3>
                        <p class="mb-0 small text-muted">
                            @php
                                $newUsers = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)
                                    ->where('created_at', '>=', now()->subMonth())
                                    ->count();
                            @endphp
                            @if($newUsers > 0)
                                <i class="fas fa-arrow-up me-1 text-success"></i> {{ $newUsers }} baru bulan ini
                            @else
                                <i class="fas fa-minus me-1"></i> Tidak ada pengguna baru
                            @endif
                        </p>
                    </div>
                    <div class="icon-container bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(hasModulePermission('risk_management'))
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Pelaporan</h6>
                        @php
                            $reportsCount = 0;
                            $pendingReports = 0;
                            
                            if (class_exists('\\App\\Models\\RiskReport')) {
                                $reportsCount = \App\Models\RiskReport::where('tenant_id', auth()->user()->tenant_id)->count();
                                $pendingReports = \App\Models\RiskReport::where('tenant_id', auth()->user()->tenant_id)
                                    ->where('status', 'open')
                                    ->count();
                            }
                        @endphp
                        <h3 class="mb-0 fs-4 fw-bold">{{ $reportsCount }}</h3>
                        <p class="mb-0 small {{ $pendingReports > 0 ? 'text-warning' : 'text-muted' }}">
                            @if($pendingReports > 0)
                                <i class="fas fa-exclamation-triangle me-1"></i> {{ $pendingReports }} menunggu
                            @else
                                <i class="fas fa-check-circle me-1"></i> Tidak ada yang tertunda
                            @endif
                        </p>
                    </div>
                    <div class="icon-container bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Aktivitas</h6>
                        @php
                            $activitiesCount = 0;
                            if (class_exists('\\App\\Models\\ActivityLog')) {
                                $activitiesCount = \App\Models\ActivityLog::where('tenant_id', auth()->user()->tenant_id)
                                    ->count();
                            }
                        @endphp
                        <h3 class="mb-0 fs-4 fw-bold">{{ $activitiesCount }}</h3>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-info-circle me-1"></i> Total aktivitas tercatat
                        </p>
                    </div>
                    <div class="icon-container bg-info bg-opacity-10 text-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 1: Bar Chart -->
    @if(hasModulePermission('user_management'))
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Aktivitas Pengguna Bulanan</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-calendar me-1"></i> Tahun ini
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Tahun ini</a></li>
                        <li><a class="dropdown-item" href="#">Tahun lalu</a></li>
                        <li><a class="dropdown-item" href="#">6 bulan terakhir</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="userActivityChart" height="300"></canvas>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Recent Activity Table -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $hasActivityLog = class_exists('\\App\\Models\\ActivityLog');
                    $activities = [];
                    
                    if ($hasActivityLog) {
                        $activities = \App\Models\ActivityLog::where('tenant_id', auth()->user()->tenant_id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    }
                @endphp
                
                @if($hasActivityLog && count($activities) > 0)
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span>{{ $activity->causer->name ?? 'Unknown User' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-history fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">Belum ada aktivitas tercatat</h6>
                        <p class="small text-muted mb-0">Aktivitas pengguna akan muncul di sini saat tersedia</p>
                    </div>
                @endif
            </div>
            @if($hasActivityLog && count($activities) > 0)
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none">Lihat semua aktivitas <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Notifications Widget -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Notifikasi</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $hasNotifications = false;
                    $notifications = [];
                    
                    // Check if we have a notification model/system
                    if (class_exists('\\App\\Models\\Notification')) {
                        $hasNotifications = true;
                        $notifications = \App\Models\Notification::where('tenant_id', auth()->user()->tenant_id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    }
                @endphp
                
                @if($hasNotifications && count($notifications) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                            <div class="flex-shrink-0 me-3 bg-{{ $notification->type ?? 'secondary' }} bg-opacity-10 p-2 rounded-circle">
                                <i class="fas fa-{{ $notification->icon ?? 'bell' }} text-{{ $notification->type ?? 'secondary' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $notification->title }}</h6>
                                <p class="mb-0 text-muted small">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @if($notification->is_new)
                            <div class="ms-auto">
                                <span class="badge rounded-pill bg-primary">Baru</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-bell-slash fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">Belum ada notifikasi</h6>
                        <p class="small text-muted mb-0">Anda akan melihat notifikasi di sini saat tersedia</p>
                    </div>
                @endif
            </div>
            @if($hasNotifications && count($notifications) > 0)
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none">Lihat semua notifikasi <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart Initialization Scripts -->
<script>
function initializeChartData() {
    return {
        init() {
            @if(hasModulePermission('user_management'))
            // Initialize user activity bar chart
            const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
            const userActivityChart = new Chart(userActivityCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Login',
                        data: [65, 59, 80, 81, 56, 55, 40, 55, 66, 77, 88, 75],
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgb(79, 70, 229)',
                        borderWidth: 1
                    }, {
                        label: 'Transaksi',
                        data: [28, 48, 40, 19, 86, 27, 90, 35, 42, 50, 64, 85],
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            @endif
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts when Alpine.js is ready
    document.querySelectorAll('[x-data]').forEach(function(el) {
        if (el.__x && typeof el.__x.data.init === 'function') {
            el.__x.data.init();
        }
    });
});
</script>