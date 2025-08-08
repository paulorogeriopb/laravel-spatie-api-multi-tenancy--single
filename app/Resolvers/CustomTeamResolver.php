<?php

namespace App\Resolvers;

use Spatie\Permission\Contracts\PermissionsTeamResolver;

class CustomTeamResolver implements PermissionsTeamResolver
{
    /**
     * Retorna o tenant_id atual para o pacote Spatie Permission.
     *
     * @return string|null
     */
    public function getPermissionsTeamId(): ?string
    {
        return auth()->check() ? auth()->user()->tenant_id : null;
    }

    /**
     * Define o tenant_id atual (opcional).
     *
     * @param  string|null  $id
     * @return void
     */
    public function setPermissionsTeamId($id): void
    {
        // Você pode implementar essa lógica se quiser, ou deixar vazio.
    }
}
