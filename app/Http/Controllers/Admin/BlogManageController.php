<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogManageController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $query = Blog::query()
            ->with(['category', 'tags'])
            ->latest();

        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->paginate(15)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.blogs.index', compact('blogs', 'categories', 'search', 'status', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();

        return view('admin.blogs.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blogs', 'slug')],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blog_tags,id'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);

        $blog = Blog::query()->create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'cover_image' => $validated['cover_image'] ?? null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'] ?? 'draft',
            'views' => 0,
        ]);

        $blog->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('admin.blogs.edit', $blog->id)
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(int $id)
    {
        $blog = Blog::query()->with('tags')->findOrFail($id);
        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();

        return view('admin.blogs.edit', compact('blog', 'categories', 'tags'));
    }

    public function update(Request $request, int $id)
    {
        $blog = Blog::query()->with('tags')->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blogs', 'slug')->ignore($blog->id)],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blog_tags,id'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);

        $blog->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'cover_image' => $validated['cover_image'] ?? null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'] ?? $blog->status,
        ]);

        $blog->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('admin.blogs.edit', $blog->id)
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(int $id)
    {
        $blog = Blog::query()->findOrFail($id);
        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    public function publish(int $id)
    {
        $blog = Blog::query()->findOrFail($id);
        $blog->update(['status' => 'published']);

        return redirect()->back()->with('success', 'Blog post published successfully.');
    }

    public function changeDraft(int $id)
    {
        $blog = Blog::query()->findOrFail($id);
        $blog->update(['status' => 'draft']);

        return redirect()->back()->with('success', 'Blog post saved as draft.');
    }
}
