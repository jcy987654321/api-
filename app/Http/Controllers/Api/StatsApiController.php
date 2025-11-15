<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'managers' => User::where('role', 'manager')->count(),
            'users' => User::where('role', 'user')->count(),
        ];

        $registrationStats = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $systemStats = [
            'database' => $this->getDatabaseStats(),
            'cache' => $this->getCacheStats(),
            'storage' => $this->getStorageStats(),
        ];

        return response()->json([
            'users' => $userStats,
            'registrations' => $registrationStats,
            'system' => $systemStats,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function getDatabaseStats(): array
    {
        try {
            $tables = DB::select('SHOW TABLES');
            return [
                'status' => 'connected',
                'tables' => count($tables),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getCacheStats(): array
    {
        try {
            $cache = cache();
            return [
                'status' => 'connected',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getStorageStats(): array
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            $size = file_exists($logFile) ? filesize($logFile) : 0;
            return [
                'status' => 'available',
                'log_size' => $size,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}