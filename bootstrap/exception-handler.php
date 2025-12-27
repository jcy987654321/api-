<?php

/**
 * 全局异常处理脚本
 *
 * 此脚本在 Laravel 应用启动时自动加载，
 * 用于确保所有异常都被正确处理
 */

use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

// 注册全局异常处理回调
if (app()->bound('exception')) {
    app('exception')->handle(function (Throwable $e) {
        // 记录异常到日志
        if (!($e instanceof ApiException)) {
            Log::channel('application')->error('Unhandled exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $e;
    });
}
