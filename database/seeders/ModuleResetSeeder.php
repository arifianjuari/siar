<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * PERINGATAN: Seeder ini akan menghapus SEMUA modul dan membuat ulang dengan ID berurutan.
     * Gunakan dengan hati-hati!
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Backup data relasi sebelum dihapus
            $tenantModules = DB::table('tenant_modules')->get();
            $roleModulePermissions = DB::table('role_module_permissions')->get();
            
            // Simpan mapping slug ke ID lama
            $oldModules = Module::all()->keyBy('slug')->map(function ($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'slug' => $module->slug,
                    'description' => $module->description,
                    'icon' => $module->icon,
                    'order' => $module->order,
                    'is_active' => $module->is_active,
                ];
            });
            
            // Hapus semua relasi terlebih dahulu
            DB::table('role_module_permissions')->truncate();
            DB::table('tenant_modules')->truncate();
            
            // Hapus semua modul
            DB::table('modules')->truncate();
            
            // Reset auto increment
            if (Schema::hasTable('modules')) {
                DB::statement('ALTER TABLE modules AUTO_INCREMENT = 1');
            }
            
            // Mapping dari slug ke ID baru
            $slugToNewId = [];
            
            // Buat ulang modul dengan ID berurutan berdasarkan urutan alfabetis
            $sortedModules = $oldModules->sortBy('name');
            $order = 1;
            
            foreach ($sortedModules as $slug => $moduleData) {
                $newModule = Module::create([
                    'name' => $moduleData['name'],
                    'code' => $moduleData['code'],
                    'slug' => $moduleData['slug'],
                    'description' => $moduleData['description'],
                    'icon' => $moduleData['icon'],
                    'order' => $order++,
                    'is_active' => $moduleData['is_active'],
                ]);
                
                $slugToNewId[$slug] = $newModule->id;
            }
            
            // Restore relasi tenant_modules dengan ID baru
            foreach ($tenantModules as $tm) {
                $oldModule = $oldModules->first(function ($mod) use ($tm) {
                    return $mod['id'] == $tm->module_id;
                });
                
                if ($oldModule && isset($slugToNewId[$oldModule['slug']])) {
                    DB::table('tenant_modules')->insert([
                        'tenant_id' => $tm->tenant_id,
                        'module_id' => $slugToNewId[$oldModule['slug']],
                        'is_active' => $tm->is_active,
                        'created_at' => $tm->created_at,
                        'updated_at' => $tm->updated_at,
                    ]);
                }
            }
            
            // Restore relasi role_module_permissions dengan ID baru
            foreach ($roleModulePermissions as $rmp) {
                $oldModule = $oldModules->first(function ($mod) use ($rmp) {
                    return $mod['id'] == $rmp->module_id;
                });
                
                if ($oldModule && isset($slugToNewId[$oldModule['slug']])) {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $rmp->role_id,
                        'module_id' => $slugToNewId[$oldModule['slug']],
                        'can_view' => $rmp->can_view,
                        'can_create' => $rmp->can_create,
                        'can_edit' => $rmp->can_edit,
                        'can_delete' => $rmp->can_delete,
                        'can_export' => $rmp->can_export ?? false,
                        'can_import' => $rmp->can_import ?? false,
                        'created_at' => $rmp->created_at,
                        'updated_at' => $rmp->updated_at,
                    ]);
                }
            }
            
            DB::commit();
            
            $this->command->info('✅ Modul berhasil direset dengan ID berurutan!');
            $this->command->info('Total modul: ' . Module::count());
            $this->command->table(
                ['ID', 'Slug', 'Name'],
                Module::orderBy('id')->get(['id', 'slug', 'name'])->toArray()
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
