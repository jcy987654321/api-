<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RealtimeService extends BaseService
{
    public function __construct(private readonly StatisticsService $statistics)
    {
    }

    public function getDashboardStats(): array
    {
        return [
            'total_apis' => $this->statistics->getApiDefinitionCount(),
            'total_blog_posts' => $this->statistics->getBlogPostCount(),
            'system_status' => 'active',
        ];
    }

    public function getApiStats(): array
    {
        $today = Carbon::today();

        return [
            'total_calls' => $this->statistics->calculateApiCalls(),
            'today_calls' => $this->statistics->calculateApiCalls($today),
            'top_apis' => $this->statistics->getTopApis(10, $today),
        ];
    }

    public function getAccessStats(): array
    {
        $today = Carbon::today();

        return [
            'pv_total' => $this->statistics->calculatePV(),
            'uv_total' => $this->statistics->calculateUV(),
            'pv_today' => $this->statistics->calculatePV($today),
            'uv_today' => $this->statistics->calculateUV($today),
            'online_users' => (int) Cache::get('realtime:sse_connections', 0),
        ];
    }

    public function getBlogStats(): array
    {
        $today = Carbon::today();

        return [
            'posts_total' => $this->statistics->getBlogPostCount(),
            'views_total' => $this->statistics->calculateBlogViews(),
            'views_today' => $this->statistics->calculateBlogViews($today),
        ];
    }

    public function getSystemStats(): array
    {
        $metrics = $this->statistics->getSystemMetrics();

        return [
            'cpu_percent' => $metrics['cpu_percent'],
            'memory_percent' => $metrics['memory_percent'],
            'db_queries_today' => $metrics['db_queries_today'],
            'disk_percent' => $metrics['disk_percent'],
        ];
    }

    public function getLinksStats(): array
    {
        return [
            'links_total' => 0,
            'links_clicks_today' => 0,
        ];
    }

    /**
     * @return array{timestamp: string, dashboard: array, access: array, api: array, blog: array, system: array, links: array, activities: array}
     */
    public function getSnapshot(): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'dashboard' => $this->getDashboardStats(),
            'access' => $this->getAccessStats(),
            'api' => $this->getApiStats(),
            'blog' => $this->getBlogStats(),
            'system' => $this->getSystemStats(),
            'links' => $this->getLinksStats(),
            'activities' => $this->statistics->getRecentActivities(10),
        ];
    }

    /**
     * @return array{changed: array, delta: array}
     */
    public function calculateChanges(array $current, ?array $previous): array
    {
        if ($previous === null) {
            return [
                'changed' => $current,
                'delta' => $this->calculateNumericDeltas($current, []),
            ];
        }

        return [
            'changed' => $this->diffRecursive($current, $previous),
            'delta' => $this->calculateNumericDeltas($current, $previous),
        ];
    }

    private function diffRecursive(array $current, array $previous): array
    {
        $changed = [];

        foreach ($current as $key => $value) {
            if (!array_key_exists($key, $previous)) {
                $changed[$key] = $value;
                continue;
            }

            $prev = $previous[$key];

            if (is_array($value) && is_array($prev)) {
                if ($this->isList($value) || $this->isList($prev)) {
                    if ($value !== $prev) {
                        $changed[$key] = $value;
                    }
                    continue;
                }

                $nested = $this->diffRecursive($value, $prev);
                if ($nested !== []) {
                    $changed[$key] = $nested;
                }
                continue;
            }

            if ($value !== $prev) {
                $changed[$key] = $value;
            }
        }

        return $changed;
    }

    private function calculateNumericDeltas(array $current, array $previous): array
    {
        $delta = [];

        foreach ($current as $key => $value) {
            $prev = $previous[$key] ?? null;

            if (is_array($value) && is_array($prev)) {
                if (!$this->isList($value) && !$this->isList($prev)) {
                    $nested = $this->calculateNumericDeltas($value, $prev);
                    if ($nested !== []) {
                        $delta[$key] = $nested;
                    }
                }
                continue;
            }

            if (is_numeric($value) && is_numeric($prev)) {
                $delta[$key] = $value - $prev;
            } elseif (is_numeric($value) && $prev === null) {
                $delta[$key] = $value;
            }
        }

        return $delta;
    }

    private function isList(array $array): bool
    {
        return array_is_list($array);
    }
}
