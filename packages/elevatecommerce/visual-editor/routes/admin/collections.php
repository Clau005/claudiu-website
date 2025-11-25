<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\VisualEditor\Http\Controllers\CollectionAdminController;

/*
|--------------------------------------------------------------------------
| Admin Collection Routes
|--------------------------------------------------------------------------
| Admin routes for managing collections
*/

// Bulk actions (must be before resource routes)
Route::post('collections/bulk-action', [CollectionAdminController::class, 'bulkAction'])
    ->name('collections.bulk-action');

// Collection item management (must be before resource routes)
Route::post('collections/{collection}/items', [CollectionAdminController::class, 'addItem'])
    ->name('collections.items.add')
    ->where('collection', '[0-9]+');

Route::delete('collections/{collection}/items/{collectable}', [CollectionAdminController::class, 'removeItem'])
    ->name('collections.items.remove')
    ->where(['collection' => '[0-9]+', 'collectable' => '[0-9]+']);

Route::post('collections/{collection}/items/reorder', [CollectionAdminController::class, 'reorderItems'])
    ->name('collections.items.reorder')
    ->where('collection', '[0-9]+');

// Publish/unpublish (must be before resource routes)
Route::post('collections/{collection}/publish', [CollectionAdminController::class, 'publish'])
    ->name('collections.publish')
    ->where('collection', '[0-9]+');

Route::post('collections/{collection}/unpublish', [CollectionAdminController::class, 'unpublish'])
    ->name('collections.unpublish')
    ->where('collection', '[0-9]+');

// Collection CRUD (resource routes should be last)
Route::resource('collections', CollectionAdminController::class);
