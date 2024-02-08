<?php

namespace SimonVomEyser\LaravelGlideImages;

use Illuminate\Support\Facades\Config;
use SimonVomEyser\LaravelGlideImages\Commands\ClearGlideImagesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelGlideImagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        Config::set('filesystems.disks.glide_public_path', [
            'driver' => 'local',
            'root' => public_path(),
            'url' => config('app.url').'/',
            'visibility' => 'public',
            'throw' => false,
        ]);

        $package
            ->name('laravel-glide-images')
            ->hasRoute('web')
            ->hasConfigFile()
            ->hasCommand(ClearGlideImagesCommand::class);
    }
}
