<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetDatabaseProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-production 
                            {--force : Force reset without confirmation}
                            {--keep-data : Keep existing data, only add missing}
                            {--i-understand-this-will-delete-all-data : Extra safety flag for force mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database for production with superadmin user and standard modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // SAFETY CHECK: Only allow in specific environments
        if (!app()->environment(['production', 'staging', 'local'])) {
            $this->error('This command can only be run in production, staging, or local environment.');
            return 1;
        }

        $this->warn('⚠️  WARNING: This will reset the database!');
        $this->warn('This action will:');
        $this->line('1. Drop all existing tables');
        $this->line('2. Run all migrations');
        $this->line('3. Create System tenant');
        $this->line('4. Create superadmin role');
        $this->line('5. Create superadmin@siar.com user');
        $this->line('6. Sync all modules from filesystem');
        $this->newLine();

        // Extra confirmation
        if (!$this->option('force')) {
            $confirmation = $this->ask('Type "RESET DATABASE" to confirm (case-sensitive)');
            
            if ($confirmation !== 'RESET DATABASE') {
                $this->error('Reset cancelled. Confirmation text did not match.');
                return 1;
            }

            if (!$this->confirm('Are you absolutely sure? This cannot be undone!', false)) {
                $this->error('Reset cancelled by user.');
                return 1;
            }
        } else {
            // Force mode requires extra safety flag
            if (!$this->option('i-understand-this-will-delete-all-data')) {
                $this->error('');
                $this->error('⚠️  FORCE MODE DETECTED!');
                $this->error('');
                $this->error('For safety, you must also include:');
                $this->error('--i-understand-this-will-delete-all-data');
                $this->error('');
                $this->error('Full command:');
                $this->error('php artisan db:reset-production --force --i-understand-this-will-delete-all-data');
                $this->error('');
                return 1;
            }
            
            $this->warn('');
            $this->warn('🚨 FORCE MODE: Skipping all confirmations!');
            $this->warn('');
        }

        try {
            $this->info('Starting database reset...');
            $this->newLine();

            // Step 1: Fresh migration
            if (!$this->option('keep-data')) {
                $this->info('Step 1/6: Running fresh migrations...');
                
                // Set environment variable to bypass safety check
                putenv('ALLOW_DANGEROUS_COMMANDS=true');
                
                Artisan::call('migrate:fresh', ['--force' => true]);
                $this->line(Artisan::output());
                
                // Unset after use
                putenv('ALLOW_DANGEROUS_COMMANDS=false');
            }

            // Step 2: Create System Tenant
            $this->info('Step 2/6: Creating System tenant...');
            $systemTenant = Tenant::firstOrCreate(
                ['id' => 1],
                [
                    'name' => 'System',
                    'slug' => 'system',
                    'code' => 'SYSTEM',
                    'type' => 'system',
                    'domain' => 'system.local',
                    'database' => config('database.connections.mysql.database'),
                    'is_active' => true,
                ]
            );
            $this->line("✓ System tenant created: ID={$systemTenant->id}");

            // Step 3: Create Superadmin Role
            $this->info('Step 3/6: Creating superadmin role...');
            $superadminRole = Role::firstOrCreate(
                ['slug' => 'superadmin'],
                [
                    'name' => 'Super Admin',
                    'description' => 'System administrator with full access',
                    'tenant_id' => $systemTenant->id,
                ]
            );
            $this->line("✓ Superadmin role created: ID={$superadminRole->id}");

            // Step 4: Create Superadmin User
            $this->info('Step 4/6: Creating superadmin user...');
            $superadminUser = User::firstOrCreate(
                ['email' => 'superadmin@siar.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('asdfasdf'),
                    'email_verified_at' => now(),
                    'role_id' => $superadminRole->id,
                    'tenant_id' => $systemTenant->id,
                    'is_active' => true,
                ]
            );
            
            // Update password if user exists
            if (!$superadminUser->wasRecentlyCreated) {
                $superadminUser->update([
                    'password' => Hash::make('asdfasdf'),
                    'role_id' => $superadminRole->id,
                    'tenant_id' => $systemTenant->id,
                ]);
                $this->line("✓ Superadmin user updated: {$superadminUser->email}");
            } else {
                $this->line("✓ Superadmin user created: {$superadminUser->email}");
            }

            // Step 5: Sync Modules from Filesystem
            $this->info('Step 5/6: Syncing modules from filesystem...');
            $modules = $this->syncModulesFromFilesystem();
            $this->line("✓ Synced {$modules} modules");

            // Step 6: Summary
            $this->newLine();
            $this->info('Step 6/6: Summary');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("System Tenant ID: {$systemTenant->id}");
            $this->line("Superadmin Role ID: {$superadminRole->id}");
            $this->line("Superadmin User ID: {$superadminUser->id}");
            $this->line("Email: {$superadminUser->email}");
            $this->line("Password: asdfasdf");
            $this->line("Modules synced: {$modules}");
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            // Clear all caches to prevent stale data
            $this->newLine();
            $this->info('🧹 Clearing all caches...');
            Artisan::call('cache:nuclear --sessions');
            $this->line('✓ All caches and sessions cleared');

            $this->newLine();
            $this->info('✅ Database reset completed successfully!');
            $this->newLine();
            
            $this->warn('IMPORTANT NEXT STEPS:');
            $this->line('1. Change the default password after first login!');
            $this->line('2. Clear your browser cache and cookies');
            $this->line('3. Close all browser tabs and re-login');
            
            return 0;

        } catch (\Exception $e) {
            $this->error('Database reset failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->line('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Sync modules from filesystem to database
     */
    private function syncModulesFromFilesystem(): int
    {
        $modulesPath = base_path('modules');
        
        if (!is_dir($modulesPath)) {
            $this->warn('Modules directory not found: ' . $modulesPath);
            return 0;
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

        $count = 0;

        foreach ($directories as $dir) {
            $moduleName = basename($dir);
            $moduleJsonPath = $dir . '/module.json';
            $configPath = $dir . '/Config/config.php';

            $name = $moduleName;
            $description = "Module {$moduleName}";
            $slug = Str::slug($moduleName);
            $icon = 'fa-cube';

            // Check if module.json exists
            if (file_exists($moduleJsonPath)) {
                $json = json_decode(file_get_contents($moduleJsonPath), true);
                if ($json) {
                    $name = $json['name'] ?? $moduleName;
                    $description = $json['description'] ?? $description;
                    $slug = $json['alias'] ?? $slug;
                    $icon = $json['icon'] ?? $icon;
                }
            } elseif (file_exists($configPath)) {
                $config = include $configPath;
                if (is_array($config)) {
                    $name = $config['name'] ?? $moduleName;
                    $description = $config['description'] ?? $description;
                    $slug = $config['alias'] ?? $slug;
                    $icon = $config['icon'] ?? $icon;
                }
            }

            $code = strtoupper(str_replace('-', '_', $slug));

            // Check if code already exists
            $codeExists = Module::where('code', $code)->exists();
            if ($codeExists) {
                $counter = 1;
                $originalCode = $code;
                while (Module::where('code', $code)->exists()) {
                    $code = $originalCode . '_' . $counter;
                    $counter++;
                }
            }

            Module::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'code' => $code,
                    'description' => $description,
                    'icon' => $icon,
                ]
            );

            $this->line("  ✓ {$name}");
            $count++;
        }

        return $count;
    }
}
