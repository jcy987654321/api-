@extends('layouts.admin')

@section('title', 'API 密钥管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">API 密钥管理</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-list"></i> 调用日志
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.api-keys.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="搜索密钥名称或 Key..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">所有状态</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>激活</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>禁用</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    
    <div class="card-body">
        @if($apiKeys->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>名称</th>
                            <th>Key</th>
                            <th>用户</th>
                            <th>速率限制</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>最后使用</th>
                            <th>调用次数</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apiKeys as $apiKey)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.api-keys.show', $apiKey->id) }}" class="fw-medium">
                                        {{ $apiKey->name }}
                                    </a>
                                </td>
                                <td><code class="text-muted">{{ Str::limit($apiKey->key, 20) }}</code></td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $apiKey->user->name }}</div>
                                        <small class="text-muted">{{ $apiKey->user->email }}</small>
                                    </div>
                                </td>
                                <td>{{ $apiKey->rate_limit }}/{{ $apiKey->rate_window }}s</td>
                                <td>
                                    @if($apiKey->isActive())
                                        <span class="badge bg-success">激活</span>
                                    @else
                                        <span class="badge bg-secondary">禁用</span>
                                    @endif
                                </td>
                                <td>{{ $apiKey->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $apiKey->last_used_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $apiKey->logs_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.api-keys.show', $apiKey->id) }}" class="btn btn-outline-primary" title="详情">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($apiKey->isActive())
                                            <form action="{{ route('admin.api-keys.deactivate', $apiKey->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="禁用" onclick="return confirm('确定要禁用此密钥吗？')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.api-keys.activate', $apiKey->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="激活">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.api-keys.destroy', $apiKey->id) }}" method="POST" class="d-inline">
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

            <div class="mt-3">
                {{ $apiKeys->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-key" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">没有找到 API 密钥</p>
            </div>
        @endif
    </div>
</div>
@endsection

@php
    use Illuminate\Support\Str;
@endphp