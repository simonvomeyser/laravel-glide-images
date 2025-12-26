# On the fly image manipulations with Glide for Laravel
[![Latest Version on Packagist](https://img.shields.io/packagist/v/simonvomeyser/laravel-glide-images.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-glide-images)

⚠️ This is a work-in-progress, the api might change considerably before the first major release.

This package provides a simple `glide(url, options)` php helper function to generate image urls with [Glide](https://glide.thephpleague.com/2.0/api/quick-reference/) on the fly in your templates.

It's aimed for ease of use and simplicity, just install this and use the `glide()` helper to handle almost anything. This package handles the setup of the endpoint for you.

```index.blade.php
<!-- will generate an image 500px in width -->
<img src="{{ glide('images/image.jpg', 500) }}">

<!-- will generate an 500x500 image in grayscale -->
<img src="{{ glide('images/image.jpg', ['w' => 500, 'h'=> 500, 'filt' => 'grayscale']) }}">

<!-- will also work with external urls -->
<img src="{{ glide('https://some-domain.com/image.png', 400) }}">

<!-- will also work -->
<img src="{{ glide(url('images/image.jpg')) }}">
```

## Installation

You can install the package via composer:

```bash
composer require simonvomeyser/laravel-glide-images
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="glide-images-config"
```

## Usage

The `glide()` helper allows you to generate URLs for image manipulations on the fly. 

### External URLs

You can also pass external URLs to the `glide()` helper. The package will automatically download the remote image, process it, and cache the result. To save disk space, the original remote image is deleted after the manipulated version has been generated and cached.

```blade
<img src="{{ glide('https://external-domain.com/photo.jpg', 600) }}">
```

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
