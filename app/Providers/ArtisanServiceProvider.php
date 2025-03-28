<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Console\Command;

class ArtisanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (App::environment('production')) {
            $this->app->booted(function () {
                $this->app['artisan']->command('migrate:fresh', function (Command $command) {
                    $command->error('Perintah migrate:fresh tidak diizinkan dalam environment PRODUCTION!');
                    return 1;
                });

                $this->app['artisan']->command('migrate:refresh', function (Command $command) {
                    $command->error('Perintah migrate:refresh tidak diizinkan dalam environment PRODUCTION!');
                    return 1;
                });
            });
        }
    }

    public function register()
    {
        //
    }
}
