<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;

Route::view('/', 'welcome')->name('home');

// category related routes
Route::group(['prefix' => 'c', 'as' => 'categories.', 'middleware' => []], function() {
    Route::controller(CategoryController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('category', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show categories and related products
        Route::get('{category:slug}', 'show')->name('show');

        // admin routes to manage categories
        Route::middleware(['auth', 'verified', 'role:editor'])->group(function() {
            Route::get('', 'index')->middleware(['permission:moderate categories'])->name('index');
            Route::get('{category:slug}/dashboard', 'dashboard')->middleware(['permission:moderate categories'])->name('dashboard');

            Route::get('create', 'create')->name('create');
            Route::get('{category:slug}/edit', 'edit')->name('edit');

            Route::post('{category:slug}/store', 'store')->name('store');
            Route::put('{category:slug}/update', 'update')->name('update');

            Route::delete('{category:slug}/delete', 'delete')->name('delete');
        });
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
