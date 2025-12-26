<?php

namespace SimonVomEyser\LaravelGlideImages;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $isRemote = filter_var($path, FILTER_VALIDATE_URL);

        try {

            $params = request()->except(['expires', 'signature', 's']);
            $source = Storage::disk('glide_public_path')->path('');

            // If the path is a valid URL and doesn't exist locally as a file,
            // we treat it as a remote image and download it for processing.
            if ($isRemote) {
                $remoteSourceFilename = md5($path);

                if ($this->cacheFileExists($filesystem, $remoteSourceFilename, $params)) {
                    $path = $remoteSourceFilename;
                } else {
                    $path = $this->downloadRemoteImage($path);
                }

                $source = $this->getRemoteSourceDirectory();
            }

            $server = ServerFactory::create([
                'response' => new SymfonyResponseFactory(app('request')),
                'source' => $source,
                'cache' => $filesystem->getDriver(),
                'cache_path_prefix' => config('glide-images.cache'),
                'max_image_size' => config('glide-images.max_image_size'),
            ]);

            $response = $server->getImageResponse($path, $params);

            if ($isRemote && file_exists($fullPath = $source . '/' . $path)) {
                unlink($fullPath);
            }

            return $response;

        } catch (\Exception $e) {
            // Log the message, return the original image
            Log::warning($e->getMessage());

            if ($isRemote) {
                return redirect($path);
            }

            return file_exists(public_path($path))
                ? response()->file(public_path($path))
                : abort(404);
        }

    }

    private function cacheFileExists(Filesystem $filesystem, $path, array $params)
    {
        $server = ServerFactory::create([
            'source' => $this->getRemoteSourceDirectory(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => config('glide-images.cache'),
        ]);

        return $server->cacheFileExists($path, $params);
    }

    private function getRemoteSourceDirectory()
    {
        return storage_path('app/' . config('glide-images.cache') . '/.remote-sources');
    }

    private function downloadRemoteImage($url)
    {
        $directory = $this->getRemoteSourceDirectory();

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = md5($url);
        $path = $directory . '/' . $filename;

        // Only download if we don't already have it (though it should be deleted after each request)
        if (!file_exists($path)) {
            $response = Http::get($url);

            if ($response->failed() || !$this->isImage($response)) {
                abort(404, 'Remote image not found');
            }

            file_put_contents($path, $response->body());
        }

        return $filename;
    }

    private function isImage($response)
    {
        $contentType = $response->header('Content-Type');

        return str_starts_with($contentType, 'image/');
    }

    private function validateSignature()
    {
        if (!config('glide-images.secure')) {
            return;
        }

        try {
            SignatureFactory::create(config('app.key'))->validateRequest(request()->url(), request()->query->all());
        } catch (SignatureException $e) {
            abort(400, $e->getMessage());
        }
    }
}
