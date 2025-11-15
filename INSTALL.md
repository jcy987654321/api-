# Installation Guide

## Quick Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Run database migrations:
```bash
php artisan migrate
php artisan db:seed
```

4. Build assets:
```bash
npm run build
```

5. Start development server:
```bash
php artisan serve
```

## Default Users

After running `php artisan db:seed`, you'll have these test accounts:

- **Admin**: admin@example.com / password
- **Manager**: manager@example.com / password  
- **User**: user@example.com / password

## Features

- Complete authentication system
- User management with role-based access
- RESTful API with token authentication
- Admin dashboard with statistics
- Responsive UI with PJAX navigation
- Modern Laravel 10 + PHP 8.0+ stack

## Access

- Web interface: http://localhost:8000
- API endpoints: http://localhost:8000/api/v1
- Admin dashboard: http://localhost:8000/admin/dashboard (requires admin access)