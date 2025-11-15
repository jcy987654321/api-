@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="max-width: 400px; margin: 0 auto;">
        <div style="
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h2 style="text-align: center; margin-bottom: 1.5rem; color: #1f2937;">Register</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div style="margin-bottom: 1rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name</label>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
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
                    @error('name')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
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
                    <label for="password_confirmation" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm Password</label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required
                        style="
                            width: 100%;
                            padding: 0.75rem;
                            border: 1px solid #d1d5db;
                            border-radius: 0.375rem;
                            font-size: 1rem;
                        "
                    >
                    @error('password_confirmation')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">
                            {{ $message }}
                        </div>
                    @enderror
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
                    Register
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #6b7280; font-size: 0.875rem;">
                    Already have an account? 
                    <a href="{{ route('login') }}" style="color: #3b82f6;">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection