<?php

declare(strict_types=1);

// Bootstrap the application
require_once __DIR__ . '/../config/bootstrap.php';

// Initialize and dispatch router
$router = new \App\Router();
$router->dispatch();