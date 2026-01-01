<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApiManageController;
use App\Http\Controllers\Admin\BlogManageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| These routes are for the admin backend and require authentication
*/

Route::middleware(['web'])->prefix('admin')->group(function () {
    // Authentication routes (no auth middleware)
    Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('admin.login.post');
    
    // Protected admin routes with AdminAuth middleware
    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('admin.password.change');
        Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('admin.password.update');

        // Settings (profile link)
        Route::get('/settings/profile', [ProfileController::class, 'show'])->name('admin.settings.profile');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Real-time stream for dashboard
        Route::get('/realtime/stream', [DashboardController::class, 'realtimeStream'])->name('admin.realtime.stream');

        // Search
        Route::get('/search', [DashboardController::class, 'search'])->name('admin.search');
        
        // API Management routes
        Route::prefix('apis')->group(function () {
            Route::get('/', [ApiManageController::class, 'index'])->name('admin.apis.index');
            Route::get('/create', [ApiManageController::class, 'create'])->name('admin.apis.create');
            Route::post('/', [ApiManageController::class, 'store'])->name('admin.apis.store');
            Route::get('/{id}/edit', [ApiManageController::class, 'edit'])->name('admin.apis.edit');
            Route::put('/{id}', [ApiManageController::class, 'update'])->name('admin.apis.update');
            Route::delete('/{id}', [ApiManageController::class, 'destroy'])->name('admin.apis.destroy');

            // API Categories
            Route::prefix('categories')->group(function () {
                Route::get('/', [ApiManageController::class, 'categories'])->name('admin.api-categories.index');
                Route::get('/create', [ApiManageController::class, 'createCategory'])->name('admin.api-categories.create');
                Route::post('/', [ApiManageController::class, 'storeCategory'])->name('admin.api-categories.store');
                Route::get('/{id}/edit', [ApiManageController::class, 'editCategory'])->name('admin.api-categories.edit');
                Route::put('/{id}', [ApiManageController::class, 'updateCategory'])->name('admin.api-categories.update');
                Route::delete('/{id}', [ApiManageController::class, 'destroyCategory'])->name('admin.api-categories.destroy');
            });

            // API Parameters
            Route::prefix('parameters')->group(function () {
                Route::get('/', [ApiManageController::class, 'parameters'])->name('admin.api-params.index');
                Route::get('/create', [ApiManageController::class, 'createParameter'])->name('admin.api-params.create');
                Route::post('/', [ApiManageController::class, 'storeParameter'])->name('admin.api-params.store');
                Route::get('/{id}/edit', [ApiManageController::class, 'editParameter'])->name('admin.api-params.edit');
                Route::put('/{id}', [ApiManageController::class, 'updateParameter'])->name('admin.api-params.update');
                Route::delete('/{id}', [ApiManageController::class, 'destroyParameter'])->name('admin.api-params.destroy');
            });
        });
        
        // Blog Management routes
        Route::prefix('blogs')->group(function () {
            Route::get('/', [BlogManageController::class, 'index'])->name('admin.blogs.index');
            Route::get('/create', [BlogManageController::class, 'create'])->name('admin.blogs.create');
            Route::post('/', [BlogManageController::class, 'store'])->name('admin.blogs.store');
            Route::get('/{id}/edit', [BlogManageController::class, 'edit'])->name('admin.blogs.edit');
            Route::put('/{id}', [BlogManageController::class, 'update'])->name('admin.blogs.update');
            Route::delete('/{id}', [BlogManageController::class, 'destroy'])->name('admin.blogs.destroy');
            Route::post('/{id}/publish', [BlogManageController::class, 'publish'])->name('admin.blogs.publish');
            Route::post('/{id}/draft', [BlogManageController::class, 'draft'])->name('admin.blogs.draft');

            // Blog Categories
            Route::prefix('categories')->group(function () {
                Route::get('/', [BlogManageController::class, 'categories'])->name('admin.blog-categories.index');
                Route::get('/create', [BlogManageController::class, 'createCategory'])->name('admin.blog-categories.create');
                Route::post('/', [BlogManageController::class, 'storeCategory'])->name('admin.blog-categories.store');
                Route::get('/{id}/edit', [BlogManageController::class, 'editCategory'])->name('admin.blog-categories.edit');
                Route::put('/{id}', [BlogManageController::class, 'updateCategory'])->name('admin.blog-categories.update');
                Route::delete('/{id}', [BlogManageController::class, 'destroyCategory'])->name('admin.blog-categories.destroy');
            });

            // Blog Tags
            Route::prefix('tags')->group(function () {
                Route::get('/', [BlogManageController::class, 'tags'])->name('admin.blog-tags.index');
                Route::get('/create', [BlogManageController::class, 'createTag'])->name('admin.blog-tags.create');
                Route::post('/', [BlogManageController::class, 'storeTag'])->name('admin.blog-tags.store');
                Route::get('/{id}/edit', [BlogManageController::class, 'editTag'])->name('admin.blog-tags.edit');
                Route::put('/{id}', [BlogManageController::class, 'updateTag'])->name('admin.blog-tags.update');
                Route::delete('/{id}', [BlogManageController::class, 'destroyTag'])->name('admin.blog-tags.destroy');
            });
        });
        
        // Settings routes
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.index');
            Route::post('/update', [SettingsController::class, 'update'])->name('admin.settings.update');
        });
        
        // Plugins routes
        Route::prefix('plugins')->group(function () {
            Route::get('/', [SettingsController::class, 'plugins'])->name('admin.plugins.index');
            Route::get('/settings', [SettingsController::class, 'pluginSettings'])->name('admin.plugins.settings');
        });

        // Statistics routes
        Route::prefix('statistics')->group(function () {
            Route::get('/access', [SettingsController::class, 'accessStatistics'])->name('admin.statistics.access');
            Route::get('/api', [SettingsController::class, 'apiStatistics'])->name('admin.statistics.api');
            Route::get('/users', [SettingsController::class, 'userStatistics'])->name('admin.statistics.users');
        });
        
        // Links routes
        Route::prefix('links')->group(function () {
            Route::get('/', [SettingsController::class, 'links'])->name('admin.links.index');
        });

        // Log Management routes
        Route::prefix('logs')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');
            Route::get('/view/{channel}/{date?}', [\App\Http\Controllers\Admin\LogController::class, 'view'])->name('admin.logs.view');
            Route::post('/clear', [\App\Http\Controllers\Admin\LogController::class, 'clear'])->name('admin.logs.clear');
        });
    });
});