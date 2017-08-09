<?php

namespace Toddish\Verify\Providers;

use Toddish\Verify\Providers\VerifyUserProvider,
    Illuminate\Support\ServiceProvider,
    Toddish\Verify\Auth\VerifyGuard;

class VerifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/verify.php' => config_path('verify.php')
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../../config/verify.php', 'verify');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => base_path('database/migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../../database/seeds/' => base_path('database/seeds')
        ], 'seeds');

        \Auth::extend('verify', function($app, $name, array $config) {
            // Вернуть экземпляр Illuminate\Contracts\Auth\Guard...

            $guard = new VerifyGuard(
                $name,
                new VerifyUserProvider($app['hash'], $config['model']),
                $app['session.store'],
                $app->request
            );
            $guard->setCookieJar($this->app['cookie']);
            $guard->setDispatcher($this->app['events']);
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
            return $guard;
        });

        \Auth::provider('verify', function($app, array $config) {
            return new VerifyUserProvider($app['hash'], $config['model']);
        });
    }

    public function register()
    {
        $this->commands([
            'Toddish\Verify\Commands\AddPermission',
            'Toddish\Verify\Commands\AddCrudPermissions',
            'Toddish\Verify\Commands\AddRole'
        ]);
    }
}