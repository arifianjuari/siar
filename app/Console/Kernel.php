<?php

namespace App\Console;

use App\Console\Commands\DatabaseBackup;
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
        DatabaseBackup::class,
        Commands\CreateTenant::class,
        Commands\FixRolePermissions::class,
        Commands\FixRoleSlugs::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Database backup setiap hari jam 2 pagi (WIB = UTC+7)
        $schedule->command('db:backup')->dailyAt('02:00')->timezone('Asia/Jakarta');
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
