<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'display_type' => 'required|in:modal,banner,inline',
            'status' => 'required|in:draft,published,archived',
            'is_active' => 'boolean',
            'show_modal' => 'boolean',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:scheduled_at',
            'priority' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['show_modal'] = $request->has('show_modal');

        $announcement = Announcement::create($validated);

        AuditLog::log(
            'announcements',
            'create',
            "Created announcement: {$announcement->title}",
            'Announcement',
            $announcement->id,
            null,
            $validated
        );

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'display_type' => 'required|in:modal,banner,inline',
            'status' => 'required|in:draft,published,archived',
            'is_active' => 'boolean',
            'show_modal' => 'boolean',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:scheduled_at',
            'priority' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['show_modal'] = $request->has('show_modal');

        $oldValues = $announcement->toArray();
        $announcement->update($validated);

        AuditLog::log(
            'announcements',
            'update',
            "Updated announcement: {$announcement->title}",
            'Announcement',
            $announcement->id,
            $oldValues,
            $validated
        );

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        AuditLog::log(
            'announcements',
            'delete',
            "Deleted announcement: {$title}",
            'Announcement',
            $announcement->id
        );

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        AuditLog::log(
            'announcements',
            'toggle_status',
            "Toggled announcement status: {$announcement->title}",
            'Announcement',
            $announcement->id
        );

        return back()->with('success', 'Announcement status updated.');
    }
}
