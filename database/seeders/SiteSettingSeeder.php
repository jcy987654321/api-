<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'API Management System',
                'type' => 'string',
                'description' => 'The name of the website',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Powerful management system for APIs and services',
                'type' => 'text',
                'description' => 'Site description for SEO and meta tags',
                'is_public' => true,
            ],
            [
                'key' => 'site_keywords',
                'value' => 'api,management,documentation,development',
                'type' => 'string',
                'description' => 'Site keywords for SEO',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
            ],
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow new user registrations',
                'is_public' => false,
            ],
            [
                'key' => 'default_api_rate_limit',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'Default API rate limit per hour',
                'is_public' => false,
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'description' => 'Contact email for the site',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }
    }
}