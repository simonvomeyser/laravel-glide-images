<?php

use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

if (!function_exists('glide')) {

    function glide($pathToImage, string | array $args  = [])
    {
        $leadingHttpPattern = "/^(http:\/\/|https:\/\/)/";

        $url = preg_match($leadingHttpPattern, $pathToImage) ?
            $pathToImage :
            url($pathToImage);

        $url = str_replace(url('/'), url('/' . config('glide-images.endpoint')), $url);

        if (is_string($args)) {
            $args = ['w' => $args];
        }

        if (!array_key_exists('fit', $args)) {
            $args['fit'] = 'max';
        }

        $httpSignature = SignatureFactory::create(config('app.key'));

        $args['s'] = $httpSignature->generateSignature($url, $args);

        $queryString = http_build_query($args);

        $finalUrl = $url;
        if (!empty($queryString)) {
            $finalUrl .= '?' . $queryString;
        }

        return $finalUrl;
    }
}

