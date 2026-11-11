<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

// Live Search Endpoint
Route::get('/search', [BlogController::class, 'liveSearch'])
    ->middleware('throttle:search')
    ->name('api.search');

// Read-Only Public REST API v1
Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostApiController::class, 'index'])->name('api.v1.posts.index');
    Route::get('/posts/trending', [PostApiController::class, 'trending'])->name('api.v1.posts.trending');
    Route::get('/posts/{slug}', [PostApiController::class, 'show'])->name('api.v1.posts.show');

    Route::get('/categories', [CategoryApiController::class, 'index'])->name('api.v1.categories.index');
    Route::get('/categories/{slug}', [CategoryApiController::class, 'show'])->name('api.v1.categories.show');
    Route::get('/categories/{slug}/posts', [CategoryApiController::class, 'posts'])->name('api.v1.categories.posts');
});
