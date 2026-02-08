<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class DevRoleOverride
{
    /**
     * Handle an incoming request.
     *
     * Force permission cache refresh when dev role override changes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('developer.enabled') && auth()->check() && session()->has('dev_role_override')) {
            // Clear Spatie's permission cache to force fresh authorization checks
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        return $next($request);
    }
}
