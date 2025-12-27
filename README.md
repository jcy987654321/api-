# API 管理系统

强大的管理系统 - Laravel PHP API 框架

## 项目简介

基于 Laravel 框架构建的强大 API 管理系统，提供完整的后台管理功能和 RESTful API 接口。

## 技术栈

- **框架**: Laravel 12.x
- **PHP**: 8.3+
- **数据库**: MySQL 8.0+
- **认证**: Laravel Sanctum
- **包管理**: Composer

## 项目结构

```
.
├── app/
│   ├── Http/
│   │   └── Controllers/     # 控制器目录
│   │       ├── ApiController.php
│   │       ├── BlogController.php
│   │       └── AdminController.php
│   ├── Models/              # 数据模型
│   │   ├── User.php
│   │   ├── Api.php
│   │   ├── Blog.php
│   │   ├── Statistic.php
│   │   └── Plugin.php
│   └── Services/            # 业务逻辑层
│       └── BaseService.php
├── config/                  # 配置文件
├── database/
│   └── migrations/          # 数据库迁移文件
├── public/                  # 公共资源目录
├── resources/
│   └── views/               # 视图文件（Blade模板）
├── routes/                  # 路由定义
│   ├── web.php             # 前台路由
│   ├── api.php             # API路由
│   └── admin.php           # 后台管理路由
└── storage/                # 日志、缓存存储
```

## 快速开始

### 环境要求

- PHP >= 8.3
- Composer
- MySQL >= 8.0
- Apache/Nginx

### 安装步骤

1. **克隆项目**
   ```bash
   git clone <repository-url>
   cd api-
   ```

2. **安装依赖**
   ```bash
   composer install
   ```

3. **配置环境变量**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **配置数据库**
   
   编辑 `.env` 文件，设置数据库连接：
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=api_system
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **创建数据库**
   ```bash
   mysql -u root -p
   CREATE DATABASE api_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

6. **运行迁移**
   ```bash
   php artisan migrate
   ```

7. **启动开发服务器**
   ```bash
   php artisan serve
   ```

   访问 http://localhost:8000

## 功能模块

### API 管理
- API 列表查询
- API 创建/编辑/删除
- API 状态管理

### 博客系统
- 博客文章管理
- 文章发布/草稿
- 作者关联

### 后台管理
- 用户管理
- 数据统计
- 插件管理

### 统计分析
- 数据统计记录
- 自定义元数据

## API 路由

### 认证相关
所有 API 请求需要通过 Sanctum 认证。

### API 端点

**APIs 管理**
- `GET /api/apis` - 获取 API 列表
- `POST /api/apis` - 创建新 API
- `GET /api/apis/{id}` - 获取 API 详情
- `PUT /api/apis/{id}` - 更新 API
- `DELETE /api/apis/{id}` - 删除 API

**博客管理**
- `GET /api/blogs` - 获取博客列表
- `POST /api/blogs` - 创建新博客
- `GET /api/blogs/{id}` - 获取博客详情
- `PUT /api/blogs/{id}` - 更新博客
- `DELETE /api/blogs/{id}` - 删除博客

**后台管理**
- `GET /admin/dashboard` - 管理面板
- `GET /admin/users` - 用户列表
- `PUT /admin/users/{id}` - 更新用户
- `DELETE /admin/users/{id}` - 删除用户

## 开发说明

### 创建新的迁移
```bash
php artisan make:migration create_table_name
```

### 创建新的模型
```bash
php artisan make:model ModelName -m
```

### 创建新的控制器
```bash
php artisan make:controller ControllerName
```

### 运行测试
```bash
php artisan test
```

## 配置说明

### 日志配置
- 日志级别: debug
- 日志通道: stack (single)
- 日志位置: storage/logs/

### 会话配置
- 驱动: database
- 生命周期: 120 分钟

### 缓存配置
- 驱动: database

## 依赖包

- **laravel/framework**: Laravel 核心框架
- **laravel/sanctum**: API 认证系统
- **laravel/tinker**: Laravel REPL 工具
- **phpunit/phpunit**: 单元测试框架

## 许可证

本项目采用 MIT 许可证。

## 贡献指南

欢迎提交 Pull Request 或 Issue。

## 联系方式

如有问题，请提交 Issue。
