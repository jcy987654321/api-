<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Full system access',
                'permissions' => ['*'],
            ]
        );

        $staffRole = Role::firstOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Limited system access',
                'permissions' => ['view_users', 'edit_profile'],
            ]
        );

        $moderatorRole = Role::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderator',
                'description' => 'Content moderation',
                'permissions' => ['moderate_content', 'view_users'],
            ]
        );

        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
