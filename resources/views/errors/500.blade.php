<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>500 - 服务器错误</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 40px;
            max-width: 700px;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 4px 4px 20px rgba(0,0,0,0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .error-message {
            font-size: 32px;
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
            color: #f5576c;
            border-color: transparent;
        }

        .btn-primary:hover {
            background: #fff;
            color: #f093fb;
        }

        .warning-icon {
            margin-bottom: 30px;
        }

        .warning-icon svg {
            width: 150px;
            height: 150px;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .support-info {
            background: rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            backdrop-filter: blur(10px);
        }

        .support-info h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .support-info p {
            font-size: 14px;
            opacity: 0.8;
        }

        .meta-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 13px;
            opacity: 0.6;
            text-align: left;
            background: rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 8px;
        }

        .meta-info code {
            background: rgba(255,255,255,0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 12px;
        }

        .countdown {
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 10 L90 85 L10 85 Z" stroke="rgba(255,255,255,0.5)" stroke-width="3" fill="rgba(255,255,255,0.1)"/>
                <circle cx="50" cy="60" r="8" fill="#fff"/>
                <rect x="47" y="25" width="6" height="20" rx="2" fill="#fff"/>
            </svg>
        </div>

        <div class="error-code">500</div>
        <h1 class="error-message">服务器错误</h1>
        <p class="error-description">
            抱歉，服务器遇到了一个意外错误，无法完成您的请求。<br>
            我们的技术团队已经收到通知，正在紧急处理中。
        </p>

        <div class="actions">
            <a href="/" class="btn btn-primary">返回首页</a>
            <a href="javascript:location.reload()" class="btn">刷新页面</a>
            <a href="/contact" class="btn">报告错误</a>
        </div>

        <div class="support-info">
            <h3>需要紧急帮助？</h3>
            <p>请联系技术支持或稍后重试。</p>
        </div>

        @if(config('app.debug'))
        <div class="meta-info">
            <p><strong>调试信息：</strong></p>
            <p>请求路径: <code>{{ request()->fullUrl() }}</code></p>
            <p>请求方法: <code>{{ request()->method() }}</code></p>
            <p>错误时间: <code>{{ now()->format('Y-m-d H:i:s') }}</code></p>
            @if(isset($exception))
            <p>异常类型: <code>{{ get_class($exception) }}</code></p>
            <p>错误信息: <code>{{ $exception->getMessage() }}</code></p>
            @endif
        </div>
        @else
        <div class="countdown">
            页面将在 <span id="countdown">10</span> 秒后自动返回首页
        </div>
        @endif
    </div>

    <script>
        @if(!config('app.debug'))
        let seconds = 10;
        const countdown = document.getElementById('countdown');

        setInterval(() => {
            seconds--;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                window.location.href = '/';
            }
        }, 1000);
        @endif
    </script>
</body>
</html>
