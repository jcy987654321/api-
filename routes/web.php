<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\ApiController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\LinkController;
use App\Http\Controllers\Front\UserProfileController;
use App\Http\Controllers\Front\FeedbackController;
use App\Http\Controllers\Front\NotificationController;
use App\Http\Controllers\Admin\RealtimeController;

/*
|--------------------------------------------------------------------------
| Web Routes - Frontend Routes
|--------------------------------------------------------------------------
| These routes are for the public frontend of the application
*/

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['user.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User profile routes
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::prefix('user/profile')->name('user.profile.')->group(function () {
        Route::get('/edit', [UserProfileController::class, 'edit'])->name('edit');
        Route::put('/', [UserProfileController::class, 'update'])->name('update');
        Route::get('/password', [UserProfileController::class, 'showChangePassword'])->name('password.show');
        Route::put('/password', [UserProfileController::class, 'changePassword'])->name('password.update');
        Route::get('/avatar', [UserProfileController::class, 'showUploadAvatar'])->name('avatar.show');
        Route::post('/avatar', [UserProfileController::class, 'uploadAvatar'])->name('avatar.update');
        Route::delete('/avatar', [UserProfileController::class, 'deleteAvatar'])->name('avatar.destroy');
    });
    
    // API key routes
    Route::prefix('user/api-keys')->name('user.api-keys.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Front\UserApiController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Front\UserApiController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Front\UserApiController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Front\UserApiController::class, 'show'])->name('show');
        Route::delete('/{id}', [\App\Http\Controllers\Front\UserApiController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/regenerate', [\App\Http\Controllers\Front\UserApiController::class, 'regenerate'])->name('regenerate');
        Route::get('/{id}/stats', [\App\Http\Controllers\Front\UserApiController::class, 'stats'])->name('stats');
    });
});

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// API routes
Route::get('/apis', [ApiController::class, 'index'])->name('apis.index');
Route::get('/apis/{id}', [ApiController::class, 'show'])->name('apis.show');
Route::get('/api-test', [ApiController::class, 'test'])->name('api.test');

// About and Contact routes
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Friend links
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::post('/links', [LinkController::class, 'store'])->name('links.store');
Route::post('/links/{id}/click', [LinkController::class, 'click'])->whereNumber('id')->name('links.click');
Route::get('/links/{category}', [LinkController::class, 'getByCategory'])->name('links.category');

// Realtime SSE stream (protected)
Route::get('/api-internal/realtime/stream', [RealtimeController::class, 'stream'])
    ->middleware('admin.auth')
    ->name('admin.realtime.stream');

// Feedback routes
Route::get('/feedback', [FeedbackController::class, 'showForm'])->name('feedback.form');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/track/{id}', [FeedbackController::class, 'track'])->name('feedback.track');

// User authenticated routes
Route::middleware(['user.auth'])->group(function () {
    // User feedback routes
    Route::prefix('user/feedbacks')->name('feedbacks.')->group(function () {
        Route::get('/', [FeedbackController::class, 'myFeedbacks'])->name('index');
        Route::get('/{id}', [FeedbackController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [FeedbackController::class, 'reply'])->name('reply');
    });

    // Notification routes
    Route::prefix('user/notifications')->name('user.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
    });

    // Notification preferences
    Route::get('/user/notification-preferences', [NotificationController::class, 'preferences'])->name('user.notification-preferences');
    Route::put('/user/notification-preferences', [NotificationController::class, 'updatePreferences'])->name('user.notification-preferences.update');
});

// Include admin routes
require __DIR__.'/admin.php';
