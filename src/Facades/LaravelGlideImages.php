<?php

namespace SimonVomEyser\LaravelGlideImages\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SimonVomEyser\LaravelGlideImages\LaravelGlideImages
 */
class LaravelGlideImages extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \SimonVomEyser\LaravelGlideImages\LaravelGlideImages::class;
    }
}
