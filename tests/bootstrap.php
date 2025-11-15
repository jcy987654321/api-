<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

// Test bootstrap for setting up test environment

// Set test environment
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'api_management_test';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASS'] = '';

// Override settings for testing
\App\Config\Settings::set('site.url', 'https://api-management.example.com');
\App\Config\Settings::set('sitemap.cache_duration', 1); // 1 second for testing
\App\Config\Settings::set('rss.cache_duration', 1); // 1 second for testing

// Ensure test database exists and is empty
try {
    $db = \App\Config\Database::getConnection();
    
    // Create test database if it doesn't exist
    $db->exec("CREATE DATABASE IF NOT EXISTS api_management_test");
    $db->exec("USE api_management_test");
    
    // Import schema (simplified for testing)
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }
    
} catch (Exception $e) {
    // Database setup failed - tests will use mocks
    error_log("Test database setup failed: " . $e->getMessage());
}