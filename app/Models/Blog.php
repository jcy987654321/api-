<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'cover_image',
        'category_id',
        'status',
        'views',
    ];

    protected $casts = [
        'views' => 'integer',
        'category_id' => 'integer',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    public static array $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blog_id', 'tag_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeWithCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeWithTag($query, $tagSlug)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function relatedPosts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return self::published()
            ->where('id', '!=', $this->id)
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->with('category', 'tags')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }
}
