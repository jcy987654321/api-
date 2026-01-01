<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-body">
    <div id="admin-app">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" id="top-navbar">
            <div class="container-fluid">
                <!-- Left: Sidebar Toggle & Logo -->
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-dark d-lg-none me-2" id="sidebar-toggle-mobile">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <button class="btn btn-link text-dark d-none d-lg-block me-2" id="sidebar-toggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-shield-check text-primary me-2 fs-4"></i>
                        <span class="fw-bold">Admin Panel</span>
                    </a>
                </div>

                <!-- Mobile Search -->
                <form class="d-lg-none my-2 my-lg-0 flex-grow-1" id="mobile-search-form">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-start-0" placeholder="Search..." id="mobile-search-input">
                    </div>
                </form>

                <!-- Right: Actions -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Desktop Search -->
                    <form class="d-none d-lg-flex my-2 my-lg-0 me-2" id="desktop-search-form">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-start-0" placeholder="Quick search..." id="desktop-search-input">
                        </div>
                    </form>

                    <!-- Theme Toggle -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown" title="Toggle Theme">
                            <i class="bi bi-moon-stars" id="theme-icon"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" data-theme="light">
                                    <i class="bi bi-sun"></i> Light
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" data-theme="dark">
                                    <i class="bi bi-moon"></i> Dark
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" data-theme="auto">
                                    <i class="bi bi-circle-half"></i> Auto
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown" title="Notifications">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none;">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-start gap-2" href="#">
                                    <i class="bi bi-info-circle text-primary mt-1"></i>
                                    <div>
                                        <small class="fw-semibold">New API created</small>
                                        <br><small class="text-muted">2 minutes ago</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-start gap-2" href="#">
                                    <i class="bi bi-check-circle text-success mt-1"></i>
                                    <div>
                                        <small class="fw-semibold">Database backup completed</small>
                                        <br><small class="text-muted">1 hour ago</small>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                        </ul>
                    </div>

                    <!-- User Menu -->
                    <div class="dropdown">
                        <a class="btn btn-link text-dark p-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" role="button">
                            <div class="user-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <span class="d-none d-md-inline fw-medium">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</span>
                            <i class="bi bi-chevron-down d-none d-md-inline small"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-item-text">
                                    <div class="fw-semibold">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</div>
                                    <small class="text-muted">{{ auth()->guard('admin')->user()->email ?? 'admin@example.com' }}</small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings.profile') }}">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
                                    @csrf
                                </form>
                                <a class="dropdown-item text-danger" href="#" onclick="document.getElementById('logout-form').submit()">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <i class="bi bi-shield-check"></i>
                        <span>Admin Panel</span>
                    </div>
                </div>

                <!-- Menu Search -->
                <div class="sidebar-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-transparent border-start-0" placeholder="Search menu..." id="menu-search">
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <ul class="nav flex-column" id="sidebar-menu">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 menu-icon"></i>
                                <span class="menu-text">Dashboard</span>
                            </a>
                        </li>

                        <!-- API Management -->
                        <li class="nav-item has-submenu">
                            <a href="#" class="nav-link {{ Request::is('admin/apis*') ? 'active' : '' }} submenu-toggle">
                                <i class="bi bi-code-square menu-icon"></i>
                                <span class="menu-text">API Management</span>
                                <i class="bi bi-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="nav flex-column submenu {{ Request::is('admin/apis*') ? 'show' : '' }}">
                                <li class="nav-item">
                                    <a href="{{ route('admin.apis.index') }}" class="nav-link {{ Request::is('admin/apis') ? 'active' : '' }}">
                                        <i class="bi bi-list-ul menu-icon"></i>
                                        <span class="menu-text">API List</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.apis.create') }}" class="nav-link">
                                        <i class="bi bi-plus-circle menu-icon"></i>
                                        <span class="menu-text">New API</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.api-categories.index') }}" class="nav-link">
                                        <i class="bi bi-tags menu-icon"></i>
                                        <span class="menu-text">Categories</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.api-params.index') }}" class="nav-link">
                                        <i class="bi bi-sliders menu-icon"></i>
                                        <span class="menu-text">Parameters</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Blog Management -->
                        <li class="nav-item has-submenu">
                            <a href="#" class="nav-link {{ (Request::is('admin/blogs*') || Request::is('admin/categories*') || Request::is('admin/tags*')) ? 'active' : '' }} submenu-toggle">
                                <i class="bi bi-journal-text menu-icon"></i>
                                <span class="menu-text">Blog Management</span>
                                <i class="bi bi-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="nav flex-column submenu {{ (Request::is('admin/blogs*') || Request::is('admin/categories*') || Request::is('admin/tags*')) ? 'show' : '' }}">
                                <li class="nav-item">
                                    <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ Request::is('admin/blogs') ? 'active' : '' }}">
                                        <i class="bi bi-list-ul menu-icon"></i>
                                        <span class="menu-text">Posts</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.blogs.create') }}" class="nav-link">
                                        <i class="bi bi-plus-circle menu-icon"></i>
                                        <span class="menu-text">New Post</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
                                        <i class="bi bi-tags menu-icon"></i>
                                        <span class="menu-text">Categories</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.tags.index') }}" class="nav-link {{ Request::is('admin/tags*') ? 'active' : '' }}">
                                        <i class="bi bi-hash menu-icon"></i>
                                        <span class="menu-text">Tags</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Plugin Management -->
                        <li class="nav-item has-submenu">
                            <a href="#" class="nav-link {{ Request::is('admin/plugins*') ? 'active' : '' }} submenu-toggle">
                                <i class="bi bi-plugin menu-icon"></i>
                                <span class="menu-text">Plugin Management</span>
                                <i class="bi bi-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="nav flex-column submenu {{ Request::is('admin/plugins*') ? 'show' : '' }}">
                                <li class="nav-item">
                                    <a href="{{ route('admin.plugins.index') }}" class="nav-link {{ Request::is('admin/plugins') ? 'active' : '' }}">
                                        <i class="bi bi-puzzle menu-icon"></i>
                                        <span class="menu-text">Plugins</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.plugins.settings') }}" class="nav-link">
                                        <i class="bi bi-gear menu-icon"></i>
                                        <span class="menu-text">Settings</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Statistics -->
                        <li class="nav-item has-submenu">
                            <a href="#" class="nav-link {{ Request::is('admin/statistics*') ? 'active' : '' }} submenu-toggle">
                                <i class="bi bi-graph-up menu-icon"></i>
                                <span class="menu-text">Statistics</span>
                                <i class="bi bi-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="nav flex-column submenu {{ Request::is('admin/statistics*') ? 'show' : '' }}">
                                <li class="nav-item">
                                    <a href="{{ route('admin.statistics.access') }}" class="nav-link">
                                        <i class="bi bi-eye menu-icon"></i>
                                        <span class="menu-text">Access Stats</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.statistics.api') }}" class="nav-link">
                                        <i class="bi bi-activity menu-icon"></i>
                                        <span class="menu-text">API Calls</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.statistics.users') }}" class="nav-link">
                                        <i class="bi bi-people menu-icon"></i>
                                        <span class="menu-text">User Stats</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- System Settings -->
                        <li class="nav-item has-submenu">
                            <a href="#" class="nav-link {{ Request::is('admin/settings*') || Request::is('admin/users*') || Request::is('admin/links*') || Request::is('admin/logs*') ? 'active' : '' }} submenu-toggle">
                                <i class="bi bi-gear menu-icon"></i>
                                <span class="menu-text">System Settings</span>
                                <i class="bi bi-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="nav flex-column submenu {{ Request::is('admin/settings*') || Request::is('admin/users*') || Request::is('admin/links*') || Request::is('admin/logs*') ? 'show' : '' }}">
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ Request::is('admin/settings') ? 'active' : '' }}">
                                        <i class="bi bi-globe menu-icon"></i>
                                        <span class="menu-text">Site Settings</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link">
                                        <i class="bi bi-people menu-icon"></i>
                                        <span class="menu-text">User Management</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.links.index') }}" class="nav-link {{ Request::is('admin/links*') ? 'active' : '' }}">
                                        <i class="bi bi-link-45deg menu-icon"></i>
                                        <span class="menu-text">Friend Links</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.logs.index') }}" class="nav-link {{ Request::is('admin/logs*') ? 'active' : '' }}">
                                        <i class="bi bi-file-text menu-icon"></i>
                                        <span class="menu-text">System Logs</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content" id="main-content">
                <!-- Page Header with Breadcrumbs -->
                <div class="page-header">
                    <div class="container-fluid">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-house-door"></i> Home
                                    </a>
                                </li>
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>

                <!-- Content -->
                <div class="content-wrapper">
                    @yield('content')
                </div>

                <!-- Footer -->
                <footer class="admin-footer">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    &copy; {{ date('Y') }} Admin Panel. All rights reserved.
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted">
                                    Version 1.0.0 | Built with Laravel 11 & Bootstrap 5
                                </small>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>

        <!-- Overlay for mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
