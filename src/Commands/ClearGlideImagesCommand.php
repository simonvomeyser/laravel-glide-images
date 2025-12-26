<?php

namespace SimonVomEyser\LaravelGlideImages\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearGlideImagesCommand extends Command
{
    protected $signature = 'glide-images:clear';

    protected $description = 'Clears all saved glide images';

    public function handle()
    {
        // The cache directory contains both the manipulated images and the
        // temporary remote source images (in the .remote-sources subdirectory).
        $directory = storage_path('app/'.config('glide-images.cache'));

        if (File::exists($directory)) {
            File::deleteDirectory($directory);
            $this->info('Glide cache and remote images cleared.');
        } else {
            $this->line('Glide cache is already empty.');
        }

        return 0;
    }
}
