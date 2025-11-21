<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ShowRouteDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:show {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show details for a specific route by name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeName = $this->argument('name');
        $route = Route::getRoutes()->getByName($routeName);

        if (!$route) {
            $this->error("No route found with name: {$routeName}");
            return 1;
        }

        $this->info("Route Details for: {$routeName}");
        $this->info("URI: " . $route->uri());
        $this->info("Methods: " . implode(', ', $route->methods()));
        $this->info("Action: " . $route->getActionName());
        $this->info("Parameter Names: " . implode(', ', $route->parameterNames()));
        $this->info("Parameter Bindings: " . json_encode($route->bindingFields()) ?: 'None');
        $this->info("Middleware: " . implode(', ', $route->middleware()));

        return 0;
    }
}
