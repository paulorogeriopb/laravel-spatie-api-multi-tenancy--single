<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => 'Tenant Exemplo',
            ]);

            $teamKey = config('permission.column_names.team_foreign_key', 'team_id');

            // Criar permissão, garantindo que IDs e teamKey estejam consistentes
            $permission = Permission::firstOrCreate(
                [
                    'name' => 'view-dashboard',
                    'guard_name' => 'web',
                    $teamKey => $tenant->id,
                ],
                [
                    'id' => (string) Str::uuid(),
                ]
            );

            // Criar role
            $role = Role::firstOrCreate(
                [
                    'name' => 'admin',
                    'guard_name' => 'web',
                    $teamKey => $tenant->id,
                ],
                [
                    'id' => (string) Str::uuid(),
                ]
            );

            // Garante que as permissões estão sincronizadas para evitar erros
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }

            // Criar usuário
            $user = User::firstOrCreate(
                ['email' => 'admin@tenantexemplo.com'],
                [
                    'id' => (string) Str::uuid(),
                    'name' => 'Usuário Admin',
                    'password' => bcrypt('12345678'),
                    'tenant_id' => $tenant->id,
                ]
            );

            // Associa role ao usuário se ainda não tiver
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        });
    }
}