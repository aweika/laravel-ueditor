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
            ], 'two');

        $this->publishes([
                __DIR__.'/../resources/views/components' => resource_path('views/aweika-laravel-ueditor/components'),
            ], 'two');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/ueditor.php', 'aweika-laravel-ueditor'
        );
    }
}
