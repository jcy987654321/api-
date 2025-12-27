<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApiManageController;
use App\Http\Controllers\Admin\BlogManageController;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| These routes are for the admin backend and require authentication
*/

Route::middleware(['web'])->prefix('admin')->group(function () {
    // Authentication routes (no auth middleware)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    
    // Protected admin routes with AdminAuth middleware
    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // API Management routes
        Route::prefix('apis')->group(function () {
            Route::get('/', [ApiManageController::class, 'index'])->name('admin.apis.index');
            Route::get('/create', [ApiManageController::class, 'create'])->name('admin.apis.create');
            Route::post('/', [ApiManageController::class, 'store'])->name('admin.apis.store');
            Route::get('/{id}/edit', [ApiManageController::class, 'edit'])->name('admin.apis.edit');
            Route::put('/{id}', [ApiManageController::class, 'update'])->name('admin.apis.update');
            Route::delete('/{id}', [ApiManageController::class, 'destroy'])->name('admin.apis.destroy');
        });
        
        // Blog Management routes
        Route::prefix('blogs')->group(function () {
            Route::get('/', [BlogManageController::class, 'index'])->name('admin.blogs.index');
            Route::get('/create', [BlogManageController::class, 'create'])->name('admin.blogs.create');
            Route::post('/', [BlogManageController::class, 'store'])->name('admin.blogs.store');
            Route::get('/{id}/edit', [BlogManageController::class, 'edit'])->name('admin.blogs.edit');
            Route::put('/{id}', [BlogManageController::class, 'update'])->name('admin.blogs.update');
            Route::delete('/{id}', [BlogManageController::class, 'destroy'])->name('admin.blogs.destroy');
        });
        
        // Settings routes
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.index');
            Route::post('/update', [SettingsController::class, 'update'])->name('admin.settings.update');
        });
        
        // Plugins routes
        Route::prefix('plugins')->group(function () {
            Route::get('/', [SettingsController::class, 'plugins'])->name('admin.plugins.index');
        });
        
        // Statistics routes
        Route::prefix('statistics')->group(function () {
            Route::get('/', [SettingsController::class, 'statistics'])->name('admin.statistics.index');
        });
        
        // Links routes
        Route::prefix('links')->group(function () {
            Route::get('/', [SettingsController::class, 'links'])->name('admin.links.index');
        });
    });
});