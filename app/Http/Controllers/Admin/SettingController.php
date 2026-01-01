<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(SettingService $settingService)
    {
        $settingService->ensureDefaults();

        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function edit(string $key)
    {
        $setting = Setting::query()->where('key', $key)->firstOrFail();

        return view('admin.settings.edit', [
            'setting' => $setting,
        ]);
    }

    public function update(Request $request, string $key, SettingService $settingService)
    {
        $setting = Setting::query()->where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => ['required'],
        ]);

        $settingService->set($setting->key, $validated['value']);

        return redirect()->route('admin.settings.edit', $setting->key)
            ->with('success', '设置已更新。');
    }

    public function group(string $group, SettingService $settingService)
    {
        $settingService->ensureDefaults();

        $settings = Setting::query()
            ->where('group', $group)
            ->orderBy('key')
            ->get();

        return view('admin.settings.group', [
            'group' => $group,
            'settings' => $settings,
        ]);
    }

    public function updateGroup(Request $request, string $group, SettingService $settingService)
    {
        $settings = Setting::query()->where('group', $group)->get();

        $rules = [];
        foreach ($settings as $setting) {
            $rules['values.' . $setting->key] = ['nullable'];
        }

        $validated = $request->validate($rules);

        $values = $validated['values'] ?? [];

        $settingService->setGroup($group, $values);

        return redirect()->route('admin.settings.group', $group)
            ->with('success', '分组设置已更新。');
    }
}
