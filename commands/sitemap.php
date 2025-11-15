<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use App\Services\SitemapService;

// CLI Sitemap Generator
class SitemapCommand
{
    private SitemapService $sitemapService;

    public function __construct()
    {
        $this->sitemapService = new SitemapService();
    }

    public function generate(): void
    {
        echo "Generating sitemap...\n";
        
        try {
            $xml = $this->sitemapService->generateSitemap();
            $size = strlen($xml);
            
            echo "✓ Sitemap generated successfully!\n";
            echo "  Size: {$size} bytes\n";
            echo "  Location: " . \App\Config\Settings::get('site.url') . "/sitemap.xml\n";
            
            // Count URLs
            $urlCount = substr_count($xml, '<url>');
            echo "  URLs: {$urlCount}\n";
            
        } catch (\Exception $e) {
            echo "✗ Failed to generate sitemap: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function clearCache(): void
    {
        echo "Clearing sitemap cache...\n";
        
        try {
            $this->sitemapService->clearCache();
            echo "✓ Sitemap cache cleared successfully!\n";
        } catch (\Exception $e) {
            echo "✗ Failed to clear cache: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function showHelp(): void
    {
        echo "Sitemap Generator CLI\n";
        echo "Usage: php sitemap.php [command]\n\n";
        echo "Commands:\n";
        echo "  generate     Generate the sitemap.xml file\n";
        echo "  clear        Clear the sitemap cache\n";
        echo "  help         Show this help message\n\n";
        echo "Examples:\n";
        echo "  php sitemap.php generate\n";
        echo "  php sitemap.php clear\n";
    }
}

// Parse command line arguments
$command = $argv[1] ?? 'help';

$sitemapCommand = new SitemapCommand();

switch ($command) {
    case 'generate':
        $sitemapCommand->generate();
        break;
    case 'clear':
        $sitemapCommand->clearCache();
        break;
    case 'help':
    default:
        $sitemapCommand->showHelp();
        break;
}