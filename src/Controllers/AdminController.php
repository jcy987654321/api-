<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Config\Settings;

class AdminController extends BaseController
{
    public function dashboard(): void
    {
        // Check if user is authenticated (simplified for demo)
        if (!$this->isAdmin()) {
            $this->redirect('/login');
            return;
        }

        $stats = $this->getAdminStats();
        
        $this->renderView('admin/dashboard', [
            'page_type' => 'admin',
            'stats' => $stats,
        ]);
    }

    public function seoSettings(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSeoSettings();
            $this->redirect('/admin/seo-settings?success=1');
            return;
        }

        $settings = $this->getSeoSettings();
        
        $this->renderView('admin/seo_settings', [
            'page_type' => 'admin',
            'settings' => $settings,
            'success' => isset($_GET['success']),
        ]);
    }

    public function regenerateSitemap(): void
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $sitemapController = new SitemapController();
        $sitemapController->regenerate();
    }

    public function regenerateRss(): void
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $rssController = new RssController();
        $rssController->regenerate();
    }

    private function isAdmin(): bool
    {
        // Simplified admin check - in production, use proper authentication
        return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
    }

    private function getAdminStats(): array
    {
        $db = Database::getConnection();
        
        $stats = [];
        
        // API counts
        $stmt = $db->query("SELECT COUNT(*) as total FROM apis");
        $stats['total_apis'] = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT COUNT(*) as active FROM apis WHERE status = 'active'");
        $stats['active_apis'] = $stmt->fetch()['active'];
        
        // Announcement counts
        $stmt = $db->query("SELECT COUNT(*) as total FROM announcements");
        $stats['total_announcements'] = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT COUNT(*) as published FROM announcements WHERE status = 'published'");
        $stats['published_announcements'] = $stmt->fetch()['published'];
        
        // Feedback counts
        $stmt = $db->query("SELECT COUNT(*) as total FROM feedback");
        $stats['total_feedback'] = $stmt->fetch()['total'];
        
        $stmt = $db->query("SELECT COUNT(*) as new FROM feedback WHERE status = 'new'");
        $stats['new_feedback'] = $stmt->fetch()['new'];
        
        return $stats;
    }

    private function getSeoSettings(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT `key`, `value` FROM settings ORDER BY `key`");
        $settings = [];
        
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['key']] = $row['value'];
        }
        
        return $settings;
    }

    private function saveSeoSettings(): void
    {
        $db = Database::getConnection();
        
        $seoKeys = [
            'site_name', 'site_description', 'site_keywords', 'site_url',
            'site_author', 'twitter_handle', 'default_title', 'default_description',
            'default_keywords', 'custom_keywords', 'meta_image', 'twitter_card_type',
            'sitemap_cache_duration', 'sitemap_ping_engines', 'rss_cache_duration',
            'rss_items_per_feed'
        ];
        
        foreach ($seoKeys as $key) {
            if (isset($_POST[$key])) {
                $value = $_POST[$key];
                
                $stmt = $db->prepare("
                    INSERT INTO settings (`key`, `value`) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
                ");
                $stmt->execute([$key, $value]);
                
                // Update runtime settings
                Settings::set(str_replace('_', '.', $key), $value);
            }
        }
    }
}