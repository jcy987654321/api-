<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSlot;
use App\Models\Advertisement;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::with(['adSlot', 'creator'])
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        $adSlots = AdSlot::where('is_active', true)->get();
        return view('admin.advertisements.create', compact('adSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_slot_id' => 'required|exists:ad_slots,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,html,script',
            'content' => 'required|string',
            'link_url' => 'nullable|url',
            'open_new_tab' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['open_new_tab'] = $request->has('open_new_tab');

        $advertisement = Advertisement::create($validated);

        AuditLog::log(
            'advertisements',
            'create',
            "Created advertisement: {$advertisement->title}",
            'Advertisement',
            $advertisement->id,
            null,
            $validated
        );

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement created successfully.');
    }

    public function edit(Advertisement $advertisement)
    {
        $adSlots = AdSlot::where('is_active', true)->get();
        return view('admin.advertisements.edit', compact('advertisement', 'adSlots'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'ad_slot_id' => 'required|exists:ad_slots,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,html,script',
            'content' => 'required|string',
            'link_url' => 'nullable|url',
            'open_new_tab' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['open_new_tab'] = $request->has('open_new_tab');

        $oldValues = $advertisement->toArray();
        $advertisement->update($validated);

        AuditLog::log(
            'advertisements',
            'update',
            "Updated advertisement: {$advertisement->title}",
            'Advertisement',
            $advertisement->id,
            $oldValues,
            $validated
        );

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement updated successfully.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $title = $advertisement->title;
        $advertisement->delete();

        AuditLog::log(
            'advertisements',
            'delete',
            "Deleted advertisement: {$title}",
            'Advertisement',
            $advertisement->id
        );

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }

    public function slots()
    {
        $adSlots = AdSlot::withCount('advertisements')->get();
        return view('admin.advertisements.slots', compact('adSlots'));
    }

    public function storeSlot(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:ad_slots',
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $adSlot = AdSlot::create($validated);

        AuditLog::log(
            'ad_slots',
            'create',
            "Created ad slot: {$adSlot->name}",
            'AdSlot',
            $adSlot->id,
            null,
            $validated
        );

        return back()->with('success', 'Ad slot created successfully.');
    }
}
