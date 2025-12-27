<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['api'])->prefix('api')->group(function () {
    // API Routes
});

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    // Admin Routes - 需要在 web 路由中定义
});
