<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $stats = $this->logService->getStats();

        return view('admin.logs.index', compact('stats'));
    }

    public function view(Request $request, $channel = 'laravel', $date = null)
    {
        $page = $request->get('page', 1);
        $level = $request->get('level');
        $keyword = $request->get('keyword');

        $logs = $this->logService->searchLogs($channel, $date, $level, $keyword, $page);
        $stats = $this->logService->getStats();

        return view('admin.logs.view', compact('logs', 'stats', 'channel', 'date', 'level', 'keyword'));
    }

    public function clear(Request $request)
    {
        $days = $request->get('days', 30);
        $deleted = $this->logService->clearOldLogs($days);

        return back()->with('success', "Cleared $deleted old log files.");
    }
}
