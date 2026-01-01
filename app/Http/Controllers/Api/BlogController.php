<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('category', ''));
        $tagSlug = trim((string) $request->query('tag', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 50 ? $perPage : 10;

        $query = Blog::query()
            ->with(['category', 'tags'])
            ->published()
            ->latest();

        if ($categorySlug !== '') {
            $category = Category::query()->where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($tagSlug !== '') {
            $tag = Tag::query()->where('slug', $tagSlug)->first();
            if ($tag) {
                $query->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id));
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }

    public function show(int $id)
    {
        $blog = Blog::query()
            ->with(['category', 'tags'])
            ->published()
            ->findOrFail($id);

        Blog::withoutTimestamps(function () use ($blog) {
            $blog->increment('views');
        });

        return response()->json([
            'success' => true,
            'data' => $blog,
        ]);
    }
}
