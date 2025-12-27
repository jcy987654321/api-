<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * 数据库查询日志中间件
 */
class DatabaseQueryLoggerMiddleware
{
    /**
     * 是否已记录查询数量
     */
    protected bool $queryCountLogged = false;

    /**
     * 记录开始时间
     */
    protected float $startTime;

    /**
     * 总查询数
     */
    protected int $totalQueries = 0;

    /**
     * 处理请求
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 仅在开发环境记录
        if (!config('app.debug')) {
            return $next($request);
        }

        $this->startTime = microtime(true);
        $this->totalQueries = 0;

        // 监听数据库查询
        DB::listen(function ($query) {
            $this->logQuery($query);
            $this->totalQueries++;
        });

        $response = $next($request);

        // 记录查询统计
        $this->logQueryStats();

        return $response;
    }

    /**
     * 记录单条查询
     */
    protected function logQuery($query): void
    {
        $bindings = $this->formatBindings($query->bindings);
        $sql = $this->formatSql($query->sql, $bindings);

        $logData = [
            'sql' => $sql,
            'time_ms' => round($query->time, 2),
            'connection' => $query->connectionName,
            'bindings_count' => count($query->bindings),
        ];

        // 记录慢查询
        if ($query->time > 1000) {
            Log::channel('database')->warning('Slow Query Detected', array_merge($logData, [
                'slow_threshold_ms' => 1000,
                'query_time_ms' => $query->time,
            ]));
        } else {
            Log::channel('database')->debug('Database Query', $logData);
        }
    }

    /**
     * 格式化 SQL 绑定参数
     */
    protected function formatBindings(array $bindings): array
    {
        return array_map(function ($binding) {
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
        }, $bindings);
    }

    /**
     * 格式化 SQL 语句
     */
    protected function formatSql(string $sql, array $bindings): string
    {
        // 将占位符替换为实际值
        foreach ($bindings as $binding) {
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }
        return $sql;
    }

    /**
     * 记录查询统计
     */
    protected function logQueryStats(): void
    {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);

        Log::channel('database')->info('Query Statistics', [
            'total_queries' => $this->totalQueries,
            'total_time_ms' => $duration,
            'avg_time_ms' => $this->totalQueries > 0 ? round($duration / $this->totalQueries, 2) : 0,
            'endpoint' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }
}
