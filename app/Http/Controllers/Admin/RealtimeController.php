<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RealtimeController extends Controller
{
    public function stream(Request $request, RealtimeService $realtime)
    {
        if ($request->expectsJson() || str_contains((string) $request->header('Accept'), 'application/json')) {
            return response()->json($realtime->getSnapshot());
        }

        $intervalMs = max(250, (int) config('realtime.update_interval_ms', 1000));
        $timeoutSeconds = max(30, (int) config('realtime.sse_timeout_seconds', 300));
        $clientRetryMs = max(250, (int) config('realtime.reconnect.base_delay_ms', 500));

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];

        return response()->stream(function () use ($realtime, $intervalMs, $timeoutSeconds, $clientRetryMs) {
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            ob_implicit_flush(true);
            set_time_limit(0);
            ignore_user_abort(true);

            $connectionId = (string) Str::uuid();
            Cache::add('realtime:sse_connections', 0, now()->addDay());
            Cache::increment('realtime:sse_connections');
            Cache::put("realtime:sse_connection:{$connectionId}", now()->timestamp, now()->addSeconds($timeoutSeconds + 60));

            $previousSnapshot = null;
            $startAt = microtime(true);

            try {
                $snapshot = $realtime->getSnapshot();
                $this->sendEvent('init', [
                    'connection_id' => $connectionId,
                    'timestamp' => $snapshot['timestamp'],
                    'data' => $snapshot,
                ]);
                $this->flush();

                $previousSnapshot = $snapshot;

                while (true) {
                    if (connection_aborted()) {
                        break;
                    }

                    if ((microtime(true) - $startAt) > $timeoutSeconds) {
                        $this->sendEvent('close', [
                            'reason' => 'timeout',
                            'timestamp' => now()->toIso8601String(),
                        ]);
                        break;
                    }

                    usleep($intervalMs * 1000);

                    $currentSnapshot = $realtime->getSnapshot();
                    $changes = $realtime->calculateChanges($currentSnapshot, $previousSnapshot);

                    $this->sendRetry($clientRetryMs);
                    $this->sendEvent('ping', [
                        'timestamp' => now()->toIso8601String(),
                    ]);

                    if ($changes['changed'] !== []) {
                        $this->sendEvent('update', [
                            'timestamp' => $currentSnapshot['timestamp'],
                            'changed' => $changes['changed'],
                            'delta' => $changes['delta'],
                        ]);

                        $previousSnapshot = $currentSnapshot;
                    }

                    $this->flush();
                }
            } catch (\Throwable $e) {
                $this->sendEvent('error', [
                    'message' => $e->getMessage(),
                    'timestamp' => now()->toIso8601String(),
                ]);
                $this->flush();
            } finally {
                Cache::forget("realtime:sse_connection:{$connectionId}");
                $remaining = Cache::decrement('realtime:sse_connections');
                if (is_int($remaining) && $remaining < 0) {
                    Cache::put('realtime:sse_connections', 0, now()->addDay());
                }
            }
        }, 200, $headers);
    }

    private function sendRetry(int $ms): void
    {
        echo "retry: {$ms}\n";
    }

    private function sendEvent(string $event, array $data): void
    {
        echo "event: {$event}\n";
        echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
    }

    private function flush(): void
    {
        @ob_flush();
        @flush();
    }
}
