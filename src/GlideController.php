<?php

namespace SimonVomEyser\LaravelGlideImages;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class GlideController
{
    public function __invoke(Filesystem $filesystem, $path)
    {
        $this->validateSignature();

        $source = Storage::disk('glide_public_path')->path('');

        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $source,
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => config('glide-images.cache'),
            'max_image_size' => config('glide-images.max_image_size'),
        ]);

        return $server->getImageResponse($path, request()->except(['expires', 'signature']));
    }

    private function validateSignature()
    {
        if (! config('glide-images.secure')) {
            return;
        }

        try {
            SignatureFactory::create(config('app.key'))->validateRequest(request()->url(), request()->query->all());
        } catch (SignatureException $e) {
            abort(400, $e->getMessage());
        }
    }
}
