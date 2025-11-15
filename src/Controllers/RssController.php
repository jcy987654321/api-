<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\RssService;

class RssController
{
    private RssService $rssService;

    public function __construct()
    {
        $this->rssService = new RssService();
    }

    public function index(): void
    {
        try {
            $xml = $this->rssService->generateRssFeed();
            
            header('Content-Type: application/rss+xml; charset=utf-8');
            header('Cache-Control: public, max-age=1800'); // Cache for 30 minutes
            echo $xml;
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<?xml version="1.0" encoding="UTF-8"?><error>Failed to generate RSS feed</error>';
            exit;
        }
    }

    public function regenerate(): void
    {
        try {
            $this->rssService->clearCache();
            $xml = $this->rssService->generateRssFeed();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'RSS feed regenerated successfully',
                'size' => strlen($xml),
            ]);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to regenerate RSS feed: ' . $e->getMessage(),
            ]);
            exit;
        }
    }
}