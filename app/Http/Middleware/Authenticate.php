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
        $sessionCookieName = config('session.cookie');
        $sessionCookieValue = $request->cookie($sessionCookieName);
        
        Log::warning('Redirect to login because unauthenticated', [
            'path' => $request->path(),
            'is_authenticated' => auth()->check(),
            'session_id' => $request->session()->getId(),
            'session_has_auth' => $request->session()->has('login_web_' . sha1('Illuminate\Auth\SessionGuard')),
            'session_cookie_name' => $sessionCookieName,
            'session_cookie_value' => $sessionCookieValue ? 'exists' : 'missing',
            'session_cookie_length' => $sessionCookieValue ? strlen($sessionCookieValue) : 0,
            'all_cookies_count' => count($request->cookies->all()),
            'cookies' => array_keys($request->cookies->all()),
            'session_config' => [
                'cookie_name' => config('session.cookie'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'same_site' => config('session.same_site'),
            ],
            'request_info' => [
                'host' => $request->getHost(),
                'scheme' => $request->getScheme(),
                'secure' => $request->secure(),
                'url' => $request->url(),
            ],
            'headers' => [
                'user-agent' => $request->header('User-Agent'),
                'referer' => $request->header('Referer'),
                'cookie_header' => $request->header('Cookie'),
            ]
        ]);
        return $request->expectsJson() ? null : route('login');
    }
}
