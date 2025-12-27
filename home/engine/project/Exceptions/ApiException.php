<?php

namespace App\Exceptions;

use Exception;

/**
 * API 异常基类
 */
class ApiException extends Exception
{
    /**
     * 错误代码
     */
    protected string $errorCode;

    /**
     * 附加数据
     */
    protected array $additionalData = [];

    /**
     * 构造函数
     */
    public function __construct(string $message = 'API Error', string $errorCode = 'API_ERROR', int $code = 400, ?\Throwable $previous = null, array $additionalData = [])
    {
        $this->errorCode = $errorCode;
        $this->additionalData = $additionalData;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取错误代码
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * 获取附加数据
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'message' => $this->getMessage(),
            'code' => $this->errorCode,
            'errors' => $this->additionalData,
        ];
    }
}
