@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #1f2937; margin-bottom: 0.5rem;">Admin Dashboard</h2>
        <p style="color: #6b7280;">Welcome to the management system dashboard</p>
    </div>

    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    ">
        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        ">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</p>
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">{{ $stats['users'] }}</p>
            <p style="color: #10b981; font-size: 0.75rem; margin-top: 0.5rem;">
                +{{ $stats['recent_users'] }} this week
            </p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #ef4444;
        ">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Admins</p>
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">{{ $stats['admins'] }}</p>
            <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.5rem;">
                {{ $stats['managers'] }} managers, {{ $stats['regular_users'] }} users
            </p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #10b981;
        ">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Users</p>
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">{{ $stats['users'] - $stats['recent_users'] }}</p>
            <p style="color: #6b7280; font-size: 0.75rem; margin-top: 0.5rem;">
                Registered users
            </p>
        </div>
    </div>

    @if (!empty($stats['user_growth']))
    <div style="margin-top: 2rem;">
        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #1f2937; margin-bottom: 1rem;">User Growth (Last 30 Days)</h3>
            <div style="display: flex; align-items: flex-end; gap: 0.5rem; height: 150px;">
                @foreach ($stats['user_growth'] as $growth)
                    <div style="
                        flex: 1;
                        background-color: #3b82f6;
                        border-radius: 0.25rem 0.25rem 0 0;
                        height: {{ max(10, ($growth['count'] / max(1, max(array_column($stats['user_growth'], 'count')))) * 140) }}px;
                        position: relative;
                    " title="{{ $growth['date'] }}: {{ $growth['count'] }} users">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #1f2937; margin-bottom: 1rem;">Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.users') }}" data-pjax style="
                    display: block;
                    padding: 0.75rem 1rem;
                    background-color: #f3f4f6;
                    color: #1f2937;
                    text-decoration: none;
                    border-radius: 0.375rem;
                    text-align: center;
                    font-weight: 500;
                ">Manage Users</a>
                <a href="{{ route('admin.stats') }}" data-pjax style="
                    display: block;
                    padding: 0.75rem 1rem;
                    background-color: #f3f4f6;
                    color: #1f2937;
                    text-decoration: none;
                    border-radius: 0.375rem;
                    text-align: center;
                    font-weight: 500;
                ">View Statistics</a>
            </div>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        ">
            <h3 style="color: #1f2937; margin-bottom: 1rem;">System Info</h3>
            <div style="font-size: 0.875rem; color: #6b7280;">
                <p style="margin-bottom: 0.5rem;">Laravel Version: {{ app()->version() }}</p>
                <p style="margin-bottom: 0.5rem;">PHP Version: {{ PHP_VERSION }}</p>
                <p style="margin-bottom: 0.5rem;">Environment: {{ config('app.env') }}</p>
                <p>Time: {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('home') }}" data-pjax style="
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #e5e7eb;
            color: #1f2937;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        ">
            ← Back to Home
        </a>
    </div>
</div>
@endsection
