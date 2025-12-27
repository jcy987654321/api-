<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>403 - 禁止访问</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa502 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 40px;
            max-width: 600px;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 4px 4px 20px rgba(0,0,0,0.3);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-message {
            font-size: 28px;
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .error-description {
            font-size: 16px;
            line-height: 1.8;
            opacity: 0.85;
            margin-bottom: 40px;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: rgba(255,255,255,0.95);
            color: #ff6b6b;
            border-color: transparent;
        }

        .btn-primary:hover {
            background: #fff;
            color: #ffa502;
        }

        .lock-icon {
            margin-bottom: 30px;
        }

        .lock-icon svg {
            width: 150px;
            height: 150px;
        }

        .permission-info {
            background: rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            backdrop-filter: blur(10px);
        }

        .permission-info h3 {
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .permission-info ul {
            list-style: none;
            text-align: left;
            font-size: 14px;
            opacity: 0.9;
        }

        .permission-info li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .permission-info li:last-child {
            border-bottom: none;
        }

        .permission-info li::before {
            content: "🔒";
        }

        .meta-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 13px;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lock-icon">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="20" y="45" width="60" height="45" rx="5" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="3"/>
                <path d="M35 45 V30 A25 25 0 0 1 65 30 V45" stroke="rgba(255,255,255,0.5)" stroke-width="4" fill="none"/>
                <circle cx="50" cy="67" r="8" fill="#fff"/>
                <rect x="47" y="67" width="6" height="12" rx="1" fill="#ff6b6b"/>
            </svg>
        </div>

        <div class="error-code">403</div>
        <h1 class="error-message">禁止访问</h1>
        <p class="error-description">
            抱歉，您没有权限访问此页面。<br>
            这可能是由于您的账户权限不足，或者该页面需要特定的访问权限。
        </p>

        <div class="permission-info">
            <h3>您可以尝试以下操作：</h3>
            <ul>
                <li>登录具有相应权限的账户</li>
                <li>联系管理员申请访问权限</li>
                <li>返回上一页并检查 URL</li>
                <li>浏览其他可用的页面</li>
            </ul>
        </div>

        <div class="actions">
            <a href="/login" class="btn btn-primary">登录账户</a>
            <a href="/" class="btn">返回首页</a>
            <a href="/contact" class="btn">申请权限</a>
        </div>

        @if(config('app.debug'))
        <div class="meta-info">
            <p>请求路径: {{ request()->fullUrl() }}</p>
            <p>用户: {{ auth()->check() ? auth()->user()->name : '未登录' }}</p>
            <p>请求时间: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
        @endif
    </div>
</body>
</html>
