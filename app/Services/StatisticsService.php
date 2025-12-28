<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class StatisticsService extends BaseService
{
    public function calculatePV(?Carbon $date = null): int
    {
        return $this->remember(__FUNCTION__, [$date?->toDateString() ?? 'total'], function () use ($date) {
            return $this->countRequestLogEntries($date, fn (array $context) => true);
        });
    }

    public function calculateUV(?Carbon $date = null): int
    {
        return $this->remember(__FUNCTION__, [$date?->toDateString() ?? 'total'], function () use ($date) {
            $ips = [];

            $this->iterateRequestLogs($date, function (string $line) use (&$ips) {
                $context = $this->parseLogContext($line);
                if (!is_array($context)) {
                    return;
                }

                $ip = $context['ip'] ?? null;
                if (is_string($ip) && $ip !== '') {
                    $ips[$ip] = true;
                }
            });

            return count($ips);
        });
    }

    public function calculateApiCalls(?Carbon $date = null): int
    {
        return $this->remember(__FUNCTION__, [$date?->toDateString() ?? 'total'], function () use ($date) {
            return $this->countRequestLogEntries($date, function (array $context) {
                $url = $context['url'] ?? null;
                if (!is_string($url)) {
                    return false;
                }

                $path = $this->extractPath($url);

                return str_starts_with($path, '/api') || str_starts_with($path, '/api-internal');
            });
        });
    }

    public function calculateBlogViews(?Carbon $date = null): int
    {
        return $this->remember(__FUNCTION__, [$date?->toDateString() ?? 'total'], function () use ($date) {
            return $this->countRequestLogEntries($date, function (array $context) {
                $url = $context['url'] ?? null;
                if (!is_string($url)) {
                    return false;
                }

                $path = $this->extractPath($url);

                return $path === '/blog' || str_starts_with($path, '/blog/');
            });
        });
    }

    /**
     * @return array<int, array{endpoint: string, calls: int}>
     */
    public function getTopApis(int $limit = 10, ?Carbon $date = null): array
    {
        return $this->remember(__FUNCTION__, [$limit, $date?->toDateString() ?? 'total'], function () use ($limit, $date) {
            $counts = [];

            $this->iterateRequestLogs($date, function (string $line) use (&$counts) {
                $context = $this->parseLogContext($line);
                if (!is_array($context)) {
                    return;
                }

                $url = $context['url'] ?? null;
                $method = $context['method'] ?? 'GET';

                if (!is_string($url)) {
                    return;
                }

                $path = $this->extractPath($url);

                if (!(str_starts_with($path, '/api') || str_starts_with($path, '/api-internal'))) {
                    return;
                }

                $endpoint = strtoupper((string) $method) . ' ' . $path;
                $counts[$endpoint] = ($counts[$endpoint] ?? 0) + 1;
            });

            arsort($counts);

            return collect($counts)
                ->take($limit)
                ->map(fn (int $calls, string $endpoint) => ['endpoint' => $endpoint, 'calls' => $calls])
                ->values()
                ->all();
        });
    }

    /**
     * @return array<int, array{timestamp: string, level: string, message: string}>
     */
    public function getRecentActivities(int $limit = 10): array
    {
        return $this->remember(__FUNCTION__, [$limit], function () use ($limit) {
            $entries = [];

            foreach (['security', 'requests'] as $channel) {
                foreach ($this->getLogPaths($channel) as $filePath) {
                    foreach ($this->readLastLines($filePath, 250) as $line) {
                        $parsed = $this->parseLogLine($line);
                        if ($parsed === null) {
                            continue;
                        }

                        $entries[] = [
                            'timestamp' => $parsed['timestamp'],
                            'level' => $parsed['level'],
                            'message' => $parsed['message'],
                        ];
                    }
                }
            }

            usort($entries, fn (array $a, array $b) => strcmp($b['timestamp'], $a['timestamp']));

            return array_slice($entries, 0, $limit);
        });
    }

    /**
     * @return array{cpu_percent: float, memory_percent: float, memory_used_mb: float, memory_total_mb: float, disk_percent: float, disk_used_gb: float, disk_total_gb: float, db_queries_today: int}
     */
    public function getSystemMetrics(): array
    {
        return $this->remember(__FUNCTION__, [], function () {
            $cpuPercent = $this->getCpuUsagePercent();
            [$memoryUsedMb, $memoryTotalMb, $memoryPercent] = $this->getMemoryUsage();
            [$diskUsedGb, $diskTotalGb, $diskPercent] = $this->getDiskUsage();

            return [
                'cpu_percent' => $cpuPercent,
                'memory_percent' => $memoryPercent,
                'memory_used_mb' => $memoryUsedMb,
                'memory_total_mb' => $memoryTotalMb,
                'disk_percent' => $diskPercent,
                'disk_used_gb' => $diskUsedGb,
                'disk_total_gb' => $diskTotalGb,
                'db_queries_today' => $this->countDatabaseQueries(Carbon::today()),
            ];
        });
    }

    public function getBlogPostCount(): int
    {
        return $this->remember(__FUNCTION__, [], function () {
            if (!Schema::hasTable('blogs')) {
                return 0;
            }

            return (int) \App\Models\Blog::query()->count();
        });
    }

    public function getApiDefinitionCount(): int
    {
        return $this->remember(__FUNCTION__, [], function () {
            if (!Schema::hasTable('apis')) {
                return 0;
            }

            return (int) \App\Models\Api::query()->count();
        });
    }

    public function getPluginCount(): int
    {
        return $this->remember(__FUNCTION__, [], function () {
            if (!Schema::hasTable('plugins')) {
                return 0;
            }

            return (int) \App\Models\Plugin::query()->count();
        });
    }

    private function remember(string $method, array $parts, callable $callback)
    {
        $ttl = (int) config('realtime.cache_ttl_seconds', 8);
        $key = 'realtime:stats:' . $method . ':' . md5(json_encode($parts));

        return Cache::remember($key, now()->addSeconds($ttl), $callback);
    }

    private function countRequestLogEntries(?Carbon $date, callable $filter): int
    {
        $count = 0;

        $this->iterateRequestLogs($date, function (string $line) use (&$count, $filter) {
            if (!str_contains($line, 'Request Processed')) {
                return;
            }

            $context = $this->parseLogContext($line);
            if (!is_array($context)) {
                return;
            }

            if ($filter($context) === true) {
                $count++;
            }
        });

        return $count;
    }

    private function iterateRequestLogs(?Carbon $date, callable $callback): void
    {
        $paths = $date ? [$this->getDailyLogPath('requests', $date)] : $this->getLogPaths('requests');

        foreach ($paths as $filePath) {
            if (!$filePath || !File::exists($filePath)) {
                continue;
            }

            $file = new \SplFileObject($filePath, 'r');
            while (!$file->eof()) {
                $line = (string) $file->fgets();
                if (trim($line) === '') {
                    continue;
                }

                $callback($line);
            }
        }
    }

    private function getLogPaths(string $channel): array
    {
        $paths = File::glob(storage_path("logs/{$channel}-*.log")) ?: [];
        $single = storage_path("logs/{$channel}.log");
        if (File::exists($single)) {
            $paths[] = $single;
        }

        sort($paths);

        return $paths;
    }

    private function getDailyLogPath(string $channel, Carbon $date): ?string
    {
        $daily = storage_path('logs/' . $channel . '-' . $date->toDateString() . '.log');
        if (File::exists($daily)) {
            return $daily;
        }

        $single = storage_path('logs/' . $channel . '.log');
        if (File::exists($single)) {
            return $single;
        }

        return null;
    }

    private function parseLogLine(string $line): ?array
    {
        if (!preg_match('/^\[(?<timestamp>[^\]]+)\] (?<env>\w+)\.(?<level>\w+): (?<message>.*)$/', trim($line), $m)) {
            return null;
        }

        $message = (string) ($m['message'] ?? '');
        $contextPos = strpos($message, '{');
        if ($contextPos !== false) {
            $message = trim(substr($message, 0, $contextPos));
        }

        return [
            'timestamp' => (string) $m['timestamp'],
            'level' => strtolower((string) $m['level']),
            'message' => $message,
        ];
    }

    private function parseLogContext(string $line): ?array
    {
        $line = trim($line);
        $pos = strpos($line, '{');
        if ($pos === false) {
            return null;
        }

        $json = substr($line, $pos);
        $context = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($context)) {
            return null;
        }

        return $context;
    }

    private function extractPath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }

    /**
     * @return array<int, string>
     */
    private function readLastLines(string $filePath, int $maxLines = 200): array
    {
        $lines = [];
        $buffer = '';
        $fp = fopen($filePath, 'rb');
        if ($fp === false) {
            return [];
        }

        try {
            fseek($fp, 0, SEEK_END);
            $pos = ftell($fp);

            while ($pos > 0 && count($lines) < $maxLines) {
                $readSize = min(4096, $pos);
                $pos -= $readSize;
                fseek($fp, $pos);

                $chunk = fread($fp, $readSize);
                if ($chunk === false) {
                    break;
                }

                $buffer = $chunk . $buffer;
                $parts = explode("\n", $buffer);

                $buffer = array_shift($parts) ?? '';
                while (!empty($parts)) {
                    $line = array_pop($parts);
                    if ($line === null) {
                        break;
                    }

                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }

                    $lines[] = $line;
                    if (count($lines) >= $maxLines) {
                        break;
                    }
                }
            }

            if ($buffer !== '' && count($lines) < $maxLines) {
                $lines[] = trim($buffer);
            }
        } finally {
            fclose($fp);
        }

        return $lines;
    }

    private function getCpuUsagePercent(): float
    {
        $load = sys_getloadavg();
        $load1 = is_array($load) ? (float) ($load[0] ?? 0) : 0.0;

        $cores = 0;
        if (File::exists('/proc/cpuinfo')) {
            $cores = substr_count((string) File::get('/proc/cpuinfo'), 'processor');
        }

        $cores = max(1, $cores);

        return round(min(100, ($load1 / $cores) * 100), 2);
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private function getMemoryUsage(): array
    {
        $totalKb = 0;
        $availableKb = 0;

        if (File::exists('/proc/meminfo')) {
            $content = (string) File::get('/proc/meminfo');

            if (preg_match('/^MemTotal:\s+(\d+)\s+kB/m', $content, $m)) {
                $totalKb = (int) $m[1];
            }

            if (preg_match('/^MemAvailable:\s+(\d+)\s+kB/m', $content, $m)) {
                $availableKb = (int) $m[1];
            }
        }

        if ($totalKb <= 0) {
            return [0.0, 0.0, 0.0];
        }

        $usedKb = max(0, $totalKb - $availableKb);
        $usedMb = round($usedKb / 1024, 2);
        $totalMb = round($totalKb / 1024, 2);
        $percent = round(($usedKb / $totalKb) * 100, 2);

        return [$usedMb, $totalMb, $percent];
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private function getDiskUsage(): array
    {
        $root = '/';
        $total = @disk_total_space($root);
        $free = @disk_free_space($root);

        if (!is_float($total) && !is_int($total)) {
            $total = 0;
        }

        if (!is_float($free) && !is_int($free)) {
            $free = 0;
        }

        $total = (float) $total;
        $free = (float) $free;
        if ($total <= 0) {
            return [0.0, 0.0, 0.0];
        }

        $used = max(0.0, $total - $free);
        $usedGb = round($used / 1024 / 1024 / 1024, 2);
        $totalGb = round($total / 1024 / 1024 / 1024, 2);
        $percent = round(($used / $total) * 100, 2);

        return [$usedGb, $totalGb, $percent];
    }

    private function countDatabaseQueries(Carbon $date): int
    {
        $path = $this->getDailyLogPath('database', $date);
        if (!$path || !File::exists($path)) {
            return 0;
        }

        $count = 0;
        $file = new \SplFileObject($path, 'r');
        while (!$file->eof()) {
            $line = (string) $file->fgets();
            if (str_contains($line, 'SQL Query')) {
                $count++;
            }
        }

        return $count;
    }
}
