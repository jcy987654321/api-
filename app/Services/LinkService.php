<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkService extends BaseService
{
    public const CACHE_KEY_APPROVED = 'cms:links:approved';

    public function getApproved(): array
    {
        return Cache::rememberForever(self::CACHE_KEY_APPROVED, function () {
            $links = Link::query()
                ->approved()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            return $links
                ->groupBy('category')
                ->map(fn ($items) => $items->values())
                ->toArray();
        });
    }

    public function getByCategory(string $category)
    {
        $category = trim($category);

        $all = $this->getApproved();

        return $all[$category] ?? [];
    }

    public function recordClick(int $linkId): void
    {
        Link::withoutTimestamps(function () use ($linkId) {
            Link::query()->approved()->whereKey($linkId)->increment('clicks');
        });

        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_APPROVED);
    }
}
