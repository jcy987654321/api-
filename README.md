# API - 强大的管理系统

A powerful management system built with Laravel 10+ and PHP 8.0+.

## Features

- **Modern Stack**: Laravel 10, PHP 8.0+, Vite build pipeline
- **RESTful API**: Ready-to-use API endpoints at `/api/v1`
- **PJAX Support**: Fast page transitions with jQuery PJAX
- **Blade Templating**: Beautiful base layout with sections for customization
- **Asset Management**: SCSS and JavaScript bundling with Vite
- **Admin Dashboard**: Pre-built admin dashboard layout
- **User Management**: Complete user authentication and authorization system
- **Role-based Access Control**: Flexible permission system
- **Data Analytics**: Comprehensive statistics and reporting
- **Responsive Design**: Mobile-first approach with modern UI

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js 16+ and npm
- SQLite or MySQL/PostgreSQL database

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

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database setup

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build assets

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
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ApiController.php
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── SettingsController.php
│   │   │   └── Api/
│   │   │       ├── UserApiController.php
│   │   │       └── StatsApiController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Providers/
├── config/              # Configuration files
├── database/            # Migrations and seeders
├── public/              # Public assets (compiled)
├── resources/
│   ├── js/              # JavaScript files
│   ├── scss/            # Stylesheets
│   └── views/           # Blade templates
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── auth.blade.php
│       ├── home.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── users.blade.php
│           └── settings.blade.php
├── routes/
│   ├── web.php          # Web routes
│   ├── api.php          # API routes
│   └── auth.php         # Authentication routes
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
- `GET /login` - Login page
- `POST /login` - Login authentication
- `POST /logout` - Logout
- `GET /register` - Registration page
- `POST /register` - User registration
- `GET /admin/dashboard` - Admin dashboard (PJAX enabled)
- `GET /admin/users` - User management
- `GET /admin/settings` - System settings

### API Routes

All API routes are prefixed with `/api/v1`

- `GET /` - API status
- `GET /status` - API health check
- `GET /users` - List users (authenticated)
- `POST /users` - Create user (admin)
- `GET /stats` - System statistics (admin)

## Authentication

The system includes a complete authentication system:

- User registration and login
- Password reset functionality
- Session management
- Role-based access control
- API token authentication

### User Roles

- **Admin**: Full system access
- **Manager**: Limited administrative access
- **User**: Basic access

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
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=api_management
# DB_USERNAME=root
# DB_PASSWORD=

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"

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
- **Bootstrap 5**: UI framework components
- **Chart.js**: Data visualization
- **Custom App**: Located in `resources/js/app.js`

### Stylesheets

- **SCSS**: Main stylesheet at `resources/scss/app.scss`
- **Bootstrap 5**: CSS framework
- **Vite**: Handles hot module replacement during development
- **Production**: Creates versioned assets for cache busting

## API Documentation

### Authentication

All API endpoints (except `/` and `/status`) require authentication.

#### Login

```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response:
```json
{
    "token": "your-api-token",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
    }
}
```

### GET /api/v1

Returns API status:

```json
{
    "message": "API is running",
    "status": "success",
    "version": "1.0.0"
}
```

### GET /api/v1/status

Returns health check:

```json
{
    "status": "ok",
    "timestamp": "2024-01-01T00:00:00Z",
    "database": "connected",
    "cache": "connected"
}
```

### GET /api/v1/users

Returns list of users (admin only):

```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "admin",
            "created_at": "2024-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "total": 1,
        "per_page": 15,
        "current_page": 1
    }
}
```

## Admin Dashboard Features

### User Management

- Create, read, update, delete users
- Role assignment
- Permission management
- User activity tracking

### System Statistics

- User registration analytics
- Login activity metrics
- System performance monitoring
- Database usage statistics

### Settings Management

- Application configuration
- Email settings
- Security preferences
- Backup management

## Security Features

- CSRF protection
- XSS prevention
- SQL injection protection
- Rate limiting
- Input validation
- Secure password hashing
- API rate limiting

## Troubleshooting

### PHP Not Found

Ensure PHP 8.0+ is installed:

```bash
php --version
```

### Database Connection Issues

Check your `.env` database configuration:

```bash
php artisan config:clear
php artisan cache:clear
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

### Permission Issues

Set proper permissions:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please refer to the [Laravel documentation](https://laravel.com/docs).