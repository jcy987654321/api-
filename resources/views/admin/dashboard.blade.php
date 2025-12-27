@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <h1>Admin Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Total APIs</h3>
            <p style="font-size: 2em; font-weight: bold; color: #3498db;">10</p>
            <a href="/admin/apis" style="display: inline-block; background: #3498db; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Manage APIs</a>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Total Blog Posts</h3>
            <p style="font-size: 2em; font-weight: bold; color: #2ecc71;">5</p>
            <a href="/admin/blogs" style="display: inline-block; background: #2ecc71; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Manage Blogs</a>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>System Status</h3>
            <p style="font-size: 2em; font-weight: bold; color: #e74c3c;">Active</p>
            <a href="/admin/settings" style="display: inline-block; background: #e74c3c; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">System Settings</a>
        </div>
    </div>
    
    <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>Quick Actions</h2>
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <a href="/admin/apis/create" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Create New API</a>
            <a href="/admin/blogs/create" style="display: inline-block; background: #2ecc71; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Create New Blog Post</a>
            <a href="/admin/plugins" style="display: inline-block; background: #9b59b6; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Manage Plugins</a>
        </div>
    </div>
@endsection