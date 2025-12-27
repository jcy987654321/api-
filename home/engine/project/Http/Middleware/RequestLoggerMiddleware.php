<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * 请求日志中间件
 */
class RequestLoggerMiddleware
{
    /**
     * 敏感字段列表
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'secret',
        'authorization',
        'credentials',
        'card_number',
        'cvv',
        'ssn',
        'social_security',
        'bank_account',
        'routing_number',
        'private_key',
        'encryption_key',
    ];

    /**
     * 需要部分脱敏的字段
     */
    protected array $partialMaskFields = [
        'email' => 3,      // 保留前3个字符
        'phone' => 4,      // 保留前4位
        'name' => 1,       // 保留第一个字符
        'username' => 2,   // 保留前2位
    ];

    /**
     * 处理请求
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = $this->generateRequestId();

        // 添加请求 ID 到请求对象
        $request->attributes->set('request_id', $requestId);

        // 记录请求信息
        $this->logRequest($request, $requestId);

        // 获取响应
        $response = $next($request);

        // 计算响应时间
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // 记录响应信息
        $this->logResponse($request, $response, $requestId, $duration);

        return $response;
    }

    /**
     * 生成请求 ID
     */
    protected function generateRequestId(): string
    {
        return request()->header('X-Request-ID', Str::uuid()->toString());
    }

    /**
     * 记录请求信息
     */
    protected function logRequest(Request $request, string $requestId): void
    {
        $logData = [
            'request_id' => $requestId,
            'timestamp' => now()->toIso8601String(),
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'route' => $request->route()?->uri(),
        ];

        // 记录请求头（脱敏后）
        $headers = $this->sanitizeHeaders($request->headers->all());
        $logData['headers'] = $headers;

        // 仅记录 GET 请求的查询参数，或非敏感 POST/PUT/PATCH 数据
        if ($request->isMethod('GET')) {
            $logData['query_params'] = $this->sanitizeData($request->query());
        } elseif (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $logData['body'] = $this->sanitizeData($request->all());
        }

        // 记录认证信息（仅是否通过）
        $logData['authenticated'] = auth()->check();

        Log::channel('requests')->info('HTTP Request', $logData);
    }

    /**
     * 记录响应信息
     */
    protected function logResponse(Request $request, Response $response, string $requestId, float $duration): void
    {
        $logData = [
            'request_id' => $requestId,
            'timestamp' => now()->toIso8601String(),
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => auth()->id(),
        ];

        // 获取响应内容大小
        $content = $response->getContent();
        $logData['response_size'] = strlen($content ?? '');

        // 根据状态码确定日志级别
        $level = $this->getLogLevel($response->getStatusCode());
        Log::channel('requests')->$level('HTTP Response', $logData);
    }

    /**
     * 根据状态码确定日志级别
     */
    protected function getLogLevel(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'error';
        }
        if ($statusCode >= 400) {
            return 'warning';
        }
        return 'info';
    }

    /**
     * 脱敏请求头
     */
    protected function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);

            if ($this->isSensitiveHeader($lowerKey)) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * 判断是否为敏感请求头
     */
    protected function isSensitiveHeader(string $key): bool
    {
        $sensitiveHeaders = [
            'authorization',
            'x-api-key',
            'x-auth-token',
            'x-csrf-token',
            'cookie',
            'set-cookie',
            'x-password',
            'x-secret',
        ];

        return in_array($key, $sensitiveHeaders);
    }

    /**
     * 脱敏数据
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);

            if ($this->isSensitiveField($lowerKey)) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $this->partialMask($lowerKey, (string) $value);
            }
        }

        return $sanitized;
    }

    /**
     * 判断是否为敏感字段
     */
    protected function isSensitiveField(string $key): bool
    {
        return in_array($key, array_map('strtolower', $this->sensitiveFields));
    }

    /**
     * 部分脱敏
     */
    protected function partialMask(string $key, string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        if (isset($this->partialMaskFields[$key])) {
            $visibleChars = $this->partialMaskFields[$key];
            $masked = str_repeat('*', max(4, strlen($value) - $visibleChars));
            return substr($value, 0, $visibleChars) . $masked;
        }

        return $value;
    }
}
