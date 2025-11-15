<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        // Get some sample data for the homepage
        $featuredApis = $this->getFeaturedApis();
        $latestAnnouncements = $this->getLatestAnnouncements();
        $stats = $this->getSiteStats();
        
        $this->renderView('home', [
            'page_type' => 'home',
            'featured_apis' => $featuredApis,
            'latest_announcements' => $latestAnnouncements,
            'stats' => $stats,
        ]);
    }

    private function getFeaturedApis(): array
    {
        // For now, return empty array - would fetch from database
        return [];
    }

    private function getLatestAnnouncements(): array
    {
        // For now, return empty array - would fetch from database
        return [];
    }

    private function getSiteStats(): array
    {
        return [
            'total_apis' => 0,
            'active_apis' => 0,
            'total_announcements' => 0,
        ];
    }
}