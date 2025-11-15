@extends('layouts.public')

@section('title', '首页')
@section('description', '强大的管理系统 - 为您提供高效、安全的数据管理解决方案')

@section('hero')
    <div class="hero-content">
        <h1 class="hero-title">强大的管理系统</h1>
        <p class="hero-subtitle">为您提供高效、安全的数据管理解决方案</p>
        <div class="hero-actions">
            <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">开始使用</a>
            <a href="{{ url('/docs') }}" class="btn btn-outline btn-lg" data-pjax>了解更多</a>
        </div>
    </div>
@endsection

@section('content')
    <section class="features-section">
        <h2 class="section-title">核心功能</h2>
        <div class="features-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🚀</div>
                <h3 class="feature-title">高性能</h3>
                <p class="feature-description">优化的架构设计，确保系统运行快速稳定</p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🔒</div>
                <h3 class="feature-title">安全可靠</h3>
                <p class="feature-description">多层安全防护，保护您的数据安全</p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">📊</div>
                <h3 class="feature-title">数据分析</h3>
                <p class="feature-description">强大的数据分析工具，助您做出明智决策</p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🎨</div>
                <h3 class="feature-title">美观界面</h3>
                <p class="feature-description">现代化的用户界面，提供优质的使用体验</p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">📱</div>
                <h3 class="feature-title">响应式设计</h3>
                <p class="feature-description">完美适配各种设备，随时随地访问</p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">⚡</div>
                <h3 class="feature-title">快速部署</h3>
                <p class="feature-description">简单快捷的部署流程，快速上线</p>
            </article>
        </div>
    </section>
    
    <section class="cta-section">
        <h2 class="cta-title">准备好开始了吗？</h2>
        <p class="cta-description">立即注册，体验强大的管理系统</p>
        <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">免费开始</a>
    </section>
@endsection
