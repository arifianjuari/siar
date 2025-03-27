<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class TenantRegistrationController extends Controller
{
    /**
     * Menampilkan form pendaftaran tenant
     */
    public function showRegistrationForm()
    {
        return view('tenant.register');
    }

    /**
     * Handle pendaftaran tenant baru
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:100',
            'domain' => 'required|string|max:100|unique:tenants,domain',
            'admin_name' => 'required|string|max:100',
            'admin_email' => 'required|email|max:100|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('admin_password', 'admin_password_confirmation'));
        }

        try {
            // Jalankan command provisioning tenant
            $exitCode = Artisan::call('tenant:provision', [
                '--name' => $request->institution_name,
                '--domain' => $request->domain,
                '--admin-name' => $request->admin_name,
                '--admin-email' => $request->admin_email,
                '--admin-password' => $request->admin_password,
            ]);

            if ($exitCode !== 0) {
                return redirect()->back()
                    ->with('error', 'Gagal membuat tenant. Silakan coba lagi.')
                    ->withInput($request->except('admin_password', 'admin_password_confirmation'));
            }

            // Buat URL tenant yang baru dibuat
            $tenantUrl = config('app.url_scheme', 'http://') . $request->domain . '.' . config('app.url_base', 'localhost');

            // Redirect ke halaman sukses dengan informasi tenant
            return redirect()->route('tenant.registration.success')
                ->with('tenant_info', [
                    'name' => $request->institution_name,
                    'domain' => $request->domain,
                    'admin_email' => $request->admin_email,
                    'tenant_url' => $tenantUrl
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput($request->except('admin_password', 'admin_password_confirmation'));
        }
    }

    /**
     * Menampilkan halaman sukses pendaftaran
     */
    public function success()
    {
        if (!session()->has('tenant_info')) {
            return redirect()->route('tenant.register');
        }

        return view('tenant.registration-success');
    }
}
