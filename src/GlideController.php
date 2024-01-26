<?php

namespace SimonVomEyser\LaravelGlideImages;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class GlideController
{
    public function __invoke(Filesystem $filesystem, $path)
    {
        $source = Storage::disk('glide_public_path')->path('');

        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $source,
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => config('glide-images.cache'),
        ]);

        return $server->getImageResponse($path, request()->all());
    }
}
