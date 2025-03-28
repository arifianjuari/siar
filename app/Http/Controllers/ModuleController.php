<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Cek apakah user adalah tenant admin
            if (!auth()->user()->role || auth()->user()->role->slug !== 'tenant-admin') {
                \Illuminate\Support\Facades\Log::warning('Akses ditolak: User bukan tenant admin mencoba mengakses manajemen modul', [
                    'user_id' => auth()->id(),
                    'role' => auth()->user()->role ? auth()->user()->role->name : 'No Role',
                    'url' => $request->fullUrl()
                ]);
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses manajemen modul');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar modul yang tersedia untuk tenant
     */
    public function index()
    {
        // Dapatkan tenant saat ini
        $tenant_id = session('tenant_id');
        $tenant = Tenant::find($tenant_id);

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        // Ambil modul yang dimiliki tenant
        $tenantModules = $tenant->modules()->get();

        // Pastikan setiap modul memiliki slug
        foreach ($tenantModules as $mod) {
            if (empty($mod->slug) && !empty($mod->code)) {
                $mod->slug = Str::slug($mod->code);
                $mod->save();
            }
        }

        // Ambil semua modul
        $allModules = Module::all();

        // Pastikan semua modul memiliki slug
        foreach ($allModules as $mod) {
            if (empty($mod->slug) && !empty($mod->code)) {
                $mod->slug = Str::slug($mod->code);
                $mod->save();
            }
        }

        // Filter modul yang belum dimiliki tenant
        $inactiveModules = $allModules->filter(function ($module) use ($tenantModules) {
            return !$tenantModules->contains('id', $module->id);
        });

        // Tampilkan view
        return view('modules.index', compact('tenant', 'tenantModules', 'inactiveModules'));
    }

    /**
     * Mengajukan permintaan aktivasi modul
     */
    public function requestActivation(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Menerima permintaan aktivasi modul', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'tenant_id' => session('tenant_id')
        ]);

        // Validasi request
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Modul yang dipilih tidak valid.')
                ->withErrors($validator);
        }

        // Verifikasi adanya tenant_id di session
        $tenant_id = session('tenant_id');
        if (!$tenant_id) {
            return redirect()->back()
                ->with('error', 'Tenant ID tidak ditemukan. Silakan logout dan login kembali.');
        }

        try {
            // Dapatkan tenant dan modul
            $tenant = Tenant::findOrFail($tenant_id);
            $module = Module::findOrFail($request->module_id);

            // Cek apakah tenant sudah memiliki modul ini
            if ($tenant->modules()->where('module_id', $module->id)->exists()) {
                return redirect()->back()
                    ->with('error', 'Modul sudah ada dalam daftar.');
            }

            // Tambahkan modul ke tenant dengan status tidak aktif (menunggu persetujuan)
            $tenant->modules()->attach($module->id, [
                'is_active' => false,
                'requested_at' => now(),
                'requested_by' => auth()->id(),
            ]);

            // Tambahkan log aktivitas
            ActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => auth()->id(),
                'action' => 'request_module',
                'model_type' => 'Module',
                'model_id' => $module->id,
                'description' => 'Mengajukan permintaan aktivasi modul ' . $module->name,
            ]);

            return redirect()->back()
                ->with('success', 'Pengajuan modul ' . $module->name . ' berhasil dikirim ke Superadmin.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail modul berdasarkan slug
     */
    public function show($slug)
    {
        $tenant_id = session('tenant_id');
        $tenant = Tenant::find($tenant_id);

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        // Cari modul berdasarkan slug
        $module = Module::where('slug', $slug)->first();

        if (!$module) {
            return redirect()->route('modules.index')->with('error', 'Modul tidak ditemukan');
        }

        // Cek apakah modul aktif untuk tenant ini
        $tenantModule = $tenant->modules()
            ->where('modules.id', $module->id)
            ->first();

        if (!$tenantModule) {
            return redirect()->route('modules.index')->with('error', 'Anda tidak memiliki akses ke modul ini');
        }

        // Pastikan atribut pivot tersedia
        if (!$tenantModule->pivot) {
            return redirect()->route('modules.index')->with('error', 'Terjadi kesalahan dalam mengakses data modul');
        }

        // Redirect ke dashboard modul atau tampilkan halaman modul
        return view('modules.show', compact('tenantModule', 'tenant'));
    }
}
