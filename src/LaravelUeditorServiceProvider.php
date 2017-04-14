<?php

namespace Aweika\LaravelUeditor;

use Illuminate\Support\ServiceProvider;

class LaravelUeditorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(HandleController::class);
        $this->publishes([
                __DIR__.'/../config/aweika-laravel-ueditor.php' => config_path('aweika-laravel-ueditor.php'),
            ], 'first');

        $this->publishes([
                __DIR__.'/../packages' => public_path(config('aweika-laravel-ueditor.package_path')),
            ], 'second');

        $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ueditor'),
            ], 'second');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
