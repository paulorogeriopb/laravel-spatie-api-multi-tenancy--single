<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;
use Spatie\Permission\PermissionRegistrar;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            $permissionRegistrar = $this->app->make(PermissionRegistrar::class);

            Route::bind('tenant', function ($value) {
            return Tenant::where('id', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
            });
    }
}
