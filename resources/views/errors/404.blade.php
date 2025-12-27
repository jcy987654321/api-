<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>404 - 页面未找到</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-message {
            font-size: 28px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .error-description {
            font-size: 16px;
            line-height: 1.6;
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
            color: #667eea;
            border-color: transparent;
        }

        .btn-primary:hover {
            background: #fff;
            color: #764ba2;
        }

        .illustration {
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .illustration svg {
            width: 200px;
            height: 200px;
        }

        .meta-info {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 13px;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" stroke="rgba(255,255,255,0.3)" stroke-width="8"/>
                <circle cx="100" cy="100" r="50" stroke="rgba(255,255,255,0.2)" stroke-width="4"/>
                <path d="M70 85 L90 105 L130 65" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="145" cy="55" r="15" fill="#fff" fill-opacity="0.2"/>
                <text x="145" y="60" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">?</text>
            </svg>
        </div>

        <div class="error-code">404</div>
        <h1 class="error-message">页面未找到</h1>
        <p class="error-description">
            抱歉，您访问的页面不存在或已被移动。<br>
            请检查 URL 是否正确，或尝试访问其他页面。
        </p>

        <div class="actions">
            <a href="/" class="btn btn-primary">返回首页</a>
            <a href="javascript:history.back()" class="btn">返回上一页</a>
            <a href="/contact" class="btn">联系我们</a>
        </div>

        @if(config('app.debug'))
        <div class="meta-info">
            <p>请求路径: {{ request()->fullUrl() }}</p>
            <p>请求时间: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
        @endif
    </div>
</body>
</html>
