<?php

namespace Aweika\LaravelUeditor;

use Illuminate\Support\Facades\Route;

class LaravelUeditor
{
    public static function routes()
    {
        Route::any('ueditor/handle', '\Aweika\LaravelUeditor\HandleController@index');
    }
}