@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="text-align: center; padding: 2rem 0;">
        <h1 style="color: #3b82f6; margin-bottom: 1rem;">Welcome to {{ config('app.name') }}</h1>
        <p style="font-size: 1.125rem; color: #6b7280; margin-bottom: 2rem;">
            {{ config('app.name') }} - 强大的管理系统
        </p>
        <p style="color: #6b7280; margin-bottom: 2rem;">
            Your powerful management system is ready to go!
        </p>
        
        @guest
        <div style="margin-top: 2rem;">
            <a href="{{ route('login') }}" style="
                display: inline-block;
                padding: 0.75rem 1.5rem;
                background-color: #3b82f6;
                color: white;
                border-radius: 0.375rem;
                margin: 0 0.5rem;
                text-decoration: none;
            ">
                Login
            </a>
            <a href="{{ route('register') }}" style="
                display: inline-block;
                padding: 0.75rem 1.5rem;
                background-color: #10b981;
                color: white;
                border-radius: 0.375rem;
                margin: 0 0.5rem;
                text-decoration: none;
            ">
                Register
            </a>
        </div>
        @else
        <div style="margin-top: 2rem;">
            @if(auth()->user()->canAccessAdmin())
                <a href="{{ route('admin.dashboard') }}" data-pjax style="
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    background-color: #3b82f6;
                    color: white;
                    border-radius: 0.375rem;
                    margin: 0 0.5rem;
                    text-decoration: none;
                ">
                    Go to Dashboard
                </a>
            @endif
        </div>
        @endguest
    </div>

    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 3rem;
    ">
        <div class="stat-card">
            <h3 style="color: #3b82f6; margin-bottom: 0.5rem;">User Management</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Complete authentication system with role-based access control
            </p>
        </div>

        <div class="stat-card success">
            <h3 style="color: #10b981; margin-bottom: 0.5rem;">RESTful API</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Comprehensive API endpoints with token authentication
            </p>
        </div>

        <div class="stat-card warning">
            <h3 style="color: #f59e0b; margin-bottom: 0.5rem;">Modern UI</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Responsive design with PJAX for fast navigation
            </p>
        </div>

        <div class="stat-card">
            <h3 style="color: #3b82f6; margin-bottom: 0.5rem;">Analytics</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Real-time statistics and user growth tracking
            </p>
        </div>

        <div class="stat-card success">
            <h3 style="color: #10b981; margin-bottom: 0.5rem;">Security</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Built-in CSRF protection, rate limiting, and input validation
            </p>
        </div>

        <div class="stat-card warning">
            <h3 style="color: #f59e0b; margin-bottom: 0.5rem;">Scalable</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Built on Laravel 10 with modern PHP 8.0+ features
            </p>
        </div>
    </div>

    @guest
    <div style="margin-top: 3rem; text-align: center;">
        <div style="
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        ">
            <h3 style="color: #1f2937; margin-bottom: 1rem;">Get Started</h3>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">
                Register for an account to access the admin dashboard and manage users. 
                Default admin credentials are available for testing.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('login') }}" style="
                    padding: 0.75rem 1.5rem;
                    background-color: #3b82f6;
                    color: white;
                    border-radius: 0.375rem;
                    text-decoration: none;
                    font-weight: 500;
                ">Login</a>
                <a href="{{ route('register') }}" style="
                    padding: 0.75rem 1.5rem;
                    background-color: #10b981;
                    color: white;
                    border-radius: 0.375rem;
                    text-decoration: none;
                    font-weight: 500;
                ">Register</a>
            </div>
        </div>
    </div>
    @endguest
</div>
@endsection
