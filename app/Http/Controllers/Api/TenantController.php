<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    // Listar tenants ATIVOS do usuário logado (apenas seu tenant)
 public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        $tenant = Tenant::where('id', $user->tenant_id)->where('is_active', true)->first();

        return response()->json($tenant ? [$tenant] : []);
    }

    // Criar novo tenant
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:tenants,slug',
        ]);

        $slug = $request->slug ?: Str::slug($request->name);

        // Garantir unicidade do slug
        $baseSlug = $slug;
        $counter = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => true,
        ]);

        // Vincula o usuário logado ao tenant criado
        $user = auth()->user();
        $user->tenant_id = $tenant->id;
        $user->save();

        return response()->json($tenant, 201);
    }

    // Mostrar tenant (busca por id ou slug), apenas se ativo e pertence ao usuário
     public function show($id)
        {
            $tenant = Tenant::where('id', $id)->where('is_active', true)->firstOrFail();

            if ($tenant->id !== auth()->user()->tenant_id) {
                return response()->json(['message' => 'Acesso negado.'], 403);
            }

            return response()->json($tenant);
        }


    // Atualizar tenant (apenas o do usuário)
    public function update(Request $request, Tenant $tenant)
    {
        if ($tenant->id !== auth()->user()->tenant_id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants', 'slug')->ignore($tenant->id),
            ],
        ]);

        // Atualizar somente o que foi informado
        $data = [
            'name' => $validated['name'],
        ];

        if (array_key_exists('slug', $validated) && $validated['slug']) {
            $baseSlug = Str::slug($validated['slug']);
            $slug = $baseSlug;
            $counter = 1;

            // Garante unicidade apenas se for diferente do slug atual
            while (
                Tenant::where('slug', $slug)
                    ->where('id', '!=', $tenant->id)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $data['slug'] = $slug;
        }

        $tenant->update($data);

        return response()->json([
            'message' => 'Tenant atualizado com sucesso.',
            'tenant' => $tenant,
        ]);
    }

    public function toggleStatus(Request $request, Tenant $tenant)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $tenant->is_active = $request->boolean('is_active');
        $tenant->save();

        $status = $tenant->is_active ? 'ativado' : 'desativado';

        return response()->json([
            'message' => "Tenant {$status} com sucesso.",
            'tenant' => $tenant,
        ]);
    }


    // Não permitir exclusão física, só desabilitar
    public function destroy($id)
    {

        return response()->json([
            'message' => 'Exclusão física não permitida. Use o endpoint disable para desativar o tenant.'
        ], 403);
    }
}