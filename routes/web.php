<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\FriendLinkController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\FriendLinkController as AdminFriendLinkController;
use App\Http\Controllers\Admin\AuditLogController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/api/announcements/modal', [AnnouncementController::class, 'getActiveModal'])->name('announcements.modal');

Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');

Route::get('/friend-links', [FriendLinkController::class, 'index'])->name('friend-links.index');
Route::get('/friend-links/apply', [FriendLinkController::class, 'apply'])->name('friend-links.apply');
Route::post('/friend-links/apply', [FriendLinkController::class, 'store'])->name('friend-links.store');

Route::get('/api/ads/{slot}', [AdController::class, 'show'])->name('ads.show');
Route::post('/api/ads/{advertisement}/click', [AdController::class, 'click'])->name('ads.click');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('admin.stats');
    
    Route::middleware('permission:site_settings.manage')->group(function () {
        Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('admin.site-settings.index');
        Route::post('/site-settings', [SiteSettingController::class, 'update'])->name('admin.site-settings.update');
    });
    
    Route::middleware('permission:announcements.manage')->group(function () {
        Route::resource('announcements', AdminAnnouncementController::class)->names('admin.announcements');
        Route::post('/announcements/{announcement}/toggle', [AdminAnnouncementController::class, 'toggleStatus'])->name('admin.announcements.toggle');
    });
    
    Route::middleware('permission:donations.manage')->group(function () {
        Route::resource('donations', AdminDonationController::class)->names('admin.donations');
    });
    
    Route::middleware('permission:advertisements.manage')->group(function () {
        Route::resource('advertisements', AdvertisementController::class)->names('admin.advertisements');
        Route::get('/ad-slots', [AdvertisementController::class, 'slots'])->name('admin.advertisements.slots');
        Route::post('/ad-slots', [AdvertisementController::class, 'storeSlot'])->name('admin.advertisements.slots.store');
    });
    
    Route::middleware('permission:friend_links.manage')->group(function () {
        Route::resource('friend-links', AdminFriendLinkController::class)->names('admin.friend-links');
        Route::post('/friend-links/{friendLink}/approve', [AdminFriendLinkController::class, 'approve'])->name('admin.friend-links.approve');
        Route::post('/friend-links/{friendLink}/reject', [AdminFriendLinkController::class, 'reject'])->name('admin.friend-links.reject');
    });
    
    Route::middleware('permission:audit_logs.view')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('admin.audit-logs.show');
    });
});
