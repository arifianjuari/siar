<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class TenantMonitoringController extends Controller
{
    /**
     * Menampilkan halaman monitoring tenant.
     */
    public function index(Request $request)
    {
        // Dapatkan semua tenant dengan load relasi yang diperlukan
        $tenantsQuery = Tenant::query()
            ->with(['modules' => function ($q) {
                $q->wherePivot('is_active', true);
            }])
            ->withCount('users');

        // Filter berdasarkan nama jika ada
        if ($request->has('search') && !empty($request->search)) {
            $tenantsQuery->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('domain', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $isActive = $request->status === 'active';
            $tenantsQuery->where('is_active', $isActive);
        }

        // Dapatkan tenants dengan pagination
        $tenants = $tenantsQuery->orderBy('name')->paginate(10);

        // Tambahkan data monitor untuk setiap tenant
        foreach ($tenants as $tenant) {
            // Dapatkan admin tenant
            $adminUsers = User::where('tenant_id', $tenant->id)
                ->whereHas('role', function ($q) {
                    $q->where('slug', 'tenant-admin')
                        ->orWhere('code', 'tenant-admin');
                })
                ->get();

            // Informasi login terakhir admin
            $lastLoginAdmin = $adminUsers->sortByDesc('last_login_at')->first();
            $tenant->last_admin_login = $lastLoginAdmin ? $lastLoginAdmin->last_login_at : null;
            $tenant->last_admin_name = $lastLoginAdmin ? $lastLoginAdmin->name : 'N/A';

            // Dapatkan jumlah data per modul
            $tenant->module_data_counts = $this->getModuleDataCounts($tenant);

            // Statistik aktivitas (7 hari terakhir)
            $activitiesCount = Activity::where('properties->tenant_id', $tenant->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            $tenant->recent_activities = $activitiesCount;

            // Simpan daftar admin untuk tenant
            $tenant->admin_users = $adminUsers;
        }

        // Dapatkan semua modul untuk filter
        $modules = Module::orderBy('name')->get();

        return view('superadmin.tenants.monitor', compact('tenants', 'modules'));
    }

    /**
     * Mendapatkan detail monitoring untuk tenant tertentu.
     */
    public function show(Tenant $tenant)
    {
        // Load relasi yang diperlukan
        $tenant->load([
            'modules' => function ($q) {
                $q->wherePivot('is_active', true);
            },
            'users' => function ($q) {
                $q->withTrashed();
            },
            'roles'
        ]);

        // Dapatkan admin tenant
        $adminUsers = User::where('tenant_id', $tenant->id)
            ->whereHas('role', function ($q) {
                $q->where('slug', 'tenant-admin')
                    ->orWhere('code', 'tenant-admin');
            })
            ->get();

        // Informasi login terakhir admin
        $lastLoginAdmin = $adminUsers->sortByDesc('last_login_at')->first();
        $tenant->last_admin_login = $lastLoginAdmin ? $lastLoginAdmin->last_login_at : null;
        $tenant->last_admin_name = $lastLoginAdmin ? $lastLoginAdmin->name : 'N/A';

        // Statistik user
        $userCount = $tenant->users()->count();
        $usersByRole = $tenant->users()
            ->select('roles.name', DB::raw('count(*) as total'))
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get()
            ->pluck('total', 'name')
            ->toArray();

        // Statistik aktivitas (30 hari terakhir)
        $recentActivities = Activity::where('properties->tenant_id', $tenant->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // Aktifitas login (30 hari terakhir)
        $loginActivities = Activity::where('properties->tenant_id', $tenant->id)
            ->where('description', 'login')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        // Dapatkan jumlah data per modul
        $moduleDataCounts = $this->getModuleDataCounts($tenant);

        // Statistik login harian untuk chart
        $dailyLogins = Activity::where('properties->tenant_id', $tenant->id)
            ->where('description', 'login')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return view('superadmin.tenants.monitor-detail', compact(
            'tenant',
            'adminUsers',
            'userCount',
            'usersByRole',
            'recentActivities',
            'loginActivities',
            'moduleDataCounts',
            'dailyLogins'
        ));
    }

    /**
     * Mendapatkan jumlah data per modul untuk tenant tertentu.
     */
    protected function getModuleDataCounts(Tenant $tenant)
    {
        $counts = [];
        $activeModules = $tenant->modules()->wherePivot('is_active', true)->get();

        foreach ($activeModules as $module) {
            // Kita perlu pemetaan khusus dari kode modul ke tabel database
            // Ini hanya contoh, perlu disesuaikan dengan struktur aplikasi Anda
            switch ($module->code) {
                case 'user-management':
                    $counts[$module->name] = User::where('tenant_id', $tenant->id)->count();
                    break;

                // Tambahkan case untuk modul lain seperti yang diperlukan...

                default:
                    // Default metode - mencoba menebak nama tabel dari kode modul
                    $tableName = str_replace('-', '_', $module->code);

                    try {
                        if (DB::getSchemaBuilder()->hasTable($tableName)) {
                            $counts[$module->name] = DB::table($tableName)
                                ->where('tenant_id', $tenant->id)
                                ->count();
                        } else {
                            $counts[$module->name] = 'N/A';
                        }
                    } catch (\Exception $e) {
                        $counts[$module->name] = 'Error';
                    }
                    break;
            }
        }

        return $counts;
    }
}
