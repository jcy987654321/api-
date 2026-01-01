<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LinkManageController extends Controller
{
    public function index(Request $request)
    {
        $status = trim((string) $request->query('status', 'pending'));
        $category = trim((string) $request->query('category', ''));

        $allowedStatuses = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $query = Link::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($category !== '', fn ($q) => $q->where('category', $category))
            ->orderBy('order')
            ->orderByDesc('id');

        $links = $query->paginate(20)->withQueryString();

        $categories = Link::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.links.index', compact('links', 'status', 'category', 'categories'));
    }

    public function create()
    {
        return view('admin.links.form', [
            'link' => new Link(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, LinkService $linkService)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string', 'max:500'],
            'logo_url' => ['nullable', 'url'],
            'category' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $maxOrder = (int) Link::query()->max('order');
        $validated['order'] = $maxOrder + 1;

        Link::query()->create($validated);

        $linkService->clearCache();

        return redirect()->route('admin.links.index')
            ->with('success', '友链已创建。');
    }

    public function show(int $id)
    {
        $link = Link::query()->findOrFail($id);

        return view('admin.links.form', [
            'link' => $link,
            'mode' => 'show',
        ]);
    }

    public function edit(int $id)
    {
        $link = Link::query()->findOrFail($id);

        return view('admin.links.form', [
            'link' => $link,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $id, LinkService $linkService)
    {
        $link = Link::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string', 'max:500'],
            'logo_url' => ['nullable', 'url'],
            'category' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $link->update($validated);
        $linkService->clearCache();

        return redirect()->route('admin.links.edit', $link->id)
            ->with('success', '友链已更新。');
    }

    public function approve(int $id, LinkService $linkService)
    {
        $link = Link::query()->findOrFail($id);
        $link->status = 'approved';
        $link->save();

        $linkService->clearCache();

        return redirect()->back()->with('success', '友链已批准。');
    }

    public function reject(int $id, LinkService $linkService)
    {
        $link = Link::query()->findOrFail($id);
        $link->status = 'rejected';
        $link->save();

        $linkService->clearCache();

        return redirect()->back()->with('success', '友链已拒绝。');
    }

    public function destroy(int $id, LinkService $linkService)
    {
        $link = Link::query()->findOrFail($id);
        $link->delete();

        $linkService->clearCache();

        return redirect()->route('admin.links.index')
            ->with('success', '友链已删除。');
    }

    public function updateOrder(Request $request, LinkService $linkService)
    {
        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'distinct'],
        ]);

        $ids = array_values($validated['ordered_ids']);

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Link::query()->whereKey((int) $id)->update(['order' => $index + 1]);
            }
        });

        $linkService->clearCache();

        return response()->json(['ok' => true]);
    }
}
