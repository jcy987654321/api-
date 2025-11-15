<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/bootstrap.php';

class SitemapServiceTest extends TestCase
{
    private App\Services\SitemapService $sitemapService;
    private string $testCacheDir;

    protected function setUp(): void
    {
        $this->sitemapService = new App\Services\SitemapService();
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
        $cacheFile = $this->testCacheDir . '/sitemap_xml.xml';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function testGenerateSitemap(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        $this->assertNotEmpty($xml);
        $this->assertStringContains('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContains('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $xml);
        $this->assertStringContains('</urlset>', $xml);
    }

    public function testSitemapContainsRequiredElements(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        // Check for static pages
        $this->assertStringContains('<loc>', $xml);
        $this->assertStringContains('</loc>', $xml);
        $this->assertStringContains('<lastmod>', $xml);
        $this->assertStringContains('</lastmod>', $xml);
        $this->assertStringContains('<changefreq>', $xml);
        $this->assertStringContains('</changefreq>', $xml);
        $this->assertStringContains('<priority>', $xml);
        $this->assertStringContains('</priority>', $xml);
    }

    public function testSitemapContainsHomePage(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        $this->assertStringContains('api-management.example.com/', $xml);
        $this->assertStringContains('<priority>1.0</priority>', $xml);
        $this->assertStringContains('<changefreq>daily</changefreq>', $xml);
    }

    public function testSitemapContainsApiPages(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        // Should contain API list page
        $this->assertStringContains('api-management.example.com/apis', $xml);
        
        // Should contain individual API pages (if data exists)
        // Note: This test would require actual database data
    }

    public function testSitemapContainsAnnouncementPages(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        // Should contain announcements page
        $this->assertStringContains('api-management.example.com/announcements', $xml);
    }

    public function testSitemapContainsFeedbackPage(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        // Should contain feedback page
        $this->assertStringContains('api-management.example.com/feedback', $xml);
    }

    public function testSitemapIsValidXml(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        $dom = new DOMDocument();
        $this->assertTrue($dom->loadXML($xml), 'Generated sitemap should be valid XML');
        
        // Validate against sitemap schema (basic check)
        $urlset = $dom->getElementsByTagName('urlset');
        $this->assertEquals(1, $urlset->length, 'Should have exactly one urlset element');
        
        $this->assertEquals('http://www.sitemaps.org/schemas/sitemap/0.9', 
            $urlset->item(0)->getAttribute('xmlns'));
    }

    public function testSitemapCaching(): void
    {
        // First generation should create cache
        $xml1 = $this->sitemapService->generateSitemap();
        $cacheFile = $this->testCacheDir . '/sitemap_xml.xml';
        $this->assertFileExists($cacheFile, 'Cache file should be created');
        
        // Second generation should use cache (should be identical)
        $xml2 = $this->sitemapService->generateSitemap();
        $this->assertEquals($xml1, $xml2, 'Cached sitemap should be identical');
    }

    public function testCacheClear(): void
    {
        // Generate sitemap to create cache
        $this->sitemapService->generateSitemap();
        $cacheFile = $this->testCacheDir . '/sitemap_xml.xml';
        $this->assertFileExists($cacheFile);
        
        // Clear cache
        $this->sitemapService->clearCache();
        $this->assertFileDoesNotExist($cacheFile, 'Cache file should be deleted');
    }

    public function testSitemapUrlStructure(): void
    {
        $xml = $this->sitemapService->generateSitemap();
        
        // Parse XML to inspect structure
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $urls = $dom->getElementsByTagName('url');
        $this->assertGreaterThan(0, $urls->length, 'Sitemap should contain at least one URL');
        
        foreach ($urls as $url) {
            $locs = $url->getElementsByTagName('loc');
            $this->assertEquals(1, $locs->length, 'Each URL should have exactly one loc element');
            
            $loc = $locs->item(0)->textContent;
            $this->assertStringStartsWith('https://api-management.example.com/', $loc);
            
            $priorities = $url->getElementsByTagName('priority');
            $priority = $priorities->item(0)->textContent;
            $this->assertGreaterThanOrEqual(0.0, (float) $priority);
            $this->assertLessThanOrEqual(1.0, (float) $priority);
        }
    }
}