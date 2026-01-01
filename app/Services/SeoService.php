<?php

namespace App\Services;

use App\Models\SeoConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SeoService extends BaseService
{
    public const CACHE_KEY_ALL = 'cms:seo:all';

    public const DEFAULT_PAGES = [
        'home' => '首页',
        'blog' => '博客列表',
        'about' => '关于页面',
        'contact' => '联系页面',
        'apis' => 'API列表',
        'links' => '友链',
    ];

    public function ensureDefaults(): void
    {
        foreach (self::DEFAULT_PAGES as $page => $label) {
            SeoConfig::query()->firstOrCreate(['page' => $page], ['title' => $label]);
        }

        $this->clearCache();
    }

    public function getConfig(string $page): ?SeoConfig
    {
        $page = trim($page);

        $all = $this->all();

        return $all[$page] ?? null;
    }

    /**
     * @return array{title: string, description: string, keywords: string, og_title: string, og_description: string, og_image: ?string}
     */
    public function generateMeta(string $page, array $data = []): array
    {
        /** @var SettingService $settings */
        $settings = app(SettingService::class);

        $config = $this->getConfig($page);

        $siteName = (string) $settings->get('site_name', '');
        $siteDescription = (string) $settings->get('site_description', '');
        $siteKeywords = (string) $settings->get('site_keywords', '');

        $prefix = (string) $settings->get('seo_title_prefix', '');
        $suffix = (string) $settings->get('seo_title_suffix', '');

        $baseTitle = (string) ($config?->title ?: ($data['title'] ?? $siteName));
        $title = trim(trim($prefix . ' ' . $baseTitle . ' ' . $suffix));
        if ($title === '') {
            $title = $siteName !== '' ? $siteName : 'Website';
        }

        $description = (string) ($config?->description ?: ($data['description'] ?? $siteDescription));
        $keywords = (string) ($config?->keywords ?: ($data['keywords'] ?? $siteKeywords));

        $ogTitle = (string) ($config?->og_title ?: $title);
        $ogDescription = (string) ($config?->og_description ?: $description);
        $ogImage = $config?->og_image ?: ($data['og_image'] ?? null);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => is_string($ogImage) && $ogImage !== '' ? $ogImage : null,
        ];
    }

    public function renderMeta(string $page, array $data = []): string
    {
        /** @var SettingService $settings */
        $settings = app(SettingService::class);

        $enabled = (bool) $settings->get('seo_enabled', true);
        if (!$enabled) {
            $title = $data['title'] ?? (string) $settings->get('site_name', 'Website');

            return '<title>' . e($title) . '</title>';
        }

        $meta = $this->generateMeta($page, $data);

        $siteUrl = (string) $settings->get('site_url', '');
        $currentUrl = request()->fullUrl();

        $canonical = $siteUrl !== '' ? $this->buildCanonicalUrl($siteUrl, request()->path()) : $currentUrl;

        $html = [];
        $html[] = '<title>' . e($meta['title']) . '</title>';

        if (trim($meta['description']) !== '') {
            $html[] = '<meta name="description" content="' . e($meta['description']) . '">';
        }

        if (trim($meta['keywords']) !== '') {
            $html[] = '<meta name="keywords" content="' . e($meta['keywords']) . '">';
        }

        $html[] = '<link rel="canonical" href="' . e($canonical) . '">';

        $html[] = '<meta property="og:type" content="website">';
        $html[] = '<meta property="og:title" content="' . e($meta['og_title']) . '">';

        if (trim($meta['og_description']) !== '') {
            $html[] = '<meta property="og:description" content="' . e($meta['og_description']) . '">';
        }

        $html[] = '<meta property="og:url" content="' . e($currentUrl) . '">';

        if ($meta['og_image']) {
            $html[] = '<meta property="og:image" content="' . e($meta['og_image']) . '">';
        }

        return implode("\n", $html);
    }

    public function resolvePageFromRoute(): string
    {
        $name = (string) Route::currentRouteName();

        return match ($name) {
            'home' => 'home',
            'blog.index' => 'blog',
            'blog.show', 'blog.category', 'blog.tag', 'blog.search' => 'blog',
            'about' => 'about',
            'contact' => 'contact',
            'apis.index', 'apis.show', 'api.test' => 'apis',
            'links.index', 'links.category' => 'links',
            default => 'custom',
        };
    }

    /**
     * @return array<string, SeoConfig>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY_ALL, function () {
            return SeoConfig::query()
                ->orderBy('page')
                ->get()
                ->keyBy('page')
                ->all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }

    private function buildCanonicalUrl(string $siteUrl, string $path): string
    {
        $siteUrl = rtrim($siteUrl, '/');
        $path = '/' . ltrim($path, '/');

        return $siteUrl . $path;
    }
}
