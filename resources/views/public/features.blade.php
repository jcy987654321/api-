@extends('layouts.public')

@section('title', '功能特性')
@section('description', '了解我们强大管理系统的核心功能特性')

@section('content')
    <div class="page-header">
        <h1 class="page-title">功能特性</h1>
        <p class="page-description">探索我们为您提供的强大功能</p>
    </div>

    <section class="features-section">
        <div class="features-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🚀</div>
                <h3 class="feature-title">极致性能</h3>
                <p class="feature-description">
                    采用先进的技术架构，确保系统运行流畅快速。优化的数据库查询和缓存策略，
                    让您的应用响应速度提升10倍以上。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🔒</div>
                <h3 class="feature-title">企业级安全</h3>
                <p class="feature-description">
                    多层安全防护体系，包括数据加密、访问控制、审计日志等，
                    全方位保护您的数据安全和隐私。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">📊</div>
                <h3 class="feature-title">智能数据分析</h3>
                <p class="feature-description">
                    内置强大的数据分析工具，支持多维度数据统计和可视化展示，
                    帮助您洞察业务趋势，做出明智决策。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🎨</div>
                <h3 class="feature-title">现代化设计</h3>
                <p class="feature-description">
                    精心设计的用户界面，遵循最新的UI/UX设计规范，
                    提供直观流畅的操作体验。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">📱</div>
                <h3 class="feature-title">全平台支持</h3>
                <p class="feature-description">
                    完美适配桌面、平板、手机等各种设备，随时随地都能
                    轻松访问和管理您的数据。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">⚡</div>
                <h3 class="feature-title">一键部署</h3>
                <p class="feature-description">
                    简单快捷的部署流程，支持Docker容器化部署，
                    几分钟即可完成系统搭建。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🔄</div>
                <h3 class="feature-title">实时同步</h3>
                <p class="feature-description">
                    数据实时同步功能，确保多端数据一致性，
                    团队协作更加高效便捷。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🎯</div>
                <h3 class="feature-title">高度可定制</h3>
                <p class="feature-description">
                    灵活的配置选项和插件系统，可根据您的具体需求
                    进行个性化定制和扩展。
                </p>
            </article>
            
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">🛠️</div>
                <h3 class="feature-title">开发者友好</h3>
                <p class="feature-description">
                    完善的API文档和SDK支持，方便开发者快速集成
                    和二次开发。
                </p>
            </article>
        </div>
    </section>

    <section class="cta-section">
        <h2 class="cta-title">体验这些强大功能</h2>
        <p class="cta-description">立即注册，开始您的管理之旅</p>
        <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">免费试用</a>
    </section>
@endsection

@push('styles')
<style>
.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.page-description {
    font-size: 1.25rem;
    color: var(--color-text-secondary);
}

@media (min-width: 768px) {
    .page-title {
        font-size: 3rem;
    }
}
</style>
@endpush
