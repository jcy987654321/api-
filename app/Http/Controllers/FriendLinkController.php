<?php

namespace App\Http\Controllers;

use App\Models\FriendLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FriendLinkController extends Controller
{
    public function index()
    {
        $friendLinks = FriendLink::approved()->get();
        return view('friend-links.index', compact('friendLinks'));
    }

    public function apply()
    {
        return view('friend-links.apply');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string|max:500',
            'email' => 'required|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        $validated['status'] = 'pending';

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/friend-links');
            $validated['logo'] = Storage::url($path);
        }

        FriendLink::create($validated);

        return redirect()->route('friend-links.index')
            ->with('success', 'Your friend link application has been submitted for review.');
    }
}
