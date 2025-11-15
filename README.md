# API - 强大的管理系统

A powerful management system built with Laravel 10+ and PHP 8.0+.

## Features

- **Modern Stack**: Laravel 10, PHP 8.0+, Vite build pipeline
- **RESTful API**: Ready-to-use API endpoints at `/api/v1`
- **PJAX Support**: Fast page transitions with jQuery PJAX
- **Blade Templating**: Beautiful base layout with sections for customization
- **Asset Management**: SCSS and JavaScript bundling with Vite
- **Admin Dashboard**: Pre-built admin dashboard layout

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js 16+ and npm

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd project
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Build assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

## Development

### Starting the development server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Building assets for production

```bash
npm run build
```

This generates versioned assets in the `public` directory.

### Running tests

```bash
php artisan test
```

## Project Structure

```
project/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── HomeController.php
│           ├── ApiController.php
│           └── Admin/
│               └── DashboardController.php
├── config/              # Configuration files
├── database/            # Migrations and seeders
├── public/              # Public assets (compiled)
├── resources/
│   ├── js/              # JavaScript files
│   ├── scss/            # Stylesheets
│   └── views/           # Blade templates
│       ├── layouts/
│       │   └── app.blade.php
│       ├── home.blade.php
│       └── admin/
│           └── dashboard.blade.php
├── routes/
│   ├── web.php          # Web routes
│   └── api.php          # API routes
├── storage/             # Application storage
├── tests/               # Test files
├── .env.example         # Environment variables template
├── composer.json        # PHP dependencies
├── package.json         # Node dependencies
└── vite.config.js       # Vite build configuration
```

## Routes

### Web Routes

- `GET /` - Home page
- `GET /admin/dashboard` - Admin dashboard (PJAX enabled)

### API Routes

All API routes are prefixed with `/api/v1`

- `GET /` - API status
- `GET /status` - API health check

## Configuration

### Environment Variables

Edit `.env` to configure your application:

```env
APP_NAME="API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database
```

See `.env.example` for all available options.

## Assets

### JavaScript

- **jQuery 3.7+**: Included for DOM manipulation
- **PJAX**: For seamless page transitions
- **Custom App**: Located in `resources/js/app.js`

### Stylesheets

- **SCSS**: Main stylesheet at `resources/scss/app.scss`
- **Vite**: Handles hot module replacement during development
- **Production**: Creates versioned assets for cache busting

## API Documentation

### GET /api/v1

Returns API status:

```json
{
    "message": "API is running",
    "status": "success"
}
```

### GET /api/v1/status

Returns health check:

```json
{
    "status": "ok",
    "timestamp": "2024-01-01T00:00:00Z"
}
```

## Troubleshooting

### PHP Not Found

Ensure PHP 8.0+ is installed:

```bash
php --version
```

### npm Dependencies

If you encounter issues, try clearing and reinstalling:

```bash
rm -rf node_modules package-lock.json
npm install
```

### Composer Issues

Clear and reinstall Composer dependencies:

```bash
rm -rf vendor composer.lock
composer install
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please refer to the [Laravel documentation](https://laravel.com/docs).