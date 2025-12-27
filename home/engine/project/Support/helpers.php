<?php

use App\Exceptions\ApiException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use Illuminate\Auth\AuthenticationException as LaravelAuthException;
use Illuminate\Auth\Access\AuthorizationException as LaravelAccessException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as LaravelValidationException;

/**
 * 渲染异常为 JSON 响应
 */
if (!function_exists('render_api_exception')) {
    function render_api_exception(Request $request, Throwable $e): JsonResponse
    {
        // 确定状态码
        $status = 500;
        $message = 'An unexpected error occurred';
        $code = 'INTERNAL_ERROR';
        $errors = [];

        // 自定义 API 异常
        if ($e instanceof ApiException) {
            $status = $e->getCode();
            $message = $e->getMessage();
            $code = $e->getErrorCode();
            $errors = $e->getAdditionalData();
        }
        // 认证异常
        elseif ($e instanceof AuthenticationException || $e instanceof LaravelAuthException) {
            $status = 401;
            $message = $e->getMessage() ?: 'Authentication required';
            $code = 'AUTHENTICATION_REQUIRED';
        }
        // 授权异常
        elseif ($e instanceof AuthorizationException || $e instanceof LaravelAccessException) {
            $status = 403;
            $message = $e->getMessage() ?: 'Access denied';
            $code = 'ACCESS_DENIED';
        }
        // 验证异常
        elseif ($e instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';
            $code = 'VALIDATION_ERROR';
            $errors = ['validation' => $e->getErrors()];
        }
        elseif ($e instanceof LaravelValidationException) {
            $status = 422;
            $message = 'Validation failed';
            $code = 'VALIDATION_ERROR';
            $errors = ['validation' => $e->errors()];
        }
        // 模型未找到
        elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $model = class_basename($e->getModel());
            $message = "{$model} not found";
            $code = 'NOT_FOUND';
        }
        // 数据库异常
        elseif ($e instanceof DatabaseException) {
            $status = $e->getCode();
            $message = $e->getMessage();
            $code = $e->getErrorCode();
            $errors = $e->getAdditionalData();
        }
        // HTTP 异常
        elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $status = $e->getStatusCode();
            $message = get_http_exception_message($status);
            $code = get_http_exception_code($status);
        }

        // 生产环境隐藏敏感信息
        if (!config('app.debug')) {
            $errors = [];
            if ($status >= 500) {
                $message = 'An unexpected error occurred';
            }
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * 获取 HTTP 异常消息
 */
if (!function_exists('get_http_exception_message')) {
    function get_http_exception_message(int $status): string
    {
        return match ($status) {
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Resource not found',
            405 => 'Method not allowed',
            408 => 'Request timeout',
            419 => 'Page expired',
            422 => 'Unprocessable entity',
            429 => 'Too many requests',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            504 => 'Gateway timeout',
            default => 'An error occurred',
        };
    }
}

/**
 * 获取 HTTP 异常代码
 */
if (!function_exists('get_http_exception_code')) {
    function get_http_exception_code(int $status): string
    {
        return match ($status) {
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            408 => 'REQUEST_TIMEOUT',
            419 => 'CSRF_TOKEN_MISMATCH',
            422 => 'VALIDATION_ERROR',
            429 => 'RATE_LIMIT_EXCEEDED',
            500 => 'INTERNAL_ERROR',
            502 => 'BAD_GATEWAY',
            503 => 'SERVICE_UNAVAILABLE',
            504 => 'GATEWAY_TIMEOUT',
            default => 'HTTP_ERROR',
        };
    }
}

/**
 * 检查是否为 API 请求
 */
if (!function_exists('is_api_request')) {
    function is_api_request(Request $request = null): bool
    {
        $request = $request ?: request();
        return $request->expectsJson()
            || $request->is('api/*')
            || $request->is('api')
            || $request->header('Accept') === 'application/json';
    }
}

/**
 * 获取当前请求 ID
 */
if (!function_exists('get_request_id')) {
    function get_request_id(): string
    {
        $request = request();
        return $request->attributes->get('request_id')
            ?? $request->header('X-Request-ID')
            ?? (string) \Illuminate\Support\Str::uuid();
    }
}
