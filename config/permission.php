<?php

return [

    'models' => [

        /*
         * Modelos usados para permissões e papéis.
         */
        'permission' => App\Models\Permission::class,
        'role' => App\Models\Role::class,
        'user' => App\Models\User::class,
    ],

    'table_names' => [

        /*
         * Tabelas para roles e permissions.
         */
        'roles' => 'roles',
        'permissions' => 'permissions',

        /*
         * Tabelas pivot para relacionar permissões e papéis a modelos.
         */
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',

        /*
         * Tabela que relaciona permissões a papéis.
         */
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [

        /*
         * Chave estrangeira usada nas tabelas pivot para roles e permissions.
         * Padrão: 'role_id' e 'permission_id'.
         */
        'role_pivot_key' => 'role_id',
        'permission_pivot_key' => 'permission_id',

        /*
         * Chave primária do modelo relacionada nas tabelas pivot.
         * Aqui usamos 'model_id' para UUID.
         */
        'model_morph_key' => 'model_id',

        /*
         * Chave estrangeira para teams (multitenancy).
         * Ajustado para 'tenant_id' para o seu cenário.
         */
        'team_foreign_key' => 'tenant_id',
    ],

    /*
     * Registrar método de verificação de permissões no Gate do Laravel.
     */
    'register_permission_check_method' => true,

    /*
     * Listener para Laravel Octane — geralmente falso.
     */
    'register_octane_reset_listener' => false,

    /*
     * Eventos ativados para atribuição/remoção de papéis e permissões.
     */
    'events_enabled' => false,

    /*
     * Ativar suporte a teams (multitenancy).
     */
    'teams' => true,

    /*
     * Classe para resolver o team_id/tenant_id atual.
     * Pode implementar seu próprio resolver se quiser.
     */
    'team_resolver' => \Spatie\Permission\DefaultTeamResolver::class,

    /*
     * Uso do Passport Client Credentials para permissões.
     */
    'use_passport_client_credentials' => false,

    /*
     * Mostrar nomes das permissões nas mensagens de exceção — cuidado com vazamento de info.
     */
    'display_permission_in_exception' => false,

    /*
     * Mostrar nomes dos papéis nas mensagens de exceção.
     */
    'display_role_in_exception' => false,

    /*
     * Permissões coringa (wildcard) — geralmente falso.
     */
    'enable_wildcard_permission' => false,

    /*
     * Classe para interpretar permissões wildcard.
     */
    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /*
     * Configurações do cache para permissões e papéis.
     */
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],


];