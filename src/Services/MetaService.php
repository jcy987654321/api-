<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Settings;

class MetaService
{
    public function generatePageMeta(string $page, array $data = []): array
    {
        $baseTitle = Settings::get('site.name');
        $baseUrl = Settings::get('site.url');
        $defaultDescription = Settings::get('seo.default_description');
        $defaultKeywords = Settings::get('seo.default_keywords');
        $customKeywords = Settings::get('seo.custom_keywords');
        
        $meta = [
            'title' => $baseTitle,
            'description' => $defaultDescription,
            'keywords' => trim($defaultKeywords . ', ' . $customKeywords, ', '),
            'canonical_url' => $baseUrl,
            'og_title' => $baseTitle,
            'og_description' => $defaultDescription,
            'og_image' => $baseUrl . Settings::get('seo.meta_image'),
            'og_type' => 'website',
            'og_url' => $baseUrl,
            'twitter_card' => Settings::get('seo.twitter_card_type'),
            'twitter_title' => $baseTitle,
            'twitter_description' => $defaultDescription,
            'twitter_image' => $baseUrl . Settings::get('seo.meta_image'),
            'twitter_site' => Settings::get('site.twitter_handle'),
        ];

        return match ($page) {
            'home' => $this->generateHomeMeta($meta, $data),
            'api_list' => $this->generateApiListMeta($meta, $data),
            'api_detail' => $this->generateApiDetailMeta($meta, $data),
            'announcements' => $this->generateAnnouncementsMeta($meta, $data),
            'announcement_detail' => $this->generateAnnouncementDetailMeta($meta, $data),
            'feedback' => $this->generateFeedbackMeta($meta, $data),
            default => $meta,
        };
    }

    private function generateHomeMeta(array $meta, array $data): array
    {
        $meta['title'] = Settings::get('seo.default_title');
        $meta['description'] = Settings::get('seo.default_description');
        $meta['keywords'] = Settings::get('seo.default_keywords');
        $meta['canonical_url'] = Settings::get('site.url');
        $meta['og_url'] = Settings::get('site.url');
        
        return $meta;
    }

    private function generateApiListMeta(array $meta, array $data): array
    {
        $category = $data['category'] ?? '';
        $title = $category ? "APIs in {$category} - " . Settings::get('site.name') : "API Directory - " . Settings::get('site.name');
        
        $meta['title'] = $title;
        $meta['description'] = $category 
            ? "Browse and discover {$category} APIs for your integration needs"
            : "Browse our comprehensive directory of APIs across various categories";
        $meta['keywords'] = trim(($category ? "{$category}, " : "") . Settings::get('seo.default_keywords') . ', directory, catalog');
        $meta['canonical_url'] = Settings::get('site.url') . '/apis' . ($category ? "?category={$category}" : '');
        $meta['og_url'] = $meta['canonical_url'];
        $meta['og_title'] = $title;
        $meta['twitter_title'] = $title;
        
        return $meta;
    }

    private function generateApiDetailMeta(array $meta, array $data): array
    {
        $api = $data['api'] ?? [];
        if (!$api) {
            return $meta;
        }

        $title = $api['name'] . ' - API Details';
        $description = $api['description'] ?? "Explore {$api['name']} API documentation, endpoints, and integration guides";
        
        $meta['title'] = $title;
        $meta['description'] = $description;
        $meta['keywords'] = trim("{$api['name']}, {$api['category']}, " . Settings::get('seo.default_keywords') . ', integration');
        $meta['canonical_url'] = Settings::get('site.url') . '/api/' . $api['slug'];
        $meta['og_url'] = $meta['canonical_url'];
        $meta['og_title'] = $title;
        $meta['og_description'] = $description;
        $meta['og_type'] = 'article';
        $meta['twitter_title'] = $title;
        $meta['twitter_description'] = $description;
        
        // Add status-specific meta
        if (isset($api['status'])) {
            $statusKeywords = match ($api['status']) {
                'active' => 'stable, production-ready',
                'beta' => 'beta, testing, preview',
                'deprecated' => 'deprecated, legacy, migration',
                default => '',
            };
            $meta['keywords'] = trim($meta['keywords'] . ', ' . $statusKeywords, ', ');
        }
        
        return $meta;
    }

    private function generateAnnouncementsMeta(array $meta, array $data): array
    {
        $title = "Announcements - " . Settings::get('site.name');
        
        $meta['title'] = $title;
        $meta['description'] = "Stay updated with the latest announcements, updates, and news from our API platform";
        $meta['keywords'] = 'announcements, updates, news, changelog, ' . Settings::get('seo.default_keywords');
        $meta['canonical_url'] = Settings::get('site.url') . '/announcements';
        $meta['og_url'] = $meta['canonical_url'];
        $meta['og_title'] = $title;
        $meta['twitter_title'] = $title;
        
        return $meta;
    }

    private function generateAnnouncementDetailMeta(array $meta, array $data): array
    {
        $announcement = $data['announcement'] ?? [];
        if (!$announcement) {
            return $meta;
        }

        $title = $announcement['title'] . ' - Announcement';
        $description = $announcement['summary'] ?? strip_tags(substr($announcement['content'] ?? '', 0, 160));
        
        $meta['title'] = $title;
        $meta['description'] = $description;
        $meta['keywords'] = trim("{$announcement['title']}, announcement, update, " . Settings::get('seo.default_keywords'));
        $meta['canonical_url'] = Settings::get('site.url') . '/announcement/' . $announcement['slug'];
        $meta['og_url'] = $meta['canonical_url'];
        $meta['og_title'] = $title;
        $meta['og_description'] = $description;
        $meta['og_type'] = 'article';
        $meta['twitter_title'] = $title;
        $meta['twitter_description'] = $description;
        
        return $meta;
    }

    private function generateFeedbackMeta(array $meta, array $data): array
    {
        $title = "Feedback - " . Settings::get('site.name');
        
        $meta['title'] = $title;
        $meta['description'] = "Share your feedback, suggestions, and help us improve our API platform";
        $meta['keywords'] = 'feedback, suggestions, contact, support, ' . Settings::get('seo.default_keywords');
        $meta['canonical_url'] = Settings::get('site.url') . '/feedback';
        $meta['og_url'] = $meta['canonical_url'];
        $meta['og_title'] = $title;
        $meta['twitter_title'] = $title;
        
        return $meta;
    }

    public function generateJsonLd(array $meta, array $data = []): array
    {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => Settings::get('site.name'),
            'description' => $meta['description'],
            'url' => Settings::get('site.url'),
            'author' => [
                '@type' => 'Organization',
                'name' => Settings::get('site.name'),
            ],
        ];

        // Add specific structured data based on page type
        if (isset($data['api'])) {
            $jsonLd = array_merge($jsonLd, $this->generateApiJsonLd($data['api']));
        } elseif (isset($data['announcement'])) {
            $jsonLd = array_merge($jsonLd, $this->generateArticleJsonLd($data['announcement']));
        }

        return $jsonLd;
    }

    private function generateApiJsonLd(array $api): array
    {
        return [
            '@type' => 'SoftwareApplication',
            'name' => $api['name'],
            'description' => $api['description'] ?? '',
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem' => 'Any',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
        ];
    }

    private function generateArticleJsonLd(array $article): array
    {
        return [
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['summary'] ?? '',
            'datePublished' => $article['created_at'] ?? '',
            'dateModified' => $article['updated_at'] ?? $article['created_at'] ?? '',
            'author' => [
                '@type' => 'Organization',
                'name' => Settings::get('site.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => Settings::get('site.name'),
            ],
        ];
    }
}