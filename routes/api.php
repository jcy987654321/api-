<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::prefix('v1')->group(function () {
    Route::get('/', [ApiController::class, 'index']);
    Route::get('/status', [ApiController::class, 'status']);
});
