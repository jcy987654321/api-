@extends('layouts.admin')

@section('title', 'SEO 配置')
@section('page-title', 'SEO 配置')

@section('breadcrumb')
    <li class="breadcrumb-item active">SEO</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">
            预定义页面：
            @foreach($defaultPages as $page => $label)
                <span class="badge text-bg-light">{{ $page }}</span>
            @endforeach
        </div>
        <a href="{{ route('admin.seo.create') }}" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> 新建</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 220px;">页面标识</th>
                            <th>Title</th>
                            <th style="width: 180px;">Updated</th>
                            <th style="width: 220px;" class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configs as $config)
                            <tr>
                                <td><code>{{ $config->page }}</code></td>
                                <td>{{ $config->title ?? '-' }}</td>
                                <td class="text-muted">{{ $config->updated_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.seo.preview', $config->page) }}" class="btn btn-sm btn-outline-secondary" title="预览">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.seo.edit', $config->page) }}" class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.seo.destroy', $config->page) }}" onsubmit="return confirm('确定要删除该 SEO 配置吗？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="删除">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">暂无 SEO 配置。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $configs->links() }}
    </div>
@endsection
