<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {domain} {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat tenant baru dengan modul dan user admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $email = $this->argument('email');
        $password = $this->argument('password') ?? 'password';

        // Cek apakah domain sudah digunakan
        if (Tenant::where('domain', $domain)->exists()) {
            $this->error("Domain {$domain} sudah digunakan oleh tenant lain!");
            return 1;
        }

        // Buat tenant
        $tenant = Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'is_active' => true,
        ]);

        $this->info("Tenant {$name} berhasil dibuat!");

        // Aktifkan semua modul untuk tenant ini
        $modules = Module::all();
        foreach ($modules as $module) {
            $tenant->activateModule($module->id);
        }
        $this->info("Semua modul telah diaktifkan untuk tenant {$name}");

        // Buat role admin (or get existing)
        $adminRole = Role::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'slug' => 'tenant-admin',
            ],
            [
                'name' => "Admin {$name}",
                'description' => "Role admin untuk {$name}",
                'is_active' => true,
            ]
        );
        $this->info("Role admin berhasil dibuat!");

        // Berikan semua permission untuk role admin
        foreach ($modules as $module) {
            RoleModulePermission::create([
                'role_id' => $adminRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }
        $this->info("Semua permission telah diberikan ke role admin!");

        // Buat user admin
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $adminRole->id,
            'name' => "Admin {$name}",
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $this->info("User admin berhasil dibuat dengan email: {$email} dan password: {$password}");

        $this->info("Tenant setup berhasil. Silakan login dengan email: {$email} dan password: {$password}");
        return 0;
    }
}
