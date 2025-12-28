<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    protected $sensitiveFields = ['password', 'token', 'credit_card', 'password_confirmation', 'secret'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $startTime;

        if ($this->shouldLog($request, $response)) {
            $this->logRequest($request, $response, $duration);
        }

        return $response;
    }

    protected function shouldLog(Request $request, Response $response): bool
    {
        // Don't log if it's a binary response
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && (
            str_contains($contentType, 'image/') || 
            str_contains($contentType, 'video/') || 
            str_contains($contentType, 'audio/') || 
            str_contains($contentType, 'application/pdf') ||
            str_contains($contentType, 'application/octet-stream') ||
            str_contains($contentType, 'application/zip')
        )) {
            return false;
        }

        // Don't log if it's a common static asset path
        if ($request->is('assets/*', 'css/*', 'js/*', 'vendor/*')) {
            return false;
        }

        return true;
    }

    protected function logRequest(Request $request, Response $response, float $duration): void
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'duration' => round($duration * 1000, 2) . 'ms',
            'user_id' => $request->user()?->id,
            'request_payload' => $this->maskSensitiveData($request->except(['_token', '_method'])),
        ];

        Log::channel('requests')->info('Request Processed', $data);
    }

    protected function maskSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $this->sensitiveFields)) {
                $data[$key] = '********';
            } elseif (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            }
        }
        return $data;
    }
}
