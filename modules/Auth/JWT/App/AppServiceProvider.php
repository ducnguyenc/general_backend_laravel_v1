<?php

namespace Modules\Auth\JWT\App;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\JWT\App\Repositories\AuthRepository;
use Modules\Auth\JWT\App\Repositories\AuthRepositoryInterface;
use Modules\Auth\JWT\App\Services\AuthService;
use Modules\Auth\JWT\App\Services\AuthServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        ServiceProvider::class => Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // config
        $this->mergeConfigFrom(__DIR__ . '/../config/jwt.php', 'jwt');
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', '');

        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(AuthRepositoryInterface::class, AuthRepository::class);
    }
}
