<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\VisualEditor\Http\Controllers\PageRendererController;

/*
|--------------------------------------------------------------------------
| Public Theme Routes (Catch-all)
|--------------------------------------------------------------------------
|
| Single catch-all route for all theme pages. The controller handles
| page lookup and rendering based on the active theme.
| This file is loaded LAST to ensure specific routes take precedence.
|
*/

// Home page
Route::get('/', [PageRendererController::class, 'render'])
    ->middleware('cache.headers:public;max_age=3600');

// Catch-all for all other pages
Route::get('/{slug}', [PageRendererController::class, 'render'])
    ->where('slug', '.*')
    ->middleware('cache.headers:public;max_age=3600');
