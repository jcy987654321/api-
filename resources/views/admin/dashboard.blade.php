@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1>Admin Dashboard</h1>
        <p class="text-muted">Manage your site content and settings</p>
    </div>

    <div class="row">
        @if(auth()->user()->hasPermission('site_settings.manage'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-gear"></i> Site Settings</h5>
                    <p class="card-text">Manage site branding, SEO, and contact information</p>
                    <a href="{{ route('admin.site-settings.index') }}" class="btn btn-primary" data-pjax>Manage Settings</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('announcements.manage'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-megaphone"></i> Announcements</h5>
                    <p class="card-text">Create and manage site announcements with scheduling</p>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-primary" data-pjax>Manage Announcements</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('donations.manage'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cash-coin"></i> Donations</h5>
                    <p class="card-text">Manage donation options and payment methods</p>
                    <a href="{{ route('admin.donations.index') }}" class="btn btn-primary" data-pjax>Manage Donations</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('advertisements.manage'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-badge-ad"></i> Advertisements</h5>
                    <p class="card-text">Create ad slots and manage advertisements</p>
                    <a href="{{ route('admin.advertisements.index') }}" class="btn btn-primary" data-pjax>Manage Ads</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('friend_links.manage'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-link-45deg"></i> Friend Links</h5>
                    <p class="card-text">Review and approve friend link applications</p>
                    <a href="{{ route('admin.friend-links.index') }}" class="btn btn-primary" data-pjax>Manage Links</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('audit_logs.view'))
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-journal-text"></i> Audit Logs</h5>
                    <p class="card-text">View system activity and administrative actions</p>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-primary" data-pjax>View Logs</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary" data-pjax>← Back to Home</a>
    </div>
</div>
@endsection
