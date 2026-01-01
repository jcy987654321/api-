@extends('layouts.app')

@section('title', '我的 API 密钥')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>我的 API 密钥</h1>
        <a href="{{ route('user.api-keys.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 生成新密钥
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('new_key'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">密钥生成成功！</h5>
            <p><strong>Key:</strong> <code>{{ session('new_key') }}</code></p>
            <p><strong>Secret:</strong> <code>{{ session('new_secret') }}</code></p>
            <hr>
            <p class="mb-0">请妥善保存您的 Secret，它只会显示一次！</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($apiKeys->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>名称</th>
                                <th>Key</th>
                                <th>速率限制</th>
                                <th>状态</th>
                                <th>创建时间</th>
                                <th>最后使用</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiKeys as $apiKey)
                                <tr>
                                    <td>{{ $apiKey->name }}</td>
                                    <td><code>{{ $apiKey->key }}</code></td>
                                    <td>{{ $apiKey->rate_limit }}/{{ $apiKey->rate_window }}s</td>
                                    <td>
                                        @if($apiKey->isActive())
                                            <span class="badge bg-success">激活</span>
                                        @else
                                            <span class="badge bg-secondary">禁用</span>
                                        @endif
                                    </td>
                                    <td>{{ $apiKey->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $apiKey->last_used_at?->format('Y-m-d H:i') ?? '从未' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('user.api-keys.show', $apiKey->id) }}" class="btn btn-outline-primary" title="详情">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('user.api-keys.stats', $apiKey->id) }}" class="btn btn-outline-info" title="统计">
                                                <i class="bi bi-graph-up"></i>
                                            </a>
                                            <form action="{{ route('user.api-keys.regenerate', $apiKey->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="重新生成" onclick="return confirm('确定要重新生成 Secret 吗？')">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('user.api-keys.destroy', $apiKey->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="删除" onclick="return confirm('确定要删除此密钥吗？')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $apiKeys->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-key" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">您还没有 API 密钥</p>
                    <a href="{{ route('user.api-keys.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-lg"></i> 生成您的第一个密钥
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection