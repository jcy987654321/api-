<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DonationOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function index()
    {
        $donations = DonationOption::orderBy('sort_order')->get();
        return view('admin.donations.index', compact('donations'));
    }

    public function create()
    {
        return view('admin.donations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'payment_method' => 'required|string|max:255',
            'payment_link' => 'nullable|url',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'qr_code_image' => 'nullable|image|max:2048',
            'icon_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('qr_code_image')) {
            $path = $request->file('qr_code_image')->store('public/donations/qr');
            $validated['qr_code_image'] = Storage::url($path);
        }

        if ($request->hasFile('icon_image')) {
            $path = $request->file('icon_image')->store('public/donations/icons');
            $validated['icon_image'] = Storage::url($path);
        }

        $donation = DonationOption::create($validated);

        AuditLog::log(
            'donations',
            'create',
            "Created donation option: {$donation->name}",
            'DonationOption',
            $donation->id,
            null,
            $validated
        );

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation option created successfully.');
    }

    public function edit(DonationOption $donation)
    {
        return view('admin.donations.edit', compact('donation'));
    }

    public function update(Request $request, DonationOption $donation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'payment_method' => 'required|string|max:255',
            'payment_link' => 'nullable|url',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'qr_code_image' => 'nullable|image|max:2048',
            'icon_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('qr_code_image')) {
            if ($donation->qr_code_image) {
                Storage::delete(str_replace('/storage/', 'public/', $donation->qr_code_image));
            }
            $path = $request->file('qr_code_image')->store('public/donations/qr');
            $validated['qr_code_image'] = Storage::url($path);
        }

        if ($request->hasFile('icon_image')) {
            if ($donation->icon_image) {
                Storage::delete(str_replace('/storage/', 'public/', $donation->icon_image));
            }
            $path = $request->file('icon_image')->store('public/donations/icons');
            $validated['icon_image'] = Storage::url($path);
        }

        $oldValues = $donation->toArray();
        $donation->update($validated);

        AuditLog::log(
            'donations',
            'update',
            "Updated donation option: {$donation->name}",
            'DonationOption',
            $donation->id,
            $oldValues,
            $validated
        );

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation option updated successfully.');
    }

    public function destroy(DonationOption $donation)
    {
        $name = $donation->name;

        if ($donation->qr_code_image) {
            Storage::delete(str_replace('/storage/', 'public/', $donation->qr_code_image));
        }
        if ($donation->icon_image) {
            Storage::delete(str_replace('/storage/', 'public/', $donation->icon_image));
        }

        $donation->delete();

        AuditLog::log(
            'donations',
            'delete',
            "Deleted donation option: {$name}",
            'DonationOption',
            $donation->id
        );

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation option deleted successfully.');
    }
}
