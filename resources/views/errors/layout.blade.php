<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background-color: #f3f4f6; color: #374151; }
        .container { text-align: center; max-width: 600px; padding: 20px; }
        .code { font-size: 6rem; font-weight: 800; color: #1f2937; margin: 0; line-height: 1; }
        .message { font-size: 1.5rem; margin-top: 1rem; font-weight: 600; }
        .suggestion { margin-top: 1rem; margin-bottom: 2rem; color: #6b7280; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; text-decoration: none; font-weight: 600; transition: background-color 0.2s; }
        .btn:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">@yield('code')</div>
        <div class="message">@yield('message')</div>
        <div class="suggestion">@yield('suggestion')</div>
        <a href="/" class="btn">Back to Safety</a>
    </div>
</body>
</html>
