<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Module;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Cek apakah user adalah tenant admin
            if (!auth()->user()->role || auth()->user()->role->slug !== 'tenant_admin') {
                Log::warning('Akses ditolak: User bukan tenant admin mencoba mengakses pengaturan tenant', [
                    'user_id' => auth()->id(),
                    'role' => auth()->user()->role ? auth()->user()->role->name : 'No Role',
                    'url' => $request->fullUrl()
                ]);
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses pengaturan tenant');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $tenants = Tenant::all();
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants',
            'database' => 'required|string|max:255|unique:tenants',
        ]);

        DB::beginTransaction();
        try {
            $tenant = Tenant::create($request->all());

            // Buat database baru untuk tenant
            DB::statement("CREATE DATABASE IF NOT EXISTS {$tenant->database}");

            // Jalankan migrasi untuk database tenant
            $this->runMigrations($tenant->database);

            DB::commit();
            return redirect()->route('tenants.index')->with('success', 'Tenant berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tenant: ' . $e->getMessage());
        }
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $tenant->id,
            'database' => 'required|string|max:255|unique:tenants,database,' . $tenant->id,
        ]);

        $tenant->update($request->all());
        return redirect()->route('tenants.index')->with('success', 'Tenant berhasil diperbarui');
    }

    public function destroy(Tenant $tenant)
    {
        DB::beginTransaction();
        try {
            // Hapus database tenant
            DB::statement("DROP DATABASE IF EXISTS {$tenant->database}");

            // Hapus tenant
            $tenant->delete();

            DB::commit();
            return redirect()->route('tenants.index')->with('success', 'Tenant berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus tenant: ' . $e->getMessage());
        }
    }

    private function runMigrations($database)
    {
        $originalDatabase = config('database.connections.mysql.database');
        config(['database.connections.mysql.database' => $database]);

        Artisan::call('migrate', [
            '--database' => 'mysql',
            '--path' => 'database/migrations',
            '--force' => true,
        ]);

        config(['database.connections.mysql.database' => $originalDatabase]);
    }

    /**
     * Menangani pengajuan modul dari Admin RS ke Superadmin
     */
    public function requestModule(Request $request)
    {
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

            // Notifikasi ke Superadmin (bisa diimplementasikan di sini)

            return redirect()->back()
                ->with('success', 'Pengajuan modul ' . $module->name . ' berhasil dikirim ke Superadmin.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman profil tenant
     */
    public function profile()
    {
        $tenant = Tenant::find(session('tenant_id'));

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        return view('tenant.profile', compact('tenant'));
    }

    /**
     * Update profil tenant
     */
    public function updateProfile(Request $request)
    {
        $tenant = Tenant::find(session('tenant_id'));

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update data tenant
        $tenant->name = $validated['name'];
        $tenant->description = $validated['description'];
        $tenant->address = $validated['address'];
        $tenant->phone = $validated['phone'];
        $tenant->email = $validated['email'];

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }

            $logo = $request->file('logo');
            $logoName = 'tenant_logo_' . $tenant->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('tenant_logos', $logoName, 'public');

            $tenant->logo = $logoPath;
        }

        $tenant->save();

        return redirect()->route('tenant.profile')->with('success', 'Profil tenant berhasil diperbarui');
    }

    /**
     * Tampilkan halaman pengaturan tenant
     */
    public function settings()
    {
        $tenant = Tenant::find(session('tenant_id'));

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        return view('tenant.settings', compact('tenant'));
    }

    /**
     * Update pengaturan tenant
     */
    public function updateSettings(Request $request)
    {
        $tenant = Tenant::find(session('tenant_id'));

        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Tenant tidak ditemukan');
        }

        $validated = $request->validate([
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:10',
            'language' => 'nullable|string|max:10',
        ]);

        // Update settings (simpan dalam kolom settings - json)
        $settings = $tenant->settings ?? [];
        $settings = array_merge($settings, $validated);

        $tenant->settings = $settings;
        $tenant->save();

        return redirect()->route('tenant.settings')->with('success', 'Pengaturan tenant berhasil diperbarui');
    }
}
