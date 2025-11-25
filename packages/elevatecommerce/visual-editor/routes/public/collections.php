<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\VisualEditor\Http\Controllers\CollectionPublicController;

/*
|--------------------------------------------------------------------------
| Public Collection Routes
|--------------------------------------------------------------------------
| Public-facing storefront routes for viewing collections
*/


Route::get('/collections/{slug}', [CollectionPublicController::class, 'show'])
    ->name('collections.show');
