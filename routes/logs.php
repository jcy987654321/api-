<?php

use App\Http\Controllers\LogViewerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 日志查看器路由
|--------------------------------------------------------------------------
|
| 这些路由仅供管理员使用，在生产环境中应该通过中间件保护
|
*/

Route::prefix('admin/logs')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [LogViewerController::class, 'index'])->name('logs.index');
    Route::get('/recent', [LogViewerController::class, 'recent'])->name('logs.recent');
    Route::get('/stats', [LogViewerController::class, 'stats'])->name('logs.stats');
    Route::get('/search', [LogViewerController::class, 'search'])->name('logs.search');
    Route::post('/clean', [LogViewerController::class, 'clean'])->name('logs.clean');
    Route::get('/view/{path}', [LogViewerController::class, 'show'])->name('logs.show');
    Route::delete('/delete/{path}', [LogViewerController::class, 'destroy'])->name('logs.delete');
});
