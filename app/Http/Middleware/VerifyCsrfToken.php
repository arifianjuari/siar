<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        'tenant/tags/attach-document',
        'tenant/profile',
        'tenant/settings',
        'api/*',
        'sanctum/csrf-cookie',
        // Temporary: exclude login for debugging 419 error
        'login',
        //
    ];
}
