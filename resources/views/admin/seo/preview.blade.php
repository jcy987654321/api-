@extends('layouts.admin')

@section('title', 'SEO 预览')
@section('page-title', 'SEO 预览')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="fw-semibold">页面标识：<code>{{ $config->page }}</code></div>
            <div class="text-muted small">可用于检查 meta 标签输出是否符合预期。</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seo.edit', $config->page) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> 编辑</a>
            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> 返回</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-semibold">Meta 输出</div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded mb-0" style="white-space: pre-wrap;">{{ $metaHtml }}</pre>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold">渲染效果（简化）</div>
        <div class="card-body">
            {!! $metaHtml !!}
            <div class="mt-3 text-muted small">
                注：实际页面中 meta 标签会放在 &lt;head&gt; 内部，这里仅作预览。
            </div>
        </div>
    </div>
@endsection
