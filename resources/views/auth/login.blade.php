@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="max-width: 400px; margin: 0 auto;">
        <div style="
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h2 style="text-align: center; margin-bottom: 1.5rem; color: #1f2937;">Login</h2>
            
            @if (session('error'))
                <div style="
                    background-color: #fef2f2;
                    border: 1px solid #fecaca;
                    color: #dc2626;
                    padding: 0.75rem;
                    border-radius: 0.375rem;
                    margin-bottom: 1rem;
                ">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div style="margin-bottom: 1rem;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        style="
                            width: 100%;
                            padding: 0.75rem;
                            border: 1px solid #d1d5db;
                            border-radius: 0.375rem;
                            font-size: 1rem;
                        "
                    >
                    @error('email')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required
                        style="
                            width: 100%;
                            padding: 0.75rem;
                            border: 1px solid #d1d5db;
                            border-radius: 0.375rem;
                            font-size: 1rem;
                        "
                    >
                    @error('password')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="remember" style="margin-right: 0.5rem;">
                        <span style="font-size: 0.875rem;">Remember me</span>
                    </label>
                </div>

                <button type="submit" style="
                    width: 100%;
                    padding: 0.75rem;
                    background-color: #3b82f6;
                    color: white;
                    border: none;
                    border-radius: 0.375rem;
                    font-size: 1rem;
                    font-weight: 500;
                    cursor: pointer;
                ">
                    Login
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #6b7280; font-size: 0.875rem;">
                    Don't have an account? 
                    <a href="{{ route('register') }}" style="color: #3b82f6;">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection