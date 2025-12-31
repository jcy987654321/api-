@extends('layouts.app')

@section('title', 'Login - Powerful Management System')

@section('content')
<style>
    .auth-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .auth-title {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .form-group input:focus {
        outline: none;
        border-color: #4CAF50;
    }
    .error-message {
        color: #f44336;
        font-size: 13px;
        margin-top: 5px;
    }
    .remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    .remember-me input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .remember-me label {
        margin: 0;
        color: #555;
        cursor: pointer;
    }
    .btn-submit {
        width: 100%;
        padding: 12px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-submit:hover {
        background: #45a049;
    }
    .auth-footer {
        text-align: center;
        margin-top: 20px;
        color: #666;
    }
    .auth-footer a {
        color: #4CAF50;
        text-decoration: none;
    }
    .auth-footer a:hover {
        text-decoration: underline;
    }
    .required {
        color: #f44336;
    }
</style>

<div class="auth-container">
    <h1 class="auth-title">Welcome Back</h1>
    
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required
                placeholder="Enter your email"
                autofocus
            >
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password <span class="required">*</span></label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                placeholder="Enter your password"
            >
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="remember-me">
            <input 
                type="checkbox" 
                id="remember" 
                name="remember"
            >
            <label for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn-submit">Login</button>
    </form>

    <div class="auth-footer">
        Don't have an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>
@endsection
