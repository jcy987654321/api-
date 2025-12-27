<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 日志查看控制器
 */
class LogViewerController extends Controller
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * 获取日志文件列表
     */
    public function index(Request $request): JsonResponse
    {
        $channel = $request->get('channel');
        $files = $this->logService->getLogFiles($channel);

        return $this->success([
            'files' => $files,
            'channels' => $this->logService->getChannels(),
        ]);
    }

    /**
     * 查看日志内容
     */
    public function show(Request $request, string $path): JsonResponse
    {
        $level = $request->get('level');
        $keyword = $request->get('keyword');
        $lines = (int) $request->get('lines', 100);

        $logs = $this->logService->readLogFile($path, $lines);

        if ($level || $keyword) {
            $logs = $this->logService->filterLogs($logs, $level, $keyword);
        }

        return $this->success([
            'logs' => $logs,
            'file' => basename($path),
        ]);
    }

    /**
     * 获取最近的日志
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 50);
        $channel = $request->get('channel');

        $logs = $this->logService->getRecentLogs($limit, $channel);

        return $this->success([
            'logs' => $logs,
            'count' => count($logs),
        ]);
    }

    /**
     * 获取日志统计
     */
    public function stats(Request $request): JsonResponse
    {
        $channel = $request->get('channel');
        $stats = $this->logService->getStats($channel);

        return $this->success($stats);
    }

    /**
     * 搜索日志
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->get('keyword');

        if (!$keyword) {
            return $this->error('Keyword is required', 'INVALID_REQUEST', 400);
        }

        $channel = $request->get('channel');
        $maxResults = (int) $request->get('max_results', 100);

        $results = $this->logService->searchInLogs($keyword, $channel, $maxResults);

        return $this->success([
            'keyword' => $keyword,
            'results' => $results,
            'count' => count($results),
        ]);
    }

    /**
     * 清理旧日志
     */
    public function clean(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $channel = $request->get('channel');

        $deleted = $this->logService->cleanOldLogs($days, $channel);

        return $this->success([
            'deleted_count' => count($deleted),
            'deleted_files' => $deleted,
        ], 'Logs cleaned successfully');
    }

    /**
     * 删除日志文件
     */
    public function destroy(Request $request, string $path): JsonResponse
    {
        $success = $this->logService->deleteLogFile(base64_decode($path));

        if (!$success) {
            return $this->error('Log file not found or could not be deleted', 'DELETE_FAILED', 404);
        }

        return $this->success(null, 'Log file deleted successfully');
    }
}
