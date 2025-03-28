<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\UserManagement\UserController;
use App\Http\Controllers\Modules\UserManagement\RoleController;
use App\Http\Controllers\Modules\ProductManagement\ProductController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\SuperAdmin\TenantManagementController;
use App\Http\Controllers\SuperAdmin\ModuleManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Debug routes
Route::get('/debug-session', function () {
    return response()->json([
        'session_data' => session()->all(),
        'auth_check' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role ? [
                'id' => auth()->user()->role->id,
                'name' => auth()->user()->role->name,
                'slug' => auth()->user()->role->slug
            ] : null,
            'tenant' => auth()->user()->tenant ? [
                'id' => auth()->user()->tenant->id,
                'name' => auth()->user()->tenant->name
            ] : null
        ] : null
    ]);
})->middleware('web');

// Dashboard utama - gunakan hanya middleware auth, tanpa tenant
Route::get('/dashboard', function () {
    // Untuk debug
    if (auth()->check()) {
        \Illuminate\Support\Facades\Log::info('User berhasil mengakses dashboard', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'role' => auth()->user()->role->name ?? 'tidak ada role',
            'tenant' => auth()->user()->tenant->name ?? 'tidak ada tenant'
        ]);
    } else {
        \Illuminate\Support\Facades\Log::warning('Akses dashboard oleh user tidak terotentikasi');
    }

    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Route khusus Superadmin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'superadmin'])->group(function () {
    // Dashboard Superadmin
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');

    // Tenant Management
    Route::resource('tenants', TenantManagementController::class);
    Route::post('tenants/{tenant}/toggle-module', [TenantManagementController::class, 'toggleModule'])->name('tenants.toggle-module');
    Route::post('tenants/{tenant}/reset-admin-password', [TenantManagementController::class, 'resetAdminPassword'])->name('tenants.reset-admin-password');
    Route::get('statistics', [TenantManagementController::class, 'statistics'])->name('statistics');

    // Tenant Role Management
    Route::get('tenants/{tenant}/roles/create', [TenantManagementController::class, 'createRole'])->name('tenants.roles.create');
    Route::post('tenants/{tenant}/roles', [TenantManagementController::class, 'storeRole'])->name('tenants.roles.store');
    Route::get('tenants/{tenant}/roles/{role}/edit', [TenantManagementController::class, 'editRole'])->name('tenants.roles.edit');
    Route::put('tenants/{tenant}/roles/{role}', [TenantManagementController::class, 'updateRole'])->name('tenants.roles.update');
    Route::delete('tenants/{tenant}/roles/{role}', [TenantManagementController::class, 'destroyRole'])->name('tenants.roles.destroy');

    // Tenant Role Module Permissions
    Route::get('tenants/{tenant}/roles/{role}/permissions', [TenantManagementController::class, 'editRolePermissions'])->name('tenants.roles.permissions.edit');
    Route::put('tenants/{tenant}/roles/{role}/permissions', [TenantManagementController::class, 'updateRolePermissions'])->name('tenants.roles.permissions.update');

    // Tenant Monitoring
    Route::get('tenants/monitor', [App\Http\Controllers\SuperAdmin\TenantMonitoringController::class, 'index'])->name('tenants.monitor');
    Route::get('tenants/monitor/{tenant}', [App\Http\Controllers\SuperAdmin\TenantMonitoringController::class, 'show'])->name('tenants.monitor.show');

    // Module Management
    Route::resource('modules', ModuleManagementController::class);
    Route::post('modules/{module}/activate-for-all', [ModuleManagementController::class, 'activateForAll'])->name('modules.activate-for-all');
    Route::post('modules/{module}/deactivate-for-all', [ModuleManagementController::class, 'deactivateForAll'])->name('modules.deactivate-for-all');

    // Module Request Management
    Route::post('modules/approve-request', [ModuleManagementController::class, 'approveRequest'])->name('modules.approve-request');
    Route::post('modules/reject-request', [ModuleManagementController::class, 'rejectRequest'])->name('modules.reject-request');

    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
});

// Route untuk modul-modul
Route::middleware(['auth', 'tenant'])->prefix('modules')->name('modules.')->group(function () {
    // User Management Module
    Route::prefix('user-management')->name('user-management.')->middleware('module:user-management')->group(function () {
        // Users
        Route::resource('users', App\Http\Controllers\Modules\UserManagement\UserController::class);
        // Roles
        Route::resource('roles', App\Http\Controllers\Modules\UserManagement\RoleController::class);
    });

    // Product Management Module
    Route::prefix('product-management')->name('product-management.')->middleware('module:product-management')->group(function () {
        // Products
        Route::resource('products', App\Http\Controllers\Modules\ProductManagement\ProductController::class);
    });

    // Risk Management Module
    Route::prefix('risk-management')->name('risk-management.')->middleware('module:risk-management')->group(function () {
        // Dashboard route - Dapat dilihat oleh semua pengguna yang memiliki akses ke modul
        Route::get('/', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('index');

        Route::get('/dashboard', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('dashboard');

        // Konfigurasi Akses Analisis Risiko - Khusus untuk tenant admin
        Route::get('/analysis-config', [App\Http\Controllers\Modules\RiskManagement\RiskManagementController::class, 'showAnalysisConfig'])
            ->name('analysis-config');
        Route::post('/analysis-config', [App\Http\Controllers\Modules\RiskManagement\RiskManagementController::class, 'saveAnalysisConfig'])
            ->name('save-analysis-config');

        // Melihat daftar laporan risiko - Memerlukan izin can_view
        Route::get('risk-reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'index'])
            ->name('risk-reports.index');

        // Membuat laporan risiko baru - Memerlukan izin can_create
        Route::get('risk-reports/create', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'create'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-reports.create');
        Route::post('risk-reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'store'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-reports.store');

        // Melihat detail laporan risiko - Memerlukan izin can_view
        Route::get('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'show'])
            ->name('risk-reports.show');

        // Mengedit laporan risiko - Memerlukan izin can_edit
        Route::get('risk-reports/{id}/edit', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'edit'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.edit');
        Route::put('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'update'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.update');

        // Menghapus laporan risiko - Memerlukan izin can_delete
        Route::delete('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'destroy'])
            ->middleware('check.permission:risk-management,can_delete')
            ->name('risk-reports.destroy');

        // Dashboard route
        Route::get('risk-reports/dashboard', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('risk-reports.dashboard');

        // Approval routes - Memerlukan izin can_edit
        Route::put('risk-reports/{id}/mark-in-review', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'markInReview'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.mark-in-review');
        Route::put('risk-reports/{id}/approve', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'approve'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.approve');

        // QR Code route - Dapat diakses oleh semua yang memiliki izin can_view
        Route::get('risk-reports/{id}/qr-code', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'generateQr'])
            ->name('risk-reports.qr-code');

        // Export Word routes - Memerlukan izin can_export
        Route::get('risk-reports/{id}/export-awal', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'exportWordAwal'])
            ->middleware('check.permission:risk-management,can_export')
            ->name('risk-reports.export-awal');
        Route::get('risk-reports/{id}/export-akhir', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'exportWordAkhir'])
            ->middleware('check.permission:risk-management,can_export')
            ->name('risk-reports.export-akhir');

        // Risk Analysis Routes - Memerlukan izin can_create
        Route::get('risk-reports/{reportId}/risk-analysis/create', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'create'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-analysis.create');
        Route::post('risk-reports/{reportId}/risk-analysis', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'store'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-analysis.store');
        Route::get('risk-reports/{reportId}/risk-analysis/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'show'])
            ->name('risk-analysis.show');
        Route::get('risk-reports/{reportId}/risk-analysis/{id}/edit', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'edit'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-analysis.edit');
        Route::put('risk-reports/{reportId}/risk-analysis/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'update'])
            ->middleware('check.permission:risk-management,can_create')
            ->name('risk-analysis.update');
        Route::get('risk-reports/{reportId}/risk-analysis/{id}/qr-code', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'generateQr'])
            ->name('risk-analysis.qr-code');
    });

    // Rute Manajemen Modul - PINDAHKAN KE BAWAH
    Route::get('/', [App\Http\Controllers\ModuleController::class, 'index'])->name('index');
    Route::post('/request-activation', [App\Http\Controllers\ModuleController::class, 'requestActivation'])->name('request-activation');
    Route::get('/{slug}', [App\Http\Controllers\ModuleController::class, 'show'])->name('show');
});

// Tenant routes - akses melalui subdomain
Route::domain('{tenant}.localhost')->middleware(['tenant.resolve'])->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Copy semua route yang sama dengan di atas yang memerlukan autentikasi
    Route::middleware(['auth', 'tenant'])->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('tenant.dashboard');

        // Module routes - dikelompokkan berdasarkan modul
        Route::prefix('modules')->name('tenant.modules.')->group(function () {

            // User Management Module
            Route::prefix('user-management')->name('user-management.')->middleware('module:user-management')->group(function () {
                // Users
                Route::resource('users', UserController::class);

                // Roles
                Route::resource('roles', RoleController::class);
            });

            // Product Management Module
            Route::prefix('product-management')->name('product-management.')->middleware('module:product-management')->group(function () {
                // Products
                Route::resource('products', ProductController::class);
            });

            // Risk Management Module
            Route::prefix('risk-management')->name('risk-management.')->middleware('module:risk-management')->group(function () {
                // Dashboard route - Dapat dilihat oleh semua pengguna yang memiliki akses ke modul
                Route::get('/', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('index');
                Route::get('/dashboard', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('dashboard');

                // Konfigurasi Akses Analisis Risiko - Khusus untuk tenant admin
                Route::get('/analysis-config', [App\Http\Controllers\Modules\RiskManagement\RiskManagementController::class, 'showAnalysisConfig'])
                    ->name('analysis-config');
                Route::post('/analysis-config', [App\Http\Controllers\Modules\RiskManagement\RiskManagementController::class, 'saveAnalysisConfig'])
                    ->name('save-analysis-config');

                // Melihat daftar laporan risiko - Memerlukan izin can_view
                Route::get('risk-reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'index'])
                    ->name('risk-reports.index');

                // Membuat laporan risiko baru - Memerlukan izin can_create
                Route::get('risk-reports/create', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'create'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-reports.create');
                Route::post('risk-reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'store'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-reports.store');

                // Melihat detail laporan risiko - Memerlukan izin can_view
                Route::get('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'show'])
                    ->name('risk-reports.show');

                // Mengedit laporan risiko - Memerlukan izin can_edit
                Route::get('risk-reports/{id}/edit', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'edit'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.edit');
                Route::put('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'update'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.update');

                // Menghapus laporan risiko - Memerlukan izin can_delete
                Route::delete('risk-reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'destroy'])
                    ->middleware('check.permission:risk-management,can_delete')
                    ->name('risk-reports.destroy');

                // Dashboard route
                Route::get('risk-reports/dashboard', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'dashboard'])->name('risk-reports.dashboard');

                // Approval routes - Memerlukan izin can_edit
                Route::put('risk-reports/{id}/mark-in-review', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'markInReview'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.mark-in-review');
                Route::put('risk-reports/{id}/approve', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'approve'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.approve');

                // QR Code route - Dapat diakses oleh semua yang memiliki izin can_view
                Route::get('risk-reports/{id}/qr-code', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'generateQr'])
                    ->name('risk-reports.qr-code');

                // Export Word routes - Memerlukan izin can_export
                Route::get('risk-reports/{id}/export-awal', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'exportWordAwal'])
                    ->middleware('check.permission:risk-management,can_export')
                    ->name('risk-reports.export-awal');
                Route::get('risk-reports/{id}/export-akhir', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'exportWordAkhir'])
                    ->middleware('check.permission:risk-management,can_export')
                    ->name('risk-reports.export-akhir');

                // Risk Analysis Routes - Memerlukan izin can_create
                Route::get('risk-reports/{reportId}/risk-analysis/create', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'create'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-analysis.create');
                Route::post('risk-reports/{reportId}/risk-analysis', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'store'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-analysis.store');
                Route::get('risk-reports/{reportId}/risk-analysis/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'show'])
                    ->name('risk-analysis.show');
                Route::get('risk-reports/{reportId}/risk-analysis/{id}/edit', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'edit'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-analysis.edit');
                Route::put('risk-reports/{reportId}/risk-analysis/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'update'])
                    ->middleware('check.permission:risk-management,can_create')
                    ->name('risk-analysis.update');
                Route::get('risk-reports/{reportId}/risk-analysis/{id}/qr-code', [App\Http\Controllers\Modules\RiskManagement\RiskAnalysisController::class, 'generateQr'])
                    ->name('risk-analysis.qr-code');
            });
        });
    });
});

Route::middleware(['auth'])->group(function () {
    Route::resource('tenants', TenantController::class);
});

// Route debug dashboard tanpa middleware tenant
Route::get('/dashboard-debug', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard.debug');

// Autologin route yang ditingkatkan - lebih banyak logging dan handling
Route::get('/autologin/{token}', function ($token) {
    \Illuminate\Support\Facades\Log::info('Mencoba autologin', ['token' => $token]);

    $data = \Illuminate\Support\Facades\Cache::get('login_token_' . $token);

    if (!$data) {
        \Illuminate\Support\Facades\Log::warning('Token login tidak valid atau sudah kedaluwarsa');
        return redirect()->route('login')->with('error', 'Token login tidak valid atau sudah kedaluwarsa');
    }

    $user = \App\Models\User::find($data['user_id']);

    if (!$user || $user->email !== $data['email']) {
        \Illuminate\Support\Facades\Log::warning('User tidak ditemukan', [
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'] ?? null
        ]);
        return redirect()->route('login')->with('error', 'User tidak ditemukan');
    }

    \Illuminate\Support\Facades\Log::info('User ditemukan, mencoba login', [
        'user_id' => $user->id,
        'email' => $user->email
    ]);

    auth()->login($user);

    if (auth()->check()) {
        \Illuminate\Support\Facades\Log::info('Login berhasil, user terautentikasi', [
            'user_id' => auth()->id()
        ]);
    } else {
        \Illuminate\Support\Facades\Log::error('Login gagal, user tidak terautentikasi');
    }

    \Illuminate\Support\Facades\Cache::forget('login_token_' . $token);

    // Periksa role dan arahkan ke dashboard yang sesuai
    if ($user->role && $user->role->slug === 'superadmin') {
        \Illuminate\Support\Facades\Log::info('User superadmin, mengarahkan ke dashboard superadmin');
        return redirect()->route('superadmin.dashboard');
    }

    // Redirect ke dashboard biasa untuk user non-superadmin
    return redirect()->route('dashboard.debug');
})->middleware('web')->name('autologin');

// Route login langsung
Route::get('/direct-login', function () {
    $user = \App\Models\User::where('email', 'superadmin@siar.com')->first();

    if (!$user) {
        return response()->json(['error' => 'User superadmin tidak ditemukan'], 404);
    }

    \Illuminate\Support\Facades\Log::info('Direct login mencoba login user', [
        'user_id' => $user->id,
        'email' => $user->email
    ]);

    auth()->login($user);

    if (auth()->check()) {
        \Illuminate\Support\Facades\Log::info('Direct login berhasil');

        // Karena ini adalah superadmin, arahkan langsung ke dashboard superadmin
        return redirect()->route('superadmin.dashboard');
    } else {
        \Illuminate\Support\Facades\Log::error('Direct login gagal');
        return response()->json(['error' => 'Login gagal'], 500);
    }
})->name('direct.login');

require __DIR__ . '/auth.php';

// Tenant Registration Routes
Route::prefix('tenant')->name('tenant.')->group(function () {
    Route::get('register', [App\Http\Controllers\TenantRegistrationController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [App\Http\Controllers\TenantRegistrationController::class, 'register'])->name('register.submit');
    Route::get('registration-success', [App\Http\Controllers\TenantRegistrationController::class, 'success'])->name('registration.success');
});

// Activity Log Routes
Route::prefix('logs')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/{activity}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::delete('/{activity}', [App\Http\Controllers\ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    Route::post('/purge', [App\Http\Controllers\ActivityLogController::class, 'purge'])->name('activity-logs.purge');
});

// Tenant routes - akses melalui subdomain
Route::domain('{subdomain}.' . env('APP_DOMAIN'))->group(function () {
    // Register tenant routes here
});

// Route untuk halaman modul dan permintaan aktivasi
Route::middleware(['auth', 'tenant'])->group(function () {
    // Halaman Manajemen Modul
    Route::get('/modules', [App\Http\Controllers\ModuleController::class, 'index'])->name('modules.index');

    // Pengajuan Aktivasi Modul
    Route::post('/modules/request-activation', [App\Http\Controllers\ModuleController::class, 'requestActivation'])->name('modules.request-activation');

    // Alias route untuk pengajuan modul dari Admin RS (backward compatibility)
    Route::post('/tenant/module/request', [App\Http\Controllers\TenantController::class, 'requestModule'])->name('tenant.module.request');
});

// Tenant routes
Route::middleware(['auth'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/profile', [App\Http\Controllers\TenantController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\TenantController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [App\Http\Controllers\TenantController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\TenantController::class, 'updateSettings'])->name('settings.update');
});

// Tenant Management Routes
Route::middleware(['auth', 'tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    // Unit Kerja Management
    Route::get('work-units', [App\Http\Controllers\Tenant\WorkUnitController::class, 'index'])->name('work-units.index');
    Route::get('work-units/create', [App\Http\Controllers\Tenant\WorkUnitController::class, 'create'])->name('work-units.create');
    Route::post('work-units', [App\Http\Controllers\Tenant\WorkUnitController::class, 'store'])->name('work-units.store');
    Route::get('work-units/{workUnit}', [App\Http\Controllers\Tenant\WorkUnitController::class, 'show'])->name('work-units.show');
    Route::get('work-units/{workUnit}/edit', [App\Http\Controllers\Tenant\WorkUnitController::class, 'edit'])->name('work-units.edit');
    Route::put('work-units/{workUnit}', [App\Http\Controllers\Tenant\WorkUnitController::class, 'update'])->name('work-units.update');
    Route::delete('work-units/{workUnit}', [App\Http\Controllers\Tenant\WorkUnitController::class, 'destroy'])->name('work-units.destroy');
    Route::post('work-units/update-order', [App\Http\Controllers\Tenant\WorkUnitController::class, 'updateOrder'])->name('work-units.update-order');
    Route::post('work-units/{workUnit}/toggle-status', [App\Http\Controllers\Tenant\WorkUnitController::class, 'toggleStatus'])->name('work-units.toggle-status');
});
