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
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">0</p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #10b981;
        ">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Orders</p>
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">0</p>
        </div>

        <div style="
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #f59e0b;
        ">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Revenue</p>
            <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">$0.00</p>
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
