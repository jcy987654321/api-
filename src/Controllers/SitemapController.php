<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SitemapService;

class SitemapController
{
    private SitemapService $sitemapService;

    public function __construct()
    {
        $this->sitemapService = new SitemapService();
    }

    public function index(): void
    {
        try {
            $xml = $this->sitemapService->generateSitemap();
            
            header('Content-Type: application/xml; charset=utf-8');
            header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
            echo $xml;
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<?xml version="1.0" encoding="UTF-8"?><error>Failed to generate sitemap</error>';
            exit;
        }
    }

    public function regenerate(): void
    {
        try {
            $this->sitemapService->clearCache();
            $xml = $this->sitemapService->generateSitemap();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Sitemap regenerated successfully',
                'size' => strlen($xml),
            ]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to regenerate sitemap: ' . $e->getMessage(),
            ]);
            exit;
        }
    }
}