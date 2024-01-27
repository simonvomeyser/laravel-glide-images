# On the fly image manipulations with Glide for Laravel
[![Latest Version on Packagist](https://img.shields.io/packagist/v/simonvomeyser/laravel-glide-images.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-glide-images)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/simonvomeyser/laravel-glide-images/run-tests?label=tests)](

⚠️ This is a work-in-progress, the api might change considerably before the first release.

This package provides a simple `glide(url, options)` php helper function to generate image urls with [Glide](https://glide.thephpleague.com/2.0/api/quick-reference/) on the fly in your templates.

It's aimed for easy of use and pretty opinionated in the current development stage.

```index.blade.php
<!-- will generate an image 500px in width -->
<img src="{{ glide('images/image.jpg', 500) }}">

<!-- will generate an 500x500 image in grayscale -->
<img src="{{ glide('images/image.jpg', ['w' => 500, 'h'=> 500 'filt' => 'grayscale']) }}">
```

## Installation

You can install the package via composer:

```bash
composer require simonvomeyser/laravel-glide-images
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-glide-images-config"
```

## Usage

wip

## Testing

wip

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Simon vom Eyser](https://github.com/simonvomeyser)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
