<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\AdminFeedbackController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Feedback routes (public)
Route::prefix('feedback')->name('feedback.')->group(function () {
    Route::get('/', [FeedbackController::class, 'index'])->name('form');
    Route::post('/', [FeedbackController::class, 'store'])->name('store');
    Route::get('/thankyou', [FeedbackController::class, 'thankyou'])->name('thankyou');
    Route::get('/thread/{token}', [FeedbackController::class, 'viewThread'])->name('thread.view');
    Route::post('/thread/{token}/reply', [FeedbackController::class, 'reply'])->name('thread.reply');
    Route::get('/attachment/{message}', [FeedbackController::class, 'downloadAttachment'])->name('attachment.download');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    
    // Admin feedback routes
    Route::prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/', [AdminFeedbackController::class, 'index'])->name('index');
        Route::get('/export', [AdminFeedbackController::class, 'export'])->name('export');
        Route::get('/{thread}', [AdminFeedbackController::class, 'show'])->name('show');
        Route::post('/{thread}/reply', [AdminFeedbackController::class, 'reply'])->name('reply');
        Route::put('/{thread}/status', [AdminFeedbackController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/message/{message}/redact', [AdminFeedbackController::class, 'redactMessage'])->name('message.redact');
        Route::delete('/message/{message}', [AdminFeedbackController::class, 'deleteMessage'])->name('message.delete');
        Route::delete('/{thread}', [AdminFeedbackController::class, 'deleteThread'])->name('delete');
    });
});
