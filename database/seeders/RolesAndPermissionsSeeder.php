<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'site_settings.manage', 'display_name' => 'Manage Site Settings', 'module' => 'site_settings'],
            ['name' => 'announcements.manage', 'display_name' => 'Manage Announcements', 'module' => 'announcements'],
            ['name' => 'announcements.view', 'display_name' => 'View Announcements', 'module' => 'announcements'],
            ['name' => 'donations.manage', 'display_name' => 'Manage Donations', 'module' => 'donations'],
            ['name' => 'donations.view', 'display_name' => 'View Donations', 'module' => 'donations'],
            ['name' => 'advertisements.manage', 'display_name' => 'Manage Advertisements', 'module' => 'advertisements'],
            ['name' => 'advertisements.view', 'display_name' => 'View Advertisements', 'module' => 'advertisements'],
            ['name' => 'friend_links.manage', 'display_name' => 'Manage Friend Links', 'module' => 'friend_links'],
            ['name' => 'friend_links.view', 'display_name' => 'View Friend Links', 'module' => 'friend_links'],
            ['name' => 'audit_logs.view', 'display_name' => 'View Audit Logs', 'module' => 'audit_logs'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to all features',
            ]
        );

        $contentManagerRole = Role::firstOrCreate(
            ['name' => 'content_manager'],
            [
                'display_name' => 'Content Manager',
                'description' => 'Can manage content but not system settings',
            ]
        );

        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        $contentPermissions = Permission::whereIn('name', [
            'announcements.manage',
            'donations.manage',
            'friend_links.manage',
            'advertisements.view',
        ])->pluck('id');
        $contentManagerRole->permissions()->sync($contentPermissions);
    }
}
