<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        Log::warning('Redirect to login because unauthenticated', [
            'path' => $request->path(),
            'is_authenticated' => auth()->check(),
            'session_id' => $request->session()->getId(),
            'session_has_auth' => $request->session()->has('login_web_' . sha1('Illuminate\Auth\SessionGuard')),
            'cookies' => $request->cookies->all(),
            'headers' => [
                'user-agent' => $request->header('User-Agent'),
                'referer' => $request->header('Referer'),
            ]
        ]);
        return $request->expectsJson() ? null : route('login');
    }
}
