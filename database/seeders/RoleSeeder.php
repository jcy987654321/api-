<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system administrator with all permissions',
                'permissions' => ['*'],
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff member with limited administrative access',
                'permissions' => [
                    'api.view',
                    'api.create',
                    'api.update',
                    'feedback.view',
                    'feedback.respond',
                    'announcements.view',
                    'announcements.create',
                    'announcements.update',
                ],
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with basic access',
                'permissions' => [
                    'api.view',
                    'feedback.create',
                    'donations.create',
                    'friend_links.apply',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}