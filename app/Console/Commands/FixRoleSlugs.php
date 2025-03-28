<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixRoleSlugs extends Command
{
    protected $signature = 'role:fix-slugs';
    protected $description = 'Memperbaiki slug role yang menggunakan format lama';

    public function handle()
    {
        $this->info('Memulai perbaikan slug role...');

        // Perbaiki role tenant_admin menjadi tenant-admin
        $roles = Role::where('slug', 'tenant_admin')
            ->orWhere('slug', 'Tenant_Admin')
            ->orWhere('slug', 'admin')
            ->get();

        $count = 0;
        foreach ($roles as $role) {
            $oldSlug = $role->slug;
            $role->slug = 'tenant-admin';
            $role->save();

            $this->info("Role '{$role->name}' (ID: {$role->id}) diubah dari '{$oldSlug}' menjadi 'tenant-admin'");
            $count++;
        }

        $this->info("Selesai! {$count} role telah diperbaiki.");
    }
}
