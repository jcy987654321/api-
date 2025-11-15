@extends('layouts.public')

@section('title', '关于我们')
@section('description', '了解我们的团队和使命')

@section('content')
    <div class="page-header">
        <h1 class="page-title">关于我们</h1>
        <p class="page-description">致力于打造最强大的管理系统</p>
    </div>

    <section class="content-section">
        <div class="content-wrapper">
            <h2 class="section-title">我们的使命</h2>
            <p class="section-text">
                我们致力于为企业和个人提供高效、安全、易用的管理系统解决方案。
                通过不断创新和优化，帮助用户提升工作效率，降低管理成本，实现数字化转型。
            </p>
        </div>

        <div class="content-wrapper mt-xl">
            <h2 class="section-title">核心价值观</h2>
            <div class="values-grid">
                <div class="value-card">
                    <h3 class="value-title">创新</h3>
                    <p class="value-description">持续创新，追求卓越</p>
                </div>
                <div class="value-card">
                    <h3 class="value-title">安全</h3>
                    <p class="value-description">数据安全，值得信赖</p>
                </div>
                <div class="value-card">
                    <h3 class="value-title">用户至上</h3>
                    <p class="value-description">以用户需求为导向</p>
                </div>
                <div class="value-card">
                    <h3 class="value-title">开放</h3>
                    <p class="value-description">开放合作，共赢发展</p>
                </div>
            </div>
        </div>

        <div class="content-wrapper mt-xl">
            <h2 class="section-title">联系我们</h2>
            <p class="section-text">
                如有任何问题或建议，欢迎随时与我们联系。我们的团队将竭诚为您服务。
            </p>
            <div class="contact-info mt-lg">
                <p><strong>邮箱:</strong> <a href="mailto:info@example.com">info@example.com</a></p>
                <p><strong>电话:</strong> <a href="tel:+861234567890">+86 123 456 7890</a></p>
            </div>
        </div>
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

.content-section {
    max-width: 900px;
    margin: 0 auto;
}

.content-wrapper {
    margin-bottom: 2rem;
}

.section-text {
    font-size: 1.125rem;
    line-height: 1.75;
    color: var(--color-text-secondary);
    margin-top: 1rem;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.value-card {
    background-color: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease-in-out;
}

.value-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.value-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.value-description {
    color: var(--color-text-secondary);
}

.contact-info {
    background-color: var(--color-bg-secondary);
    padding: 1.5rem;
    border-radius: 0.75rem;
}

.contact-info p {
    margin-bottom: 0.5rem;
}

@media (min-width: 768px) {
    .page-title {
        font-size: 3rem;
    }
}
</style>
@endpush
