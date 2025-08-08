<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Tenant;

// Binding UUID
Route::bind('tenant', function ($value) {
    return Tenant::where('id', $value)->firstOrFail();
});

// Autenticação
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Rotas autenticadas e com tenant ativo
Route::prefix('v1')->middleware(['auth:sanctum', 'active'])->group(function () {

    // Info do usuário logado
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // ✅ ADICIONAR ESTA ROTA para listar o tenant do usuário autenticado
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');

    // Rotas que exigem ownership do tenant
    Route::prefix('tenants')->middleware('tenant.ownership')->group(function () {
        Route::get('{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::put('{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::patch('{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggleStatus');
        Route::delete('{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    });

    // Usuários
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Permissões
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
});