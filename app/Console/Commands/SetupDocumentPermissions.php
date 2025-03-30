<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModulePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetupDocumentPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:setup-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memberikan akses ke modul document-management untuk semua role tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai mengatur permission untuk modul document-management...');

        try {
            // Ambil modul document-management
            $documentModule = Module::where('slug', 'document-management')->first();

            if (!$documentModule) {
                $this->error('Modul document-management tidak ditemukan!');
                return 1;
            }

            $this->info('Modul document-management ditemukan dengan ID: ' . $documentModule->id);

            // Ambil semua role
            $roles = Role::all();
            $this->info('Total role ditemukan: ' . $roles->count());

            $updatedCount = 0;
            $createdCount = 0;

            foreach ($roles as $role) {
                // Cek apakah permission sudah ada
                $permission = RoleModulePermission::where('role_id', $role->id)
                    ->where('module_id', $documentModule->id)
                    ->first();

                if ($permission) {
                    // Update permission yang sudah ada
                    $permission->can_view = true;
                    $permission->save();

                    $this->info('Mengupdate permission untuk role: ' . $role->name);
                    $updatedCount++;
                } else {
                    // Buat permission baru
                    RoleModulePermission::create([
                        'role_id' => $role->id,
                        'module_id' => $documentModule->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true
                    ]);

                    $this->info('Membuat permission baru untuk role: ' . $role->name);
                    $createdCount++;
                }
            }

            $this->info('Total permission diupdate: ' . $updatedCount);
            $this->info('Total permission dibuat: ' . $createdCount);

            // Verifikasi permission setelah diupdate
            $totalPermissions = RoleModulePermission::where('module_id', $documentModule->id)->count();
            $this->info('Total permission untuk modul document-management: ' . $totalPermissions);

            // Bersihkan cache
            $this->call('cache:clear');
            $this->info('Cache berhasil dibersihkan');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error saat menjalankan SetupDocumentPermissions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
