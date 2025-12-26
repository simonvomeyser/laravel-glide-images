<?php

namespace SimonVomEyser\LaravelGlideImages;

use League\Glide\Signatures\SignatureFactory;

class LaravelGlideImages
{
    public function getUrl($pathToImage, array|string $args = [])
    {
        if (empty($pathToImage)) {
            return '';
        }

        $endpoint = config('glide-images.endpoint');

        // Remove the endpoint from the path if it's already there (to avoid duplication)
        $cleanPathToImage = str_replace('/'.$endpoint, '', $pathToImage);

        // Prepend the url with the base url if it doesn't start with http or https
        $leadingHttpPattern = "/^(http:\/\/|https:\/\/)/";
        $url = preg_match($leadingHttpPattern, $cleanPathToImage) ?
            $cleanPathToImage :
            url($cleanPathToImage);

        $urlComponents = parse_url($url);
        $originalUrlArgs = [];

        // If the original URL has query parameters, we extract them to merge with new args
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $originalUrlArgs);
        }

        // if the url now does not contain our app url, we have a url to another domain
        $isExternal = ! str_contains($url, (string) config('app.url'));

        // We strip the query string from the path as it will be handled via the $args merge
        $urlWithoutQuery = explode('?', $url)[0];

        $appUrlComponents = parse_url((string) config('app.url'));
        $urlComponents['scheme'] = $appUrlComponents['scheme'] ?? null;
        $urlComponents['host'] = $appUrlComponents['host'] ?? null;
        $urlComponents['port'] = $appUrlComponents['port'] ?? null;

        // For external images, the entire original URL becomes part of the Glide path
        if ($isExternal) {
            $urlComponents['path'] = '/'.$endpoint.'/'.$urlWithoutQuery;
        } else {
            $urlComponents['path'] = '/'.$endpoint.$urlComponents['path'];
        }

        $urlComponents['query'] = null; // Important: Clear query as it's now part of the path (or will be re-added)

        // Handle shorthand for width (e.g., glide('img.jpg', 400))
        if (is_string($args)) {
            $args = ['w' => $args];
        }

        // Apply default configuration for fit and quality if not specified
        if (! array_key_exists('fit', $args)) {
            $args['fit'] = config('glide-images.fit');
        }

        if (! array_key_exists('q', $args)) {
            $args['q'] = config('glide-images.quality');
        }

        // merge the original url args with the new args
        $args = array_merge($originalUrlArgs, $args);

        $urlComponents['query'] = http_build_query($args);

        $url = $this->unparseUrl($urlComponents);

        // If security is enabled, we append a signature to the URL
        if (config('glide-images.secure')) {
            $urlWithoutParams = explode('?', $url)[0];
            $httpSignatureFactory = SignatureFactory::create(config('app.key'));
            $signature = $httpSignatureFactory->generateSignature($urlWithoutParams, $args);

            $url .= "&s=$signature";
        }

        return $url;
    }

    protected function unparseUrl($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
