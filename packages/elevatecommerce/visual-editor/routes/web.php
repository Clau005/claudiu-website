<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\VisualEditor\Http\Controllers\DashboardController;
use ElevateCommerce\VisualEditor\Http\Controllers\Auth\LoginController;
use ElevateCommerce\VisualEditor\Http\Controllers\ThemeController;
use ElevateCommerce\VisualEditor\Http\Controllers\PageController;
use ElevateCommerce\VisualEditor\Http\Controllers\MediaController;
use ElevateCommerce\VisualEditor\Http\Controllers\Api\PageApiController;
use ElevateCommerce\VisualEditor\Http\Controllers\InquiryController;
use ElevateCommerce\VisualEditor\Http\Controllers\InquiryAdminController;
use ElevateCommerce\VisualEditor\Http\Middleware\RedirectIfNotAdmin;

/*
|--------------------------------------------------------------------------
| Visual Editor Routes
|--------------------------------------------------------------------------
*/

Route::prefix(config('visual-editor.route_prefix', 'admin'))
    ->name('admin.')
    ->middleware('web')
    ->group(function () {
        
        // Redirect /admin to /admin/dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        
        // Guest routes
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login']);
        });

        // Authenticated routes
        Route::middleware(RedirectIfNotAdmin::class)->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

            // Themes
            Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
            Route::post('/themes/{slug}/activate', [ThemeController::class, 'activate'])->name('themes.activate');
            Route::post('/themes/{slug}/duplicate', [ThemeController::class, 'duplicate'])->name('themes.duplicate');
            Route::delete('/themes/{slug}', [ThemeController::class, 'destroy'])->name('themes.destroy');

            // Pages
            Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
            Route::post('/pages/bulk-action', [PageController::class, 'bulkAction'])->name('pages.bulk-action');
            Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
            Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
            Route::get('/pages/{id}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::get('/pages/{id}/preview', [PageController::class, 'preview'])->name('pages.preview');
            Route::post('/pages/{id}/publish', [PageController::class, 'publish'])->name('pages.publish');
            Route::post('/pages/{id}/unpublish', [PageController::class, 'unpublish'])->name('pages.unpublish');
            Route::delete('/pages/{id}', [PageController::class, 'destroy'])->name('pages.destroy');

            // Media Library
            Route::get('/media', [MediaController::class, 'index'])->name('media.index');
            Route::post('/media/bulk-action', [MediaController::class, 'bulkAction'])->name('media.bulk-action');
            Route::get('/media/{id}', [MediaController::class, 'show'])->name('media.show');
            Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
            Route::put('/media/{id}/replace', [MediaController::class, 'replace'])->name('media.replace');
            Route::put('/media/{id}', [MediaController::class, 'update'])->name('media.update');
            Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

            // Inquiries
            Route::get('/inquiries', [InquiryAdminController::class, 'index'])->name('inquiries.index');
            Route::get('/inquiries/{inquiry}', [InquiryAdminController::class, 'show'])->name('inquiries.show');
            Route::put('/inquiries/{inquiry}', [InquiryAdminController::class, 'update'])->name('inquiries.update');
            Route::delete('/inquiries/{inquiry}', [InquiryAdminController::class, 'destroy'])->name('inquiries.destroy');
            Route::post('/inquiries/bulk-action', [InquiryAdminController::class, 'bulkAction'])->name('inquiries.bulk-action');

            // API Routes for Page Editor
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/pages/{id}', [PageApiController::class, 'show'])->name('pages.show');
                Route::put('/pages/{id}', [PageApiController::class, 'update'])->name('pages.update');
                Route::get('/themes/{themeId}/pages', [PageApiController::class, 'themePages'])->name('themes.pages');
                Route::put('/themes/{id}', [\ElevateCommerce\VisualEditor\Http\Controllers\Api\ThemeApiController::class, 'update'])->name('themes.update');
                Route::post('/themes/{id}/publish', [\ElevateCommerce\VisualEditor\Http\Controllers\Api\ThemeApiController::class, 'publish'])->name('themes.publish');
                Route::get('/media', [MediaController::class, 'apiIndex'])->name('media.index');
            });
        });
    });

/*
|--------------------------------------------------------------------------
| Admin Collection Routes
|--------------------------------------------------------------------------
*/
Route::prefix(config('visual-editor.route_prefix', 'admin'))
    ->name('admin.')
    ->middleware(['web', RedirectIfNotAdmin::class])
    ->group(__DIR__ . '/admin/collections.php');

/*
|--------------------------------------------------------------------------
| Collection API Routes
|--------------------------------------------------------------------------
*/
Route::prefix(config('visual-editor.route_prefix', 'admin') . '/api')
    ->name('admin.api.')
    ->middleware(['web', RedirectIfNotAdmin::class])
    ->group(__DIR__ . '/api/collections.php');

/*
|--------------------------------------------------------------------------
| Public Collection Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')
    ->group(__DIR__ . '/public/collections.php');

/*
|--------------------------------------------------------------------------
| Public Inquiry Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {
    Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
});

/*
|--------------------------------------------------------------------------
| Public Theme Routes (Catch-all - MUST BE LAST)
|--------------------------------------------------------------------------
| Public routes are now loaded in bootstrap/app.php using the `then:` parameter
| to ensure they load AFTER all package and app routes.
| See: bootstrap/app.php
*/
