<?php

namespace Aweika\LaravelUeditor;

use Illuminate\Support\Facades\Route;

class LaravelUeditor
{
    public static function routes()
    {
        Route::any(config('aweika-laravel-ueditor.server_url'), '\Aweika\LaravelUeditor\HandleController@index');
    }

    public static function serverUrl(){
        return '/'.trim(config('aweika-laravel-ueditor.server_url'), '/');
    }

    public static function component(){
        return 'vendor.laravel-ueditor.ueditor';
    }
}