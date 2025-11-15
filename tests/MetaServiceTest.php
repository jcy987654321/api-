<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/bootstrap.php';

class MetaServiceTest extends TestCase
{
    private App\Services\MetaService $metaService;

    protected function setUp(): void
    {
        $this->metaService = new App\Services\MetaService();
    }

    public function testGenerateHomeMeta(): void
    {
        $meta = $this->metaService->generatePageMeta('home');
        
        $this->assertArrayHasKey('title', $meta);
        $this->assertArrayHasKey('description', $meta);
        $this->assertArrayHasKey('keywords', $meta);
        $this->assertArrayHasKey('canonical_url', $meta);
        $this->assertArrayHasKey('og_title', $meta);
        $this->assertArrayHasKey('og_description', $meta);
        $this->assertArrayHasKey('og_image', $meta);
        $this->assertArrayHasKey('twitter_title', $meta);
        $this->assertArrayHasKey('twitter_description', $meta);
        
        $this->assertNotEmpty($meta['title']);
        $this->assertNotEmpty($meta['description']);
        $this->assertNotEmpty($meta['keywords']);
        $this->assertStringContains('api-management.example.com', $meta['canonical_url']);
    }

    public function testGenerateApiDetailMeta(): void
    {
        $apiData = [
            'api' => [
                'id' => 1,
                'name' => 'Weather API',
                'slug' => 'weather-api',
                'description' => 'Get current weather conditions and forecasts',
                'category' => 'Weather',
                'status' => 'active'
            ]
        ];
        
        $meta = $this->metaService->generatePageMeta('api_detail', $apiData);
        
        $this->assertStringContains('Weather API', $meta['title']);
        $this->assertStringContains('weather-api', $meta['canonical_url']);
        $this->assertStringContains('Weather', $meta['keywords']);
        $this->assertStringContains('active', $meta['keywords']);
        $this->assertEquals('article', $meta['og_type']);
    }

    public function testGenerateApiListMetaWithCategory(): void
    {
        $data = ['category' => 'Weather'];
        $meta = $this->metaService->generatePageMeta('api_list', $data);
        
        $this->assertStringContains('Weather', $meta['title']);
        $this->assertStringContains('Weather', $meta['description']);
        $this->assertStringContains('Weather', $meta['keywords']);
        $this->assertStringContains('category=Weather', $meta['canonical_url']);
    }

    public function testGenerateAnnouncementDetailMeta(): void
    {
        $announcementData = [
            'announcement' => [
                'id' => 1,
                'title' => 'New API Released',
                'slug' => 'new-api-released',
                'summary' => 'We are excited to announce a new API',
                'content' => 'Full content here...',
                'created_at' => '2023-01-01'
            ]
        ];
        
        $meta = $this->metaService->generatePageMeta('announcement_detail', $announcementData);
        
        $this->assertStringContains('New API Released', $meta['title']);
        $this->assertStringContains('new-api-released', $meta['canonical_url']);
        $this->assertEquals('article', $meta['og_type']);
    }

    public function testGenerateJsonLdForApi(): void
    {
        $apiData = [
            'api' => [
                'name' => 'Weather API',
                'description' => 'Get weather data'
            ]
        ];
        
        $meta = $this->metaService->generatePageMeta('api_detail', $apiData);
        $jsonLd = $this->metaService->generateJsonLd($meta, $apiData);
        
        $this->assertArrayHasKey('@context', $jsonLd);
        $this->assertEquals('https://schema.org', $jsonLd['@context']);
        $this->assertArrayHasKey('@type', $jsonLd);
        $this->assertEquals('SoftwareApplication', $jsonLd['@type']);
        $this->assertEquals('Weather API', $jsonLd['name']);
    }

    public function testGenerateJsonLdForArticle(): void
    {
        $articleData = [
            'announcement' => [
                'title' => 'New API Released',
                'summary' => 'We are excited to announce',
                'created_at' => '2023-01-01'
            ]
        ];
        
        $meta = $this->metaService->generatePageMeta('announcement_detail', $articleData);
        $jsonLd = $this->metaService->generateJsonLd($meta, $articleData);
        
        $this->assertArrayHasKey('@type', $jsonLd);
        $this->assertEquals('Article', $jsonLd['@type']);
        $this->assertEquals('New API Released', $jsonLd['headline']);
    }

    public function testMetaTagFallbacks(): void
    {
        $meta = $this->metaService->generatePageMeta('nonexistent_page');
        
        // Should fall back to defaults
        $this->assertNotEmpty($meta['title']);
        $this->assertNotEmpty($meta['description']);
        $this->assertNotEmpty($meta['keywords']);
        $this->assertEquals('website', $meta['og_type']);
    }
}