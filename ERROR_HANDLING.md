# 统一错误处理和日志记录系统

## 概述

本系统为 Laravel 项目提供统一的错误处理和日志记录功能，包括：

- 自定义异常处理
- 多通道日志记录
- 请求日志中间件
- 数据库查询日志
- 日志查看工具
- 美观的错误页面

## 目录结构

```
app/
├── Exceptions/
│   ├── ApiException.php          # API 异常基类
│   ├── AuthenticationException.php # 认证异常
│   ├── AuthorizationException.php # 授权异常
│   ├── DatabaseException.php     # 数据库异常
│   ├── ValidationException.php   # 验证异常
│   └── Handler.php               # 异常处理器
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php        # 基础控制器
│   │   └── LogViewerController.php # 日志查看控制器
│   └── Middleware/
│       ├── DatabaseQueryLoggerMiddleware.php # 数据库查询日志
│       └── RequestLoggerMiddleware.php       # 请求日志
├── Services/
│   └── LogService.php            # 日志服务
└── helpers.php                   # 助手函数

config/
└── logging.php                   # 日志配置

resources/views/errors/
├── 403.blade.php                 # 403 错误页面
├── 404.blade.php                 # 404 错误页面
└── 500.blade.php                 # 500 错误页面

routes/
└── logs.php                      # 日志查看路由
```

## 使用方法

### 1. 异常处理

#### 抛出 API 异常

```php
use App\Exceptions\ApiException;

// 基本用法
throw new ApiException('操作失败', 'OPERATION_FAILED', 400);

// 带额外数据
throw new ApiException('创建失败', 'CREATE_FAILED', 400, null, [
    'field' => 'email',
    'reason' => '邮箱已被使用'
]);
```

#### 认证异常

```php
use App\Exceptions\AuthenticationException;

// 未登录
throw AuthenticationException::notLoggedIn();

// Token 过期
throw AuthenticationException::tokenExpired();

// Token 无效
throw AuthenticationException::invalidToken();
```

#### 授权异常

```php
use App\Exceptions\AuthorizationException;

// 禁止访问
throw AuthorizationException::forbidden();

// 权限不足
throw AuthorizationException::insufficientPermission(
    'edit_users',
    ['view_users', 'create_users'],
    'User'
);
```

#### 验证异常

```php
use App\Exceptions\ValidationException;

// 快速创建
throw ValidationException::quick('email', '邮箱格式不正确');

// 自定义验证错误
throw new ValidationException('验证失败', [
    'email' => ['邮箱格式不正确'],
    'password' => ['密码至少8位']
]);
```

### 2. 日志记录

#### 使用 Log Facade

```php
use Illuminate\Support\Facades\Log;

// 应用日志
Log::channel('application')->info('User logged in', [
    'user_id' => $user->id,
    'ip' => request()->ip(),
]);

// 请求日志（自动由中间件记录）
// Log::channel('requests')->info(...)

// 数据库日志（自动由中间件记录）
// Log::channel('database')->debug(...)

// 安全日志
Log::channel('security')->warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => request()->ip(),
]);
```

### 3. 中间件配置

#### 在 Kernel.php 中注册

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\RequestLoggerMiddleware::class,
    ],

    'api' => [
        // ...
    ],
];

protected $routeMiddleware = [
    // ...
    'request.logger' => \App\Http\Middleware\RequestLoggerMiddleware::class,
    'db.query.logger' => \App\Http\Middleware\DatabaseQueryLoggerMiddleware::class,
];
```

#### 在路由中使用

```php
// 全局路由中间件
Route::middleware(['request.logger'])->group(function () {
    // 路由
});

// 特定路由
Route::get('/admin/logs', function () {
    // ...
})->middleware(['auth', 'request.logger']);
```

### 4. 日志查看

#### 通过 API

```bash
# 获取日志文件列表
GET /admin/logs

# 获取最近的日志
GET /admin/logs/recent?limit=50

# 获取日志统计
GET /admin/logs/stats

# 搜索日志
GET /admin/logs/search?keyword=error

# 清理旧日志
POST /admin/logs/clean
{
    "days": 30,
    "channel": "application"
}
```

### 5. 响应格式

#### 成功响应

```json
{
    "success": true,
    "message": "操作成功",
    "data": {
        "id": 1,
        "name": "示例"
    }
}
```

#### 错误响应

```json
{
    "success": false,
    "message": "参数验证失败",
    "code": "VALIDATION_ERROR",
    "errors": {
        "validation": {
            "email": ["邮箱格式不正确"],
            "password": ["密码至少8位"]
        }
    }
}
```

### 6. 敏感数据脱敏

以下字段会自动脱敏：
- password, password_confirmation
- token, access_token, refresh_token
- api_key, secret
- authorization
- credentials

部分脱敏的字段：
- email (保留前3位)
- phone (保留前4位)
- name (保留第1位)

## 配置

### 日志通道

在 `config/logging.php` 中配置：

| 通道 | 用途 | 默认保留天数 |
|------|------|-------------|
| application | 应用主日志 | 30 |
| requests | HTTP 请求日志 | 14 |
| database | 数据库查询日志 | 7 |
| security | 安全相关日志 | 90 |
| debug | 调试日志（仅开发） | 7 |
| error | 错误日志 | - |

### 日志级别

从低到高：debug < info < notice < warning < error < critical < alert < emergency

## 错误页面

系统包含三个美观的错误页面：
- 403.blade.php - 禁止访问
- 404.blade.php - 页面未找到
- 500.blade.php - 服务器错误

这些页面在生产环境中自动重定向。

## 助手函数

```php
// 抛出 API 异常
api_exception('消息', 'CODE', 400);

// 抛出未授权异常
unauthorized();

// 抛出禁止访问异常
forbidden();

// 抛出验证异常
validation_error(['field' => ['错误信息']]);

// 记录安全日志
security_log('action', ['context' => 'data']);

// 脱敏数据
$sanitized = sanitize_data($request->all());

// 检查是否为 API 请求
if (is_api_request()) {
    // 返回 JSON
}

// 获取客户端真实 IP
$ip = client_ip();

// 格式化字节大小
$formatted = format_bytes(1024 * 1024); // "1 MB"
```

## 测试

运行测试验证功能：

```bash
php artisan test
```

## 注意事项

1. 生产环境中，确保 `APP_DEBUG=false`，错误详情不会返回给用户
2. 定期清理旧日志以节省磁盘空间
3. 敏感日志已自动脱敏，但仍需注意不要记录敏感信息
4. 日志查看路由应该通过认证中间件保护
