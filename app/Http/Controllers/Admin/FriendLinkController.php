<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FriendLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FriendLinkController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = FriendLink::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $friendLinks = $query->orderBy('sort_order')->orderByDesc('created_at')->paginate(20);
        
        return view('admin.friend-links.index', compact('friendLinks', 'status'));
    }

    public function create()
    {
        return view('admin.friend-links.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:pending,approved,rejected,inactive',
            'admin_notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/friend-links');
            $validated['logo'] = Storage::url($path);
        }

        $friendLink = FriendLink::create($validated);

        AuditLog::log(
            'friend_links',
            'create',
            "Created friend link: {$friendLink->name}",
            'FriendLink',
            $friendLink->id,
            null,
            $validated
        );

        return redirect()->route('admin.friend-links.index')
            ->with('success', 'Friend link created successfully.');
    }

    public function edit(FriendLink $friendLink)
    {
        return view('admin.friend-links.edit', compact('friendLink'));
    }

    public function update(Request $request, FriendLink $friendLink)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:pending,approved,rejected,inactive',
            'admin_notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($friendLink->logo) {
                Storage::delete(str_replace('/storage/', 'public/', $friendLink->logo));
            }
            $path = $request->file('logo')->store('public/friend-links');
            $validated['logo'] = Storage::url($path);
        }

        $oldValues = $friendLink->toArray();
        $friendLink->update($validated);

        AuditLog::log(
            'friend_links',
            'update',
            "Updated friend link: {$friendLink->name}",
            'FriendLink',
            $friendLink->id,
            $oldValues,
            $validated
        );

        return redirect()->route('admin.friend-links.index')
            ->with('success', 'Friend link updated successfully.');
    }

    public function destroy(FriendLink $friendLink)
    {
        $name = $friendLink->name;

        if ($friendLink->logo) {
            Storage::delete(str_replace('/storage/', 'public/', $friendLink->logo));
        }

        $friendLink->delete();

        AuditLog::log(
            'friend_links',
            'delete',
            "Deleted friend link: {$name}",
            'FriendLink',
            $friendLink->id
        );

        return redirect()->route('admin.friend-links.index')
            ->with('success', 'Friend link deleted successfully.');
    }

    public function approve(FriendLink $friendLink)
    {
        $friendLink->approve(auth()->user());

        AuditLog::log(
            'friend_links',
            'approve',
            "Approved friend link: {$friendLink->name}",
            'FriendLink',
            $friendLink->id
        );

        if ($friendLink->email) {
            $this->sendStatusEmail($friendLink, 'approved');
        }

        return back()->with('success', 'Friend link approved successfully.');
    }

    public function reject(Request $request, FriendLink $friendLink)
    {
        $reason = $request->input('reason');
        $friendLink->reject($reason);

        AuditLog::log(
            'friend_links',
            'reject',
            "Rejected friend link: {$friendLink->name}",
            'FriendLink',
            $friendLink->id
        );

        if ($friendLink->email) {
            $this->sendStatusEmail($friendLink, 'rejected', $reason);
        }

        return back()->with('success', 'Friend link rejected.');
    }

    private function sendStatusEmail(FriendLink $friendLink, string $status, ?string $reason = null)
    {
        try {
            Mail::send('emails.friend-link-status', [
                'friendLink' => $friendLink,
                'status' => $status,
                'reason' => $reason,
            ], function ($message) use ($friendLink, $status) {
                $message->to($friendLink->email)
                    ->subject("Friend Link Application {$status}");
            });
        } catch (\Exception $e) {
        }
    }
}
