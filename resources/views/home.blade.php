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
        <div style="margin-top: 2rem;">
            <a href="{{ route('admin.dashboard') }}" data-pjax style="
                display: inline-block;
                padding: 0.75rem 1.5rem;
                background-color: #3b82f6;
                color: white;
                border-radius: 0.375rem;
                margin: 0 0.5rem;
            ">
                Go to Dashboard
            </a>
        </div>
    </div>

    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 3rem;
    ">
        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #3b82f6; margin-bottom: 0.5rem;">API Ready</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                RESTful API endpoints available at /api/v1
            </p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #3b82f6; margin-bottom: 0.5rem;">PJAX Ready</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Fast page transitions with PJAX enabled
            </p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #3b82f6; margin-bottom: 0.5rem;">Modern Stack</h3>
            <p style="color: #6b7280; font-size: 0.875rem;">
                Laravel 10+, PHP 8.0+, Vite build pipeline
            </p>
        </div>
    </div>
</div>
@endsection
