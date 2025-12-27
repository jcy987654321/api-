# API 管理系统的 Laravel 11 基础框架

## 简介

强大的管理系统 - A powerful management system built with Laravel 11.

## 技术栈

- **框架**: Laravel 11
- **PHP**: ^8.2
- **数据库**: MySQL
- **认证**: Laravel Sanctum

## 安装

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## 目录结构

```
app/
├── Models/         # 数据模型
├── Http/Controllers/ # 控制器
├── Services/       # 业务逻辑层
└── Exceptions/     # 自定义异常
```

## License

MIT
