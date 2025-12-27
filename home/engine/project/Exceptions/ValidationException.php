<?php

namespace App\Exceptions;

use Exception;

/**
 * 验证异常类
 */
class ValidationException extends ApiException
{
    /**
     * 验证错误字段
     */
    protected array $errors;

    /**
     * 构造函数
     */
    public function __construct(string $message = 'Validation failed', array $errors = [], string $errorCode = 'VALIDATION_ERROR', int $code = 422, ?\Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $errorCode, $code, $previous, ['errors' => $errors]);
    }

    /**
     * 获取验证错误
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 获取特定字段的错误
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * 检查是否有特定字段的错误
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * 从 Laravel 验证异常创建
     */
    public static function fromLaravelValidation(\Illuminate\Validation\ValidationException $e): self
    {
        return new self(
            message: 'The given data was invalid.',
            errors: $e->errors(),
            errorCode: 'VALIDATION_ERROR',
            code: 422,
            previous: $e
        );
    }

    /**
     * 创建快速验证失败响应
     */
    public static function quick(string $field, string $message): self
    {
        return new self(
            message: 'Validation failed',
            errors: [$field => [$message]],
            errorCode: 'VALIDATION_ERROR',
            code: 422
        );
    }
}
