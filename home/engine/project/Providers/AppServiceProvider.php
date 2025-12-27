<?php

namespace App\Providers;

use App\Http\Middleware\RequestLoggerMiddleware;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册任何应用服务
     */
    public function register(): void
    {
        //
    }

    /**
     * 启动任何应用服务
     */
    public function boot(): void
    {
        // 注册请求日志中间件别名
        $router = $this->app['router'];
        $router->aliasMiddleware('request.logger', RequestLoggerMiddleware::class);
    }
}
