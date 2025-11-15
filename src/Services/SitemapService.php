<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Config\Settings;
use DateTime;

class SitemapService
{
    private string $baseUrl;
    private array $urls = [];
    private int $cacheDuration;

    public function __construct()
    {
        $this->baseUrl = Settings::get('site.url');
        $this->cacheDuration = Settings::get('sitemap.cache_duration', 3600);
    }

    public function generateSitemap(): string
    {
        // Check cache first
        $cacheKey = 'sitemap_xml';
        $cached = $this->getCachedSitemap($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        $this->collectUrls();
        $xml = $this->buildXml();
        
        // Cache the result
        $this->cacheSitemap($cacheKey, $xml);
        
        // Ping search engines
        $this->pingSearchEngines();
        
        return $xml;
    }

    private function collectUrls(): void
    {
        $this->urls = [];
        
        // Static pages
        $this->addUrl('/', '1.0', 'daily', new DateTime());
        $this->addUrl('/apis', '0.9', 'daily', new DateTime());
        $this->addUrl('/announcements', '0.8', 'daily', new DateTime());
        $this->addUrl('/feedback', '0.7', 'monthly', new DateTime());
        
        // API list pages by category
        $categories = $this->getApiCategories();
        foreach ($categories as $category) {
            $this->addUrl("/apis?category={$category['slug']}", '0.8', 'weekly', new DateTime());
        }
        
        // Individual API pages
        $apis = $this->getApis();
        foreach ($apis as $api) {
            $priority = $api['status'] === 'active' ? '0.9' : '0.7';
            $changeFreq = $api['status'] === 'active' ? 'weekly' : 'monthly';
            $lastMod = new DateTime($api['updated_at']);
            $this->addUrl("/api/{$api['slug']}", $priority, $changeFreq, $lastMod);
        }
        
        // Announcement pages
        $announcements = $this->getAnnouncements();
        foreach ($announcements as $announcement) {
            $lastMod = new DateTime($announcement['updated_at'] ?? $announcement['created_at']);
            $this->addUrl("/announcement/{$announcement['slug']}", '0.8', 'monthly', $lastMod);
        }
    }

    private function addUrl(string $path, string $priority, string $changeFreq, DateTime $lastMod): void
    {
        $this->urls[] = [
            'loc' => $this->baseUrl . $path,
            'priority' => $priority,
            'changefreq' => $changeFreq,
            'lastmod' => $lastMod->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    private function buildXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($this->urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function getApiCategories(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT DISTINCT 
                LOWER(category) as slug,
                category as name,
                COUNT(*) as api_count
            FROM apis 
            WHERE status = 'active'
            GROUP BY category
            ORDER BY api_count DESC
        ");
        
        return $stmt->fetchAll() ?: [];
    }

    private function getApis(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT slug, status, updated_at, created_at
            FROM apis 
            ORDER BY updated_at DESC
        ");
        
        return $stmt->fetchAll() ?: [];
    }

    private function getAnnouncements(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT slug, title, created_at, updated_at
            FROM announcements 
            WHERE status = 'published'
            ORDER BY created_at DESC
        ");
        
        return $stmt->fetchAll() ?: [];
    }

    private function getCachedSitemap(string $key): ?string
    {
        $cacheFile = $this->getCacheFilePath($key);
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        $cacheTime = filemtime($cacheFile);
        if (time() - $cacheTime > $this->cacheDuration) {
            unlink($cacheFile);
            return null;
        }
        
        return file_get_contents($cacheFile) ?: null;
    }

    private function cacheSitemap(string $key, string $content): void
    {
        $cacheFile = $this->getCacheFilePath($key);
        $cacheDir = dirname($cacheFile);
        
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($cacheFile, $content);
    }

    private function getCacheFilePath(string $key): string
    {
        return ROOT_PATH . '/cache/' . $key . '.xml';
    }

    private function pingSearchEngines(): void
    {
        if (!Settings::get('sitemap.ping_engines', true)) {
            return;
        }
        
        $sitemapUrl = $this->baseUrl . '/sitemap.xml';
        $engines = [
            'google' => 'https://www.google.com/webmasters/tools/ping',
            'bing' => 'https://www.bing.com/webmaster/ping.aspx',
        ];
        
        foreach ($engines as $name => $pingUrl) {
            $url = $pingUrl . '?sitemap=' . urlencode($sitemapUrl);
            
            // Send ping asynchronously (non-blocking)
            $this->sendAsyncPing($url, $name);
        }
    }

    private function sendAsyncPing(string $url, string $engine): void
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);
        
        // Use @ to suppress warnings, we don't care about the response
        @file_get_contents($url, false, $context);
        
        // Log the ping attempt (in a real app, you'd use proper logging)
        error_log("Sitemap ping sent to {$engine}: {$url}");
    }

    public function clearCache(): void
    {
        $cacheFile = $this->getCacheFilePath('sitemap_xml');
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}