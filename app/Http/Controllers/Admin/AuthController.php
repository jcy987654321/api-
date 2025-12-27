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
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        // Logout logic will be implemented here
        return redirect()->route('admin.login');
    }
}