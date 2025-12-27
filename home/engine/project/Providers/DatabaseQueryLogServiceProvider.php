<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DatabaseQueryLogServiceProvider extends ServiceProvider
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
        // 仅在开发环境监听数据库查询
        if ($this->app->isLocal() || config('app.debug')) {
            $this->listenToQueries();
        }
    }

    /**
     * 监听数据库查询
     */
    protected function listenToQueries(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $this->logQuery($query);
        });
    }

    /**
     * 记录查询
     */
    protected function logQuery(QueryExecuted $query): void
    {
        // 格式化绑定参数
        $bindings = array_map(function ($binding) {
            if (is_bool($binding)) {
                return $binding ? 'true' : 'false';
            }
            if (is_null($binding)) {
                return 'NULL';
            }
            if (is_numeric($binding)) {
                return $binding;
            }
            return "'" . addslashes((string) $binding) . "'";
        }, $query->bindings);

        // 替换占位符
        $sql = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
        $formattedSql = vsprintf($sql, $bindings);

        $logData = [
            'sql' => $formattedSql,
            'time_ms' => round($query->time, 2),
            'connection' => $query->connectionName,
            'bindings_count' => count($query->bindings),
        ];

        // 慢查询警告
        if ($query->time > 1000) {
            Log::channel('database')->warning('Slow Query Detected', array_merge($logData, [
                'slow_threshold_ms' => 1000,
                'query_time_ms' => $query->time,
            ]));
        } else {
            Log::channel('database')->debug('Database Query', $logData);
        }
    }
}
