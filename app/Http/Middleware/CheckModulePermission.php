<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class CheckModulePermission
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleCode
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduleCode, ?string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this resource.');
        }

        $user = Auth::user();

        // Check module access first
        if (!$this->permissionService->userHasModuleAccess($user, $moduleCode)) {
            abort(403, 'You do not have access to this module.');
        }

        // If specific permission is required, check it
        if ($permission) {
            if (!$this->permissionService->userHasPermission($user, $moduleCode, $permission)) {
                abort(403, "You do not have the required permission: {$permission}");
            }
        }

        // Store permissions in request for later use in controllers/views
        $permissions = $this->permissionService->getUserModulePermissions($user, $moduleCode);
        $request->merge([
            'user_permissions' => $permissions,
            'module_code' => $moduleCode
        ]);

        // Share permissions to views
        view()->share('userPermissions', $permissions);
        view()->share('currentModuleCode', $moduleCode);

        return $next($request);
    }
}
