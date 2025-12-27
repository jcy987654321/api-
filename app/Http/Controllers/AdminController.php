<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Api;
use App\Models\Blog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_apis' => Api::count(),
            'total_blogs' => Blog::count(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return response()->json($stats);
    }

    public function users()
    {
        $users = User::paginate(15);
        return response()->json($users);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
