<?php

require_once __DIR__ . '/vendor/autoload.php';

// Inisiasi aplikasi Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

// Fungsi util untuk output berwarna
function out($text, $color = 'white')
{
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
    ];

    $reset = "\033[0m";
    echo $colors[$color] . $text . $reset . PHP_EOL;
}

out("PERBAIKAN AKSES MODUL DI DASHBOARD", "cyan");
out("==========================================", "cyan");
out("");

// 1. Perbaikan dashboard.blade.php - hapus override hasModulePermission
out("1. PERBAIKAN DASHBOARD TEMPLATE", "blue");
out("------------------------------------------", "blue");

$dashboardPath = base_path('resources/views/dashboard.blade.php');
if (file_exists($dashboardPath)) {
    $content = file_get_contents($dashboardPath);

    // Cari dan hapus fungsi hasModulePermission yang dioverride
    if (preg_match('/if\s*\(\s*!\s*function_exists\s*\(\s*[\'"]hasModulePermission[\'"]\s*\)\s*\)\s*\{.*?function\s+hasModulePermission.*?return\s+true.*?\}/s', $content, $matches)) {
        out("Menemukan override fungsi hasModulePermission di dashboard.blade.php", "yellow");

        // Ganti dengan string kosong
        $newContent = str_replace($matches[0], '', $content);

        // Simpan file
        file_put_contents($dashboardPath, $newContent);
        out("Override fungsi hasModulePermission berhasil dihapus", "green");
    } else {
        out("Tidak ditemukan override fungsi hasModulePermission di dashboard.blade.php", "green");
    }
} else {
    out("File dashboard.blade.php tidak ditemukan!", "red");
}

out("");

// 2. Perbaikan CheckModuleAccess.php - perbaiki parameter hasModulePermission
out("2. PERBAIKAN MIDDLEWARE CHECK MODULE ACCESS", "blue");
out("------------------------------------------", "blue");

$middlewarePath = app_path('Http/Middleware/CheckModuleAccess.php');
if (file_exists($middlewarePath)) {
    $content = file_get_contents($middlewarePath);

    // Cari dan ganti parameter 'view' dengan 'can_view'
    if (preg_match('/hasModulePermission\s*\(\s*([^,]+),\s*([^,]+),\s*[\'"]view[\'"]\s*\)/', $content, $matches)) {
        out("Menemukan penggunaan parameter 'view' di hasModulePermission", "yellow");

        // Ganti dengan 'can_view'
        $newContent = str_replace($matches[0], "hasModulePermission({$matches[1]}, {$matches[2]}, 'can_view')", $content);

        // Simpan file
        file_put_contents($middlewarePath, $newContent);
        out("Parameter hasModulePermission berhasil diubah dari 'view' menjadi 'can_view'", "green");
    } else {
        out("Tidak ditemukan penggunaan parameter 'view' di hasModulePermission", "green");
    }
} else {
    out("File CheckModuleAccess.php tidak ditemukan!", "red");
}

out("");

// 3. Tambahkan widget modul ke dashboard_widgets.blade.php jika belum ada
out("3. TAMBAHKAN WIDGET MODUL KE DASHBOARD", "blue");
out("------------------------------------------", "blue");

$dashboardWidgetsPath = resource_path('views/layouts/partials/dashboard_widgets.blade.php');
if (file_exists($dashboardWidgetsPath)) {
    $content = file_get_contents($dashboardWidgetsPath);

    // Cek apakah sudah ada widget untuk modul
    if (strpos($content, 'Modul Tersedia') === false) {
        out("Widget modul belum ada di dashboard_widgets.blade.php", "yellow");

        // Tambahkan widget modul
        $modulWidget = <<<EOT

<!-- Modul Tersedia Widget -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Modul Tersedia</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    \$tenant = auth()->user()->tenant;
                    \$modules = \$tenant ? \$tenant->modules()->wherePivot('is_active', true)->get() : collect([]);
                @endphp
                
                @if(\$modules->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Tidak ada modul aktif untuk tenant Anda.
                        </div>
                    </div>
                @else
                    @foreach(\$modules as \$module)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm module-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas {{ \$module->icon ?? 'fa-cube' }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ \$module->name }}</h5>
                                            <p class="text-muted mb-0 small">{{ \$module->description }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ url('modules/' . \$module->slug) }}" class="btn btn-primary w-100">
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
EOT;

        // Tambahkan ke akhir file
        $newContent = $content . $modulWidget;

        // Simpan file
        file_put_contents($dashboardWidgetsPath, $newContent);
        out("Widget modul berhasil ditambahkan ke dashboard_widgets.blade.php", "green");
    } else {
        out("Widget modul sudah ada di dashboard_widgets.blade.php", "green");
    }
} else {
    out("File dashboard_widgets.blade.php tidak ditemukan! Membuat file baru...", "yellow");

    $modulWidget = <<<EOT
<!-- Dashboard Widgets -->

<!-- Statistik Widget -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card dashboard-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Total Pengguna</h6>
                        <h3 class="mb-0 fs-4 fw-bold">{{ \App\Models\User::where('tenant_id', auth()->user()->tenant_id)->count() }}</h3>
                    </div>
                    <div class="stat-icon bg-primary text-white">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card dashboard-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Modul Aktif</h6>
                        <h3 class="mb-0 fs-4 fw-bold">
                            {{ auth()->user()->tenant ? auth()->user()->tenant->modules()->wherePivot('is_active', true)->count() : 0 }}
                        </h3>
                    </div>
                    <div class="stat-icon bg-success text-white">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card dashboard-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Role</h6>
                        <h3 class="mb-0 fs-4 fw-bold">
                            {{ \App\Models\Role::where('tenant_id', auth()->user()->tenant_id)->count() }}
                        </h3>
                    </div>
                    <div class="stat-icon bg-info text-white">
                        <i class="fas fa-user-tag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card dashboard-stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Status</h6>
                        <h3 class="mb-0 fs-4 fw-bold text-success">Aktif</h3>
                    </div>
                    <div class="stat-icon bg-warning text-white">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modul Tersedia Widget -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Modul Tersedia</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    \$tenant = auth()->user()->tenant;
                    \$modules = \$tenant ? \$tenant->modules()->wherePivot('is_active', true)->get() : collect([]);
                @endphp
                
                @if(\$modules->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Tidak ada modul aktif untuk tenant Anda.
                        </div>
                    </div>
                @else
                    @foreach(\$modules as \$module)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm module-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas {{ \$module->icon ?? 'fa-cube' }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ \$module->name }}</h5>
                                            <p class="text-muted mb-0 small">{{ \$module->description }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ url('modules/' . \$module->slug) }}" class="btn btn-primary w-100">
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
EOT;

    // Buat direktori jika belum ada
    if (!file_exists(resource_path('views/layouts/partials'))) {
        mkdir(resource_path('views/layouts/partials'), 0755, true);
    }

    // Simpan file
    file_put_contents($dashboardWidgetsPath, $modulWidget);
    out("File dashboard_widgets.blade.php berhasil dibuat dengan widget modul", "green");
}

out("");

// 4. Bersihkan cache sistem
out("4. BERSIHKAN CACHE SISTEM", "blue");
out("------------------------------------------", "blue");

try {
    out("Membersihkan cache aplikasi...", "white");
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    out(\Illuminate\Support\Facades\Artisan::output(), "white");

    out("Membersihkan cache konfigurasi...", "white");
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    out(\Illuminate\Support\Facades\Artisan::output(), "white");

    out("Membersihkan cache view...", "white");
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    out(\Illuminate\Support\Facades\Artisan::output(), "white");

    out("Cache berhasil dibersihkan", "green");
} catch (\Exception $e) {
    out("Terjadi kesalahan saat membersihkan cache: " . $e->getMessage(), "red");
}

out("");
out("PERBAIKAN SELESAI", "cyan");
out("==========================================", "cyan");
out("Langkah selanjutnya:", "yellow");
out("1. Logout dan login kembali untuk memperbarui sesi", "white");
out("2. Akses dashboard dan periksa apakah modul sudah muncul", "white");
out("3. Jika masih ada masalah, jalankan script diagnosa: php fix_dashboard_modules.php", "white");
