<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleViewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

// Route::view('/', 'welcome')->name('home');

// article related routes
Route::group(['prefix' => '', 'as' => 'pages.', 'middleware' => []], function() {
    Route::controller(PageController::class)->group(function() {
        // routes to show pages
        // Route::get('/', 'welcome')->name('home');
        Route::view('/', 'welcome')->name('home');
    });
});

// article related routes
Route::group(['prefix' => 'a', 'as' => 'articles.', 'middleware' => []], function() {
    Route::controller(ArticleController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('article', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show articles and related products
        Route::get('{article:slug}', 'show')->name('show');

        // admin routes to manage articles
        Route::middleware(['auth', 'verified', 'role:super-administrator|editor'])->group(function() {
            Route::get('', 'index')->middleware(['permission:moderate articles'])->name('index');

            Route::get('{article:slug}/dashboard', 'dashboard')->middleware(['permission:moderate articles'])->name('dashboard');

            Route::get('create', 'create')->name('create');
            Route::get('{article:slug}/edit', 'edit')->name('edit');

            Route::post('store', 'store')->name('store');
            Route::put('{article:slug}/update', 'update')->name('update');

            Route::delete('{article:slug}/delete', 'delete')->name('delete');
        });
    });
});

// article related routes
Route::group(['prefix' => 'av', 'as' => 'articleviews.', 'middleware' => []], function() {
    Route::controller(ArticleViewController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('articleview', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show articles and related products
        Route::get('{articleview:slug}', 'show')->name('show');

        // admin routes to manage articles
        Route::middleware(['auth', 'verified', 'role:editor'])->group(function() {
            // ...
        });
    });
});

// category related routes
Route::group(['prefix' => 'c', 'as' => 'categories.', 'middleware' => []], function() {
    Route::controller(CategoryController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('category', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show categories and related products
        Route::get('{category:slug}', 'show')->name('show');

        // admin routes to manage categories
        Route::middleware(['auth', 'verified', 'role:super-administrator|editor'])->group(function() {
            Route::get('', 'index')->middleware(['permission:moderate category'])->name('index');

            Route::get('{category:slug}/dashboard', 'dashboard')->middleware(['permission:moderate category'])->name('dashboard');

            Route::get('create', 'create')->name('create');
            Route::get('{category:slug}/edit', 'edit')->name('edit');

            Route::post('store', 'store')->name('store');
            Route::put('{category:slug}/update', 'update')->name('update');

            Route::delete('{category:slug}/delete', 'delete')->name('delete');
        });
    });
});

// comment related routes
Route::group(['prefix' => 'comment', 'as' => 'comments.', 'middleware' => []], function() {
    Route::controller(CommentController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('comment', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show comments and related products
        Route::get('{comment:comment_uidentity}', 'show')->name('show');

        // route to add new comment
        Route::get('new', 'new')->name('new');

        // admin routes to manage comments
        Route::middleware(['auth', 'verified', 'role:role:super-administrator|editor'])->group(function() {
            Route::get('', 'index')->middleware(['permission:moderate comments'])->name('index');

            Route::get('{comment:comment_uidentity}/dashboard', 'dashboard')->middleware(['permission:moderate comments'])->name('dashboard');

            Route::get('{comment:comment_uidentity}/edit', 'edit')->name('edit');

            Route::post('{comment:comment_uidentity}/store', 'store')->name('store');
            Route::put('{comment:comment_uidentity}/update', 'update')->name('update');

            Route::delete('{comment:comment_uidentity}/delete', 'delete')->name('delete');
        });
    });
});

// tag related routes
Route::group(['prefix' => 't', 'as' => 'tags.', 'middleware' => []], function() {
    Route::controller(TagController::class)->group(function() {
        // prevent using create and new as slugs
        Route::pattern('tag', '^(?!create|new$)([a-zA-Z0-9-]+)$');

        // routes to show tags and related products
        Route::get('{tag:slug}', 'show')->name('show');

        // admin routes to manage tags
        Route::middleware(['auth', 'verified', 'role:super-administrator|editor'])->group(function() {
            Route::get('', 'index')->middleware(['permission:moderate tags'])->name('index');

            Route::get('{tag:slug}/dashboard', 'dashboard')->middleware(['permission:moderate tags'])->name('dashboard');

            Route::get('create', 'create')->middleware(['permission:moderate tags'])->name('create');
            Route::get('{tag:slug}/edit', 'edit')->middleware(['permission:moderate tags'])->name('edit');

            Route::post('store', 'store')->middleware(['permission:moderate tags'])->name('store');
            Route::put('{tag:slug}/update', 'update')->middleware(['permission:moderate tags'])->name('update');

            Route::delete('{tag:slug}/delete', 'delete')->middleware(['permission:moderate tags'])->name('delete');
        });
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
