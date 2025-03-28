<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PreventMigrationInProduction
{
    public function handle(Request $request, Closure $next)
    {
        if (App::environment('production')) {
            $command = $request->input('command');

            if (in_array($command, ['migrate:fresh', 'migrate:refresh'])) {
                return response()->json([
                    'error' => 'Perintah ini tidak diizinkan dalam environment PRODUCTION untuk mencegah kehilangan data.',
                    'command' => $command
                ], 403);
            }
        }

        return $next($request);
    }
}
