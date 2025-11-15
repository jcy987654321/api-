<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use App\Services\RssService;

// CLI RSS Generator
class RssCommand
{
    private RssService $rssService;

    public function __construct()
    {
        $this->rssService = new RssService();
    }

    public function generate(): void
    {
        echo "Generating RSS feed...\n";
        
        try {
            $xml = $this->rssService->generateRssFeed();
            $size = strlen($xml);
            
            echo "✓ RSS feed generated successfully!\n";
            echo "  Size: {$size} bytes\n";
            echo "  Location: " . \App\Config\Settings::get('site.url') . "/rss.xml\n";
            
            // Count items
            $itemCount = substr_count($xml, '<item>');
            echo "  Items: {$itemCount}\n";
            
        } catch (\Exception $e) {
            echo "✗ Failed to generate RSS feed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function clearCache(): void
    {
        echo "Clearing RSS cache...\n";
        
        try {
            $this->rssService->clearCache();
            echo "✓ RSS cache cleared successfully!\n";
        } catch (\Exception $e) {
            echo "✗ Failed to clear cache: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function regenerate(): void
    {
        echo "Regenerating RSS feed (clearing cache first)...\n";
        
        try {
            $this->rssService->clearCache();
            $xml = $this->rssService->generateRssFeed();
            $size = strlen($xml);
            
            echo "✓ RSS feed regenerated successfully!\n";
            echo "  Size: {$size} bytes\n";
            echo "  Location: " . \App\Config\Settings::get('site.url') . "/rss.xml\n";
            
            // Count items
            $itemCount = substr_count($xml, '<item>');
            echo "  Items: {$itemCount}\n";
            
        } catch (\Exception $e) {
            echo "✗ Failed to regenerate RSS feed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function showHelp(): void
    {
        echo "RSS Feed Generator CLI\n";
        echo "Usage: php rss.php [command]\n\n";
        echo "Commands:\n";
        echo "  generate     Generate the RSS feed\n";
        echo "  regenerate   Clear cache and regenerate the RSS feed\n";
        echo "  clear        Clear the RSS cache\n";
        echo "  help         Show this help message\n\n";
        echo "Examples:\n";
        echo "  php rss.php generate\n";
        echo "  php rss.php regenerate\n";
        echo "  php rss.php clear\n";
    }
}

// Parse command line arguments
$command = $argv[1] ?? 'help';

$rssCommand = new RssCommand();

switch ($command) {
    case 'generate':
        $rssCommand->generate();
        break;
    case 'regenerate':
        $rssCommand->regenerate();
        break;
    case 'clear':
        $rssCommand->clearCache();
        break;
    case 'help':
    default:
        $rssCommand->showHelp();
        break;
}