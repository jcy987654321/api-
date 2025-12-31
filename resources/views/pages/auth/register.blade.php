@extends('layouts.app')

@section('title', 'Register - Powerful Management System')

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
    .form-group input {
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
    <h1 class="auth-title">Create Your Account</h1>
    
    <form action="{{ route('register.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Full Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required
                placeholder="Enter your full name"
            >
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required
                placeholder="Enter your email"
            >
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Phone Number (Optional)</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}"
                placeholder="Enter your phone number"
            >
            @error('phone')
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
                placeholder="At least 8 characters"
            >
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required
                placeholder="Re-enter your password"
            >
        </div>

        <button type="submit" class="btn-submit">Register</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Login here</a>
    </div>
</div>
@endsection
