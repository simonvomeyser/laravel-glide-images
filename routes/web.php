<?php

use Illuminate\Support\Facades\Route;
use SimonVomEyser\LaravelGlideImages\GlideController;

Route::get('/'.config('glide-images.endpoint').'/{path}',
    GlideController::class)
    ->where('path', '.*');
