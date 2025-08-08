<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;


class AuthController extends Controller
{
     public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Cria o Tenant
        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'name' => $data['tenant_name'],
        ]);

        // Associa o Tenant ao usuário

         $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $tenant->id,
        ]);

        // Autentica o usuário e gera token Sanctum
         $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'tenant' => $tenant,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 401);
        }

        if (! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Senha incorreta.'], 401);
        }


        if (!$user->is_active) {
            return response()->json(['message' => 'Usuário desativado'], 403);
        }

        // Remove tokens antigos
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}