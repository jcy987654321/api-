<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * 基础控制器
 */
abstract class Controller
{
    use ValidatesRequests;

    /**
     * 成功响应
     */
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 成功响应（带分页）
     */
    protected function successWithPaginate($paginated, string $message = 'Success'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ];

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 错误响应
     */
    protected function error(string $message, string $code = 'ERROR', int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ];

        return response()->json($response, $status, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 未授权响应
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 'UNAUTHORIZED', 401);
    }

    /**
     * 禁止访问响应
     */
    protected function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return $this->error($message, 'FORBIDDEN', 403);
    }

    /**
     * 资源未找到响应
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 'NOT_FOUND', 404);
    }

    /**
     * 验证错误响应
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, 'VALIDATION_ERROR', 422, [
            'validation' => $errors,
        ]);
    }

    /**
     * 服务器错误响应
     */
    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, 'INTERNAL_ERROR', 500);
    }

    /**
     * 抛出 API 异常
     */
    protected function throwApiException(string $message, string $code = 'API_ERROR', int $status = 400): never
    {
        throw new ApiException($message, $code, $status);
    }

    /**
     * 抛出未授权异常
     */
    protected function throwUnauthorized(?string $message = null): never
    {
        throw AuthenticationException::notLoggedIn();
    }

    /**
     * 抛出禁止访问异常
     */
    protected function throwForbidden(?string $message = null): never
    {
        throw AuthorizationException::forbidden();
    }

    /**
     * 抛出未找到异常
     */
    protected function throwNotFound(string $resource = 'Resource'): never
    {
        $this->throwApiException("{$resource} not found", 'NOT_FOUND', 404);
    }

    /**
     * 抛出验证异常
     */
    protected function throwValidationError(string $field, string $message): never
    {
        throw ValidationException::quick($field, $message);
    }

    /**
     * 响应创建成功
     */
    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * 响应更新成功
     */
    protected function updated(mixed $data = null, string $message = 'Updated successfully'): JsonResponse
    {
        return $this->success($data, $message);
    }

    /**
     * 响应删除成功
     */
    protected function deleted(string $message = 'Deleted successfully'): JsonResponse
    {
        return $this->success(null, $message);
    }

    /**
     * 响应恢复成功
     */
    protected function restored(string $message = 'Restored successfully'): JsonResponse
    {
        return $this->success(null, $message);
    }
}
