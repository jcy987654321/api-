<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $tags = Tag::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tags.index', compact('tags', 'search'));
    }

    public function create()
    {
        $tag = new Tag();

        return view('admin.tags.form', [
            'tag' => $tag,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blog_tags', 'slug')],
        ]);

        Tag::query()->create($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    public function edit(int $id)
    {
        $tag = Tag::query()->findOrFail($id);

        return view('admin.tags.form', [
            'tag' => $tag,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $id)
    {
        $tag = Tag::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blog_tags', 'slug')->ignore($tag->id)],
        ]);

        $tag->update($validated);

        return redirect()->route('admin.tags.edit', $tag->id)
            ->with('success', 'Tag updated successfully.');
    }

    public function destroy(int $id)
    {
        $tag = Tag::query()->findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}
