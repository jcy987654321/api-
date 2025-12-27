<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 日志服务类
 */
class LogService
{
    /**
     * 日志文件目录
     */
    protected string $logDirectory;

    /**
     * 日志通道列表
     */
    protected array $channels = [
        'application',
        'requests',
        'database',
        'security',
        'debug',
        'error',
        'console',
    ];

    /**
     * 日志级别列表
     */
    protected array $logLevels = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logDirectory = storage_path('logs');
    }

    /**
     * 获取日志通道列表
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * 获取日志文件列表
     */
    public function getLogFiles(?string $channel = null): array
    {
        $files = [];
        $directories = $channel ? [$this->getChannelPath($channel)] : $this->getAllChannelPaths();

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $channelName = basename(dirname($directory));

            foreach (File::files($directory) as $file) {
                $files[] = [
                    'channel' => $channelName,
                    'path' => $file->getPathname(),
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        // 按修改时间排序
        usort($files, fn($a, $b) => strtotime($b['modified']) - strtotime($a['modified']));

        return $files;
    }

    /**
     * 读取日志内容
     */
    public function readLogFile(string $path, int $lines = 100): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $content = shell_exec("tail -n {$lines} " . escapeshellarg($path));
        $lines = explode("\n", trim($content));

        return array_filter(array_map(function ($line) {
            return $this->parseLogLine($line);
        }, $lines));
    }

    /**
     * 解析单行日志
     */
    protected function parseLogLine(string $line): ?array
    {
        if (empty(trim($line))) {
            return null;
        }

        // 匹配 Laravel 日志格式
        // [YYYY-MM-DD HH:MM:SS] channel.LEVEL : message
        $pattern = '/^\[(.+?)\]\s+(\w+)\.(\w+)\.(.+)$/';

        if (preg_match($pattern, $line, $matches)) {
            return [
                'raw' => $line,
                'timestamp' => $matches[1],
                'channel' => $matches[2],
                'level' => strtoupper($matches[3]),
                'message' => trim($matches[4]),
            ];
        }

        // 尝试匹配其他格式
        return [
            'raw' => $line,
            'timestamp' => null,
            'channel' => null,
            'level' => null,
            'message' => $line,
        ];
    }

    /**
     * 过滤日志
     */
    public function filterLogs(array $logs, ?string $level = null, ?string $keyword = null, ?string $channel = null): array
    {
        return array_filter($logs, function ($log) use ($level, $keyword, $channel) {
            // 过滤通道
            if ($channel && $log['channel'] !== $channel) {
                return false;
            }

            // 过滤级别
            if ($level && strtoupper($log['level']) !== strtoupper($level)) {
                return false;
            }

            // 过滤关键词
            if ($keyword && !Str::contains(strtolower($log['message']), strtolower($keyword))) {
                return false;
            }

            return true;
        });
    }

    /**
     * 获取最近的日志条目
     */
    public function getRecentLogs(int $limit = 50, ?string $channel = null): array
    {
        $allLogs = [];

        foreach ($this->getLogFiles($channel) as $file) {
            $logs = $this->readLogFile($file['path'], $limit);
            $allLogs = array_merge($allLogs, $logs);
        }

        // 按时间戳排序（降序）
        usort($allLogs, fn($a, $b) => strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0));

        return array_slice($allLogs, 0, $limit);
    }

    /**
     * 按级别统计日志数量
     */
    public function countByLevel(?string $channel = null): array
    {
        $counts = array_fill_keys($this->logLevels, 0);

        foreach ($this->getLogFiles($channel) as $file) {
            $logs = $this->readLogFile($file['path'], 10000);

            foreach ($logs as $log) {
                $level = strtolower($log['level'] ?? '');
                if (isset($counts[$level])) {
                    $counts[$level]++;
                }
            }
        }

        return $counts;
    }

    /**
     * 获取日志统计信息
     */
    public function getStats(?string $channel = null): array
    {
        $files = $this->getLogFiles($channel);
        $totalSize = array_sum(array_column($files, 'size'));
        $totalCount = 0;

        $logs = $this->getRecentLogs(1000, $channel);
        $levelCounts = $this->countByLevel($channel);

        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'recent_logs_count' => count($logs),
            'level_counts' => $levelCounts,
            'channels' => $this->getChannels(),
        ];
    }

    /**
     * 清理旧日志
     */
    public function cleanOldLogs(int $days = 30, ?string $channel = null): array
    {
        $cutoffTime = now()->subDays($days)->timestamp;
        $deleted = [];

        foreach ($this->getLogFiles($channel) as $file) {
            if ($file['modified'] && strtotime($file['modified']) < $cutoffTime) {
                if (File::delete($file['path'])) {
                    $deleted[] = $file['path'];
                }
            }
        }

        Log::channel('application')->info('Old logs cleaned', [
            'deleted_count' => count($deleted),
            'days_threshold' => $days,
        ]);

        return $deleted;
    }

    /**
     * 删除特定日志文件
     */
    public function deleteLogFile(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        Log::channel('application')->info('Log file deleted', [
            'path' => $path,
        ]);

        return File::delete($path);
    }

    /**
     * 获取通道路径
     */
    protected function getChannelPath(string $channel): string
    {
        return $this->logDirectory . '/' . $channel;
    }

    /**
     * 获取所有通道路径
     */
    protected function getAllChannelPaths(): array
    {
        return array_map(fn($channel) => $this->getChannelPath($channel), $this->channels);
    }

    /**
     * 格式化字节大小
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * 写入自定义日志
     */
    public function writeLog(string $channel, string $level, string $message, array $context = []): void
    {
        Log::channel($channel)->$level($message, $context);
    }

    /**
     * 获取今天的日志文件
     */
    public function getTodayLogFiles(): array
    {
        $today = now()->format('Y-m-d');
        $files = [];

        foreach ($this->getLogFiles() as $file) {
            if (Str::contains($file['name'], $today)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * 搜索日志中的关键词
     */
    public function searchInLogs(string $keyword, ?string $channel = null, int $maxResults = 100): array
    {
        $results = [];
        $keywordLower = strtolower($keyword);

        foreach ($this->getLogFiles($channel) as $file) {
            $content = file_get_contents($file['path']);

            if (strpos(strtolower($content), $keywordLower) !== false) {
                $lines = explode("\n", $content);
                $matchedLines = [];

                foreach ($lines as $index => $line) {
                    if (strpos(strtolower($line), $keywordLower) !== false) {
                        $matchedLines[] = $this->parseLogLine($line);

                        if (count($matchedLines) >= $maxResults) {
                            break;
                        }
                    }
                }

                $results = array_merge($results, $matchedLines);
            }

            if (count($results) >= $maxResults) {
                break;
            }
        }

        return $results;
    }
}
