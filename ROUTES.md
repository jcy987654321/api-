# Laravel Route Structure Documentation

This document describes the route structure implemented for the Powerful Management System.

## Route Files

### 1. Frontend Routes (`routes/web.php`)
- **GET /** - Home page
- **GET /blog** - Blog list
- **GET /blog/{slug}** - Blog detail
- **GET /apis** - API list
- **GET /apis/{id}** - API detail
- **GET /api-test** - API test page
- **GET /about** - About page
- **GET /contact** - Contact page

### 2. Admin Routes (`routes/admin.php`)
- **GET /admin/login** - Admin login page
- **POST /admin/login** - Admin login processing
- **POST /admin/logout** - Admin logout (requires auth)
- **GET /admin** - Admin dashboard (requires auth)
- **/admin/apis/** - API management routes (CRUD)
- **/admin/blogs/** - Blog management routes (CRUD)
- **/admin/settings/** - System settings routes
- **/admin/plugins/** - Plugin management routes
- **/admin/statistics/** - Statistics routes
- **/admin/links/** - Links management routes

### 3. API Routes (`routes/api.php`)
- **GET /api/apis** - List APIs
- **GET /api/apis/{id}** - Get specific API
- **GET /api/blogs** - List blog posts
- **GET /api/blogs/{id}** - Get specific blog post
- **GET /api/users** - List users
- **GET /api/users/{id}** - Get specific user
- **GET /api/plugins** - List plugins
- **GET /api/statistics** - Get statistics

### 4. Internal API Routes (`routes/api-internal.php`)
- **/api/internal/** - Internal system management APIs

## Middleware

### AdminAuth Middleware
- Protects all admin routes except login
- Redirects unauthenticated users to login page
- Checks for admin privileges

### LogRequest Middleware
- Logs all incoming requests
- Records method, URL, IP, and user agent

### ApiCorsMiddleware
- Handles CORS headers for API routes
- Allows cross-origin requests from any domain

## Controllers

### Frontend Controllers
- **HomeController** - Handles home, about, and contact pages
- **BlogController** - Handles blog listing and detail pages
- **ApiController** - Handles API listing, detail, and test pages

### Admin Controllers
- **AuthController** - Handles admin authentication
- **DashboardController** - Handles admin dashboard
- **ApiManageController** - Handles API CRUD operations
- **BlogManageController** - Handles blog CRUD operations
- **SettingsController** - Handles system settings

### API Controllers
- **ApiController** - API endpoint controller
- **BlogController** - Blog API controller
- **UserController** - User API controller
- **PluginController** - Plugin API controller
- **StatisticController** - Statistics API controller

## Views

### Layouts
- **app.blade.php** - Frontend main layout
- **admin.blade.php** - Admin main layout

### Frontend Pages
- **index.blade.php** - Home page
- **about.blade.php** - About page
- **contact.blade.php** - Contact page
- **blog/index.blade.php** - Blog list
- **blog/show.blade.php** - Blog detail
- **apis/index.blade.php** - API list
- **apis/show.blade.php** - API detail
- **api-test.blade.php** - API test page

### Admin Pages
- **login.blade.php** - Admin login
- **dashboard.blade.php** - Admin dashboard

## Usage

To test the routes:
```bash
php artisan route:list
```

To run the application:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`