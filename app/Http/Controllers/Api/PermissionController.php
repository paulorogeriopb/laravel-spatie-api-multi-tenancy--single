<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Validation\Rule;
use App\Models\Tenant;
class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

   public function store(Request $request)
    {
        // Validação básica dos campos
       $request->validate([
    'name' => [
        'required',
        'string',
        Rule::unique('permissions')->where(function ($query) use ($request) {
            return $query->where('tenant_id', $request->tenant_id);
        }),
    ],
    'guard_name' => 'required|string',
    'tenant_id' => 'required|uuid|exists:tenants,id',
]);

        // Cria a permissão
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'tenant_id' => $request->tenant_id,
        ]);

        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return $permission;
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($data);

        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully.']);
    }
}
