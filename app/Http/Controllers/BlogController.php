<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('author')->paginate(15);
        return response()->json($blogs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,published,archived',
        ]);

        $blog = Blog::create(array_merge($validated, [
            'author_id' => $request->user()->id,
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]));

        return response()->json($blog, 201);
    }

    public function show(Blog $blog)
    {
        return response()->json($blog->load('author'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'status' => 'string|in:draft,published,archived',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'published' && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return response()->json($blog);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
