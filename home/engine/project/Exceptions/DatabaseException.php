<?php

namespace App\Exceptions;

use Exception;

/**
 * 数据库异常类
 */
class DatabaseException extends ApiException
{
    /**
     * 构造函数
     */
    public function __construct(string $message = 'Database Error', string $errorCode = 'DATABASE_ERROR', int $code = 500, ?\Throwable $previous = null, array $additionalData = [])
    {
        parent::__construct($message, $errorCode, $code, $previous, $additionalData);
    }

    /**
     * 从查询异常创建
     */
    public static function fromQuery(\Throwable $e, string $query, array $bindings = []): self
    {
        return new self(
            message: 'Database query failed: ' . $e->getMessage(),
            errorCode: 'DATABASE_QUERY_ERROR',
            code: 500,
            previous: $e,
            additionalData: [
                'query' => $query,
                'bindings' => $bindings,
                'driver_error_code' => method_exists($e, 'getCode') ? $e->getCode() : null,
            ]
        );
    }

    /**
     * 从连接异常创建
     */
    public static function fromConnection(\Throwable $e, string $connection): self
    {
        return new self(
            message: 'Database connection failed',
            errorCode: 'DATABASE_CONNECTION_ERROR',
            code: 503,
            previous: $e,
            additionalData: [
                'connection' => $connection,
            ]
        );
    }
}
