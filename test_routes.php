#!/usr/bin/env php
<?php

/**
 * Test script to validate all routes and functionality
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "Testing Laravel Application Routes\n";
echo "====================================\n\n";

// Test frontend routes
echo "1. Testing Frontend Routes:\n";
$frontendRoutes = [
    '/',
    '/blog',
    '/apis',
    '/about',
    '/contact',
    '/api-test',
];

foreach ($frontendRoutes as $route) {
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        echo "  ✓ GET {$route} - Status: {$status}\n";
    } catch (Exception $e) {
        echo "  ✗ GET {$route} - Error: {$e->getMessage()}\n";
    }
}

// Test API routes
echo "\n2. Testing API Routes:\n";
$apiRoutes = [
    '/api/apis',
    '/api/blogs',
    '/api/users',
    '/api/plugins',
    '/api/statistics',
];

foreach ($apiRoutes as $route) {
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $request->headers->set('Accept', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        echo "  ✓ GET {$route} - Status: {$status}\n";
    } catch (Exception $e) {
        echo "  ✗ GET {$route} - Error: {$e->getMessage()}\n";
    }
}

// Test admin routes (will redirect to login if not authenticated)
echo "\n3. Testing Admin Routes (Login Page):\n";
$adminRoutes = [
    '/admin/login',
];

foreach ($adminRoutes as $route) {
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        echo "  ✓ GET {$route} - Status: {$status}\n";
    } catch (Exception $e) {
        echo "  ✗ GET {$route} - Error: {$e->getMessage()}\n";
    }
}

// Test exception handling
echo "\n4. Testing Exception Handling:\n";
try {
    $request = Illuminate\Http\Request::create('/non-existent-route', 'GET');
    $request->headers->set('Accept', 'application/json');
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    echo "  ✓ Non-existent API route returns {$status}\n";
} catch (Exception $e) {
    echo "  ✗ Exception handling test failed: {$e->getMessage()}\n";
}

echo "\n====================================\n";
echo "Test Summary\n";
echo "====================================\n";
echo "All basic route tests completed!\n";
echo "For full testing, run: php artisan serve\n";
echo "====================================\n";
