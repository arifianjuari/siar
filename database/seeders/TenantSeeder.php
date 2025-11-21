<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'System',
                'domain' => 'system',
                'database' => 'system',
                'description' => 'Sistem utama untuk pengelolaan global',
                'address' => 'Jl. Sistem No.1',
                'city' => 'Malang',
                'phone' => '081111111111',
                'email' => 'system@siar.test',
                'logo' => 'system_logo.png',
                'letter_head' => 'Sistem Kesehatan Nasional',
                'settings' => json_encode(['timezone' => 'Asia/Jakarta']),
                'admin_name' => 'Superadmin',
                'admin_email' => 'superadmin@siar.com',
                'admin_password' => 'asdfasdf',
            ],
            [
                'name' => 'RS Bhayangkara Tk.III Hasta Brata Batu',
                'domain' => 'rsbbatu',
                'database' => 'rsbbatu',
                'description' => 'Rumah Sakit Bhayangkara Kota Batu',
                'address' => 'Jl. Sultan Agung No.9, Batu',
                'city' => 'Batu',
                'phone' => '082222222222',
                'email' => 'info@rsbbatu.id',
                'logo' => 'rsbb_logo.png',
                'letter_head' => 'RS Bhayangkara Tk.III Hasta Brata Batu',
                'settings' => json_encode(['timezone' => 'Asia/Jakarta']),
                'admin_name' => 'Admin RSBB',
                'admin_email' => 'adminrsbbatu@gmail.com',
                'admin_password' => 'asdfasdf',
            ],
        ];

        $modules = Module::all();

        foreach ($tenants as $data) {
            // Cek apakah tenant dengan domain yang sama sudah ada
            $tenant = Tenant::firstOrCreate(
                ['domain' => $data['domain']],
                [
                    'name' => $data['name'],
                    'database' => $data['database'],
                    'description' => $data['description'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'logo' => $data['logo'],
                    'letter_head' => $data['letter_head'],
                    'settings' => $data['settings'],
                    'is_active' => true,
                ]
            );

            if ($tenant->wasRecentlyCreated) {
                $this->command->info("Tenant {$tenant->name} baru berhasil dibuat.");
            } else {
                $this->command->info("Tenant {$tenant->name} sudah ada, melewati pembuatan tenant.");
                continue; // Lanjut ke tenant berikutnya jika sudah ada
            }

            // Aktifkan semua modul
            foreach ($modules as $module) {
                $tenant->activateModule($module->id);
            }

            // Cek apakah role dengan slug tenant-admin sudah ada untuk tenant ini
            $adminRole = Role::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'slug' => 'tenant-admin'
                ],
                [
                    'name' => 'Tenant Admin',
                    'description' => 'Administrator ' . $tenant->name,
                    'is_active' => true,
                ]
            );

            // Permission full untuk semua modul
            foreach ($modules as $module) {
                RoleModulePermission::updateOrCreate(
                    [
                        'role_id' => $adminRole->id,
                        'module_id' => $module->id
                    ],
                    [
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                );
            }

            // Cek apakah user admin dengan email yang sama sudah ada
            $user = User::firstOrCreate(
                ['email' => $data['admin_email']],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $adminRole->id,
                    'name' => $data['admin_name'],
                    'password' => Hash::make($data['admin_password']),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("Tenant {$tenant->name}, role dan admin berhasil dibuat atau diperbarui.");
        }
    }
}
