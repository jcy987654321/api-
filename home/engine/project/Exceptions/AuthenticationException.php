<?php

namespace App\Exceptions;

use Exception;

/**
 * 认证异常类
 */
class AuthenticationException extends ApiException
{
    /**
     * 认证提供者
     */
    protected ?string $provider;

    /**
     * 构造函数
     */
    public function __construct(string $message = 'Authentication required', string $errorCode = 'AUTHENTICATION_REQUIRED', int $code = 401, ?\Throwable $previous = null, ?string $provider = null, array $additionalData = [])
    {
        $this->provider = $provider;
        $data = array_merge(['provider' => $provider], $additionalData);
        parent::__construct($message, $errorCode, $code, $previous, $data);
    }

    /**
     * 获取认证提供者
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * 未登录异常
     */
    public static function notLoggedIn(?string $provider = null): self
    {
        return new self(
            message: 'You must be logged in to access this resource.',
            errorCode: 'AUTHENTICATION_REQUIRED',
            code: 401,
            provider: $provider
        );
    }

    /**
     * Token 过期异常
     */
    public static function tokenExpired(?string $provider = null): self
    {
        return new self(
            message: 'Your authentication token has expired.',
            errorCode: 'TOKEN_EXPIRED',
            code: 401,
            provider: $provider
        );
    }

    /**
     * Token 无效异常
     */
    public static function invalidToken(?string $provider = null): self
    {
        return new self(
            message: 'The provided authentication token is invalid.',
            errorCode: 'INVALID_TOKEN',
            code: 401,
            provider: $provider
        );
    }

    /**
     * 从 Laravel 认证异常创建
     */
    public static function fromLaravelAuth(\Illuminate\Auth\AuthenticationException $e): self
    {
        return new self(
            message: $e->getMessage(),
            errorCode: 'AUTHENTICATION_REQUIRED',
            code: 401,
            provider: $e->redirectTo() ?? null
        );
    }
}
