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
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TenantController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Cek apakah user adalah tenant admin
            if (!auth()->user()->role || auth()->user()->role->slug !== 'tenant-admin') {
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

        return view('roles.tenant.profile', compact('tenant'));
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
            'city' => 'nullable|string|max:100',
            'ceo' => 'nullable|string|max:255',
            'ceo_rank' => 'nullable|string|max:100',
            'ceo_nrp' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'letter_head' => 'nullable|string',
        ]);

        // Update data tenant
        $tenant->name = $validated['name'];
        $tenant->description = $validated['description'];
        $tenant->address = $validated['address'];
        $tenant->city = $validated['city'];
        $tenant->ceo = $validated['ceo'];
        $tenant->ceo_rank = $validated['ceo_rank'];
        $tenant->ceo_nrp = $validated['ceo_nrp'];
        $tenant->phone = $validated['phone'];
        $tenant->email = $validated['email'];
        $tenant->letter_head = $validated['letter_head'];

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            try {
                // Hapus logo lama jika ada
                if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                    Storage::disk('public')->delete($tenant->logo);
                }

                $logo = $request->file('logo');

                // Validasi ukuran dan tipe file lagi untuk keamanan
                if (!$logo->isValid() || $logo->getSize() > 2048 * 1024) {
                    return redirect()->back()->with('error', 'Logo tidak valid atau ukurannya terlalu besar (maks 2MB)');
                }

                // Dapatkan ekstensi file
                $extension = strtolower($logo->getClientOriginalExtension());

                // Validasi ekstensi file
                if (empty($extension) || !in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    Log::error('Ekstensi file tidak valid atau kosong: ' . $extension);
                    return redirect()->back()->with('error', 'Format file tidak valid. Gunakan JPG, PNG, atau GIF.');
                }

                // Gunakan slug nama tenant untuk nama file yang lebih aman
                $slugName = preg_replace('/[^a-z0-9-]/', '', Str::slug($tenant->name));
                // Batasi panjang nama file
                $slugName = substr($slugName, 0, 30);
                $timestamp = time();
                $logoName = 'logo_' . $slugName . '.' . $extension;

                // Pastikan tidak ada karakter line break
                $logoName = str_replace(["\r", "\n", "\t", " "], "", $logoName);

                // Simpan logo di folder images publik
                $logoPath = 'tenant_logos/' . $logoName;
                $publicPath = 'images/' . $logoName;

                // Cek dan buat direktori jika belum ada
                $directory = storage_path('app/public/tenant_logos');
                if (!file_exists($directory)) {
                    if (!mkdir($directory, 0777, true)) {
                        Log::error("Gagal membuat direktori tenant_logos");
                        return redirect()->back()->with('error', 'Gagal membuat direktori penyimpanan logo');
                    }
                }

                // Pastikan direktori publik juga ada
                $publicDirectory = public_path('images');
                if (!file_exists($publicDirectory)) {
                    if (!mkdir($publicDirectory, 0777, true)) {
                        Log::error("Gagal membuat direktori images publik");
                        return redirect()->back()->with('error', 'Gagal membuat direktori penyimpanan logo publik');
                    }
                }

                // Simpan dan resize gambar dengan Intervention Image
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($logo);

                    // Resize ke maksimal 400px lebar, tinggi menyesuaikan dengan aspek rasio
                    $img->scale(width: 400);

                    // Tentukan format output berdasarkan ekstensi
                    $format = $extension;
                    if ($extension === 'jpg') {
                        $format = 'jpeg';
                    }

                    // Simpan file dengan format yang ditentukan
                    if ($format === 'jpeg' || $format === 'jpg') {
                        // Simpan di storage
                        $img->toJpeg(80)->save(storage_path('app/public/' . $logoPath));

                        // Simpan juga di direktori publik
                        $img->toJpeg(80)->save(public_path($publicPath));
                    } elseif ($format === 'png') {
                        // Simpan di storage
                        $img->toPng()->save(storage_path('app/public/' . $logoPath));

                        // Simpan juga di direktori publik
                        $img->toPng()->save(public_path($publicPath));
                    } elseif ($format === 'gif') {
                        // Simpan di storage
                        $img->toGif()->save(storage_path('app/public/' . $logoPath));

                        // Simpan juga di direktori publik
                        $img->toGif()->save(public_path($publicPath));
                    } else {
                        // Default ke JPG jika format tidak dikenali
                        // Simpan di storage
                        $img->toJpeg(80)->save(storage_path('app/public/' . $logoPath));

                        // Simpan juga di direktori publik
                        $img->toJpeg(80)->save(public_path($publicPath));
                    }

                    // Pastikan file benar-benar disimpan
                    $savedFilePath = storage_path('app/public/' . $logoPath);
                    $savedPublicPath = public_path($publicPath);

                    if (!file_exists($savedFilePath) && !file_exists($savedPublicPath)) {
                        Log::error("File logo gagal disimpan: " . $savedFilePath . " and " . $savedPublicPath);
                        throw new \Exception('File logo gagal disimpan ke direktori.');
                    }

                    // Pastikan setidaknya file publik ada dan dapat dibaca
                    if (file_exists($savedPublicPath) && !is_readable($savedPublicPath)) {
                        Log::error("File logo publik tidak dapat dibaca: " . $savedPublicPath);
                        chmod($savedPublicPath, 0644);
                    }

                    // Debug: log detail file yang berhasil disimpan
                    Log::info("Logo berhasil disimpan", [
                        'storage_path' => $logoPath,
                        'public_path' => $publicPath,
                        'full_storage_path' => $savedFilePath,
                        'full_public_path' => $savedPublicPath,
                        'file_exists_in_storage' => file_exists($savedFilePath) ? 'Yes' : 'No',
                        'file_exists_in_public' => file_exists($savedPublicPath) ? 'Yes' : 'No',
                    ]);

                    // Simpan path di database
                    $tenant->logo = $logoPath;
                } catch (\Exception $e) {
                    Log::error('Error saat memproses gambar: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses logo: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                Log::error('Error saat upload logo tenant: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload logo: ' . $e->getMessage());
            }
        }

        $tenant->save();

        return redirect()->route('tenant.profile', ['refresh' => time()])->with('success', 'Profil tenant berhasil diperbarui');
    }

    /**
     * Stream logo tenant langsung dari storage tanpa bergantung pada symlink.
     */
    public function logo(Request $request)
    {
        $tenant = Tenant::find(session('tenant_id'));
        if (!$tenant || !$tenant->logo) {
            abort(404);
        }

        try {
            $storagePath = storage_path('app/public/' . $tenant->logo); // e.g. tenant_logos/filename
            if (!file_exists($storagePath)) {
                // Coba juga path di public/images (jika ada)
                $fallbackPublic = public_path('images/' . basename($tenant->logo));
                if (file_exists($fallbackPublic)) {
                    return response()->file($fallbackPublic, [
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);
                }
                abort(404);
            }

            // Tentukan mime type sederhana dari ekstensi
            $ext = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => 'application/octet-stream',
            };

            return response()->file($storagePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menayangkan logo tenant', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant?->id,
                'logo' => $tenant?->logo,
            ]);
            abort(404);
        }
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
