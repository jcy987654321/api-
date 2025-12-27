<?php

use App\Exceptions\ApiException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;

/**
 * 异常助手函数
 */

/**
 * 抛出 API 异常
 */
function api_exception(string $message, string $code = 'API_ERROR', int $status = 400): never
{
    throw new ApiException($message, $code, $status);
}

/**
 * 抛出未授权异常
 */
function unauthorized(?string $message = null, ?string $provider = null): never
{
    throw AuthenticationException::notLoggedIn($provider);
}

/**
 * 抛出禁止访问异常
 */
function forbidden(?string $message = null, ?string $requiredPermission = null): never
{
    throw AuthorizationException::forbidden($requiredPermission);
}

/**
 * 抛出验证异常
 */
function validation_error(array $errors, string $message = 'Validation failed'): never
{
    throw new ValidationException($message, $errors);
}

/**
 * 抛出验证异常（快速方法）
 */
function validation_failed(string $field, string $message): never
{
    throw ValidationException::quick($field, $message);
}

/**
 * 抛出数据库异常
 */
function database_error(string $message, string $code = 'DATABASE_ERROR', int $status = 500): never
{
    throw new DatabaseException($message, $code, $status);
}

/**
 * 抛出未找到异常
 */
function not_found(string $resource = 'Resource'): never
{
    api_exception("{$resource} not found", 'NOT_FOUND', 404);
}

/**
 * 安全执行回调，捕获异常
 */
function safe_call(callable $callback, ?callable $onError = null): mixed
{
    try {
        return $callback();
    } catch (ApiException $e) {
        if ($onError) {
            return $onError($e);
        }
        throw $e;
    } catch (\Exception $e) {
        if ($onError) {
            return $onError($e);
        }
        throw new ApiException($e->getMessage(), 'INTERNAL_ERROR', 500, $e);
    }
}

/**
 * 获取或默认值
 */
function get_or_null(array $data, string $key, mixed $default = null): mixed
{
    return $data[$key] ?? $default;
}

/**
 * 检查是否为 AJAX 请求
 */
function is_ajax_request(): bool
{
    return request()->ajax() || request()->wantsJson();
}

/**
 * 创建标准成功响应
 */
function success_response(mixed $data = null, string $message = 'Success', int $code = 200): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], $code, [], JSON_UNESCAPED_UNICODE);
}

/**
 * 创建标准错误响应
 */
function error_response(string $message, string $code = 'ERROR', int $status = 400, array $errors = []): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'code' => $code,
        'errors' => $errors,
    ], $status, [], JSON_UNESCAPED_UNICODE);
}

/**
 * 记录安全日志
 */
function security_log(string $action, array $context = []): void
{
    \Illuminate\Support\Facades\Log::channel('security')->info($action, array_merge([
        'ip' => request()->ip(),
        'user_id' => auth()->id(),
        'user_agent' => request()->userAgent(),
    ], $context));
}

/**
 * 脱敏敏感数据
 */
function sanitize_data(array $data, array $fields = []): array
{
    $sensitiveFields = array_merge([
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'secret',
        'authorization',
        'credentials',
    ], $fields);

    $sanitized = [];

    foreach ($data as $key => $value) {
        if (in_array(strtolower($key), array_map('strtolower', $sensitiveFields))) {
            $sanitized[$key] = '[REDACTED]';
        } elseif (is_array($value)) {
            $sanitized[$key] = sanitize_data($value, $fields);
        } else {
            $sanitized[$key] = $value;
        }
    }

    return $sanitized;
}

/**
 * 获取客户端真实 IP
 */
function client_ip(): string
{
    $ips = [
        request()->ip(),
        request()->getClientIp(),
        $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
        $_SERVER['HTTP_X_REAL_IP'] ?? null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ];

    foreach ($ips as $ip) {
        if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '0.0.0.0';
}

/**
 * 格式化字节大小
 */
function format_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}
