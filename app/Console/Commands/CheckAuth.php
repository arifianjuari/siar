<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class CheckAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:auth {email? : Email user yang akan dicek}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa user dan tenant di database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Memeriksa Database ===');

        // Periksa Tenant
        $tenants = Tenant::all();
        $this->info('Total Tenant: ' . $tenants->count());

        foreach ($tenants as $tenant) {
            $this->info("- Tenant: {$tenant->name}, Domain: {$tenant->domain}, Database: {$tenant->database}, Active: " . ($tenant->is_active ? 'Yes' : 'No'));
        }

        $this->newLine();

        // Periksa Role
        $roles = Role::all();
        $this->info('Total Role: ' . $roles->count());

        foreach ($roles as $role) {
            $this->info("- Role: {$role->name}, Slug: {$role->slug}, Tenant: {$role->tenant_id}, Active: " . ($role->is_active ? 'Yes' : 'No'));
        }

        $this->newLine();

        // Periksa User (semua atau spesifik)
        $email = $this->argument('email');
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->info("User {$user->name}:");
                $this->info("- ID: {$user->id}");
                $this->info("- Email: {$user->email}");
                $this->info("- Role ID: {$user->role_id}");
                $this->info("- Role: " . ($user->role ? $user->role->name . ' (' . $user->role->slug . ')' : 'No Role'));
                $this->info("- Tenant ID: {$user->tenant_id}");
                $this->info("- Tenant: " . ($user->tenant ? $user->tenant->name : 'No Tenant'));
                $this->info("- Active: " . ($user->is_active ? 'Yes' : 'No'));
            } else {
                $this->error("User dengan email {$email} tidak ditemukan");
            }
        } else {
            $users = User::all();
            $this->info('Total User: ' . $users->count());

            foreach ($users as $user) {
                $this->info("- User: {$user->name}, Email: {$user->email}, Role: " .
                    ($user->role ? $user->role->name : 'No Role') .
                    ", Tenant: " . ($user->tenant ? $user->tenant->name : 'No Tenant') .
                    ", Active: " . ($user->is_active ? 'Yes' : 'No'));
            }
        }

        return Command::SUCCESS;
    }
}
