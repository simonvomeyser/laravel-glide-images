<?php

namespace SimonVomEyser\LaravelGlideImages;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class GlideController
{
    public function __invoke(Filesystem $filesystem, $path)
    {
        $this->validateSignature();

        $source = Storage::disk('glide_public_path')->path('');
        $isRemote = false;

        // If the path is a valid URL and doesn't exist locally as a file,
        // we treat it as a remote image and download it for processing.
        if (! file_exists($source.'/'.$path) && filter_var($path, FILTER_VALIDATE_URL)) {
            $path = $this->downloadRemoteImage($path);
            $source = $this->getRemoteSourceDirectory();
            $isRemote = true;
        }

        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(app('request')),
            'source' => $source,
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => config('glide-images.cache'),
            'max_image_size' => config('glide-images.max_image_size'),
        ]);

        $response = $server->getImageResponse($path, request()->except(['expires', 'signature', 's']));

        // Clean up: If we downloaded a remote image, we delete the source file after
        // Glide has generated the (cached) response to save disk space.
        if ($isRemote) {
            $fullPath = $this->getRemoteSourceDirectory().'/'.$path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        return $response;
    }

    private function getRemoteSourceDirectory()
    {
        return storage_path('app/'.config('glide-images.cache').'/.remote-sources');
    }

    private function downloadRemoteImage($url)
    {
        $directory = $this->getRemoteSourceDirectory();

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = md5($url);
        $path = $directory.'/'.$filename;

        // Only download if we don't already have it (though it should be deleted after each request)
        if (! file_exists($path)) {
            $content = file_get_contents($url);
            if ($content === false) {
                abort(404, 'Remote image not found');
            }
            file_put_contents($path, $content);
        }

        return $filename;
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
