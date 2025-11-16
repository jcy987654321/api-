<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('description', '强大的管理系统 - Powerful Management System')">
    <meta name="keywords" content="@yield('keywords', 'management, system, admin')">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">
    
    <title>@yield('title', '管理系统') - {{ config('app.name', 'API System') }}</title>
    
    <!-- Preload critical assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/scss/public/styles.scss', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    @endif
    
    @stack('styles')
    
    <!-- Theme initialization script (must run before body renders) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const savedOverride = localStorage.getItem('themeOverride');
            
            if (savedOverride === 'true' && savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else {
                const hour = new Date().getHours();
                const autoTheme = (hour >= 6 && hour < 18) ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', autoTheme);
            }
        })();
    </script>
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Header Navigation -->
    <header class="site-header" role="banner">
        <nav class="container navbar" role="navigation" aria-label="Main navigation">
            <div class="navbar-brand">
                <a href="{{ url('/') }}" class="logo" data-pjax>
                    <span class="logo-icon" aria-hidden="true">⚡</span>
                    <span class="logo-text">{{ config('app.name', 'API System') }}</span>
                </a>
                
                <button 
                    type="button" 
                    class="mobile-menu-toggle" 
                    aria-expanded="false" 
                    aria-controls="main-menu"
                    aria-label="Toggle navigation menu"
                >
                    <span class="hamburger-icon" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
            
            <div class="navbar-menu" id="main-menu">
                <ul class="nav-links" role="menubar">
                    <li role="none">
                        <a href="{{ url('/') }}" class="nav-link" data-pjax role="menuitem">
                            <span>首页</span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ url('/features') }}" class="nav-link" data-pjax role="menuitem">
                            <span>功能</span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ url('/docs') }}" class="nav-link" data-pjax role="menuitem">
                            <span>文档</span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ url('/about') }}" class="nav-link" data-pjax role="menuitem">
                            <span>关于</span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="{{ url('/contact') }}" class="nav-link" data-pjax role="menuitem">
                            <span>联系</span>
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-actions">
                    <button 
                        type="button" 
                        class="theme-toggle" 
                        aria-label="Toggle theme"
                        title="Toggle theme"
                    >
                        <span class="theme-icon theme-icon-light" aria-hidden="true">☀️</span>
                        <span class="theme-icon theme-icon-dark" aria-hidden="true">🌙</span>
                    </button>
                    
                    @guest
                        <a href="{{ url('/login') }}" class="btn btn-outline">登录</a>
                        <a href="{{ url('/register') }}" class="btn btn-primary">注册</a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">控制台</a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>
    
    <!-- PJAX Container -->
    <div id="pjax-container">
        <!-- Hero Section (optional, can be overridden) -->
        @hasSection('hero')
            <section class="hero" role="region" aria-label="Hero section">
                <div class="container">
                    @yield('hero')
                </div>
            </section>
        @endif
        
        <!-- Main Content -->
        <main id="main-content" class="main-content" role="main">
            <div class="container">
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Footer -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3 class="footer-title">关于我们</h3>
                    <p class="footer-description">
                        强大的管理系统，为您提供高效、安全的数据管理解决方案。
                    </p>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">快速链接</h3>
                    <ul class="footer-links">
                        <li><a href="{{ url('/features') }}" data-pjax>功能特性</a></li>
                        <li><a href="{{ url('/docs') }}" data-pjax>使用文档</a></li>
                        <li><a href="{{ url('/pricing') }}" data-pjax>价格方案</a></li>
                        <li><a href="{{ url('/support') }}" data-pjax>技术支持</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">法律信息</h3>
                    <ul class="footer-links">
                        <li><a href="{{ url('/privacy') }}" data-pjax>隐私政策</a></li>
                        <li><a href="{{ url('/terms') }}" data-pjax>服务条款</a></li>
                        <li><a href="{{ url('/cookies') }}" data-pjax>Cookie 政策</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">联系方式</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:info@example.com">info@example.com</a></li>
                        <li><a href="tel:+861234567890">+86 123 456 7890</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="copyright">
                    &copy; {{ date('Y') }} {{ config('app.name', 'API System') }}. All rights reserved.
                </p>
                <div class="social-links" role="navigation" aria-label="Social media links">
                    <a href="#" aria-label="GitHub" class="social-link">GitHub</a>
                    <a href="#" aria-label="Twitter" class="social-link">Twitter</a>
                    <a href="#" aria-label="LinkedIn" class="social-link">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scroll Controls -->
    <div class="scroll-controls" role="navigation" aria-label="Page scroll controls">
        <button 
            type="button" 
            class="scroll-btn scroll-top" 
            aria-label="Scroll to top"
            title="Scroll to top"
            tabindex="0"
        >
            <span aria-hidden="true">↑</span>
        </button>
        <button 
            type="button" 
            class="scroll-btn scroll-bottom" 
            aria-label="Scroll to bottom"
            title="Scroll to bottom"
            tabindex="0"
        >
            <span aria-hidden="true">↓</span>
        </button>
    </div>
    
    <!-- PJAX Loading Indicator -->
    <div class="pjax-loader" role="status" aria-live="polite" aria-label="Loading content">
        <div class="loader-spinner" aria-hidden="true"></div>
        <span class="sr-only">Loading...</span>
    </div>
    
    <!-- Scripts -->
    @if (!file_exists(public_path('build/manifest.json')))
        <script src="{{ asset('js/public.js') }}"></script>
    @endif
    
    @stack('scripts')
</body>
</html>
