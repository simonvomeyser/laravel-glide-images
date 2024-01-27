<?php

namespace SimonVomEyser\LaravelGlideImages;

use SimonVomEyser\LaravelGlideImages\Commands\ClearGlideImagesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelGlideImagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        app()->config["filesystems.disks.glide_public_path"] = [
            'driver' => 'local',
            'root' => public_path(),
            'url' => config('app.url') . '/',
            'visibility' => 'public',
            'throw' => false,
        ];

        $package
            ->name('laravel-glide-images')
            ->hasRoute('web')
            ->hasConfigFile()
            ->hasCommand(ClearGlideImagesCommand::class);
    }
}
