<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;

class SafetyHookServiceProvider extends ServiceProvider
{
    /**
     * Daftar perintah berbahaya yang diblokir di production
     *
     * @var array
     */
    protected $blockedCommands = [
        'migrate:fresh',
        'migrate:refresh',
        'db:wipe',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Hanya aktif di environment production
        if (App::environment('production')) {
            Event::listen(CommandStarting::class, function ($event) {
                $command = $event->command;
                
                // Allow db:reset-production to bypass safety check
                // This command has its own safety mechanisms (force flag + explicit confirmation)
                if ($command === 'db:reset-production') {
                    return;
                }

                if (in_array($command, $this->blockedCommands)) {
                    $this->abortCommand();
                }
            });
        }
    }

    /**
     * Menghentikan eksekusi perintah berbahaya
     */
    protected function abortCommand(): void
    {
        echo "PERINTAH BERBAHAYA DIBLOKIR!\n";
        echo "Perintah ini tidak dapat dijalankan di environment production.\n";
        echo "Jika Anda yakin ingin menjalankan perintah ini, silakan ubah APP_ENV menjadi local atau development.\n";
        exit(1);
    }
}
