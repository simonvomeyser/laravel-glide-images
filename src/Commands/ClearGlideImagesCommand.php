<?php

namespace SimonVomEyser\LaravelGlideImages\Commands;

use Illuminate\Console\Command;
use SimonVomEyser\LaravelGlideImages\File;

class ClearGlideImagesCommand extends Command
{
    protected $signature = 'glide-images:clear';

    protected $description = 'Clears all saved glide images';

    public function handle()
    {
        $directory = storage_path('/app/'.config('glide-images.cache'));

        if (empty(File::exists($directory))) {
            $this->line('No images in glide cache');

            return 0;
        }

        File::deleteDirectory($directory);
        $this->line('Entire glide cache directory deleted.');

        return 0;
    }
}
