<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder syncs modules from the filesystem to the database.
     * It's useful for initial setup or when deploying to a new environment.
     */
    public function run(): void
    {
        $this->command->info('Syncing modules from filesystem...');

        $filesystemModules = $this->discoverModulesFromFilesystem();

        if (empty($filesystemModules)) {
            $this->command->warn('No modules found in filesystem!');
            return;
        }

        DB::beginTransaction();

        try {
            $created = 0;
            $updated = 0;

            foreach ($filesystemModules as $fsModule) {
                $slug = $fsModule['alias'] ?? Str::slug($fsModule['name']);
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
                    Module::create($moduleData);
                    $this->command->info("  ✓ Created: {$fsModule['name']}");
                    $created++;
                } else {
                    // Update description and icon if changed
                    $updated_fields = [];
                    if ($existingModule->description !== $moduleData['description']) {
                        $updated_fields[] = 'description';
                    }
                    if ($existingModule->icon !== $moduleData['icon']) {
                        $updated_fields[] = 'icon';
                    }

                    if (!empty($updated_fields)) {
                        $existingModule->update($moduleData);
                        $this->command->info("  ✓ Updated: {$fsModule['name']} (" . implode(', ', $updated_fields) . ")");
                        $updated++;
                    } else {
                        $this->command->line("  - Unchanged: {$fsModule['name']}");
                    }
                }
            }

            DB::commit();

            $this->command->newLine();
            $this->command->info("Module sync completed! Created: {$created}, Updated: {$updated}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Module sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Discover modules from filesystem
     */
    private function discoverModulesFromFilesystem(): array
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
            ];

            // Check if module.json exists
            if (file_exists($moduleJsonPath)) {
                $json = json_decode(file_get_contents($moduleJsonPath), true);
                if ($json) {
                    $moduleData['name'] = $json['name'] ?? $moduleName;
                    $moduleData['description'] = $json['description'] ?? '';
                    $moduleData['version'] = $json['version'] ?? '1.0.0';
                    $moduleData['alias'] = $json['alias'] ?? Str::slug($moduleName);
                    $moduleData['icon'] = $json['icon'] ?? 'fa-cube';
                }
            } elseif (file_exists($configPath)) {
                $config = include $configPath;
                if (is_array($config)) {
                    $moduleData['name'] = $config['name'] ?? $moduleName;
                    $moduleData['description'] = $config['description'] ?? '';
                    $moduleData['version'] = $config['version'] ?? '1.0.0';
                    $moduleData['alias'] = $config['alias'] ?? Str::slug($moduleName);
                    $moduleData['icon'] = $config['icon'] ?? 'fa-cube';
                }
            } else {
                // No metadata file found, use defaults
                $moduleData['alias'] = Str::slug($moduleName);
                $moduleData['description'] = "Module {$moduleName}";
            }

            $discovered[] = $moduleData;
        }

        return $discovered;
    }
}
