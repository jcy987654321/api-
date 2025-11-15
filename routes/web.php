<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('admin.stats');
});
