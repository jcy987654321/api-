<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internal API Routes
|--------------------------------------------------------------------------
| These routes are for internal system management APIs
*/

Route::middleware(['api'])->prefix('api/internal')->group(function () {
    // Internal API Routes will be defined here
});