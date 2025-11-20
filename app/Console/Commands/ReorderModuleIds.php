<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReorderModuleIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:reorder-ids 
                            {--dry-run : Tampilkan preview tanpa mengubah data}
                            {--sort=name : Urutkan berdasarkan (name|slug|code)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder module IDs to be sequential (1, 2, 3, ...) while preserving all relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sortBy = $this->option('sort');
        $isDryRun = $this->option('dry-run');
        
        if (!in_array($sortBy, ['name', 'slug', 'code'])) {
            $this->error('Invalid sort option. Use: name, slug, or code');
            return 1;
        }
        
        $this->info('🔄 Module ID Reordering Tool');
        $this->newLine();
        
        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }
        
        // Get current modules
        $currentModules = Module::orderBy($sortBy)->get();
        
        if ($currentModules->isEmpty()) {
            $this->error('No modules found in database');
            return 1;
        }
        
        // Show current state
        $this->info('Current Module IDs:');
        $this->table(
            ['Current ID', 'New ID', 'Slug', 'Name'],
            $currentModules->map(function ($module, $index) {
                return [
                    $module->id,
                    $index + 1,
                    $module->slug,
                    $module->name,
                ];
            })
        );
        
        // Check if reordering is needed
        $needsReordering = false;
        foreach ($currentModules as $index => $module) {
            if ($module->id != ($index + 1)) {
                $needsReordering = true;
                break;
            }
        }
        
        if (!$needsReordering) {
            $this->info('✅ Module IDs are already sequential. No reordering needed.');
            return 0;
        }
        
        if ($isDryRun) {
            $this->info('✅ Dry run complete. Use without --dry-run to apply changes.');
            return 0;
        }
        
        // Confirm before proceeding
        if (!$this->confirm('⚠️  This will reorder all module IDs. Continue?', false)) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        try {
            $this->info('Starting reordering process...');
            $this->newLine();
            
            // Step 1: Backup relationships
            $this->info('📦 Backing up relationships...');
            $tenantModules = DB::table('tenant_modules')->get();
            $roleModulePermissions = DB::table('role_module_permissions')->get();
            
            // Create mapping from old ID to slug
            $idToSlug = $currentModules->pluck('slug', 'id')->toArray();
            
            // Step 2: Temporarily disable foreign key checks
            $this->info('🔓 Disabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Step 3: Clear relationships (use delete instead of truncate for transaction compatibility)
            $this->info('🗑️  Clearing relationships...');
            DB::table('role_module_permissions')->delete();
            DB::table('tenant_modules')->delete();
            
            // Step 4: Reorder modules
            $this->info('🔄 Reordering modules...');
            DB::table('modules')->delete();
            
            // Reset auto increment
            DB::statement('ALTER TABLE modules AUTO_INCREMENT = 1');
            
            // Create mapping from slug to new ID
            $slugToNewId = [];
            $progressBar = $this->output->createProgressBar($currentModules->count());
            $progressBar->start();
            
            foreach ($currentModules as $index => $module) {
                $newModule = Module::create([
                    'name' => $module->name,
                    'code' => $module->code,
                    'slug' => $module->slug,
                    'description' => $module->description,
                    'icon' => $module->icon,
                    'order' => $module->order,
                    'is_active' => $module->is_active,
                ]);
                
                $slugToNewId[$module->slug] = $newModule->id;
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Step 5: Restore relationships with new IDs
            $this->info('📥 Restoring tenant_modules relationships...');
            $progressBar = $this->output->createProgressBar($tenantModules->count());
            $progressBar->start();
            
            foreach ($tenantModules as $tm) {
                if (isset($idToSlug[$tm->module_id]) && isset($slugToNewId[$idToSlug[$tm->module_id]])) {
                    DB::table('tenant_modules')->insert([
                        'tenant_id' => $tm->tenant_id,
                        'module_id' => $slugToNewId[$idToSlug[$tm->module_id]],
                        'is_active' => $tm->is_active,
                        'created_at' => $tm->created_at,
                        'updated_at' => $tm->updated_at,
                    ]);
                }
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            $this->info('📥 Restoring role_module_permissions relationships...');
            $progressBar = $this->output->createProgressBar($roleModulePermissions->count());
            $progressBar->start();
            
            foreach ($roleModulePermissions as $rmp) {
                if (isset($idToSlug[$rmp->module_id]) && isset($slugToNewId[$idToSlug[$rmp->module_id]])) {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $rmp->role_id,
                        'module_id' => $slugToNewId[$idToSlug[$rmp->module_id]],
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
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Step 6: Re-enable foreign key checks
            $this->info('🔒 Re-enabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $this->newLine();
            $this->info('✅ Module IDs successfully reordered!');
            $this->newLine();
            
            // Show final state
            $this->info('Final Module IDs:');
            $this->table(
                ['ID', 'Slug', 'Name'],
                Module::orderBy('id')->get(['id', 'slug', 'name'])->toArray()
            );
            
            $this->newLine();
            $this->info('📊 Statistics:');
            $this->info("   Modules reordered: {$currentModules->count()}");
            $this->info("   Tenant relationships restored: {$tenantModules->count()}");
            $this->info("   Role permissions restored: {$roleModulePermissions->count()}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $this->error('❌ Error occurred: ' . $e->getMessage());
            $this->error('All changes have been rolled back.');
            
            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }
            
            return 1;
        }
    }
}
