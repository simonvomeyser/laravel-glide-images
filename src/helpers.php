<?php

use League\Glide\Signatures\SignatureFactory;

if (!function_exists('glide')) {

    function glide($pathToImage, string|array $args = [])
    {
        // first, remove possible already existing glide endpoint from the path
        // this way, we can do something like glide(glide('path/to/image.jpg', 'w=100'), 'h=100'); and it will work
        $pathToImage = str_replace('/' . config('glide-images.endpoint'), '', $pathToImage);

        // remove all query strings from the url that are possible glide definitions
        // @todo this should only remove the glide query strings
        $pathToImage = strtok($pathToImage, '?');

        // Prepend the url with the base url if it doesn't start with http or https
        $leadingHttpPattern = "/^(http:\/\/|https:\/\/)/";
        $url = preg_match($leadingHttpPattern, $pathToImage) ?
            $pathToImage :
            url($pathToImage);

        $url = str_replace(url('/'), url('/' . config('glide-images.endpoint')), $url);

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

        $queryString = http_build_query($args);

        $finalUrl = $url;

        if (!empty($queryString)) {
            $finalUrl .= '?' . $queryString;
        }

        return $finalUrl;
    }
}

