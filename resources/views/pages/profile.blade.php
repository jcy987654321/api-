@extends('layouts.app')

@section('title', 'My Profile - Powerful Management System')

@section('content')
<style>
    .profile-container {
        max-width: 800px;
        margin: 40px auto;
    }
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 8px 8px 0 0;
        text-align: center;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        color: #667eea;
    }
    .profile-name {
        font-size: 28px;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .profile-email {
        font-size: 16px;
        opacity: 0.9;
    }
    .profile-body {
        background: white;
        padding: 30px;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .profile-section {
        margin-bottom: 30px;
    }
    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        width: 180px;
        font-weight: 500;
        color: #666;
    }
    .info-value {
        flex: 1;
        color: #333;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }
    .badge-active {
        background: #d4edda;
        color: #155724;
    }
    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    .badge-suspended {
        background: #fff3cd;
        color: #856404;
    }
    .badge-user {
        background: #d1ecf1;
        color: #0c5460;
    }
    .badge-admin {
        background: #f8d7da;
        color: #721c24;
    }
    .profile-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .btn-primary {
        background: #4CAF50;
        color: white;
    }
    .btn-primary:hover {
        background: #45a049;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    .empty-value {
        color: #999;
        font-style: italic;
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-email">{{ $user->email }}</div>
    </div>

    <div class="profile-body">
        <div class="profile-section">
            <h2 class="section-title">Account Information</h2>
            
            <div class="info-row">
                <div class="info-label">User ID:</div>
                <div class="info-value">#{{ $user->id }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Email Address:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Phone Number:</div>
                <div class="info-value">
                    @if($user->phone)
                        {{ $user->phone }}
                    @else
                        <span class="empty-value">Not provided</span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Account Status:</div>
                <div class="info-value">
                    <span class="badge badge-{{ $user->status }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">
                    <span class="badge badge-{{ $user->role }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Email Verified:</div>
                <div class="info-value">
                    @if($user->email_verified_at)
                        <span style="color: #4CAF50;">✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                    @else
                        <span style="color: #f44336;">✗ Not verified</span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Member Since:</div>
                <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Last Updated:</div>
                <div class="info-value">{{ $user->updated_at->diffForHumans() }}</div>
            </div>
        </div>

        <div class="profile-actions">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <a href="/apis" class="btn btn-secondary">Browse APIs</a>
        </div>
    </div>
</div>
@endsection
