<?php

use League\Glide\Signatures\SignatureFactory;

if (!function_exists('unparse_url')) {
    function unparse_url($parsed_url) {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
if (!function_exists('glide')) {

    function glide($pathToImage, string|array $args = [])
    {
        // first, remove possible already existing glide endpoint from the path
        // this way, we can do something like glide(glide('path/to/image.jpg', 'w=100'), 'h=100'); and it will work
        $cleanPathToImage = str_replace('/' . config('glide-images.endpoint'), '', $pathToImage);

        // Prepend the url with the base url if it doesn't start with http or https
        $leadingHttpPattern = "/^(http:\/\/|https:\/\/)/";
        $url = preg_match($leadingHttpPattern, $cleanPathToImage) ?
            $cleanPathToImage :
            url($cleanPathToImage);

        // if the url now does not contain our app url, we have a url to another domain
        // so we don't want to prepend the glide endpoint
        if (!str_contains($url, config('app.url'))) {
            return $pathToImage;
        }

        $urlComponents = parse_url($url);
        $originalUrlArgs = [];

        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $originalUrlArgs);
        }

        // prepend the glide endpoint to the url
        $urlComponents['path'] = '/' . config('glide-images.endpoint') . $urlComponents['path'];

        if (is_string($args)) {
            $args = ['w' => $args];
        }

        if (!array_key_exists('fit', $args)) {
            $args['fit'] = config('glide-images.fit');
        }

        if (!array_key_exists('q', $args)) {
            $args['q'] = config('glide-images.quality');
        }

        if(config('glide-images.secure')) {
            $httpSignature = SignatureFactory::create(config('app.key'));
            $args['s'] = $httpSignature->generateSignature($url, $args);
        }

        // merge the original url args with the new args
        $args = array_merge($originalUrlArgs, $args);

        $urlComponents['query'] = http_build_query($args);

        return unparse_url($urlComponents);

    }
}

