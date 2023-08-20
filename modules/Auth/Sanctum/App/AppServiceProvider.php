<?php

namespace Modules\Auth\Sanctum\App;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\Sanctum\App\Repositories\AuthRepository;
use Modules\Auth\Sanctum\App\Repositories\AuthRepositoryInterface;
use Modules\Auth\Sanctum\App\Services\AuthService;
use Modules\Auth\Sanctum\App\Services\AuthServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // route
        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        // migration
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // language
        $this->loadTranslationsFrom(__DIR__.'/../lang', '');

        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(AuthRepositoryInterface::class, AuthRepository::class);
    }
}
