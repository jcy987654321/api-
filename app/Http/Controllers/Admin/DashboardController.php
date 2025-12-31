<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function realtimeStream(Request $request)
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $startTime = time();

            while (true) {
                // Check if client disconnected
                if (connection_aborted() || (time() - $startTime) > 300) {
                    break;
                }

                // Generate real-time data
                $data = $this->getDashboardData();

                // Send SSE event
                echo "data: " . json_encode($data) . "\n\n";
                ob_flush();
                flush();

                // Wait before next update (every 3 seconds)
                sleep(3);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    protected function getDashboardData()
    {
        // In a real application, this would fetch from database and cache
        return [
            'access' => [
                'pv_total' => rand(100000, 200000),
                'uv_total' => rand(50000, 100000),
                'pv_today' => rand(1000, 5000),
                'online_users' => rand(10, 100),
            ],
            'api' => [
                'total_calls' => rand(1000000, 2000000),
                'today_calls' => rand(5000, 20000),
            ],
            'system' => [
                'cpu_percent' => rand(10, 80),
                'memory_percent' => rand(30, 70),
                'db_queries_today' => rand(10000, 50000),
            ],
            'blog' => [
                'posts_total' => rand(50, 200),
                'views_total' => rand(100000, 500000),
            ],
            'links' => [
                'links_total' => rand(10, 50),
                'links_clicks_today' => rand(100, 1000),
            ],
            'top_apis' => [
                ['name' => 'User API', 'calls' => rand(1000, 2000)],
                ['name' => 'Search API', 'calls' => rand(800, 1500)],
                ['name' => 'Data API', 'calls' => rand(600, 1200)],
            ],
            'activities' => [
                ['message' => 'New API created', 'time' => '2 minutes ago'],
                ['message' => 'Blog post updated', 'time' => '15 minutes ago'],
                ['message' => 'Database backup completed', 'time' => '1 hour ago'],
            ],
        ];
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        // Implement search functionality here
        return view('admin.search', compact('query'));
    }
}