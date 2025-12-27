<?php

namespace App\Providers;

use App\Http\Middleware\DatabaseQueryLoggerMiddleware;
use App\Http\Middleware\RequestLoggerMiddleware;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * 注册服务
     */
    public function register(): void
    {
        //
    }

    /**
     * 启动服务
     */
    public function boot(): void
    {
        // 注册路由中间件别名
        $router = $this->app['router'];

        $router->aliasMiddleware('request.logger', RequestLoggerMiddleware::class);
        $router->aliasMiddleware('db.query.logger', DatabaseQueryLoggerMiddleware::class);
    }
}
