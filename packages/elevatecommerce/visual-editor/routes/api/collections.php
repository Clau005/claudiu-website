<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\VisualEditor\Http\Controllers\Api\CollectionApiController;

/*
|--------------------------------------------------------------------------
| Collection API Routes
|--------------------------------------------------------------------------
| API routes for collection editor and data fetching
*/

Route::get('collections/{collection}/available-items', [CollectionApiController::class, 'availableItems'])
    ->name('collections.available-items');

Route::get('collections/{collection}/items', [CollectionApiController::class, 'items'])
    ->name('collections.items');
