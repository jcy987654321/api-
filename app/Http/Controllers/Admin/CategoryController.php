<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        $category = new Category();

        return view('admin.categories.form', [
            'category' => $category,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blog_categories', 'slug')],
            'description' => ['nullable', 'string'],
        ]);

        Category::query()->create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id)
    {
        $category = Category::query()->findOrFail($id);

        return view('admin.categories.form', [
            'category' => $category,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $id)
    {
        $category = Category::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', Rule::unique('blog_categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.edit', $category->id)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(int $id)
    {
        $category = Category::query()->findOrFail($id);

        try {
            $category->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Unable to delete category. Please remove related blog posts first.');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
