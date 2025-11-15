<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\StatsApiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('v1')->group(function () {
    Route::get('/', [ApiController::class, 'index']);
    Route::get('/status', [ApiController::class, 'status']);
    
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users', [UserApiController::class, 'index']);
        Route::get('/users/{user}', [UserApiController::class, 'show']);
        Route::post('/users', [UserApiController::class, 'store']);
        Route::put('/users/{user}', [UserApiController::class, 'update']);
        Route::delete('/users/{user}', [UserApiController::class, 'destroy']);
        
        Route::get('/stats', [StatsApiController::class, 'index']);
    });
});
