<?php

namespace SimonVomEyser\LaravelGlideImages\Commands;

use Illuminate\Console\Command;

class LaravelGlideImagesCommand extends Command
{
    public $signature = 'laravel-glide-images';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
