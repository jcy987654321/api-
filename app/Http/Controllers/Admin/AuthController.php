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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $request->session()->regenerate();
        $request->session()->put('admin.authenticated', true);
        $request->session()->put('admin.email', $request->string('email')->toString());

        \Illuminate\Support\Facades\Log::channel('security')->info('Admin login', [
            'email' => $request->get('email'),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Log::channel('security')->info('Admin logout', [
            'email' => $request->session()->get('admin.email'),
            'ip' => $request->ip(),
        ]);

        $request->session()->forget(['admin.authenticated', 'admin.email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}