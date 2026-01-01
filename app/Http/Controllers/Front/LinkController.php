<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Services\LinkService;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(LinkService $linkService)
    {
        $groupedLinks = $linkService->getApproved();
        $categories = collect(array_keys($groupedLinks))->sort()->values();

        return view('pages.links.index', [
            'groupedLinks' => $groupedLinks,
            'categories' => $categories,
            'currentCategory' => null,
            'seoPage' => 'links',
        ]);
    }

    public function getByCategory(string $category, LinkService $linkService)
    {
        $groupedLinks = $linkService->getApproved();
        $categories = collect(array_keys($groupedLinks))->sort()->values();

        return view('pages.links.index', [
            'groupedLinks' => [$category => $linkService->getByCategory($category)],
            'categories' => $categories,
            'currentCategory' => $category,
            'seoPage' => 'links',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'description' => ['nullable', 'string', 'max:500'],
            'logo_url' => ['nullable', 'url'],
            'category' => ['required', 'string', 'max:50'],
        ]);

        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id();

        Link::query()->create($validated);

        return redirect()->route('links.index')
            ->with('success', '友链已提交，等待审核。');
    }

    public function click(int $id, LinkService $linkService)
    {
        $linkService->recordClick($id);

        return response()->json(['ok' => true]);
    }
}
