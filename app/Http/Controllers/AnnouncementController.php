<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::active()
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(10);
        
        return view('announcements.index', compact('announcements'));
    }

    public function getActiveModal(Request $request)
    {
        $announcement = Announcement::active()
            ->modal()
            ->orderByDesc('priority')
            ->first();
        
        if (!$announcement) {
            return response()->json(['announcement' => null]);
        }
        
        return response()->json([
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'type' => $announcement->type,
            ]
        ]);
    }
}
