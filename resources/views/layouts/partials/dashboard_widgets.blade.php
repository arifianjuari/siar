<!-- Dashboard Widgets & Charts -->
<div class="row" x-data="{ chartData: initializeChartData() }">
    <!-- Statistik Widget -->
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Total Pengguna</h6>
                        <h3 class="mb-0 fs-4 fw-bold">267</h3>
                        <p class="mb-0 small text-success"><i class="fas fa-arrow-up me-1"></i> 12% bulan ini</p>
                    </div>
                    <div class="icon-container bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Modul Aktif</h6>
                        <h3 class="mb-0 fs-4 fw-bold">14</h3>
                        <p class="mb-0 small text-success"><i class="fas fa-arrow-up me-1"></i> 3 baru</p>
                    </div>
                    <div class="icon-container bg-success bg-opacity-10 text-success">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Pelaporan</h6>
                        <h3 class="mb-0 fs-4 fw-bold">49</h3>
                        <p class="mb-0 small text-warning"><i class="fas fa-exclamation-triangle me-1"></i> 5 menunggu</p>
                    </div>
                    <div class="icon-container bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card card-stat info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Aktivitas</h6>
                        <h3 class="mb-0 fs-4 fw-bold">128</h3>
                        <p class="mb-0 small text-info"><i class="fas fa-info-circle me-1"></i> 24 jam terakhir</p>
                    </div>
                    <div class="icon-container bg-info bg-opacity-10 text-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 1: Bar Chart -->
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
    
    <!-- Chart 2: Pie Chart -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Distribusi Modul</h5>
            </div>
            <div class="card-body">
                <canvas id="moduleDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Table -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
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
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">B</div>
                                        <span>Budi Santoso</span>
                                    </div>
                                </td>
                                <td>Login ke sistem</td>
                                <td><span class="badge bg-light text-dark">Baru saja</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">A</div>
                                        <span>Ani Permata</span>
                                    </div>
                                </td>
                                <td>Membuat laporan baru</td>
                                <td>10 menit yang lalu</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">C</div>
                                        <span>Candra Wijaya</span>
                                    </div>
                                </td>
                                <td>Mengupdate profil</td>
                                <td>45 menit yang lalu</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">D</div>
                                        <span>Dina Maryati</span>
                                    </div>
                                </td>
                                <td>Menambahkan komentar</td>
                                <td>1 jam yang lalu</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">E</div>
                                        <span>Eko Prasetyo</span>
                                    </div>
                                </td>
                                <td>Mengunduh laporan</td>
                                <td>3 jam yang lalu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none">Lihat semua aktivitas <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Notifications Widget -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Notifikasi</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                        <div class="flex-shrink-0 me-3 bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Email baru dari Admin</h6>
                            <p class="mb-0 text-muted small">Mohon periksa kotak masuk Anda untuk informasi penting...</p>
                            <small class="text-muted">30 menit yang lalu</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge rounded-pill bg-primary">Baru</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                        <div class="flex-shrink-0 me-3 bg-success bg-opacity-10 p-2 rounded-circle">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Tugas selesai</h6>
                            <p class="mb-0 text-muted small">Laporan bulanan telah selesai diproses</p>
                            <small class="text-muted">2 jam yang lalu</small>
                        </div>
                    </div>
                    
                    <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                        <div class="flex-shrink-0 me-3 bg-warning bg-opacity-10 p-2 rounded-circle">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Peringatan sistem</h6>
                            <p class="mb-0 text-muted small">Penyimpanan Anda hampir penuh (85%)</p>
                            <small class="text-muted">5 jam yang lalu</small>
                        </div>
                    </div>
                    
                    <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                        <div class="flex-shrink-0 me-3 bg-info bg-opacity-10 p-2 rounded-circle">
                            <i class="fas fa-user-plus text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Pengguna baru ditambahkan</h6>
                            <p class="mb-0 text-muted small">Rini Susanti telah terdaftar sebagai Asisten Manajer</p>
                            <small class="text-muted">1 hari yang lalu</small>
                        </div>
                    </div>
                    
                    <div class="list-group-item border-0 d-flex align-items-center px-3 py-3">
                        <div class="flex-shrink-0 me-3 bg-danger bg-opacity-10 p-2 rounded-circle">
                            <i class="fas fa-calendar-alt text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Pengingat rapat</h6>
                            <p class="mb-0 text-muted small">Rapat evaluasi kinerja dijadwalkan besok pukul 10:00</p>
                            <small class="text-muted">1 hari yang lalu</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none">Lihat semua notifikasi <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Chart Initialization Scripts -->
<script>
function initializeChartData() {
    return {
        init() {
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

            // Initialize module distribution pie chart
            const moduleDistributionCtx = document.getElementById('moduleDistributionChart').getContext('2d');
            const moduleDistributionChart = new Chart(moduleDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Manajemen User', 'Manajemen Risiko', 'Laporan', 'Keuangan', 'Lainnya'],
                    datasets: [{
                        data: [25, 20, 30, 15, 10],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(79, 70, 229)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(59, 130, 246)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
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
<!-- Modul Tersedia Widget -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Modul Tersedia</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    $tenant = auth()->user()->tenant;
                    $modules = $tenant ? $tenant->modules()->wherePivot('is_active', true)->get() : collect([]);
                @endphp
                
                @if($modules->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Tidak ada modul aktif untuk tenant Anda.
                        </div>
                    </div>
                @else
                    @foreach($modules as $module)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm module-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas {{ $module->icon ?? 'fa-cube' }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ $module->name }}</h5>
                                            <p class="text-muted mb-0 small">{{ $module->description }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ url('modules/' . $module->slug) }}" class="btn btn-primary w-100">
                                            <i class="fas fa-external-link-alt me-2"></i> Akses Modul
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>