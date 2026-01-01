@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div id="realtime-dashboard"
     data-stream-url="{{ route('admin.realtime.stream') }}"
     data-realtime-config='@json(config('realtime'))'>

    <!-- Realtime Connection Status -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Real-time Dashboard</h5>
                    <small class="text-muted">
                        <span class="rt-status-dot rounded-circle d-inline-block me-2" style="width: 10px; height: 10px; background: #aaa;" data-rt-status-dot></span>
                        <span data-rt-status-text>Disconnected</span>
                        <span class="ms-3">Last update: <span data-rt-updated-at>--</span></span>
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total PV -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Page Views</div>
                        <div class="stat-value mt-2" data-rt-number data-key="access.pv_total">--</div>
                        <div class="stat-change" data-rt-delta data-key="access.pv_total">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="bi bi-eye"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total UV -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Visitors</div>
                        <div class="stat-value mt-2" data-rt-number data-key="access.uv_total">--</div>
                        <div class="stat-change" data-rt-delta data-key="access.uv_total">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon success">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Calls -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total API Calls</div>
                        <div class="stat-value mt-2" data-rt-number data-key="api.total_calls">--</div>
                        <div class="stat-change" data-rt-delta data-key="api.total_calls">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i class="bi bi-activity"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today PV -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Page Views Today</div>
                        <div class="stat-value mt-2" data-rt-number data-key="access.pv_today">--</div>
                        <div class="stat-change" data-rt-delta data-key="access.pv_today">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Calls Today -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">API Calls Today</div>
                        <div class="stat-value mt-2" data-rt-number data-key="api.today_calls">--</div>
                        <div class="stat-change" data-rt-delta data-key="api.today_calls">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon danger">
                        <i class="bi bi-lightning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Online Users -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Online Users</div>
                        <div class="stat-value mt-2" data-rt-number data-key="access.online_users">--</div>
                        <div class="stat-label text-muted small">Active SSE connections</div>
                    </div>
                    <div class="stat-icon success">
                        <i class="bi bi-wifi"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- CPU Usage -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">CPU Usage</div>
                        <div class="stat-value mt-2" data-rt-number data-format="percent" data-key="system.cpu_percent">--</div>
                        <div class="stat-change" data-rt-delta data-key="system.cpu_percent">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-cpu"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memory Usage -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Memory Usage</div>
                        <div class="stat-value mt-2" data-rt-number data-format="percent" data-key="system.memory_percent">--</div>
                        <div class="stat-change" data-rt-delta data-key="system.memory_percent">
                            <i class="bi bi-dash-circle"></i>
                            <span>--</span>
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i class="bi bi-memory"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Access Trend Chart -->
        <div class="col-lg-8 col-12">
            <div class="card admin-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Access Trend</h5>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="accessTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Ranking -->
        <div class="col-lg-4 col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top APIs Today</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="apiRankingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row g-4 mb-4">
        <!-- DB Queries -->
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon info me-3">
                            <i class="bi bi-database"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Database Queries Today</div>
                            <div class="stat-value" data-rt-number data-key="system.db_queries_today">--</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blog Posts -->
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon success me-3">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Blog Posts</div>
                            <div class="stat-value" data-rt-number data-key="blog.posts_total">--</div>
                            <div class="stat-label text-muted small">Total views: <span data-rt-number data-key="blog.views_total">--</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links -->
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon warning me-3">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Friend Links</div>
                            <div class="stat-value" data-rt-number data-key="links.links_total">--</div>
                            <div class="stat-label text-muted small">Clicks today: <span data-rt-text data-key="links.links_clicks_today">--</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8 col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="activities-list" data-rt-activities>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock-history fs-1 mb-2 d-block"></i>
                            Loading activities...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.apis.create') }}" class="btn btn-admin btn-admin-primary">
                            <i class="bi bi-plus-lg me-2"></i> Create New API
                        </a>
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-admin btn-admin-success">
                            <i class="bi bi-pencil-square me-2"></i> Create New Blog Post
                        </a>
                        <a href="{{ route('admin.plugins.index') }}" class="btn btn-admin btn-admin-primary" style="background-color: #9b59b6; border-color: #9b59b6;">
                            <i class="bi bi-plugin me-2"></i> Manage Plugins
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-admin btn-admin-warning" style="background-color: #f59e0b; border-color: #f59e0b;">
                            <i class="bi bi-gear me-2"></i> Site Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current User</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-4" style="width: 64px; height: 64px; font-size: 2rem;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</h5>
                            <p class="text-muted mb-2">{{ auth()->guard('admin')->user()->email ?? 'admin@example.com' }}</p>
                            <div class="d-flex gap-3">
                                <span class="badge badge-admin badge-admin-primary">
                                    <i class="bi bi-shield-check me-1"></i> Administrator
                                </span>
                                <span class="badge badge-admin badge-admin-success">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Online
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Last Login</div>
                            <div>{{ auth()->guard('admin')->user()->last_login_at?->format('Y-m-d H:i:s') ?? 'Unknown' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Styles for Realtime Dashboard -->
<style>
    .rt-status-dot {
        transition: background-color 0.3s ease;
    }
    .rt-dot-connected {
        background: #10b981 !important;
    }
    .rt-dot-connecting {
        background: #f59e0b !important;
    }
    .rt-dot-error {
        background: #ef4444 !important;
    }

    .rt-value {
        transition: transform 0.18s ease, color 0.18s ease;
    }
    .rt-flash {
        transform: scale(1.05);
    }
</style>

@endsection

@push('scripts')
<script>
    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
        // Access Trend Chart
        const accessTrendCtx = document.getElementById('accessTrendChart');
        if (accessTrendCtx) {
            new Chart(accessTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Page Views',
                        data: [1200, 1900, 1700, 2100, 1800, 1400, 1600],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Unique Visitors',
                        data: [800, 1200, 1100, 1400, 1200, 900, 1000],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // API Ranking Chart
        const apiRankingCtx = document.getElementById('apiRankingChart');
        if (apiRankingCtx) {
            new Chart(apiRankingCtx, {
                type: 'bar',
                data: {
                    labels: ['API 1', 'API 2', 'API 3', 'API 4', 'API 5'],
                    datasets: [{
                        label: 'Calls Today',
                        data: [450, 380, 320, 280, 210],
                        backgroundColor: [
                            '#4f46e5',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#3b82f6'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>

<!-- Realtime Dashboard Script -->
<script src="{{ asset('js/realtime-dashboard.js') }}" defer></script>
@endpush
