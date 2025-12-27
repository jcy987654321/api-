<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            border-left: 3px solid transparent;
        }
        .sidebar-nav li a:hover, .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #3498db;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f5f5f5;
        }
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header" style="padding: 0 20px 20px; font-weight: bold; font-size: 1.2em;">
                Admin Panel
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin" class="{{ Request::is('admin') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="/admin/apis" class="{{ Request::is('admin/apis*') ? 'active' : '' }}">API Management</a></li>
                <li><a href="/admin/blogs" class="{{ Request::is('admin/blogs*') ? 'active' : '' }}">Blog Management</a></li>
                <li><a href="/admin/settings" class="{{ Request::is('admin/settings*') ? 'active' : '' }}">Settings</a></li>
                <li><a href="/admin/plugins" class="{{ Request::is('admin/plugins*') ? 'active' : '' }}">Plugins</a></li>
                <li><a href="/admin/statistics" class="{{ Request::is('admin/statistics*') ? 'active' : '' }}">Statistics</a></li>
                <li><a href="/admin/links" class="{{ Request::is('admin/links*') ? 'active' : '' }}">Links</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <div>Welcome to Admin Dashboard</div>
                <div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Logout</button>
                    </form>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>