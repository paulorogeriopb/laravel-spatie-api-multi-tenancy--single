<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class SetTenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $tenantId = auth()->user()->tenant_id;

            // Seta o tenant atual para Spatie Permissions
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);
        }

        return $next($request);
    }
}
