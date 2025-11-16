<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            align-items: center;
        }

        .navbar h1 {
            font-size: 24px;
            color: #333;
        }

        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .card-link {
            display: inline-block;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .card-link:hover {
            text-decoration: underline;
        }

        .welcome-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .welcome-message h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .welcome-message p {
            font-size: 16px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Panel</h1>
        <div class="navbar-right">
            <div class="user-info">
                <span>{{ auth('admin')->user()->name }}</span>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome-message">
            <h1>Welcome back, {{ auth('admin')->user()->name }}!</h1>
            <p>You have full access to the admin panel.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>User Management</h2>
                <p>Manage admin and staff users, view their roles, and handle permissions.</p>
                <a href="{{ route('admin.users.index') }}" class="card-link">View Users →</a>
            </div>

            <div class="card">
                <h2>Login Activity</h2>
                <p>Monitor login attempts and view detailed activity logs for security.</p>
                <a href="{{ route('admin.users.login-history', auth('admin')->user()) }}" class="card-link">View Activity →</a>
            </div>

            <div class="card">
                <h2>Account Security</h2>
                <p>Manage your account security settings and session management.</p>
                <a href="#" class="card-link">Security Settings →</a>
            </div>
        </div>
    </div>
</body>
</html>
