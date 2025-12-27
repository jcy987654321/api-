<?php

namespace App\Exceptions;

use Exception;

/**
 * 授权异常类
 */
class AuthorizationException extends ApiException
{
    /**
     * 所需权限
     */
    protected ?string $requiredPermission;

    /**
     * 当前用户拥有的权限
     */
    protected array $currentPermissions;

    /**
     * 资源类型
     */
    protected ?string $resource;

    /**
     * 资源 ID
     */
    protected mixed $resourceId;

    /**
     * 构造函数
     */
    public function __construct(
        string $message = 'Access denied',
        string $errorCode = 'ACCESS_DENIED',
        int $code = 403,
        ?\Throwable $previous = null,
        ?string $requiredPermission = null,
        array $currentPermissions = [],
        ?string $resource = null,
        mixed $resourceId = null,
        array $additionalData = []
    ) {
        $this->requiredPermission = $requiredPermission;
        $this->currentPermissions = $currentPermissions;
        $this->resource = $resource;
        $this->resourceId = $resourceId;

        $data = array_merge([
            'required_permission' => $requiredPermission,
            'current_permissions' => $currentPermissions,
            'resource' => $resource,
            'resource_id' => $resourceId,
        ], $additionalData);

        parent::__construct($message, $errorCode, $code, $previous, $data);
    }

    /**
     * 获取所需权限
     */
    public function getRequiredPermission(): ?string
    {
        return $this->requiredPermission;
    }

    /**
     * 获取当前权限
     */
    public function getCurrentPermissions(): array
    {
        return $this->currentPermissions;
    }

    /**
     * 获取资源
     */
    public function getResource(): ?string
    {
        return $this->resource;
    }

    /**
     * 获取资源 ID
     */
    public function getResourceId(): mixed
    {
        return $this->resourceId;
    }

    /**
     * 通用拒绝访问异常
     */
    public static function forbidden(
        ?string $requiredPermission = null,
        ?string $resource = null,
        mixed $resourceId = null
    ): self {
        return new self(
            message: $requiredPermission
                ? "You don't have permission to perform this action."
                : 'Access denied.',
            errorCode: 'ACCESS_DENIED',
            code: 403,
            requiredPermission: $requiredPermission,
            resource: $resource,
            resourceId: $resourceId
        );
    }

    /**
     * 权限不足异常
     */
    public static function insufficientPermission(
        string $required,
        array $has = [],
        ?string $resource = null
    ): self {
        return new self(
            message: "Insufficient permissions. Required: {$required}",
            errorCode: 'INSUFFICIENT_PERMISSIONS',
            code: 403,
            requiredPermission: $required,
            currentPermissions: $has,
            resource: $resource
        );
    }

    /**
     * 资源不存在异常（403 而非 404，用于隐藏资源存在性）
     */
    public static function resourceHidden(
        ?string $resource = null,
        mixed $resourceId = null
    ): self {
        return new self(
            message: 'Access denied.',
            errorCode: 'ACCESS_DENIED',
            code: 403,
            resource: $resource,
            resourceId: $resourceId
        );
    }

    /**
     * 从 Laravel 授权异常创建
     */
    public static function fromLaravelAuthorization(\Illuminate\Auth\Access\AuthorizationException $e): self
    {
        return new self(
            message: $e->getMessage(),
            errorCode: 'ACCESS_DENIED',
            code: 403
        );
    }
}
