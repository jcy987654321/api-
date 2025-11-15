<?php

declare(strict_types=1);

namespace App\Controllers;

class RobotsController
{
    public function index(): void
    {
        $baseUrl = \App\Config\Settings::get('site.url');
        $sitemapUrl = $baseUrl . '/sitemap.xml';
        
        $robotsContent = "User-agent: *\n";
        
        // Disallow common admin/private paths
        $robotsContent .= "Disallow: /admin/\n";
        $robotsContent .= "Disallow: /api/admin/\n";
        $robotsContent .= "Disallow: /user/\n";
        $robotsContent .= "Disallow: /login\n";
        $robotsContent .= "Disallow: /register\n";
        $robotsContent .= "Disallow: /cache/\n";
        $robotsContent .= "Disallow: /config/\n";
        $robotsContent .= "Disallow: /src/\n";
        $robotsContent .= "Disallow: /vendor/\n";
        
        // Allow important paths
        $robotsContent .= "Allow: /api/\n";
        $robotsContent .= "Allow: /apis\n";
        $robotsContent .= "Allow: /announcements\n";
        $robotsContent .= "Allow: /feedback\n";
        
        // Crawl delay (optional, be nice to servers)
        $robotsContent .= "Crawl-delay: 1\n";
        
        // Sitemap reference
        $robotsContent .= "Sitemap: " . $sitemapUrl . "\n";
        
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
        echo $robotsContent;
        exit;
    }
}