<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/bootstrap.php';

class RssServiceTest extends TestCase
{
    private App\Services\RssService $rssService;
    private string $testCacheDir;

    protected function setUp(): void
    {
        $this->rssService = new App\Services\RssService();
        $this->testCacheDir = ROOT_PATH . '/cache';
        
        // Ensure cache directory exists
        if (!is_dir($this->testCacheDir)) {
            mkdir($this->testCacheDir, 0755, true);
        }
        
        // Clear any existing cache
        $this->clearCache();
    }

    protected function tearDown(): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        $cacheFile = $this->testCacheDir . '/rss_feed.xml';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function testGenerateRssFeed(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $this->assertNotEmpty($xml);
        $this->assertStringContains('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContains('<rss version="2.0"', $xml);
        $this->assertStringContains('<channel>', $xml);
        $this->assertStringContains('</channel>', $xml);
        $this->assertStringContains('</rss>', $xml);
    }

    public function testRssFeedContainsRequiredElements(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        // Check for required channel elements
        $this->assertStringContains('<title>', $xml);
        $this->assertStringContains('</title>', $xml);
        $this->assertStringContains('<description>', $xml);
        $this->assertStringContains('</description>', $xml);
        $this->assertStringContains('<link>', $xml);
        $this->assertStringContains('</link>', $xml);
        $this->assertStringContains('<language>', $xml);
        $this->assertStringContains('</language>', $xml);
        $this->assertStringContains('<lastBuildDate>', $xml);
        $this->assertStringContains('</lastBuildDate>', $xml);
    }

    public function testRssFeedContainsAtomLink(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        // Should contain self-referencing Atom link
        $this->assertStringContains('<atom:link', $xml);
        $this->assertStringContains('rel="self"', $xml);
        $this->assertStringContains('type="application/rss+xml"', $xml);
        $this->assertStringContains('href="https://api-management.example.com/rss.xml"', $xml);
    }

    public function testRssFeedIsValidXml(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $dom = new DOMDocument();
        $this->assertTrue($dom->loadXML($xml), 'Generated RSS feed should be valid XML');
        
        // Validate RSS structure
        $rss = $dom->getElementsByTagName('rss');
        $this->assertEquals(1, $rss->length, 'Should have exactly one rss element');
        
        $this->assertEquals('2.0', $rss->item(0)->getAttribute('version'));
        
        $channels = $dom->getElementsByTagName('channel');
        $this->assertEquals(1, $channels->length, 'Should have exactly one channel element');
    }

    public function testRssFeedItemsStructure(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $items = $dom->getElementsByTagName('item');
        // Note: Items count depends on database data
        foreach ($items as $item) {
            // Each item should have required elements
            $titles = $item->getElementsByTagName('title');
            $this->assertEquals(1, $titles->length, 'Each item should have exactly one title');
            
            $descriptions = $item->getElementsByTagName('description');
            $this->assertEquals(1, $descriptions->length, 'Each item should have exactly one description');
            
            $links = $item->getElementsByTagName('link');
            $this->assertEquals(1, $links->length, 'Each item should have exactly one link');
            
            $guids = $item->getElementsByTagName('guid');
            $this->assertEquals(1, $guids->length, 'Each item should have exactly one guid');
            
            $pubDates = $item->getElementsByTagName('pubDate');
            $this->assertEquals(1, $pubDates->length, 'Each item should have exactly one pubDate');
            
            // Validate date format (RFC 2822)
            $pubDate = $pubDates->item(0)->textContent;
            $timestamp = strtotime($pubDate);
            $this->assertNotFalse($timestamp, 'pubDate should be a valid RFC 2822 date');
        }
    }

    public function testRssFeedCaching(): void
    {
        // First generation should create cache
        $xml1 = $this->rssService->generateRssFeed();
        $cacheFile = $this->testCacheDir . '/rss_feed.xml';
        $this->assertFileExists($cacheFile, 'Cache file should be created');
        
        // Second generation should use cache (should be identical)
        $xml2 = $this->rssService->generateRssFeed();
        $this->assertEquals($xml1, $xml2, 'Cached RSS feed should be identical');
    }

    public function testCacheClear(): void
    {
        // Generate RSS feed to create cache
        $this->rssService->generateRssFeed();
        $cacheFile = $this->testCacheDir . '/rss_feed.xml';
        $this->assertFileExists($cacheFile);
        
        // Clear cache
        $this->rssService->clearCache();
        $this->assertFileDoesNotExist($cacheFile, 'Cache file should be deleted');
    }

    public function testRssFeedChannelMetadata(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $channel = $dom->getElementsByTagName('channel')->item(0);
        
        $title = $channel->getElementsByTagName('title')->item(0)->textContent;
        $this->assertEquals('API Management System', $title);
        
        $description = $channel->getElementsByTagName('description')->item(0)->textContent;
        $this->assertNotEmpty($description);
        
        $link = $channel->getElementsByTagName('link')->item(0)->textContent;
        $this->assertEquals('https://api-management.example.com', $link);
        
        $language = $channel->getElementsByTagName('language')->item(0)->textContent;
        $this->assertEquals('en-us', $language);
    }

    public function testRssFeedItemCategories(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $items = $dom->getElementsByTagName('item');
        foreach ($items as $item) {
            $categories = $item->getElementsByTagName('category');
            $this->assertGreaterThanOrEqual(1, $categories->length, 'Each item should have at least one category');
        }
    }

    public function testRssFeedItemAuthors(): void
    {
        $xml = $this->rssService->generateRssFeed();
        
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $items = $dom->getElementsByTagName('item');
        foreach ($items as $item) {
            $authors = $item->getElementsByTagName('author');
            $this->assertEquals(1, $authors->length, 'Each item should have exactly one author');
            
            $author = $authors->item(0)->textContent;
            $this->assertNotEmpty($author, 'Author should not be empty');
        }
    }
}