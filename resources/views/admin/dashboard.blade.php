@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div id="realtime-dashboard"
         data-stream-url="{{ route('admin.realtime.stream') }}"
         data-realtime-config='@json(config('realtime'))'>
        <div class="rt-header">
            <div>
                <h1 style="margin: 0;">Admin Dashboard</h1>
                <div style="color: #666; margin-top: 6px; font-size: 0.95em;">
                    <span class="rt-status-dot" data-rt-status-dot></span>
                    <span data-rt-status-text>Disconnected</span>
                    <span style="margin-left: 10px;">Last update: <span data-rt-updated-at>--</span></span>
                </div>
            </div>
        </div>

        <style>
            .rt-header { display: flex; justify-content: space-between; align-items: flex-start; }
            .rt-status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #aaa; margin-right: 6px; vertical-align: middle; }
            .rt-dot-connected { background: #2ecc71; }
            .rt-dot-connecting { background: #f1c40f; }
            .rt-dot-error { background: #e74c3c; }

            .rt-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-top: 18px; }
            .rt-card { background: #fff; padding: 16px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05); }
            .rt-title { color: #666; font-size: 0.95em; }
            .rt-value { font-size: 2em; font-weight: 700; margin-top: 6px; transition: transform 180ms ease, color 180ms ease; }
            .rt-delta { margin-left: 8px; font-size: 0.95em; font-weight: 600; }
            .rt-up { color: #2ecc71; }
            .rt-down { color: #e74c3c; }
            .rt-flash { transform: scale(1.05); }

            .rt-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
            @media (max-width: 900px) { .rt-panels { grid-template-columns: 1fr; } }

            .rt-list { margin: 10px 0 0; padding-left: 18px; }
            .rt-list li { margin: 6px 0; color: #333; }
            .rt-muted { color: #777; font-size: 0.95em; }
        </style>

        <div class="rt-grid">
            <div class="rt-card">
                <div class="rt-title">Total PV</div>
                <div><span class="rt-value" data-rt-number data-key="access.pv_total">--</span><span class="rt-delta" data-rt-delta data-key="access.pv_total"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Total UV</div>
                <div><span class="rt-value" data-rt-number data-key="access.uv_total">--</span><span class="rt-delta" data-rt-delta data-key="access.uv_total"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">API Calls (Total)</div>
                <div><span class="rt-value" data-rt-number data-key="api.total_calls">--</span><span class="rt-delta" data-rt-delta data-key="api.total_calls"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">New Visits Today (PV)</div>
                <div><span class="rt-value" data-rt-number data-key="access.pv_today">--</span><span class="rt-delta" data-rt-delta data-key="access.pv_today"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">API Calls Today</div>
                <div><span class="rt-value" data-rt-number data-key="api.today_calls">--</span><span class="rt-delta" data-rt-delta data-key="api.today_calls"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Online Dashboard Tabs</div>
                <div><span class="rt-value" data-rt-number data-key="access.online_users">--</span><span class="rt-delta" data-rt-delta data-key="access.online_users"></span></div>
                <div class="rt-muted" style="margin-top: 6px;">Active SSE connections</div>
            </div>
            <div class="rt-card">
                <div class="rt-title">CPU Usage</div>
                <div><span class="rt-value" data-rt-number data-format="percent" data-key="system.cpu_percent">--</span><span class="rt-delta" data-rt-delta data-key="system.cpu_percent"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Memory Usage</div>
                <div><span class="rt-value" data-rt-number data-format="percent" data-key="system.memory_percent">--</span><span class="rt-delta" data-rt-delta data-key="system.memory_percent"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">DB Queries Today</div>
                <div><span class="rt-value" data-rt-number data-key="system.db_queries_today">--</span><span class="rt-delta" data-rt-delta data-key="system.db_queries_today"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Blog Posts</div>
                <div><span class="rt-value" data-rt-number data-key="blog.posts_total">--</span><span class="rt-delta" data-rt-delta data-key="blog.posts_total"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Blog Views (Total)</div>
                <div><span class="rt-value" data-rt-number data-key="blog.views_total">--</span><span class="rt-delta" data-rt-delta data-key="blog.views_total"></span></div>
            </div>
            <div class="rt-card">
                <div class="rt-title">Links</div>
                <div><span class="rt-value" data-rt-number data-key="links.links_total">--</span><span class="rt-delta" data-rt-delta data-key="links.links_total"></span></div>
                <div class="rt-muted" style="margin-top: 6px;">Clicks today: <span data-rt-text data-key="links.links_clicks_today">--</span></div>
            </div>
        </div>

        <div class="rt-panels">
            <div class="rt-card">
                <h2 style="margin: 0; font-size: 1.15em;">Top APIs (Today)</h2>
                <ol class="rt-list" data-rt-top-apis>
                    <li class="rt-muted">--</li>
                </ol>
            </div>

            <div class="rt-card">
                <h2 style="margin: 0; font-size: 1.15em;">Recent Activities</h2>
                <ul class="rt-list" style="list-style: none; padding-left: 0;" data-rt-activities>
                    <li class="rt-muted">--</li>
                </ul>
            </div>
        </div>

        <div class="rt-card" style="margin-top: 14px;">
            <h2 style="margin: 0; font-size: 1.15em;">Quick Actions</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;">
                <a href="/admin/apis/create" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">Create New API</a>
                <a href="/admin/blogs/create" style="display: inline-block; background: #2ecc71; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">Create New Blog Post</a>
                <a href="/admin/plugins" style="display: inline-block; background: #9b59b6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">Manage Plugins</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/realtime-dashboard.js') }}" defer></script>
@endsection
