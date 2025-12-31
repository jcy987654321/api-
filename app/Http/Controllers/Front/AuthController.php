<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        if (session()->has('user_id')) {
            return redirect()->route('profile');
        }
        
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        if (session()->has('user_id')) {
            return redirect()->route('profile');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'role' => 'user',
            'is_active' => true,
        ]);

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        return redirect()->route('profile')
            ->with('success', 'Registration successful! Welcome to our platform.');
    }

    public function showLoginForm()
    {
        if (session()->has('user_id')) {
            return redirect()->route('profile');
        }
        
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        if (session()->has('user_id')) {
            return redirect()->route('profile');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'password.required' => 'Password is required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid email or password');
        }

        if ($user->status !== 'active' || !$user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Your account is not active. Please contact support.');
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        $remember = $request->has('remember');
        if ($remember) {
            session()->put('remember_user', true);
        }

        return redirect()->intended(route('profile'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'remember_user']);
        session()->flush();
        
        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully');
    }

    public function profile(Request $request)
    {
        $userId = session('user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'User not found');
        }

        return view('pages.profile', ['user' => $user]);
    }
}
