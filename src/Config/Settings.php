<?php

declare(strict_types=1);

namespace App\Config;

class Settings
{
    private static array $settings = [
        'site' => [
            'name' => 'API Management System',
            'description' => 'Discover and explore powerful APIs for your projects',
            'keywords' => 'API, integration, development, REST, GraphQL',
            'url' => 'https://api-management.example.com',
            'author' => 'API Management Team',
            'twitter_handle' => '@api_management',
        ],
        'seo' => [
            'default_title' => 'API Management System',
            'default_description' => 'Discover and explore powerful APIs for your projects',
            'default_keywords' => 'API, integration, development, REST, GraphQL',
            'custom_keywords' => '',
            'meta_image' => '/assets/images/og-default.jpg',
            'twitter_card_type' => 'summary_large_image',
        ],
        'sitemap' => [
            'cache_duration' => 3600, // 1 hour
            'ping_engines' => true,
        ],
        'rss' => [
            'cache_duration' => 1800, // 30 minutes
            'items_per_feed' => 50,
        ],
    ];

    public static function initialize(): void
    {
        // Load settings from database or environment if needed
        // For now, use defaults
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$settings;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $settings = &self::$settings;
        
        foreach ($keys as $k) {
            if (!isset($settings[$k])) {
                $settings[$k] = [];
            }
            $settings = &$settings[$k];
        }
        
        $settings = $value;
    }

    public static function getAll(): array
    {
        return self::$settings;
    }
}