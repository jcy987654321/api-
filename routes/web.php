<?php

use App\Http\Controllers\Front\ApiController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Frontend Routes
|--------------------------------------------------------------------------
| These routes are for the public frontend of the application
*/

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// API routes
Route::get('/apis', [ApiController::class, 'index'])->name('apis.index');
Route::get('/apis/{id}', [ApiController::class, 'show'])->name('apis.show');
Route::get('/api-test', [ApiController::class, 'test'])->name('api.test');

// About and Contact routes
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Include admin routes
require __DIR__.'/admin.php';
