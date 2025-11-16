<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .info-value {
            color: #666;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }

        .badge-admin {
            background: #fff3cd;
            color: #856404;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button, .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-warning {
            background: #ffc107;
            color: #333;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Panel</h1>
    </div>

    <div class="container">
        <div class="card">
            <h2>User Profile</h2>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="info-group">
                <div class="info-label">Name</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Role</div>
                <div class="info-value">
                    @if ($user->is_admin)
                        <span class="badge badge-admin">Admin</span>
                    @else
                        @foreach ($user->roles as $role)
                            <span class="badge badge-admin">{{ $role->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Last Login</div>
                <div class="info-value">
                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i:s') : 'Never' }}
                    @if ($user->last_login_ip)
                        ({{ $user->last_login_ip }})
                    @endif
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Account Locked</div>
                <div class="info-value">
                    @if ($user->is_account_locked)
                        <span class="badge" style="background: #f8d7da; color: #721c24;">Yes - Until {{ $user->account_locked_until->format('M d, Y H:i') }}</span>
                    @else
                        <span class="badge" style="background: #d4edda; color: #155724;">No</span>
                    @endif
                </div>
            </div>

            <div class="button-group">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
                <a href="{{ route('admin.users.login-history', $user) }}" class="btn btn-primary">View Login History</a>
                @if ($user->is_account_locked)
                    <form action="{{ route('admin.users.unlock', $user) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">Unlock Account</button>
                    </form>
                @endif
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
            </div>
        </div>
    </div>
</body>
</html>
