<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Perintah-perintah yang dilarang dijalankan di production
     */
    protected $blockedCommands = [
        'migrate:fresh',
        'migrate:refresh',
        'db:wipe',
        'schema:drop',
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (App::environment('production')) {
            $this->blockDangerousCommands();
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Blokir perintah-perintah yang berbahaya
     */
    protected function blockDangerousCommands()
    {
        foreach ($this->blockedCommands as $command) {
            $this->app->extend($command, function () use ($command) {
                return new class($command) extends \Illuminate\Console\Command {
                    protected $name;

                    public function __construct($name)
                    {
                        parent::__construct();
                        $this->name = $name;
                    }

                    public function handle()
                    {
                        $this->error(sprintf(
                            '[DILARANG] Perintah %s tidak diizinkan pada lingkungan PRODUCTION!',
                            $this->name
                        ));

                        $this->error('Gunakan migrasi inkremental untuk mengubah skema database di production.');
                        $this->error('Jika Anda benar-benar membutuhkan operasi ini, jalankan pada lingkungan development atau gunakan mysqldump.');

                        return 1;
                    }
                };
            });
        }
    }
}
