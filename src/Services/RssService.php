<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Config\Settings;
use DateTime;

class RssService
{
    private string $baseUrl;
    private array $items = [];
    private int $cacheDuration;
    private int $maxItems;

    public function __construct()
    {
        $this->baseUrl = Settings::get('site.url');
        $this->cacheDuration = Settings::get('rss.cache_duration', 1800);
        $this->maxItems = Settings::get('rss.items_per_feed', 50);
    }

    public function generateRssFeed(): string
    {
        // Check cache first
        $cacheKey = 'rss_feed';
        $cached = $this->getCachedRss($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        $this->collectItems();
        $xml = $this->buildXml();
        
        // Cache the result
        $this->cacheRss($cacheKey, $xml);
        
        return $xml;
    }

    private function collectItems(): void
    {
        $this->items = [];
        
        // Get latest APIs
        $apis = $this->getLatestApis();
        foreach ($apis as $api) {
            $this->items[] = [
                'title' => "New API: {$api['name']}",
                'description' => $api['description'] ?? "Discover {$api['name']}, a new API in the {$api['category']} category",
                'link' => $this->baseUrl . "/api/{$api['slug']}",
                'guid' => "api-{$api['id']}",
                'pubDate' => new DateTime($api['created_at']),
                'category' => $api['category'],
                'author' => Settings::get('site.name'),
            ];
        }
        
        // Get latest announcements
        $announcements = $this->getLatestAnnouncements();
        foreach ($announcements as $announcement) {
            $this->items[] = [
                'title' => $announcement['title'],
                'description' => $announcement['summary'] ?? strip_tags(substr($announcement['content'], 0, 300)),
                'link' => $this->baseUrl . "/announcement/{$announcement['slug']}",
                'guid' => "announcement-{$announcement['id']}",
                'pubDate' => new DateTime($announcement['created_at']),
                'category' => 'Announcement',
                'author' => $announcement['author'] ?? Settings::get('site.name'),
            ];
        }
        
        // Sort by publication date (newest first)
        usort($this->items, function ($a, $b) {
            return $b['pubDate'] <=> $a['pubDate'];
        });
        
        // Limit to max items
        $this->items = array_slice($this->items, 0, $this->maxItems);
    }

    private function buildXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "  <channel>\n";
        
        // Channel metadata
        $xml .= "    <title>" . htmlspecialchars(Settings::get('site.name')) . "</title>\n";
        $xml .= "    <description>" . htmlspecialchars(Settings::get('site.description')) . "</description>\n";
        $xml .= "    <link>" . htmlspecialchars($this->baseUrl) . "</link>\n";
        $xml .= "    <language>en-us</language>\n";
        $xml .= "    <copyright>Copyright " . date('Y') . " " . htmlspecialchars(Settings::get('site.name')) . "</copyright>\n";
        $xml .= "    <managingEditor>" . htmlspecialchars(Settings::get('site.author')) . "</managingEditor>\n";
        $xml .= "    <webMaster>" . htmlspecialchars(Settings::get('site.author')) . "</webMaster>\n";
        $xml .= "    <lastBuildDate>" . date('r') . "</lastBuildDate>\n";
        $xml .= "    <generator>API Management System RSS Generator</generator>\n";
        
        // Self-referencing link
        $xml .= "    <atom:link href=\"" . htmlspecialchars($this->baseUrl . "/rss.xml") . "\" rel=\"self\" type=\"application/rss+xml\" />\n";
        
        // Add items
        foreach ($this->items as $item) {
            $xml .= "    <item>\n";
            $xml .= "      <title>" . htmlspecialchars($item['title']) . "</title>\n";
            $xml .= "      <description>" . htmlspecialchars($item['description']) . "</description>\n";
            $xml .= "      <link>" . htmlspecialchars($item['link']) . "</link>\n";
            $xml .= "      <guid isPermaLink=\"false\">" . htmlspecialchars($item['guid']) . "</guid>\n";
            $xml .= "      <pubDate>" . $item['pubDate']->format('r') . "</pubDate>\n";
            $xml .= "      <category>" . htmlspecialchars($item['category']) . "</category>\n";
            $xml .= "      <author>" . htmlspecialchars($item['author']) . "</author>\n";
            $xml .= "    </item>\n";
        }
        
        $xml .= "  </channel>\n";
        $xml .= "</rss>";
        
        return $xml;
    }

    private function getLatestApis(): array
    {
        $db = Database::getConnection();
        $limit = (int) ($this->maxItems * 0.6); // 60% of items for APIs
        
        $stmt = $db->prepare("
            SELECT id, name, slug, description, category, created_at
            FROM apis 
            WHERE status = 'active'
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll() ?: [];
    }

    private function getLatestAnnouncements(): array
    {
        $db = Database::getConnection();
        $limit = (int) ($this->maxItems * 0.4); // 40% of items for announcements
        
        $stmt = $db->prepare("
            SELECT id, title, slug, summary, content, created_at, author
            FROM announcements 
            WHERE status = 'published'
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll() ?: [];
    }

    private function getCachedRss(string $key): ?string
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

    private function cacheRss(string $key, string $content): void
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

    public function clearCache(): void
    {
        $cacheFile = $this->getCacheFilePath('rss_feed');
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function regenerateOnContentChange(): void
    {
        // This method should be called when content is updated
        $this->clearCache();
        
        // Optionally, pre-generate the cache
        $this->generateRssFeed();
    }
}