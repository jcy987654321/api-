<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PluginController;
use App\Http\Controllers\Api\StatisticController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are for user-generated API interfaces
*/

Route::middleware(['api', 'api.cors'])->prefix('api')->group(function () {
    // API endpoints
    Route::get('/apis', [ApiController::class, 'index']);
    Route::get('/apis/{id}', [ApiController::class, 'show']);
    
    // Blog API endpoints
    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index']);
        Route::get('/search', [BlogController::class, 'search']);
        Route::get('/{slug}', [BlogController::class, 'show']);
        Route::get('/category/{slug}', [BlogController::class, 'categoryPosts']);
        Route::get('/tag/{slug}', [BlogController::class, 'tagPosts']);
    });
    
    Route::get('/blog/categories', [BlogController::class, 'categories']);
    Route::get('/blog/tags', [BlogController::class, 'tags']);
    
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    
    Route::get('/plugins', [PluginController::class, 'index']);
    Route::get('/statistics', [StatisticController::class, 'index']);
});

// Include internal API routes
require __DIR__.'/api-internal.php';