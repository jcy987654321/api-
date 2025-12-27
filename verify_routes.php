<?php

// This script can be used to verify the route structure
// Run with: php verify_routes.php

echo "=== Route Structure Verification ===\n\n";

echo "1. Route Files:\n";
$routeFiles = [
    'routes/web.php',
    'routes/admin.php', 
    'routes/api.php',
    'routes/api-internal.php'
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

echo "\n2. Controller Files:\n";
$controllers = [
    'app/Http/Controllers/Controller.php',
    'app/Http/Controllers/Front/HomeController.php',
    'app/Http/Controllers/Front/BlogController.php', 
    'app/Http/Controllers/Front/ApiController.php',
    'app/Http/Controllers/Admin/AuthController.php',
    'app/Http/Controllers/Admin/DashboardController.php',
    'app/Http/Controllers/Admin/ApiManageController.php',
    'app/Http/Controllers/Admin/BlogManageController.php',
    'app/Http/Controllers/Admin/SettingsController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        echo "✓ $controller exists\n";
    } else {
        echo "✗ $controller missing\n";
    }
}

echo "\n3. Middleware Files:\n";
$middleware = [
    'app/Http/Middleware/AdminAuth.php',
    'app/Http/Middleware/LogRequest.php',
    'app/Http/Middleware/ApiCorsMiddleware.php'
];

foreach ($middleware as $mw) {
    if (file_exists($mw)) {
        echo "✓ $mw exists\n";
    } else {
        echo "✗ $mw missing\n";
    }
}

echo "\n4. View Files:\n";
$views = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/pages/index.blade.php',
    'resources/views/pages/about.blade.php',
    'resources/views/pages/contact.blade.php',
    'resources/views/pages/blog/index.blade.php',
    'resources/views/pages/blog/show.blade.php',
    'resources/views/pages/apis/index.blade.php',
    'resources/views/pages/apis/show.blade.php',
    'resources/views/pages/api-test.blade.php',
    'resources/views/admin/login.blade.php',
    'resources/views/admin/dashboard.blade.php'
];

foreach ($views as $view) {
    if (file_exists($view)) {
        echo "✓ $view exists\n";
    } else {
        echo "✗ $view missing\n";
    }
}

echo "\n=== Verification Complete ===\n";
echo "To test the routes, run: php artisan route:list\n";
echo "To test the application, run: php artisan serve\n";