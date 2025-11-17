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
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TenantRoleController;

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
})->name('welcome');

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

// Debug route untuk cek konfigurasi session
Route::get('/debug-config', function () {
    try {
        $sessionsCount = DB::table('sessions')->count();
    } catch (\Exception $e) {
        $sessionsCount = 'Error: ' . $e->getMessage();
    }
    
    return response()->json([
        'session_config' => [
            'driver' => config('session.driver'),
            'domain' => config('session.domain'),
            'secure' => config('session.secure'),
            'same_site' => config('session.same_site'),
            'lifetime' => config('session.lifetime'),
            'cookie' => config('session.cookie'),
        ],
        'env_vars' => [
            'SESSION_DRIVER' => env('SESSION_DRIVER'),
            'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
            'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
            'APP_URL' => env('APP_URL'),
            'APP_DOMAIN' => env('APP_DOMAIN'),
            'APP_ENV' => env('APP_ENV'),
        ],
        'sessions_table' => [
            'count' => $sessionsCount,
        ],
        'request_info' => [
            'url' => request()->url(),
            'secure' => request()->secure(),
            'host' => request()->getHost(),
            'scheme' => request()->getScheme(),
        ],
    ]);
});

// Route untuk clear semua cookie session (untuk testing)
Route::get('/clear-session-cookies', function () {
    $response = response()->json([
        'message' => 'Silakan hapus cookie secara manual di browser DevTools > Application > Cookies',
        'instructions' => [
            '1. Buka Developer Tools (F12)',
            '2. Buka tab Application > Cookies',
            '3. Hapus semua cookie yang ada, terutama siar_session (ada 2 duplikat)',
            '4. Refresh halaman dan coba login lagi',
        ],
        'current_cookies' => request()->cookies->all(),
    ]);
    
    // Set cookie dengan Max-Age 0 untuk menghapus cookie
    $cookieName = config('session.cookie');
    $response->cookie($cookieName, '', -1, '/', null, true, true, false, 'Lax');
    
    return $response;
})->middleware('web');

// Debug route untuk cek apakah user sudah login
Route::get('/debug-auth', function () {
    $session = request()->session();
    $sessionCookieName = config('session.cookie');
    
    // Cek session di database
    $sessionInDb = null;
    try {
        if (config('session.driver') === 'database') {
            $sessionInDb = DB::table('sessions')
                ->where('id', $session->getId())
                ->first();
        }
    } catch (\Exception $e) {
        $sessionInDb = 'Error: ' . $e->getMessage();
    }
    
    return response()->json([
        'auth_check' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role ? [
                'id' => auth()->user()->role->id,
                'name' => auth()->user()->role->name,
                'slug' => auth()->user()->role->slug,
            ] : null,
            'tenant' => auth()->user()->tenant ? [
                'id' => auth()->user()->tenant->id,
                'name' => auth()->user()->tenant->name,
            ] : null,
        ] : null,
        'session_info' => [
            'session_id' => $session->getId(),
            'session_cookie_name' => $sessionCookieName,
            'has_session_cookie' => request()->hasCookie($sessionCookieName),
            'session_cookie_value' => request()->cookie($sessionCookieName) ? 'exists (length: ' . strlen(request()->cookie($sessionCookieName)) . ')' : 'missing',
            'session_has_auth_key' => $session->has('login_web_' . sha1('Illuminate\Auth\SessionGuard')),
            'all_cookies' => array_keys(request()->cookies->all()),
            'all_cookies_count' => count(request()->cookies->all()),
        ],
        'session_database' => [
            'exists_in_db' => is_object($sessionInDb),
            'user_id' => is_object($sessionInDb) ? $sessionInDb->user_id : null,
            'ip_address' => is_object($sessionInDb) ? $sessionInDb->ip_address : null,
            'last_activity' => is_object($sessionInDb) ? date('Y-m-d H:i:s', $sessionInDb->last_activity) : null,
            'payload_length' => is_object($sessionInDb) ? strlen($sessionInDb->payload) : null,
        ],
        'session_config' => [
            'driver' => config('session.driver'),
            'cookie' => config('session.cookie'),
            'domain' => config('session.domain'),
            'secure' => config('session.secure'),
            'same_site' => config('session.same_site'),
            'path' => config('session.path'),
            'http_only' => config('session.http_only'),
        ],
        'request_info' => [
            'host' => request()->getHost(),
            'scheme' => request()->getScheme(),
            'secure' => request()->secure(),
            'url' => request()->url(),
            'ip' => request()->ip(),
        ],
        'session_data_keys' => array_keys(session()->all()),
    ]);
})->middleware('web');

// Debug route untuk test session persistence
Route::get('/debug-session-test', function () {
    $testKey = 'debug_test_' . time();
    $testValue = 'test_value_' . rand(1000, 9999);
    
    // Set test value
    session()->put($testKey, $testValue);
    session()->save();
    
    // Read back
    $readValue = session()->get($testKey);
    
    return response()->json([
        'test' => [
            'key' => $testKey,
            'value_set' => $testValue,
            'value_read' => $readValue,
            'match' => $testValue === $readValue,
        ],
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'instruction' => 'Refresh halaman ini beberapa kali. Jika session_id berubah setiap refresh, berarti session tidak persist.',
    ]);
})->middleware('web');

// Debug route untuk cek database sessions
Route::get('/debug-database', function () {
    $errors = [];
    $sessionsData = null;
    $migrationsData = null;
    
    // Cek tabel sessions
    try {
        $sessionsData = [
            'exists' => true,
            'count' => DB::table('sessions')->count(),
            'recent_5' => DB::table('sessions')
                ->orderBy('last_activity', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => substr($session->id, 0, 20) . '...',
                        'user_id' => $session->user_id,
                        'ip_address' => $session->ip_address,
                        'last_activity' => date('Y-m-d H:i:s', $session->last_activity),
                        'payload_length' => strlen($session->payload),
                    ];
                }),
        ];
    } catch (\Exception $e) {
        $errors[] = 'Sessions table error: ' . $e->getMessage();
        $sessionsData = ['exists' => false, 'error' => $e->getMessage()];
    }
    
    // Cek migrations
    try {
        $migrationsData = DB::table('migrations')
            ->where('migration', 'like', '%sessions%')
            ->get(['migration', 'batch']);
    } catch (\Exception $e) {
        $errors[] = 'Migrations error: ' . $e->getMessage();
    }
    
    return response()->json([
        'database_connection' => [
            'default' => config('database.default'),
            'connection' => env('DB_CONNECTION'),
            'database' => env('DB_DATABASE'),
        ],
        'session_config' => [
            'driver' => config('session.driver'),
            'connection' => config('session.connection'),
            'table' => config('session.table'),
        ],
        'sessions_table' => $sessionsData,
        'migrations' => $migrationsData,
        'errors' => $errors,
        'current_session' => [
            'id' => session()->getId(),
            'data_keys' => array_keys(session()->all()),
        ],
    ]);
})->middleware('web');

// Route untuk create/reset superadmin user (TEMPORARY - HAPUS SETELAH PRODUCTION)
Route::get('/setup-superadmin', function () {
    try {
        // 1. Buat tenant system jika belum ada
        $tenant = \App\Models\Tenant::firstOrCreate(
            ['name' => 'System'],
            [
                'domain' => 'system',
                'database' => 'system',
                'is_active' => true,
            ]
        );

        // 2. Buat role superadmin jika belum ada
        $role = \App\Models\Role::firstOrCreate(
            ['slug' => 'superadmin', 'tenant_id' => $tenant->id],
            [
                'name' => 'Superadmin',
                'description' => 'Administrator Sistem dengan akses penuh',
                'is_active' => true,
            ]
        );

        // 3. Buat atau update user superadmin
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@siar.com'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => 'Superadmin',
                'password' => \Illuminate\Support\Facades\Hash::make('asdfasdf'),
                'is_active' => true,
            ]
        );

        // Reload user dengan relationship
        $user->load(['role', 'tenant']);

        return response()->json([
            'success' => true,
            'message' => 'Superadmin user berhasil dibuat/diperbarui!',
            'credentials' => [
                'email' => 'superadmin@siar.com',
                'password' => 'asdfasdf',
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ] : null,
                'tenant' => $user->tenant ? [
                    'id' => $user->tenant->id,
                    'name' => $user->tenant->name,
                ] : null,
            ],
            'middleware_check' => [
                'has_role' => $user->role ? true : false,
                'role_slug' => $user->role ? $user->role->slug : null,
                'is_superadmin' => $user->role && $user->role->slug === 'superadmin',
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant ? $user->tenant->name : null,
                'is_system_tenant' => $user->tenant && ($user->tenant->id === 1 || $user->tenant->name === 'System'),
                'will_pass_middleware' => $user->role && $user->role->slug === 'superadmin' && $user->tenant && ($user->tenant->id === 1 || $user->tenant->name === 'System'),
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

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

    return roleView('dashboard', 'pages.dashboard');
})->middleware('auth')->name('dashboard');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::get('/profile/photo/remove', [App\Http\Controllers\ProfileController::class, 'removePhoto'])->name('profile.remove-photo');
});

// Route khusus Superadmin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'superadmin'])->group(function () {
    // Dashboard Superadmin
    Route::get('/dashboard', function () {
        return view('roles.superadmin.dashboard');
    })->name('dashboard');

    // Tenant Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantManagementController::class, 'index'])->name('index');
        Route::get('/create', [TenantManagementController::class, 'create'])->name('create');
        Route::post('/', [TenantManagementController::class, 'store'])->name('store');
        Route::get('/{tenant}', [TenantManagementController::class, 'show'])->name('show');
        Route::get('/{tenant}/edit', [TenantManagementController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{tenant}', [TenantManagementController::class, 'update'])->name('update');
        Route::delete('/{tenant}', [TenantManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{tenant}/reset-admin-password', [TenantManagementController::class, 'resetAdminPassword'])->name('reset-admin-password');

        // Module Management
        Route::post('/{tenant}/toggle-module', [TenantManagementController::class, 'toggleModule'])->name('toggle-module');

        // Role Management
        Route::prefix('{tenant}/roles')->name('roles.')->group(function () {
            Route::get('/', [TenantRoleController::class, 'index'])->name('index');
            Route::get('/create', [TenantRoleController::class, 'create'])->name('create');
            Route::post('/', [TenantRoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [TenantRoleController::class, 'edit'])->name('edit');
            Route::match(['put', 'patch'], '/{role}', [TenantRoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [TenantRoleController::class, 'destroy'])->name('destroy');
            Route::get('/{role}/permissions/edit', [TenantRoleController::class, 'editPermissions'])->name('permissions.edit');
            Route::match(['put', 'patch'], '/{role}/permissions', [TenantRoleController::class, 'updatePermissions'])->name('permissions.update');
        });
    });

    // Risk Management
    Route::prefix('risk-management')->name('risk-management.')->group(function () {
        Route::get('reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'show'])->name('reports.show');
        Route::get('reports/create', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'create'])->name('reports.create');
        Route::post('reports', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{id}/edit', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'edit'])->name('reports.edit');
        Route::put('reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'update'])->name('reports.update');
        Route::delete('reports/{id}', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'destroy'])->name('reports.destroy');
    });

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
    // User Management Module - Routes didefinisikan di routes/modules/UserManagement.php
    // Route::prefix('user-management') dihapus karena sudah ada di UserManagement.php

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

        // Rute untuk hubungan dengan modul kegiatan
        Route::post('risk-reports/{id}/link-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'linkActivity'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.link-activity');
        Route::post('risk-reports/{id}/unlink-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'unlinkActivity'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.unlink-activity');
        Route::post('risk-reports/{id}/create-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'createActivityFromRisk'])
            ->middleware('check.permission:risk-management,can_edit')
            ->name('risk-reports.create-activity');
    });

    // Rute Manajemen Modul - PINDAHKAN KE BAWAH
    Route::get('/', [App\Http\Controllers\ModuleController::class, 'index'])->name('index');
    Route::post('/request-activation', [App\Http\Controllers\ModuleController::class, 'requestActivation'])->name('request-activation');

    // Route khusus untuk Korespondensi
    Route::get('/correspondence', [App\Http\Controllers\Modules\Correspondence\CorrespondenceController::class, 'index'])->name('correspondence.index');

    Route::get('/{slug}', [App\Http\Controllers\ModuleController::class, 'show'])->name('show');

    /**
     * Document Management Module
     */
    Route::prefix('document-management')->name('document-management.')->middleware('module:document-management')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Modules\DocumentManagement\DocumentManagementController::class, 'dashboard'])->name('dashboard');

        // Documents routes
        Route::resource('documents', \App\Http\Controllers\Modules\DocumentManagement\DocumentController::class)
            ->except(['show', 'edit', 'update']);
        
        // Custom routes dengan middleware tambahan
        Route::get('documents/{document}', [\App\Http\Controllers\Modules\DocumentManagement\DocumentController::class, 'show'])
            ->name('documents.show')
            ->middleware(['check.permission:document-management,can_view', 'tenant.document']);

        Route::get('documents/{document}/edit', [\App\Http\Controllers\Modules\DocumentManagement\DocumentController::class, 'edit'])
            ->name('documents.edit')
            ->middleware(['check.permission:document-management,can_edit', 'tenant.document']);

        Route::put('documents/{document}', [\App\Http\Controllers\Modules\DocumentManagement\DocumentController::class, 'update'])
            ->name('documents.update')
            ->middleware(['check.permission:document-management,can_edit', 'tenant.document']);

        // Dokumen Revisi Route
        Route::post('documents/{id}/revise', [\App\Http\Controllers\Modules\DocumentManagement\DocumentController::class, 'revise'])
            ->name('documents.revise')
            ->middleware(['check.permission:document-management,can_create', 'tenant.document']);

        // Documents By Tag Route
        Route::get('documents-by-tag/{slug}', [\App\Http\Controllers\Modules\DocumentManagement\DocumentManagementController::class, 'documentsByTag'])
            ->name('documents-by-tag');

        // Documents By Type Route
        Route::get('documents-by-type/{type}', [\App\Http\Controllers\Modules\DocumentManagement\DocumentManagementController::class, 'documentsByType'])
            ->name('documents-by-type');
    });

    /**
     * Correspondence Module - Routes didefinisikan di routes/modules/Correspondence.php
     * Route dihapus karena sudah ada di Correspondence.php untuk menghindari duplicate
     */
});

// Tenant routes - akses melalui subdomain
// Definisi domain tenant yang lebih fleksibel menggunakan env APP_DOMAIN
Route::domain('{tenant}.' . env('APP_URL_BASE'))->middleware(['tenant.resolve'])->group(function () {
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

                // Rute untuk hubungan dengan modul kegiatan
                Route::post('risk-reports/{id}/link-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'linkActivity'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.link-activity');
                Route::post('risk-reports/{id}/unlink-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'unlinkActivity'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.unlink-activity');
                Route::post('risk-reports/{id}/create-activity', [App\Http\Controllers\Modules\RiskManagement\RiskReportController::class, 'createActivityFromRisk'])
                    ->middleware('check.permission:risk-management,can_edit')
                    ->name('risk-reports.create-activity');
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

// Tenant routes - akses melalui subdomain (old code, commented out for reference)
// Route::domain('{subdomain}.' . env('APP_DOMAIN'))->group(function () {
//     // Register tenant routes here
// });

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
    Route::get('/profile', [App\Http\Controllers\TenantController::class, 'profile'])->middleware('disable.debug')->name('profile');
    Route::post('/profile', [App\Http\Controllers\TenantController::class, 'updateProfile'])->middleware('disable.debug')->name('profile.update');
    Route::get('/settings', [App\Http\Controllers\TenantController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\TenantController::class, 'updateSettings'])->name('settings.update');

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

    // Tag Management
    Route::get('tags', [App\Http\Controllers\Tenant\TagController::class, 'index'])->name('tags.index');
    Route::get('tags/create', [App\Http\Controllers\Tenant\TagController::class, 'create'])->name('tags.create');
    Route::post('tags', [App\Http\Controllers\Tenant\TagController::class, 'store'])->name('tags.store');
    Route::get('tags/{tag}', [App\Http\Controllers\Tenant\TagController::class, 'show'])->name('tags.show');
    Route::get('tags/{tag}/edit', [App\Http\Controllers\Tenant\TagController::class, 'edit'])->name('tags.edit');
    Route::put('tags/{tag}', [App\Http\Controllers\Tenant\TagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}', [App\Http\Controllers\Tenant\TagController::class, 'destroy'])->name('tags.destroy');
    Route::post('tags/update-order', [App\Http\Controllers\Tenant\TagController::class, 'updateOrder'])->name('tags.update-order');

    // Tag Document Relations
    Route::post('tags/attach-document', [App\Http\Controllers\Tenant\TagController::class, 'attachTagToDocument'])->name('tags.attach-document');
    Route::delete('tags/detach-document', [App\Http\Controllers\Tenant\TagController::class, 'attachTagToDocument'])->name('tags.detach-document');
    Route::post('tags/delete-tag', [App\Http\Controllers\Tenant\TagController::class, 'deleteTag'])->name('tags.delete-tag');
    Route::post('tags/create-and-attach', [App\Http\Controllers\Tenant\TagController::class, 'createAndAttachTag'])->name('tags.create-and-attach');
    Route::get('tags/documents/{slug}', [App\Http\Controllers\Tenant\TagController::class, 'getDocumentsByTag'])->name('tags.documents');

    // Document References Management
    Route::resource('document-references', App\Http\Controllers\Tenant\DocumentReferenceController::class);
});

// Static Pages
Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('pages.privacy');

Route::get('/terms-of-service', function () {
    return view('pages.terms');
})->name('pages.terms');

Route::get('/help', function () {
    return view('pages.help');
})->name('pages.help');

// Unit Kerja routes (dashboard terletak di modules/WorkUnit.php)
Route::middleware(['web', 'auth', 'tenant', 'check.permission:work-units,can_view'])
    ->get('/work-units-dashboard', [App\Http\Controllers\Modules\WorkUnitController::class, 'globalDashboard'])
    ->name('work-units.global-dashboard');

// Activity Management Module Routes
Route::middleware(['web', 'auth', 'tenant', 'module:activity-management'])
    ->prefix('activity-management')
    ->name('modules.activity-management.')
    ->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\Modules\ActivityManagement\DashboardController::class, 'index'])
            ->name('dashboard');

        // Activity routes
        Route::resource('activities', App\Http\Controllers\Modules\ActivityManagement\ActivityController::class);
        Route::put('activities/{uuid}/update-status', [App\Http\Controllers\Modules\ActivityManagement\ActivityController::class, 'updateStatus'])
            ->name('activities.update-status');

        // Activity comments
        Route::resource('comments', App\Http\Controllers\Modules\ActivityManagement\ActivityCommentController::class)
            ->except(['index', 'show', 'create']);
        Route::get('activities/{activity}/comments', [App\Http\Controllers\Modules\ActivityManagement\ActivityCommentController::class, 'index'])
            ->name('comments.index');

        // Activity assignees
        Route::resource('assignees', App\Http\Controllers\Modules\ActivityManagement\ActivityAssigneeController::class)
            ->except(['index', 'show', 'create']);
        Route::get('activities/{activity}/assignees', [App\Http\Controllers\Modules\ActivityManagement\ActivityAssigneeController::class, 'index'])
            ->name('assignees.index');

        // Actionable items
        Route::resource('actionable-items', App\Http\Controllers\Modules\ActivityManagement\ActionableItemController::class)
            ->except(['index', 'show', 'create']);
        Route::get('activities/{activity}/actionable-items', [App\Http\Controllers\Modules\ActivityManagement\ActionableItemController::class, 'index'])
            ->name('actionable-items.index');
    });

// Route langsung untuk dashboard SPO
Route::get('/work-units/spo/dashboard', [\App\Http\Controllers\Modules\WorkUnit\SPOController::class, 'dashboard'])
    ->name('work-units.spo.dashboard.direct')
    ->middleware(['web', 'auth', 'tenant']);

// Route untuk debugging gambar (gunakan middleware auth jika diinginkan)
Route::post('/log-image-error', function (Request $request) {
    Log::error('Image loading error', [
        'original_url' => $request->input('originalUrl'),
        'alternative_url' => $request->input('alternativeUrl'),
        'user_agent' => $request->header('User-Agent'),
        'ip' => $request->ip()
    ]);

    return response()->json(['status' => 'logged']);
})->middleware('web');

require __DIR__ . '/modules/ActivityManagement.php';
require __DIR__ . '/modules/WorkUnit.php';
require __DIR__ . '/modules/Correspondence.php';
require __DIR__ . '/modules/UserManagement.php';
require __DIR__ . '/modules/KendaliMutuBiaya.php';

// Tenant User Management Routes
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::prefix('superadmin/tenants/{tenant}/users')
        ->name('superadmin.tenants.users.')
        ->controller(App\Http\Controllers\TenantUserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{user}/edit', 'edit')->name('edit');
            Route::put('/{user}', 'update')->name('update');
            Route::delete('/{user}', 'destroy')->name('destroy');
            Route::post('/{user}/reset-password', 'resetPassword')->name('reset-password');
        });
});
