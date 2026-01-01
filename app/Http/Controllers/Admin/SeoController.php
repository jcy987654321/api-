<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoConfig;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeoController extends Controller
{
    public function index(SeoService $seoService)
    {
        $seoService->ensureDefaults();

        $configs = SeoConfig::query()->orderBy('page')->paginate(20);

        return view('admin.seo.index', [
            'configs' => $configs,
            'defaultPages' => SeoService::DEFAULT_PAGES,
        ]);
    }

    public function create()
    {
        return view('admin.seo.form', [
            'config' => new SeoConfig(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, SeoService $seoService)
    {
        $validated = $request->validate([
            'page' => ['required', 'string', 'max:255', Rule::unique('seo_configs', 'page')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'url'],
        ]);

        SeoConfig::query()->create($validated);

        $seoService->clearCache();

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO 配置已创建。');
    }

    public function edit(string $page)
    {
        $config = SeoConfig::query()->where('page', $page)->firstOrFail();

        return view('admin.seo.form', [
            'config' => $config,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, string $page, SeoService $seoService)
    {
        $config = SeoConfig::query()->where('page', $page)->firstOrFail();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'url'],
        ]);

        $config->update($validated);
        $seoService->clearCache();

        return redirect()->route('admin.seo.edit', $config->page)
            ->with('success', 'SEO 配置已更新。');
    }

    public function destroy(string $page, SeoService $seoService)
    {
        $config = SeoConfig::query()->where('page', $page)->firstOrFail();
        $config->delete();

        $seoService->clearCache();

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO 配置已删除。');
    }

    public function preview(string $page, SeoService $seoService)
    {
        $config = SeoConfig::query()->where('page', $page)->firstOrFail();

        $metaHtml = $seoService->renderMeta($page);

        return view('admin.seo.preview', [
            'config' => $config,
            'metaHtml' => $metaHtml,
        ]);
    }
}
