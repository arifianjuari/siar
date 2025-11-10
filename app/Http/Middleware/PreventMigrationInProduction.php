<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class PreventMigrationInProduction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
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
