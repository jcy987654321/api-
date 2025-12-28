<?php

return [
    'update_interval_ms' => (int) env('REALTIME_UPDATE_INTERVAL_MS', 1000),

    'cache_ttl_seconds' => (int) env('REALTIME_CACHE_TTL_SECONDS', 8),

    'sse_timeout_seconds' => (int) env('REALTIME_SSE_TIMEOUT_SECONDS', 300),

    'reconnect' => [
        'max_retries' => (int) env('REALTIME_RECONNECT_MAX_RETRIES', 10),
        'base_delay_ms' => (int) env('REALTIME_RECONNECT_BASE_DELAY_MS', 500),
        'max_delay_ms' => (int) env('REALTIME_RECONNECT_MAX_DELAY_MS', 15000),
    ],
];
