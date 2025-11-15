<?php

declare(strict_types=1);

use App\Config\Database;
use App\Config\Settings;

// Root path
define('ROOT_PATH', dirname(__DIR__));

// Autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Initialize database connection
Database::initialize();

// Initialize settings
Settings::initialize();