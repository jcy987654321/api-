<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * 应用异常处理器
 */
class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * 不需要报告的异常
     */
    protected $dontReport = [
        ApiException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        ValidationException::class,
        NotFoundHttpException::class,
    ];

    /**
     * 不需要记录上下文的异常
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
        'password_confirm',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'secret',
        'authorization',
        'credentials',
    ];

    /**
     * 注册异常处理回调
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // 自定义报告逻辑
        });
    }

    /**
     * 渲染异常为 HTTP 响应
     */
    public function render($request, Throwable $e): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // 如果是 API 请求，返回 JSON 响应
        if ($request->expectsJson() || $request->is('api/*') || $request->is('api')) {
            return $this->renderApiException($request, $e);
        }

        // 对于非 API 请求，让父类处理（渲染错误页面）
        return parent::render($request, $e);
    }

    /**
     * 渲染 API 异常
     */
    protected function renderApiException(Request $request, Throwable $e): JsonResponse
    {
        // 自定义 API 异常
        if ($e instanceof ApiException) {
            return $this->jsonResponse(
                success: false,
                message: $e->getMessage(),
                code: $e->getErrorCode(),
                errors: $e->getAdditionalData(),
                status: $e->getCode()
            );
        }

        // Laravel 验证异常
        if ($e instanceof ValidationException) {
            return $this->jsonResponse(
                success: false,
                message: 'Validation failed',
                code: 'VALIDATION_ERROR',
                errors: ['validation' => $e->errors()],
                status: 422
            );
        }

        // 认证异常
        if ($e instanceof AuthenticationException) {
            return $this->jsonResponse(
                success: false,
                message: $e->getMessage() ?: 'Authentication required',
                code: 'AUTHENTICATION_REQUIRED',
                errors: [],
                status: 401
            );
        }

        // 授权异常
        if ($e instanceof AuthorizationException) {
            return $this->jsonResponse(
                success: false,
                message: $e->getMessage() ?: 'Access denied',
                code: 'ACCESS_DENIED',
                errors: [],
                status: 403
            );
        }

        // 模型未找到异常
        if ($e instanceof ModelNotFoundException) {
            $model = $e->getModel();
            $modelName = class_basename($model);
            return $this->jsonResponse(
                success: false,
                message: "{$modelName} not found",
                code: 'NOT_FOUND',
                errors: [
                    'resource_type' => $modelName,
                    'resource_id' => $e->getIds(),
                ],
                status: 404
            );
        }

        // HTTP 异常（404, 500 等）
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            $message = $this->getHttpExceptionMessage($status);

            return $this->jsonResponse(
                success: false,
                message: $message,
                code: $this->getHttpExceptionCode($status),
                errors: ['status_code' => $status],
                status: $status
            );
        }

        // 数据库查询异常
        if ($e instanceof QueryException) {
            $message = config('app.debug')
                ? 'Database query failed: ' . $e->getMessage()
                : 'A database error occurred';

            return $this->jsonResponse(
                success: false,
                message: $message,
                code: 'DATABASE_ERROR',
                errors: config('app.debug') ? [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'driver_code' => $e->getCode(),
                ] : [],
                status: 500
            );
        }

        // 其他异常 - 在生产环境隐藏详情
        $message = config('app.debug')
            ? $e->getMessage()
            : 'An unexpected error occurred';

        return $this->jsonResponse(
            success: false,
            message: $message,
            code: 'INTERNAL_ERROR',
            errors: config('app.debug') ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(10)->map(fn($t) => [
                    'file' => $t['file'] ?? null,
                    'line' => $t['line'] ?? null,
                    'function' => ($t['class'] ?? '') . '::' . ($t['function'] ?? ''),
                ])->toArray(),
            ] : [],
            status: 500
        );
    }

    /**
     * 创建 JSON 响应
     */
    protected function jsonResponse(
        bool $success,
        string $message,
        string $code,
        array $errors = [],
        int $status = 200
    ): JsonResponse {
        $response = [
            'success' => $success,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ];

        // 添加时间戳（仅生产环境）
        if (!config('app.debug')) {
            $response['timestamp'] = now()->toIso8601String();
        }

        return response()->json($response, $status, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取 HTTP 异常消息
     */
    protected function getHttpExceptionMessage(int $status): string
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

    /**
     * 获取 HTTP 异常代码
     */
    protected function getHttpExceptionCode(int $status): string
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

    /**
     * 报告异常
     */
    public function report(Throwable $e): void
    {
        // 跳过不需要报告的异常
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return;
            }
        }

        // 记录到日志
        \Log::channel('application')->error('Exception reported', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]);

        parent::report($e);
    }
}
