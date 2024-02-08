<?php

use SimonVomEyser\LaravelGlideImages\Facades\LaravelGlideImages;

if (! function_exists('glide')) {

    function glide($pathToImage, string|array $args = [])
    {
        return LaravelGlideImages::getUrl($pathToImage, $args);
    }
}
