<?php

namespace SimonVomEyser\LaravelGlideImages;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SimonVomEyser\LaravelGlideImages\Commands\LaravelGlideImagesCommand;

class LaravelGlideImagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-glide-images')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-glide-images_table')
            ->hasCommand(LaravelGlideImagesCommand::class);
    }
}
