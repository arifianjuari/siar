<?php

namespace App\Console;

use App\Console\Commands\BackupDatabaseCommand;
use App\Console\Commands\TenantProvisionCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TenantProvisionCommand::class,
        BackupDatabaseCommand::class,
        Commands\CreateTenant::class,
        Commands\FixRolePermissions::class,
        Commands\FixRoleSlugs::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan backup database setiap hari jam 2 pagi
        $schedule->command('db:backup')
            ->dailyAt('02:00')
            ->appendOutputTo(storage_path('logs/backup-database.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
