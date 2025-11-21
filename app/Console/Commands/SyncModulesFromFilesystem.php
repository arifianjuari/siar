<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncModulesFromFilesystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:sync 
                            {--dry-run : Run without making changes}
                            {--force : Force sync even if modules are in use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync modules from filesystem to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting module synchronization...');
        $this->newLine();

        $filesystemModules = $this->discoverModulesFromFilesystem();

        if (empty($filesystemModules)) {
            $this->error('No modules found in filesystem!');
            $this->warn('Please ensure modules/ directory exists and contains valid modules.');
            return 1;
        }

        $moduleCount = count($filesystemModules);
        $this->info("Found {$moduleCount} modules in filesystem:");
        $this->table(
            ['Name', 'Slug', 'Has module.json', 'In DB'],
            collect($filesystemModules)->map(fn($m) => [
                $m['name'] ?? 'Unknown',
                $m['alias'] ?? Str::slug($m['name'] ?? 'unknown'),
                isset($m['metadata']) ? '✓' : '✗',
                $m['exists_in_db'] ? '✓' : '✗',
            ])
        );
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('Dry run mode - no changes will be made');
            return 0;
        }

        // Skip confirmation if running non-interactively
        if (!$this->option('no-interaction') && !$this->confirm('Do you want to proceed with synchronization?', true)) {
            $this->warn('Synchronization cancelled.');
            return 0;
        }

        try {
            DB::beginTransaction();

            $created = 0;
            $updated = 0;
            $deleted = 0;

            // Collect slugs from filesystem
            $filesystemSlugs = [];

            foreach ($filesystemModules as $fsModule) {
                $slug = $fsModule['alias'] ?? Str::slug($fsModule['name']);
                $filesystemSlugs[] = $slug;

                // Generate code from slug
                $code = strtoupper(str_replace('-', '_', $slug));

                $moduleData = [
                    'name' => $fsModule['name'],
                    'code' => $code,
                    'slug' => $slug,
                    'description' => $fsModule['description'] ?? 'Module ' . $fsModule['name'],
                    'icon' => $fsModule['icon'] ?? 'fa-cube',
                ];

                $existingModule = Module::where('slug', $slug)->first();

                if (!$existingModule) {
                    // Check if code already exists (avoid duplicate)
                    $codeExists = Module::where('code', $code)->exists();
                    if ($codeExists) {
                        // Generate unique code by appending number
                        $counter = 1;
                        $originalCode = $code;
                        while (Module::where('code', $code)->exists()) {
                            $code = $originalCode . '_' . $counter;
                            $counter++;
                        }
                        $moduleData['code'] = $code;
                        $this->warn("⚠ Code conflict detected, using: {$code}");
                    }
                    
                    Module::create($moduleData);
                    $this->info("✓ Created: {$fsModule['name']}");
                    $created++;
                } else {
                    // Update only safe fields (not code to avoid unique constraint)
                    $updateData = [];
                    $updated_fields = [];
                    
                    if ($existingModule->name !== $moduleData['name']) {
                        $updateData['name'] = $moduleData['name'];
                        $updated_fields[] = 'name';
                    }
                    if ($existingModule->description !== $moduleData['description']) {
                        $updateData['description'] = $moduleData['description'];
                        $updated_fields[] = 'description';
                    }
                    if ($existingModule->icon !== $moduleData['icon']) {
                        $updateData['icon'] = $moduleData['icon'];
                        $updated_fields[] = 'icon';
                    }

                    if (!empty($updateData)) {
                        $existingModule->update($updateData);
                        $this->info("✓ Updated: {$fsModule['name']} (" . implode(', ', $updated_fields) . ")");
                        $updated++;
                    } else {
                        $this->line("- Unchanged: {$fsModule['name']}");
                    }
                }
            }

            // Handle orphaned modules
            $orphanedModules = Module::whereNotIn('slug', $filesystemSlugs)->get();

            foreach ($orphanedModules as $orphanedModule) {
                $usedByTenants = $orphanedModule->tenants()->wherePivot('is_active', true)->count();

                if ($usedByTenants > 0 && !$this->option('force')) {
                    $this->warn("⚠ Skipped: {$orphanedModule->name} (used by {$usedByTenants} tenant(s))");
                    continue;
                }

                $orphanedModule->delete();
                $this->info("✓ Deleted: {$orphanedModule->name}");
                $deleted++;
            }

            DB::commit();
            
            // Clear sidebar cache for all tenants
            $this->clearSidebarCache();

            $this->newLine();
            $this->info('Synchronization completed successfully!');
            $this->line("Created: {$created}");
            $this->line("Updated: {$updated}");
            $this->line("Deleted: {$deleted}");

            if ($orphanedModules->count() > $deleted) {
                $skipped = $orphanedModules->count() - $deleted;
                $this->warn("Skipped: {$skipped} (in use by tenants)");
                $this->line('Use --force to delete modules even if they are in use');
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Synchronization failed!');
            $this->error($e->getMessage());
            return 1;
        }
    }

    /**
     * Discover modules from filesystem
     */
    private function discoverModulesFromFilesystem()
    {
        $discovered = [];
        $modulesPath = base_path('modules');

        if (!is_dir($modulesPath)) {
            return $discovered;
        }

        // Try glob first, fallback to scandir
        $directories = @glob($modulesPath . '/*', GLOB_ONLYDIR);
        if ($directories === false) {
            $directories = [];
            $items = scandir($modulesPath);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $fullPath = $modulesPath . '/' . $item;
                if (is_dir($fullPath)) {
                    $directories[] = $fullPath;
                }
            }
        }

        foreach ($directories as $dir) {
            $moduleName = basename($dir);
            $moduleJsonPath = $dir . '/module.json';
            $configPath = $dir . '/Config/config.php';

            $moduleData = [
                'name' => $moduleName,
                'path' => $dir,
                'exists_in_db' => false,
                'metadata' => null,
            ];

            // Check if module.json exists
            if (file_exists($moduleJsonPath)) {
                $json = json_decode(file_get_contents($moduleJsonPath), true);
                if ($json) {
                    $moduleData['metadata'] = $json;
                    $moduleData['name'] = $json['name'] ?? $moduleName;
                    $moduleData['description'] = $json['description'] ?? '';
                    $moduleData['version'] = $json['version'] ?? '1.0.0';
                    $moduleData['alias'] = $json['alias'] ?? Str::slug($moduleName);
                }
            } elseif (file_exists($configPath)) {
                $config = include $configPath;
                if (is_array($config)) {
                    $moduleData['metadata'] = $config;
                    $moduleData['name'] = $config['name'] ?? $moduleName;
                    $moduleData['description'] = $config['description'] ?? '';
                    $moduleData['version'] = $config['version'] ?? '1.0.0';
                    $moduleData['alias'] = $config['alias'] ?? Str::slug($moduleName);
                }
            }

            // Check if exists in database
            $existingModule = Module::where('slug', $moduleData['alias'] ?? Str::slug($moduleName))->first();
            $moduleData['exists_in_db'] = !is_null($existingModule);
            $moduleData['db_module'] = $existingModule;

            $discovered[] = $moduleData;
        }

        return $discovered;
    }
    
    /**
     * Clear sidebar cache for all tenants
     */
    private function clearSidebarCache()
    {
        try {
            $tenantIds = \App\Models\Tenant::pluck('id');
            
            foreach ($tenantIds as $tenantId) {
                $cacheKey = 'sidebar_modules_tenant_' . $tenantId;
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
            }
            
            $this->info('✓ Sidebar cache cleared for all tenants');
        } catch (\Exception $e) {
            $this->warn('⚠ Failed to clear sidebar cache: ' . $e->getMessage());
        }
    }
}
