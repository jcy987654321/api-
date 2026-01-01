<?php

namespace App\Http\Controllers\Front;

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

        $query = Blog::query()
            ->with(['category', 'tags'])
            ->published()
            ->latest();

        $selectedCategory = null;
        if ($categorySlug !== '') {
            $selectedCategory = Category::query()->where('slug', $categorySlug)->firstOrFail();
            $query->where('category_id', $selectedCategory->id);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->paginate(10)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('pages.blog.index', compact('blogs', 'categories', 'search', 'selectedCategory'));
    }

    public function show(string $slug)
    {
        $blog = Blog::query()
            ->with(['category', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        Blog::withoutTimestamps(function () use ($blog) {
            $blog->increment('views');
        });

        $relatedBlogs = Blog::query()
            ->with('category')
            ->published()
            ->where('category_id', $blog->category_id)
            ->whereKeyNot($blog->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('pages.blog.show', compact('blog', 'relatedBlogs'));
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $search = trim((string) $request->query('q', ''));

        $query = $category->blogs()
            ->with(['category', 'tags'])
            ->published()
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->paginate(10)->withQueryString();

        return view('pages.blog.category', compact('category', 'blogs', 'search'));
    }

    public function tag(Request $request, string $slug)
    {
        $tag = Tag::query()->where('slug', $slug)->firstOrFail();
        $search = trim((string) $request->query('q', ''));

        $query = $tag->blogs()
            ->with(['category', 'tags'])
            ->published()
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->paginate(10)->withQueryString();

        return view('pages.blog.tag', compact('tag', 'blogs', 'search'));
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $blogs = Blog::query()
            ->with(['category', 'tags'])
            ->published()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.blog.search', compact('blogs', 'search'));
    }
}
