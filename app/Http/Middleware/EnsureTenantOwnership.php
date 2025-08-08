<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->route('tenant');

        if (is_string($tenant)) {
            $tenant = Tenant::find($tenant);
            if (!$tenant) {
                abort(404, 'Tenant não encontrado.');
            }
        }

        if ($tenant->id !== $request->user()->tenant_id) {
            abort(403, 'Acesso negado ao tenant.');
        }

        // ❗Só bloqueia se o tenant estiver inativo E não for o endpoint de toggle-status
        $isToggleStatusRoute = $request->routeIs('tenants.toggleStatus');

        if (!$tenant->is_active && !$isToggleStatusRoute) {
            abort(403, 'Tenant desativado.');
        }

        // Atualiza o parâmetro da rota
        $request->route()->setParameter('tenant', $tenant);

        return $next($request);
    }
}