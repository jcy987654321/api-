<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogService extends BaseService
{
    /**
     * Get log statistics
     */
    public function getStats(): array
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $stats = [
            'total_size' => 0,
            'file_count' => 0,
            'channels' => [],
        ];

        foreach ($files as $file) {
            if ($file->getExtension() === 'log') {
                $size = $file->getSize();
                $stats['total_size'] += $size;
                $stats['file_count']++;

                $filename = $file->getFilename();
                $parts = explode('-', $filename);
                $channel = $parts[0];

                if (! isset($stats['channels'][$channel])) {
                    $stats['channels'][$channel] = [
                        'count' => 0,
                        'size' => 0,
                    ];
                }
                $stats['channels'][$channel]['count']++;
                $stats['channels'][$channel]['size'] += $size;
            }
        }

        // Format size
        $stats['total_size_human'] = $this->formatBytes($stats['total_size']);
        foreach ($stats['channels'] as &$channel) {
            $channel['size_human'] = $this->formatBytes($channel['size']);
        }

        return $stats;
    }

    /**
     * Clear old logs
     */
    public function clearOldLogs(int $days = 30): int
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $deletedCount = 0;
        $cutoff = Carbon::now()->subDays($days);

        foreach ($files as $file) {
            if ($file->getExtension() === 'log') {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());
                if ($lastModified->lt($cutoff)) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }
        }

        Log::info("Cleared $deletedCount log files older than $days days.");

        return $deletedCount;
    }

    /**
     * Search logs (Simplified implementation)
     */
    public function searchLogs(string $channel = 'laravel', ?string $date = null, ?string $level = null, ?string $keyword = null, int $page = 1, int $perPage = 50): array
    {
        $date = $date ?: date('Y-m-d');
        $filename = "{$channel}-{$date}.log";
        $filePath = storage_path("logs/{$filename}");

        if (! File::exists($filePath)) {
            // Try without date if it's the main log file (though we configured daily)
            $filePath = storage_path("logs/{$channel}.log");
            if (! File::exists($filePath)) {
                return ['data' => [], 'total' => 0];
            }
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $results = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Simple parsing of Laravel log format: [timestamp] environment.LEVEL: message {"context":...}
            if (preg_match('/^\[(?P<timestamp>.*)\] (?P<env>\w+)\.(?P<level>\w+): (?P<message>.*)/', $line, $matches)) {
                $entry = [
                    'timestamp' => $matches['timestamp'],
                    'level' => strtolower($matches['level']),
                    'message' => $matches['message'],
                ];

                if ($level && strtolower($level) !== $entry['level']) {
                    continue;
                }
                if ($keyword && ! str_contains(strtolower($line), strtolower($keyword))) {
                    continue;
                }

                $results[] = $entry;
            } else {
                // Handle multi-line entries (stack traces) by appending to the last entry
                if (! empty($results)) {
                    $results[count($results) - 1]['message'] .= "\n".$line;
                }
            }
        }

        // Pagination
        $total = count($results);
        $results = array_reverse($results); // Newest first
        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($results, $offset, $perPage);

        return [
            'data' => $pagedData,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => ceil($total / $perPage),
        ];
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
