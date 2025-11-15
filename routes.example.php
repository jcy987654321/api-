<?php

/**
 * Example Routes for Public Layout
 * 
 * Copy these routes to your routes/web.php file to use the public layout pages.
 * Adjust namespaces and controller references as needed for your application.
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes serve the public-facing pages using the public layout.
| All routes use PJAX for smooth navigation.
|
*/

// Homepage
Route::get('/', function () {
    return view('public.home');
})->name('home');

// Features page
Route::get('/features', function () {
    return view('public.features');
})->name('features');

// About page
Route::get('/about', function () {
    return view('public.about');
})->name('about');

// Documentation page (to be created)
Route::get('/docs', function () {
    return view('public.docs');
})->name('docs');

// Contact page (to be created)
Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

// Pricing page (to be created)
Route::get('/pricing', function () {
    return view('public.pricing');
})->name('pricing');

// Support page (to be created)
Route::get('/support', function () {
    return view('public.support');
})->name('support');

/*
|--------------------------------------------------------------------------
| Legal Pages
|--------------------------------------------------------------------------
|
| Privacy policy, terms of service, and cookie policy pages.
|
*/

Route::get('/privacy', function () {
    return view('public.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('public.terms');
})->name('terms');

Route::get('/cookies', function () {
    return view('public.cookies');
})->name('cookies');

/*
|--------------------------------------------------------------------------
| Alternative: Using Controllers
|--------------------------------------------------------------------------
|
| For more complex logic, use controllers instead of closures:
|
| Route::get('/', [PublicController::class, 'home'])->name('home');
| Route::get('/features', [PublicController::class, 'features'])->name('features');
| Route::get('/about', [PublicController::class, 'about'])->name('about');
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes require authentication middleware.
| Adjust according to your authentication setup (Laravel Breeze, Jetstream, etc.)
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| API Routes (Optional)
|--------------------------------------------------------------------------
|
| If you need API endpoints for your public pages:
|
*/

// Route::prefix('api')->group(function () {
//     Route::get('/features', function () {
//         return response()->json([
//             'features' => [
//                 ['title' => 'Feature 1', 'description' => '...'],
//                 ['title' => 'Feature 2', 'description' => '...'],
//             ]
//         ]);
//     });
// });

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
|
| This route will match any undefined routes and return a 404 page.
| Uncomment to enable:
|
*/

// Route::fallback(function () {
//     return view('public.404');
// });
