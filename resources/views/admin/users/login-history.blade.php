<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login History</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 28px;
            color: #333;
        }

        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background: #5568d3;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        th {
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            color: #666;
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-failure {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-blocked {
            background: #fff3cd;
            color: #856404;
        }

        .location {
            font-size: 12px;
            color: #999;
        }

        .device {
            font-size: 12px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 4px;
            border: 1px solid #ddd;
            color: #667eea;
            text-decoration: none;
        }

        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Panel</h1>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <h2>Login History for {{ $user->name }}</h2>
                <p style="color: #666; margin-top: 5px;">{{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.show', $user) }}" class="btn">Back to User</a>
        </div>

        @if ($history->count())
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Device</th>
                            <th>Browser</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $log)
                            <tr>
                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-{{ $log->status }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td>
                                    <div class="location">
                                        @if ($log->city)
                                            {{ $log->city }}
                                        @endif
                                        @if ($log->country_name)
                                            {{ $log->country_name }}
                                        @else
                                            Unknown
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="device">
                                        {{ $log->device_type ? ucfirst($log->device_type) : 'Unknown' }}
                                        @if ($log->is_mobile)
                                            📱
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="device">
                                        {{ $log->browser_name ?? 'Unknown' }}
                                        @if ($log->browser_version)
                                            {{ $log->browser_version }}
                                        @endif
                                    </div>
                                    <div class="location">
                                        {{ $log->os_name ?? 'Unknown' }}
                                        @if ($log->os_version)
                                            {{ $log->os_version }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($log->failure_reason)
                                        <div class="location">{{ $log->failure_reason }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $history->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; background: white; border-radius: 8px;">
                <p style="color: #999;">No login history found for this user</p>
            </div>
        @endif
    </div>
</body>
</html>
