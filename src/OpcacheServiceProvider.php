<?php

namespace Pollen\Opcache;

use Illuminate\Support\ServiceProvider;
use Pollen\Opcache\Http\Middleware\Request;

class OpcacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * This method is called after all other service providers have been registered,
     * meaning you have access to all other services that have been registered by the framework.
     */
    public function boot(): void
    {
        // Register commands for use in the console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Clear::class,
                Commands\Config::class,
                Commands\Status::class,
                Commands\Compile::class,
            ]);

            // Publish the package configuration to the application's own config directory.
            $this->publishes([
                __DIR__.'/../config/opcache.php' => config_path('opcache.php'),
            ], 'config');
        }
    }

    /**
     * Register any application services.
     *
     * This method is a great spot to register your service provider's
     * own services in the container.
     */
    public function register(): void
    {
        // Merge the package configuration file with the application's published copy.
        $this->mergeConfigFrom(__DIR__.'/../config/opcache.php', 'opcache');

        // Register routes.
        $this->app->router->group([
            'middleware' => [Request::class],
            'prefix' => config('opcache.prefix'),
            'namespace' => 'Pollen\Opcache\Http\Controllers',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });

        // Register the service class in the container.
        $this->app->singleton(OpcacheClass::class, function ($app) {
            return new OpcacheClass();
        });
    }
}
