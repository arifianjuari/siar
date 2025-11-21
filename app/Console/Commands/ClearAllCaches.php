<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:nuclear
                            {--sessions : Also clear all sessions from database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nuclear option: Clear ALL caches, sessions, and compiled files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting nuclear cache clear...');
        $this->newLine();

        // 1. Application Cache
        $this->info('Step 1/9: Clearing application cache...');
        try {
            Artisan::call('cache:clear');
            $this->line('✓ Application cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Application cache: ' . $e->getMessage());
        }

        // 2. Configuration Cache
        $this->info('Step 2/9: Clearing config cache...');
        try {
            Artisan::call('config:clear');
            $this->line('✓ Config cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Config cache: ' . $e->getMessage());
        }

        // 3. Route Cache
        $this->info('Step 3/9: Clearing route cache...');
        try {
            Artisan::call('route:clear');
            $this->line('✓ Route cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Route cache: ' . $e->getMessage());
        }

        // 4. View Cache
        $this->info('Step 4/9: Clearing view cache...');
        try {
            Artisan::call('view:clear');
            $this->line('✓ View cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ View cache: ' . $e->getMessage());
        }

        // 5. Event Cache
        $this->info('Step 5/9: Clearing event cache...');
        try {
            Artisan::call('event:clear');
            $this->line('✓ Event cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Event cache: ' . $e->getMessage());
        }

        // 6. Compiled Class Cache
        $this->info('Step 6/9: Clearing compiled classes...');
        try {
            Artisan::call('clear-compiled');
            $this->line('✓ Compiled classes cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Compiled classes: ' . $e->getMessage());
        }

        // 7. Optimize Clear
        $this->info('Step 7/9: Running optimize:clear...');
        try {
            Artisan::call('optimize:clear');
            $this->line('✓ Optimization cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Optimization: ' . $e->getMessage());
        }

        // 8. All Cache Stores
        $this->info('Step 8/9: Flushing all cache stores...');
        try {
            Cache::flush();
            $this->line('✓ All cache stores flushed');
        } catch (\Exception $e) {
            $this->warn('⚠ Cache stores: ' . $e->getMessage());
        }

        // 9. Sessions (optional)
        if ($this->option('sessions')) {
            $this->info('Step 9/9: Clearing all sessions...');
            
            // Clear database sessions
            try {
                if (Schema::hasTable('sessions')) {
                    $count = DB::table('sessions')->count();
                    DB::table('sessions')->truncate();
                    $this->line("✓ Cleared {$count} sessions from database");
                }
            } catch (\Exception $e) {
                $this->warn('⚠ Database sessions: ' . $e->getMessage());
            }
            
            // Clear file sessions
            try {
                $sessionPath = storage_path('framework/sessions');
                if (is_dir($sessionPath)) {
                    $files = glob($sessionPath . '/*');
                    $count = 0;
                    foreach ($files as $file) {
                        if (is_file($file) && basename($file) !== '.gitignore') {
                            unlink($file);
                            $count++;
                        }
                    }
                    $this->line("✓ Cleared {$count} session files from storage");
                } else {
                    $this->warn('⚠ Session directory not found');
                }
            } catch (\Exception $e) {
                $this->warn('⚠ File sessions: ' . $e->getMessage());
            }
            
            // Clear Redis sessions (if applicable)
            try {
                if (config('session.driver') === 'redis') {
                    \Illuminate\Support\Facades\Redis::flushdb();
                    $this->line('✓ Cleared Redis sessions');
                }
            } catch (\Exception $e) {
                $this->warn('⚠ Redis sessions: ' . $e->getMessage());
            }
        } else {
            $this->info('Step 9/9: Skipping sessions (use --sessions to clear)');
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Nuclear cache clear completed!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->warn('IMPORTANT: After this, users should:');
        $this->line('1. Clear browser cache (Ctrl+Shift+Del)');
        $this->line('2. Clear cookies for the domain');
        $this->line('3. Close all browser tabs');
        $this->line('4. Re-login to the application');

        return 0;
    }
}
