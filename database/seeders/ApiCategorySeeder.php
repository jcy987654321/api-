<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiCategory;

class ApiCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Authentication',
                'slug' => 'authentication',
                'description' => 'API endpoints for user authentication and authorization',
                'icon' => 'shield',
                'sort_order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'description' => 'Endpoints for managing user accounts and profiles',
                'icon' => 'users',
                'sort_order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Content Management',
                'slug' => 'content-management',
                'description' => 'API for managing content and media',
                'icon' => 'folder',
                'sort_order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Analytics',
                'slug' => 'analytics',
                'description' => 'Analytics and reporting endpoints',
                'icon' => 'chart',
                'sort_order' => 4,
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            ApiCategory::create($category);
        }
    }
}