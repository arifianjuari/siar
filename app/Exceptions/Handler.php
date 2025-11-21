<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Menangani error CSRF token
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->isXmlHttpRequest()) {
                return response()->json([
                    'error' => 'CSRF token mismatch. Mohon muat ulang halaman.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Session telah kedaluwarsa. Silakan coba lagi.');
        });
    }
}
