<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->keyBy('key');
        return view('admin.site-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_keywords' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'donation_link' => 'nullable|url',
            'group_link' => 'nullable|url',
            'favicon' => 'nullable|image|max:2048',
            'logo' => 'nullable|image|max:2048',
        ]);

        $changes = [];

        foreach ($validated as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('public/settings');
                $value = Storage::url($path);
            }

            if ($value !== null) {
                $oldValue = SiteSetting::get($key);
                SiteSetting::set($key, $value, in_array($key, ['contact_email', 'contact_phone']));
                
                if ($oldValue !== $value) {
                    $changes[$key] = ['old' => $oldValue, 'new' => $value];
                }
            }
        }

        if (!empty($changes)) {
            AuditLog::log(
                'site_settings',
                'update',
                'Updated site settings',
                null,
                null,
                null,
                $changes
            );
        }

        return back()->with('success', 'Site settings updated successfully.');
    }
}
