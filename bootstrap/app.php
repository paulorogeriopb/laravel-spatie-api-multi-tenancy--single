<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
       // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
          $middleware->alias([
            'permission' => \App\Http\Middleware\ACLMiddleware::class,
            // Opcional:
            'tenant' => \App\Http\Middleware\SetTenantMiddleware::class,
            'tenant.ownership' => \App\Http\Middleware\EnsureTenantOwnership::class,
            'active' => \App\Http\Middleware\CheckIfUserIsActive::class,
          ]);

          $middleware->group('api', [
                \App\Http\Middleware\SetTenantMiddleware::class,
          ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();