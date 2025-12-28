<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Login logic will be implemented here
        \Illuminate\Support\Facades\Log::channel('security')->info('Admin login attempt', [
            'email' => $request->get('email'),
            'ip' => $request->ip()
        ]);
        
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        // Logout logic will be implemented here
        \Illuminate\Support\Facades\Log::channel('security')->info('Admin logout', [
            'user_id' => $request->user()?->id,
            'ip' => $request->ip()
        ]);
        
        return redirect()->route('admin.login');
    }
}